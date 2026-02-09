@extends('layouts.app')
@section('title','Edit Machine Size')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Edit Machine Size</h4>
    <div class="text-muted">Ubah ukuran mesin</div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('machine-sizes.update',$machineSize) }}">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Size ID <span class="text-danger">*</span></label>
          <input class="form-control" name="size_id"
                 value="{{ old('size_id', $machineSize->size_id) }}"
                 maxlength="10" required>
          <div class="form-text">Jika ID diubah dan sudah dipakai, DB bisa menolak.</div>
        </div>

        <div class="col-md-9">
          <label class="form-label">Nama Ukuran <span class="text-danger">*</span></label>
          <input class="form-control" name="size_name"
                 value="{{ old('size_name', $machineSize->size_name) }}"
                 maxlength="50" required>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('machine-sizes.index') }}">Kembali</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
@endsection
