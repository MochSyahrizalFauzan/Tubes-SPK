@extends('layouts.app')
@section('title', 'Customers')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Customers</h4>
      <div class="text-muted">Data pelanggan</div>
    </div>
    <a class="btn btn-primary" href="{{ route('customers.create') }}">+ Tambah</a>
  </div>

  <form class="row g-2 mb-3" method="GET" action="{{ route('customers.index') }}">
    <div class="col-md-4">
      <input class="form-control" name="q" value="{{ $q }}" placeholder="Cari nama / kode...">
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
            <th>ID</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Telp</th>
            <th>Alamat</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($customers as $c)
          <tr>
            <td>{{ $c->customer_id }}</td>
            <td>{{ $c->customer_code ?? '-' }}</td>
            <td class="fw-semibold">{{ $c->customer_name }}</td>
            <td>{{ $c->phone ?? '-' }}</td>
            <td style="max-width:320px" class="text-truncate">{{ $c->address ?? '-' }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('customers.edit',$c) }}">Edit</a>
              <form class="d-inline" method="POST" action="{{ route('customers.destroy',$c) }}"
                    onsubmit="return confirm('Hapus customer ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $customers->links() }}
    </div>
  </div>
@endsection
