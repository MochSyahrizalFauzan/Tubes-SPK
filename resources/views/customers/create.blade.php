@extends('layouts.app')
@section('title','Tambah Customer')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Tambah Customer</h4>
    <div class="text-muted">Input data pelanggan baru</div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('customers.store') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Kode Customer</label>
          <input class="form-control" name="customer_code" value="{{ old('customer_code') }}" placeholder="CUST-001 (opsional)">
        </div>
        <div class="col-md-8">
          <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
          <input class="form-control" name="customer_name" value="{{ old('customer_name') }}" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Telepon</label>
          <input class="form-control" name="phone" value="{{ old('phone') }}">
        </div>
        <div class="col-md-8">
          <label class="form-label">Alamat</label>
          <input class="form-control" name="address" value="{{ old('address') }}">
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">Batal</a>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div>
@endsection
