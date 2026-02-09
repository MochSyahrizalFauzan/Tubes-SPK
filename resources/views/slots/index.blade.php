@extends('layouts.app')
@section('title','Slots')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Slots</h4>
      <div class="text-muted">Kapasitas pengerjaan paralel (default 7 slot)</div>
    </div>

    <form method="POST" action="{{ route('slots.seed') }}">
      @csrf
      <button class="btn btn-outline-primary" type="submit"
              onclick="return confirm('Pastikan default S1..S7 ada dan aktif?')">
        Seed Default S1..S7
      </button>
    </form>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:140px">Slot ID</th>
            <th>Nama Slot</th>
            <th style="width:140px">Status</th>
            <th class="text-end" style="width:160px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($slots as $s)
            <tr>
              <td class="fw-semibold">{{ $s->slot_id }}</td>
              <td>{{ $s->slot_name }}</td>
              <td>
                @if($s->is_active)
                  <span class="badge text-bg-success">Active</span>
                @else
                  <span class="badge text-bg-secondary">Inactive</span>
                @endif
              </td>
              <td class="text-end">
                <form method="POST" action="{{ route('slots.toggle', $s->slot_id) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-secondary" type="submit">
                    Toggle
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">
                Belum ada slot. Klik “Seed Default S1..S7”.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
