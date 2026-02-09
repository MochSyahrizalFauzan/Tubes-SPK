@extends('layouts.app')
@section('title','Riwayat Run')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Riwayat Scheduling Run</h4>
      <div class="text-muted">Daftar eksekusi ILP</div>
    </div>
    <a class="btn btn-primary" href="{{ route('scheduling.runForm') }}">+ Run Baru</a>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Run Date</th>
            <th>Slots</th>
            <th>Total Orders</th>
            <th>Method</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($runs as $r)
          <tr>
            <td class="fw-semibold">{{ $r->run_id }}</td>
            <td>{{ $r->run_date }}</td>
            <td>{{ $r->capacity_slots }}</td>
            <td>{{ $r->total_orders }}</td>
            <td><span class="badge text-bg-secondary">{{ $r->method }}</span></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('scheduling.results',$r->run_id) }}">Lihat</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">Belum ada run.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $runs->links() }}
    </div>
  </div>
@endsection
