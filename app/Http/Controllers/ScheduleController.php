<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Slot;
use App\Models\ScheduleRun;
use App\Models\ScheduleResult;
use App\Models\AppUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class ScheduleController extends Controller
{
  public function runForm()
  {
    $slots = Slot::where('is_active', 1)->orderBy('slot_id')->get();
    $pendingCount = Order::whereIn('status', ['NEW', 'HOLD'])->count();

    return view('scheduling.run', [
      'slots' => $slots,
      'pendingCount' => $pendingCount,
      'baseDate' => date('Y-m-d'),
    ]);
  }

  public function run(Request $request)
  {
    $data = $request->validate([
      'base_date'      => 'required|date',
      'time_limit_sec' => 'nullable|integer|min:1|max:120',
      'note'           => 'nullable|string|max:255',
    ]);

    $baseDate = Carbon::parse($data['base_date'])->format('Y-m-d');
    $timeLimit = (int)($data['time_limit_sec'] ?? 10);

    $slots = Slot::where('is_active', 1)->orderBy('slot_id')->pluck('slot_id')->values();
    if ($slots->count() < 1) {
      return back()->withErrors(['base_date' => 'Slot aktif tidak ada. Seed slots S1..S7 dulu.']);
    }

    $orders = Order::whereIn('status', ['NEW', 'HOLD'])
      ->orderBy('due_date')
      ->orderBy('order_date')
      ->get(['order_id', 'order_date', 'due_date', 'process_days']);

    if ($orders->isEmpty()) {
      return back()->withErrors(['base_date' => 'Tidak ada order status NEW/HOLD untuk dijadwalkan.']);
    }

    // FK schedule_runs.created_by -> app_users.user_id (sesuai setup kamu)
    $createdBy = $this->ensureAppUserForBreezeUser();

    $payload = [
      'base_date' => $baseDate,
      'slots' => $slots->all(),
      'time_limit_sec' => $timeLimit,
      'workers' => 8,
      'orders' => $orders->map(fn($o) => [
        'order_id' => $o->order_id,
        'order_date' => Carbon::parse($o->order_date)->format('Y-m-d'),
        'due_date' => Carbon::parse($o->due_date)->format('Y-m-d'),
        'process_days' => (int)$o->process_days,
      ])->all(),
    ];

    $python = $this->pythonBin();
    $scriptPath = base_path('python/solver.py');

    try {
      $process = new Process([$python, $scriptPath]);
      $process->setInput(json_encode($payload));
      $process->setTimeout(180);
      $process->run();

      if (!$process->isSuccessful()) {
        $msg = trim($process->getErrorOutput() ?: $process->getOutput());
        return back()->withErrors([
          'base_date' => "Gagal menjalankan solver. Pastikan Python & ortools terinstall.\n" . $msg
        ]);
      }

      $out = json_decode($process->getOutput(), true);
      if (!is_array($out) || !($out['ok'] ?? false) || !isset($out['results'])) {
        return back()->withErrors([
          'base_date' => 'Solver tidak mengembalikan hasil valid: ' . ($out['message'] ?? 'unknown')
        ]);
      }
    } catch (\Throwable $e) {
      return back()->withErrors([
        'base_date' => "Terjadi kesalahan menjalankan solver: " . $e->getMessage()
      ]);
    }

    return DB::transaction(function () use ($data, $createdBy, $orders, $out, $slots, $baseDate) {
      $run = ScheduleRun::create([
        'run_date'       => now()->toDateTimeString(),
        'base_date'      => $baseDate, // ✅ penting untuk due_day & laporan
        'capacity_slots' => (int)$slots->count(),
        'total_orders'   => (int)$orders->count(),
        'method'         => 'ILP-ORTOOLS',
        'created_by'     => $createdBy,
        'note'           => $data['note'] ?? null,
      ]);

      $rows = [];
      foreach ($out['results'] as $r) {
        $rows[] = [
          'run_id' => $run->run_id,
          'order_id' => $r['order_id'],
          'decision' => $r['decision'],
          'slot_id' => $r['slot_id'] ?? null,
          'start_day' => (int)$r['start_day'],
          'finish_day' => (int)$r['finish_day'],
          'tardiness_days' => (int)$r['tardiness_days'],
          'reason' => $r['reason'] ?? null,
        ];
      }

      ScheduleResult::insert($rows);

      // Update status order: PRODUCE -> PRO / LATE
      // (lebih efisien pakai batch loop ringan saja)
      foreach ($rows as $rw) {
        $newStatus = ((int)$rw['tardiness_days']) > 0 ? 'LATE' : 'PRO';
        DB::table('orders')
          ->where('order_id', $rw['order_id'])
          ->update(['status' => $newStatus]);
      }

      return redirect()->route('scheduling.results', $run->run_id)
        ->with('success', 'Scheduling ILP berhasil dibuat. Total tardiness: ' . ((int)($out['objective_total_tardiness'] ?? 0)));
    });
  }

  public function runs()
  {
    $runs = ScheduleRun::orderByDesc('run_id')->paginate(10);
    return view('scheduling.runs', compact('runs'));
  }

  public function results($runId)
  {
    $run = ScheduleRun::where('run_id', $runId)->firstOrFail();

    $results = ScheduleResult::where('run_id', $runId)
      ->join('orders', 'schedule_results.order_id', '=', 'orders.order_id')
      ->join('machine_types', 'orders.machine_type_id', '=', 'machine_types.machine_type_id')
      ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
      ->select([
        'schedule_results.*',
        'orders.machine_type_id',
        'orders.size_id',
        'orders.process_days',
        'orders.due_date',
        'orders.status',
        'machine_types.machine_name',
        'customers.customer_name',
      ])
      ->orderBy('schedule_results.slot_id')
      ->orderBy('schedule_results.start_day')
      ->get();

    // ✅ pakai base_date dari run (kalau null, fallback ke tanggal run_date)
    $base = $run->base_date
      ? Carbon::parse($run->base_date)->startOfDay()
      : Carbon::parse($run->run_date)->startOfDay();

    $results = $results->map(function ($r) use ($base) {
      $due = Carbon::parse($r->due_date)->startOfDay();
      $r->due_day = (int)$base->diffInDays($due, false);
      return $r;
    });

    $maxFinish = (int)($results->max('finish_day') ?? 0);
    $horizon = max(1, $maxFinish);

    $grouped = $results->groupBy('slot_id');

    return view('scheduling.results', compact('run', 'results', 'grouped', 'horizon'));
  }

  private function pythonBin(): string
  {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'python' : 'python3';
  }

  private function ensureAppUserForBreezeUser(): int
  {
    $u = auth()->user();
    $id = (int)$u->id;

    AppUser::updateOrCreate(
      ['user_id' => $id],
      [
        'name' => $u->name ?? 'Admin',
        'username' => $u->email ?? ('admin' . $id),
        'password_hash' => $u->password ?? '',
        'role' => 'admin',
        'is_active' => 1,
      ]
    );

    return $id;
  }
}
