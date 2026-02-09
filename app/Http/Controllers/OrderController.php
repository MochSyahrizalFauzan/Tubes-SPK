<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\MachineType;
use App\Models\MachineSize;
use App\Models\MachineProcessingRule;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q',''));
    $status = $request->query('status','');
    $from = $request->query('from','');
    $to = $request->query('to','');

    $orders = Order::query()
      ->with(['customer','machineType','machineSize','invoice'])
      ->when($q !== '', function($query) use ($q){
        $query->where('order_id','like',"%$q%")
              ->orWhere('note','like',"%$q%")
              ->orWhereHas('customer', fn($cq) => $cq->where('customer_name','like',"%$q%"));
      })
      ->when($status !== '', fn($query) => $query->where('status',$status))
      ->when($from !== '', fn($query) => $query->whereDate('order_date','>=',$from))
      ->when($to !== '', fn($query) => $query->whereDate('order_date','<=',$to))
      ->orderByDesc('order_date')
      ->orderByDesc('created_at')
      ->paginate(10)
      ->withQueryString();

    return view('orders.index', compact('orders','q','status','from','to'));
  }

  public function create()
  {
    $customers = Customer::orderBy('customer_name')->get();
    $machineTypes = MachineType::orderBy('machine_type_id')->get();
    $machineSizes = MachineSize::orderBy('size_id')->get();
    $invoices = Invoice::orderByDesc('invoice_date')->limit(100)->get();

    return view('orders.create', compact('customers','machineTypes','machineSizes','invoices'));
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'customer_id'     => 'required|integer|exists:customers,customer_id',
      'invoice_id'      => 'nullable|string|max:25|exists:invoices,invoice_id',
      'machine_type_id' => 'required|string|exists:machine_types,machine_type_id',
      'size_id'         => 'required|string|exists:machine_sizes,size_id',
      'qty'             => 'required|integer|min:1|max:1000',
      'unit'            => 'nullable|string|max:30',
      'order_date'      => 'required|date',
      'due_date'        => 'nullable|date',
      'note'            => 'nullable|string|max:255',
    ]);

    // ambil rule (wajib ada)
    $rule = MachineProcessingRule::where('machine_type_id', $data['machine_type_id'])
      ->where('size_id', $data['size_id'])
      ->first();

    if (!$rule) {
      return back()->withInput()->withErrors([
        'size_id' => 'Processing Rule belum ada untuk kombinasi Machine Type + Size ini. Isi dulu di menu Processing Rules.'
      ]);
    }

    return DB::transaction(function () use ($data, $rule, $request) {
      $orderId = $this->generateOrderId($data['order_date']);

      $processDays = (int) $rule->process_days;
      $defaultDue = date('Y-m-d', strtotime($data['order_date'] . " +{$rule->due_days} days"));

      $order = Order::create([
        'order_id'        => $orderId,
        'invoice_id'      => $data['invoice_id'] ?? null,
        'customer_id'     => $data['customer_id'],
        'machine_type_id' => $data['machine_type_id'],
        'size_id'         => $data['size_id'],
        'process_days'    => $processDays,
        'qty'             => $data['qty'],
        'unit'            => $data['unit'] ?? null,
        'order_date'      => $data['order_date'],
        'due_date'        => $data['due_date'] ?? $defaultDue,
        'status'          => 'NEW',
        'note'            => $data['note'] ?? null,
      ]);

      return redirect()->route('orders.index')->with('success', "Order berhasil dibuat: {$order->order_id}");
    });
  }

  public function edit(Order $order)
  {
    $customers = Customer::orderBy('customer_name')->get();
    $machineTypes = MachineType::orderBy('machine_type_id')->get();
    $machineSizes = MachineSize::orderBy('size_id')->get();
    $invoices = Invoice::orderByDesc('invoice_date')->limit(100)->get();

    return view('orders.edit', compact('order','customers','machineTypes','machineSizes','invoices'));
  }

  public function update(Request $request, Order $order)
  {
    $data = $request->validate([
      'customer_id'     => 'required|integer|exists:customers,customer_id',
      'invoice_id'      => 'nullable|string|max:25|exists:invoices,invoice_id',
      'machine_type_id' => 'required|string|exists:machine_types,machine_type_id',
      'size_id'         => 'required|string|exists:machine_sizes,size_id',
      'qty'             => 'required|integer|min:1|max:1000',
      'unit'            => 'nullable|string|max:30',
      'order_date'      => 'required|date',
      'due_date'        => 'required|date',
      'status'          => 'required|in:NEW,PRO,HOLD,DONE,LATE',
      'note'            => 'nullable|string|max:255',
    ]);

    // kalau machine_type/size berubah → refresh process_days dari rule
    $rule = MachineProcessingRule::where('machine_type_id', $data['machine_type_id'])
      ->where('size_id', $data['size_id'])
      ->first();

    if (!$rule) {
      return back()->withInput()->withErrors([
        'size_id' => 'Processing Rule belum ada untuk kombinasi Machine Type + Size ini.'
      ]);
    }

    $order->update([
      'invoice_id'      => $data['invoice_id'] ?? null,
      'customer_id'     => $data['customer_id'],
      'machine_type_id' => $data['machine_type_id'],
      'size_id'         => $data['size_id'],
      'process_days'    => (int) $rule->process_days,
      'qty'             => $data['qty'],
      'unit'            => $data['unit'] ?? null,
      'order_date'      => $data['order_date'],
      'due_date'        => $data['due_date'],
      'status'          => $data['status'],
      'note'            => $data['note'] ?? null,
    ]);

    return redirect()->route('orders.index')->with('success', 'Order berhasil diupdate.');
  }

  public function destroy(Order $order)
  {
    $order->delete();
    return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus.');
  }

  public function show(Order $order)
  {
    return redirect()->route('orders.edit', $order);
  }

  private function generateOrderId(string $orderDate): string
  {
    // Format: ORD-YYYYMMDD-0001
    $datePart = date('Ymd', strtotime($orderDate));
    $prefix = "ORD-{$datePart}-";

    // lock untuk menghindari double id saat submit bersamaan
    $last = DB::table('orders')
      ->where('order_id', 'like', $prefix . '%')
      ->orderByDesc('order_id')
      ->lockForUpdate()
      ->value('order_id');

    $next = 1;
    if ($last) {
      $lastSeq = (int) substr($last, -4);
      $next = $lastSeq + 1;
    }

    return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
  }
}
