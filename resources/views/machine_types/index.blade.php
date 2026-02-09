@extends('layouts.app')
@section('title', 'Machine Types')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Machine Types</h4>
      <div class="text-muted">Jenis mesin (aktif/nonaktif)</div>
    </div>
    <a class="btn btn-primary" href="{{ route('machine-types.create') }}">+ Tambah</a>
  </div>

  <form class="row g-2 mb-3" method="GET" action="{{ route('machine-types.index') }}">
    <div class="col-md-4">
      <input class="form-control" name="q" value="{{ $q }}" placeholder="Cari ID / nama...">
    </div>
    <div class="col-md-2">
      <button class="btn btn-outline-secondary w-100" type="submit">Cari</button>
    </div>
  </form>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:140px">ID</th>
            <th>Nama Mesin</th>
            <th style="width:120px">Status</th>
            <th class="text-end" style="width:180px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($machineTypes as $m)
          <tr>
            <td class="fw-semibold">{{ $m->machine_type_id }}</td>
            <td>{{ $m->machine_name }}</td>
            <td>
              @if($m->is_active)
                <span class="badge text-bg-success">Active</span>
              @else
                <span class="badge text-bg-secondary">Inactive</span>
              @endif
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('machine-types.edit',$m) }}">Edit</a>
              <form class="d-inline" method="POST" action="{{ route('machine-types.destroy',$m) }}"
                    onsubmit="return confirm('Hapus machine type ini? Jika sudah dipakai di orders/rules, akan ditolak oleh DB.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $machineTypes->links() }}
    </div>
  </div>
@endsection
