@extends('layouts.app')
@section('title','Tambah Machine Size')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Tambah Machine Size</h4>
    <div class="text-muted">Input ukuran mesin</div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('machine-sizes.store') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Size ID <span class="text-danger">*</span></label>
          <input class="form-control" name="size_id" value="{{ old('size_id') }}"
                 maxlength="10" required placeholder="S / L / KECIL">
          <div class="form-text">Maks 10 karakter.</div>
        </div>

        <div class="col-md-9">
          <label class="form-label">Nama Ukuran <span class="text-danger">*</span></label>
          <input class="form-control" name="size_name" value="{{ old('size_name') }}"
                 maxlength="50" required placeholder="Kecil / Besar">
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('machine-sizes.index') }}">Batal</a>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div>
@endsection
