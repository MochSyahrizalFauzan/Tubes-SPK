@extends('layouts.app')
@section('title','Tambah Order')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Tambah Order</h4>
    <div class="text-muted">Process days otomatis dari Processing Rules</div>
  </div>

  <div class="card p-3">
    @if($customers->isEmpty() || $machineTypes->isEmpty() || $machineSizes->isEmpty())
      <div class="alert alert-warning mb-0">
        Master data belum lengkap. Isi dulu Customers, Machine Types, Machine Sizes.
      </div>
    @else
      <form method="POST" action="{{ route('orders.store') }}">
        @csrf

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Customer <span class="text-danger">*</span></label>
            <select class="form-select" name="customer_id" required>
              <option value="">- pilih -</option>
              @foreach($customers as $c)
                <option value="{{ $c->customer_id }}" {{ old('customer_id')==$c->customer_id?'selected':'' }}>
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
                <option value="{{ $i->invoice_id }}" {{ old('invoice_id')==$i->invoice_id?'selected':'' }}>
                  {{ $i->invoice_id }} — {{ $i->invoice_date }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Machine Type <span class="text-danger">*</span></label>
            <select class="form-select" name="machine_type_id" required>
              <option value="">- pilih -</option>
              @foreach($machineTypes as $m)
                <option value="{{ $m->machine_type_id }}" {{ old('machine_type_id')==$m->machine_type_id?'selected':'' }}>
                  {{ $m->machine_type_id }} — {{ $m->machine_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Size <span class="text-danger">*</span></label>
            <select class="form-select" name="size_id" required>
              <option value="">- pilih -</option>
              @foreach($machineSizes as $s)
                <option value="{{ $s->size_id }}" {{ old('size_id')==$s->size_id?'selected':'' }}>
                  {{ $s->size_id }} — {{ $s->size_name }}
                </option>
              @endforeach
            </select>
            <div class="form-text">Wajib ada rule untuk kombinasi ini.</div>
          </div>

          <div class="col-md-2">
            <label class="form-label">Qty <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="qty" min="1" max="1000"
                   value="{{ old('qty',1) }}" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Unit</label>
            <input class="form-control" name="unit" value="{{ old('unit','unit') }}" maxlength="30">
          </div>

          <div class="col-md-3">
            <label class="form-label">Order Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="order_date"
                   value="{{ old('order_date', date('Y-m-d')) }}" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Due Date (opsional)</label>
            <input type="date" class="form-control" name="due_date" value="{{ old('due_date') }}">
            <div class="form-text">Kosongkan untuk pakai default dari rule (order_date + due_days).</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Note</label>
            <input class="form-control" name="note" value="{{ old('note') }}" maxlength="255">
          </div>
        </div>

        <div class="d-flex gap-2 mt-3">
          <a class="btn btn-outline-secondary" href="{{ route('orders.index') }}">Batal</a>
          <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
      </form>
    @endif
  </div>
@endsection
