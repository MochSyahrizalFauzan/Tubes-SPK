@extends('layouts.app')
@section('title', 'Machine Sizes')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Machine Sizes</h4>
      <div class="text-muted">Ukuran mesin (Kecil/Besar)</div>
    </div>
    <a class="btn btn-primary" href="{{ route('machine-sizes.create') }}">+ Tambah</a>
  </div>

  <form class="row g-2 mb-3" method="GET" action="{{ route('machine-sizes.index') }}">
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
            <th>Nama Ukuran</th>
            <th class="text-end" style="width:180px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($machineSizes as $s)
          <tr>
            <td class="fw-semibold">{{ $s->size_id }}</td>
            <td>{{ $s->size_name }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('machine-sizes.edit',$s) }}">Edit</a>
              <form class="d-inline" method="POST" action="{{ route('machine-sizes.destroy',$s) }}"
                    onsubmit="return confirm('Hapus ukuran ini? Jika sudah dipakai di rules/orders, DB bisa menolak.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $machineSizes->links() }}
    </div>
  </div>
@endsection
