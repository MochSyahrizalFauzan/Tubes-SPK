<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    // KPI utama
    $totalCustomers = (int) DB::table('customers')->count();
    $totalOrders    = (int) DB::table('orders')->count();
    $totalInvoices  = (int) DB::table('invoices')->count();
    $activeSlots    = (int) DB::table('slots')->where('is_active', 1)->count();

    // Order by status
    $statusCounts = DB::table('orders')
      ->select('status', DB::raw('COUNT(*) as total'))
      ->groupBy('status')
      ->pluck('total', 'status')
      ->toArray();

    $pendingOrders = (int) (($statusCounts['NEW'] ?? 0) + ($statusCounts['HOLD'] ?? 0));
    $lateOrders    = (int) ($statusCounts['LATE'] ?? 0);
    $inProgress    = (int) ($statusCounts['PRO'] ?? 0);
    $doneOrders    = (int) ($statusCounts['DONE'] ?? 0);

    // Run ILP
    $totalRuns = (int) DB::table('schedule_runs')->count();
    $lastRun = DB::table('schedule_runs')->orderByDesc('run_id')->first();

    $lastRunSummary = null;
    if ($lastRun) {
      $sumTardiness = (int) DB::table('schedule_results')
        ->where('run_id', $lastRun->run_id)
        ->sum('tardiness_days');

      $lateInRun = (int) DB::table('schedule_results')
        ->where('run_id', $lastRun->run_id)
        ->where('tardiness_days', '>', 0)
        ->count();

      $lastRunSummary = [
        'run_id' => $lastRun->run_id,
        'run_date' => $lastRun->run_date,
        'method' => $lastRun->method,
        'total_orders' => (int) $lastRun->total_orders,
        'capacity_slots' => (int) $lastRun->capacity_slots,
        'sum_tardiness' => $sumTardiness,
        'late_jobs' => $lateInRun,
      ];
    }

    // Data chart sederhana: orders 14 hari terakhir (by order_date)
    $ordersDaily = DB::table('orders')
      ->select(DB::raw('DATE(order_date) as d'), DB::raw('COUNT(*) as total'))
      ->where('order_date', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 13 DAY)'))
      ->groupBy('d')
      ->orderBy('d')
      ->get();

    // isi tanggal yang kosong biar chart rapi
    $dailyMap = [];
    foreach ($ordersDaily as $row) $dailyMap[$row->d] = (int)$row->total;

    $labels = [];
    $values = [];
    for ($i = 13; $i >= 0; $i--) {
      $d = date('Y-m-d', strtotime("-$i days"));
      $labels[] = $d;
      $values[] = $dailyMap[$d] ?? 0;
    }

    // ====== Gantt Dashboard: Running Orders (PRO/LATE) from last run ======
$gantt = [
  'run_id' => null,
  'horizon' => 1,
  'grouped' => collect(),
];

if ($lastRun) {
  $runId = $lastRun->run_id;

$ganttResults = DB::table('schedule_results as sr')
  // ambil schedule_results TERBARU per order_id (latest run)
  ->join(DB::raw('(
    SELECT order_id, MAX(run_id) AS last_run_id
    FROM schedule_results
    GROUP BY order_id
  ) AS last'), function ($join) {
    $join->on('sr.order_id', '=', 'last.order_id')
         ->on('sr.run_id', '=', 'last.last_run_id');
  })
  ->join('orders as o', 'sr.order_id', '=', 'o.order_id')
  ->join('machine_types as mt', 'o.machine_type_id', '=', 'mt.machine_type_id')
  ->join('customers as c', 'o.customer_id', '=', 'c.customer_id')
  ->whereIn('o.status', ['PRO','LATE'])
  ->select([
    'sr.run_id',
    'sr.slot_id',
    'sr.order_id',
    'sr.start_day',
    'sr.finish_day',
    'sr.tardiness_days',
    'mt.machine_name',
    'c.customer_name',
    'o.status',
  ])
  ->orderBy('sr.slot_id')
  ->orderBy('sr.start_day')
  ->get();


$maxFinish = (int) ($ganttResults->max('finish_day') ?? 0);
$horizon = max(1, $maxFinish);


$gantt = [
  'run_id' => $lastRun?->run_id, // hanya untuk info, gantt pakai latest per-order
  'horizon' => $horizon,
  'grouped' => $ganttResults->groupBy('slot_id'),
];

}


return view('dashboard.index', compact(
  'totalCustomers','totalOrders','totalInvoices','activeSlots',
  'pendingOrders','lateOrders','inProgress','doneOrders',
  'totalRuns','lastRunSummary',
  'labels','values','statusCounts',
  'gantt'
));


  }
}
