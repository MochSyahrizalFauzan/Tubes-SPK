@extends('layouts.app')
@section('title','Edit Order')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Edit Order</h4>
    <div class="text-muted">Order ID: <span class="fw-semibold">{{ $order->order_id }}</span></div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('orders.update',$order) }}">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Customer <span class="text-danger">*</span></label>
          <select class="form-select" name="customer_id" required>
            @foreach($customers as $c)
              <option value="{{ $c->customer_id }}"
                {{ old('customer_id',$order->customer_id)==$c->customer_id?'selected':'' }}>
                {{ $c->customer_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Invoice (opsional)</label>
          <select class="form-select" name="invoice_id">
            <option value="">- tanpa invoice -</option>
            @foreach($invoices as $i)
              <option value="{{ $i->invoice_id }}"
                {{ old('invoice_id',$order->invoice_id)==$i->invoice_id?'selected':'' }}>
                {{ $i->invoice_id }} — {{ $i->invoice_date }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Machine Type <span class="text-danger">*</span></label>
          <select class="form-select" name="machine_type_id" required>
            @foreach($machineTypes as $m)
              <option value="{{ $m->machine_type_id }}"
                {{ old('machine_type_id',$order->machine_type_id)==$m->machine_type_id?'selected':'' }}>
                {{ $m->machine_type_id }} — {{ $m->machine_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Size <span class="text-danger">*</span></label>
          <select class="form-select" name="size_id" required>
            @foreach($machineSizes as $s)
              <option value="{{ $s->size_id }}"
                {{ old('size_id',$order->size_id)==$s->size_id?'selected':'' }}>
                {{ $s->size_id }} — {{ $s->size_name }}
              </option>
            @endforeach
          </select>
          <div class="form-text">Process days akan mengikuti rule.</div>
        </div>

        <div class="col-md-2">
          <label class="form-label">Qty <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="qty" min="1" max="1000"
                 value="{{ old('qty',$order->qty) }}" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Unit</label>
          <input class="form-control" name="unit" value="{{ old('unit',$order->unit) }}" maxlength="30">
        </div>

        <div class="col-md-3">
          <label class="form-label">Order Date <span class="text-danger">*</span></label>
          <input type="date" class="form-control" name="order_date"
                 value="{{ old('order_date', optional($order->order_date)->format('Y-m-d')) }}" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Due Date <span class="text-danger">*</span></label>
          <input type="date" class="form-control" name="due_date"
                 value="{{ old('due_date', optional($order->due_date)->format('Y-m-d')) }}" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Status <span class="text-danger">*</span></label>
          <select class="form-select" name="status" required>
            @foreach(['NEW','PRO','HOLD','DONE','LATE'] as $s)
              <option value="{{ $s }}" {{ old('status',$order->status)===$s?'selected':'' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-12">
          <label class="form-label">Note</label>
          <input class="form-control" name="note" value="{{ old('note',$order->note) }}" maxlength="255">
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('orders.index') }}">Kembali</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>

      <div class="mt-3 text-muted small">
        Process Days saat ini: <span class="fw-semibold">{{ $order->process_days }}</span> hari (dari Processing Rules).
      </div>
    </form>
  </div>
@endsection
