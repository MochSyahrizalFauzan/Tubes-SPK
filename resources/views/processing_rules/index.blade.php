@extends('layouts.app')
@section('title', 'Processing Rules')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Processing Rules</h4>
      <div class="text-muted">Aturan durasi proses & due default per kombinasi mesin + ukuran</div>
    </div>
    <a class="btn btn-primary" href="{{ route('processing-rules.create') }}">+ Tambah</a>
  </div>

  <form class="row g-2 mb-3" method="GET" action="{{ route('processing-rules.index') }}">
    <div class="col-md-4">
      <input class="form-control" name="q" value="{{ $q }}" placeholder="Cari machine_type_id / size_id...">
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
            <th style="width:80px">ID</th>
            <th>Machine</th>
            <th style="width:140px">Size</th>
            <th style="width:140px">Process Days</th>
            <th style="width:120px">Due Days</th>
            <th class="text-end" style="width:180px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($rules as $r)
          <tr>
            <td>{{ $r->rule_id }}</td>
            <td>
              <div class="fw-semibold">{{ $r->machine_type_id }}</div>
              <div class="text-muted small">{{ $r->machineType?->machine_name ?? '-' }}</div>
            </td>
            <td>
              <div class="fw-semibold">{{ $r->size_id }}</div>
              <div class="text-muted small">{{ $r->machineSize?->size_name ?? '-' }}</div>
            </td>
            <td class="fw-semibold">{{ $r->process_days }} hari</td>
            <td class="fw-semibold">{{ $r->due_days }} hari</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('processing-rules.edit',$r) }}">Edit</a>
              <form class="d-inline" method="POST" action="{{ route('processing-rules.destroy',$r) }}"
                    onsubmit="return confirm('Hapus rule ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">Belum ada rules.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $rules->links() }}
    </div>
  </div>
@endsection
