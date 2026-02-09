@extends('layouts.app')
@section('title','Tambah Processing Rule')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Tambah Processing Rule</h4>
    <div class="text-muted">Set durasi proses & due default</div>
  </div>

  <div class="card p-3">
    @if($machineTypes->isEmpty() || $machineSizes->isEmpty())
      <div class="alert alert-warning mb-0">
        Machine Types atau Machine Sizes masih kosong. Isi dulu master datanya.
      </div>
    @else
      <form method="POST" action="{{ route('processing-rules.store') }}">
        @csrf

        <div class="row g-3">
          <div class="col-md-5">
            <label class="form-label">Machine Type <span class="text-danger">*</span></label>
            <select class="form-select" name="machine_type_id" required>
              <option value="">- pilih -</option>
              @foreach($machineTypes as $m)
                <option value="{{ $m->machine_type_id }}"
                  {{ old('machine_type_id') == $m->machine_type_id ? 'selected' : '' }}>
                  {{ $m->machine_type_id }} — {{ $m->machine_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Size <span class="text-danger">*</span></label>
            <select class="form-select" name="size_id" required>
              <option value="">- pilih -</option>
              @foreach($machineSizes as $s)
                <option value="{{ $s->size_id }}"
                  {{ old('size_id') == $s->size_id ? 'selected' : '' }}>
                  {{ $s->size_id }} — {{ $s->size_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label">Process Days <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="process_days" min="1" max="365"
                   value="{{ old('process_days', 7) }}" required>
          </div>

          <div class="col-md-2">
            <label class="form-label">Due Days <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="due_days" min="1" max="365"
                   value="{{ old('due_days', 7) }}" required>
          </div>
        </div>

        <div class="d-flex gap-2 mt-3">
          <a class="btn btn-outline-secondary" href="{{ route('processing-rules.index') }}">Batal</a>
          <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
      </form>
    @endif
  </div>
@endsection
