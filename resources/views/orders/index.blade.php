@extends('layouts.app')
@section('title','Orders')

@section('content')
  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Orders</h4>
      <div class="text-muted">Data pesanan produksi (Make To Order)</div>
    </div>
    <a class="btn btn-primary" href="{{ route('orders.create') }}">
      + Tambah Order
    </a>
  </div>

  {{-- Filter --}}
  <form class="card p-3 mb-3" method="GET" action="{{ route('orders.index') }}">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label small text-muted">Pencarian</label>
        <input class="form-control"
               name="q"
               value="{{ $q }}"
               placeholder="Order ID / Customer / Catatan">
      </div>

      <div class="col-md-2">
        <label class="form-label small text-muted">Status</label>
        <select class="form-select" name="status">
          <option value="">Semua</option>
          @foreach(['NEW','PRO','HOLD','DONE','LATE'] as $s)
            <option value="{{ $s }}" {{ $status===$s ? 'selected':'' }}>
              {{ $s }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label small text-muted">Dari</label>
        <input type="date" class="form-control" name="from" value="{{ $from }}">
      </div>

      <div class="col-md-2">
        <label class="form-label small text-muted">Sampai</label>
        <input type="date" class="form-control" name="to" value="{{ $to }}">
      </div>

      <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-secondary w-100" type="submit">
          Filter
        </button>
        <a class="btn btn-outline-light border w-100"
           href="{{ route('orders.index') }}">
          Reset
        </a>
      </div>
    </div>
  </form>

  {{-- Table --}}
  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Mesin</th>
            <th>Qty</th>
            <th>Order</th>
            <th>Due</th>
            <th>Status</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($orders as $o)
          <tr>
            {{-- Order ID --}}
            <td class="fw-semibold">{{ $o->order_id }}</td>

            {{-- Customer --}}
            <td>
              <div class="fw-semibold">
                {{ $o->customer?->customer_name ?? '-' }}
              </div>
              <div class="text-muted small">
                {{ $o->customer?->customer_code ?? '' }}
              </div>
            </td>

            {{-- Machine --}}
            <td>
              <div class="fw-semibold">
                {{ $o->machineType?->machine_name ?? '-' }}
              </div>
              <div class="text-muted small">
                Size: {{ $o->machineSize?->size_name ?? '-' }}
              </div>
              <div class="text-muted small">
                Proses: {{ $o->process_days }} hari
              </div>
            </td>

            {{-- Qty --}}
            <td>{{ $o->qty }} {{ $o->unit ?? '' }}</td>

            {{-- Dates --}}
            <td>{{ optional($o->order_date)->format('Y-m-d') }}</td>
            <td>{{ optional($o->due_date)->format('Y-m-d') }}</td>

            {{-- Status --}}
            <td>
              @php
                $badge = match($o->status){
                  'NEW'  => 'secondary',
                  'PRO'  => 'success',
                  'HOLD' => 'warning',
                  'DONE' => 'dark',
                  'LATE' => 'danger',
                  default => 'secondary'
                };
              @endphp
              <span class="badge text-bg-{{ $badge }}">
                {{ $o->status }}
              </span>
            </td>

            {{-- Actions --}}
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary"
                 href="{{ route('orders.edit',$o) }}">
                Edit
              </a>

              @if(!in_array($o->status, ['PRO','DONE']))
                <form class="d-inline"
                      method="POST"
                      action="{{ route('orders.destroy',$o) }}"
                      onsubmit="return confirm('Hapus order {{ $o->order_id }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" type="submit">
                    Hapus
                  </button>
                </form>
              @else
                <button class="btn btn-sm btn-outline-secondary"
                        disabled
                        title="Order sedang diproses / selesai">
                  Hapus
                </button>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              Tidak ada data order.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
      {{ $orders->links() }}
    </div>
  </div>
@endsection
