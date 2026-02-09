@extends('layouts.app')
@section('title','Edit Customer')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Edit Customer</h4>
    <div class="text-muted">Ubah data pelanggan</div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('customers.update',$customer) }}">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Kode Customer</label>
          <input class="form-control" name="customer_code"
                 value="{{ old('customer_code', $customer->customer_code) }}">
        </div>
        <div class="col-md-8">
          <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
          <input class="form-control" name="customer_name" required
                 value="{{ old('customer_name', $customer->customer_name) }}">
        </div>

        <div class="col-md-4">
          <label class="form-label">Telepon</label>
          <input class="form-control" name="phone"
                 value="{{ old('phone', $customer->phone) }}">
        </div>
        <div class="col-md-8">
          <label class="form-label">Alamat</label>
          <input class="form-control" name="address"
                 value="{{ old('address', $customer->address) }}">
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">Kembali</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
@endsection
