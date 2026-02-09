@extends('layouts.app')
@section('title','Tambah Machine Type')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Tambah Machine Type</h4>
    <div class="text-muted">Input jenis mesin</div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('machine-types.store') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Machine Type ID <span class="text-danger">*</span></label>
          <input class="form-control" name="machine_type_id" value="{{ old('machine_type_id') }}"
                 maxlength="10" required placeholder="MT01">
          <div class="form-text">Maks 10 karakter (contoh: HP, CC, MT01).</div>
        </div>

        <div class="col-md-7">
          <label class="form-label">Nama Mesin <span class="text-danger">*</span></label>
          <input class="form-control" name="machine_name" value="{{ old('machine_name') }}" required
                 maxlength="120" placeholder="Mesin Heat Press">
        </div>

        <div class="col-md-2 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('machine-types.index') }}">Batal</a>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div>
@endsection
