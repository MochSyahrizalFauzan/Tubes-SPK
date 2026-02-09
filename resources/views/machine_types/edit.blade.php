@extends('layouts.app')
@section('title','Edit Machine Type')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Edit Machine Type</h4>
    <div class="text-muted">Ubah jenis mesin</div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('machine-types.update',$machineType) }}">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Machine Type ID <span class="text-danger">*</span></label>
          <input class="form-control" name="machine_type_id"
                 value="{{ old('machine_type_id', $machineType->machine_type_id) }}"
                 maxlength="10" required>
          <div class="form-text">Jika ID diubah dan sudah dipakai di rules/orders, DB bisa menolak.</div>
        </div>

        <div class="col-md-7">
          <label class="form-label">Nama Mesin <span class="text-danger">*</span></label>
          <input class="form-control" name="machine_name"
                 value="{{ old('machine_name', $machineType->machine_name) }}"
                 maxlength="120" required>
        </div>

        <div class="col-md-2 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   id="is_active" {{ old('is_active', $machineType->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('machine-types.index') }}">Kembali</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
@endsection
