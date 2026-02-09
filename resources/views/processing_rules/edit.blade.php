@extends('layouts.app')
@section('title','Edit Processing Rule')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Edit Processing Rule</h4>
    <div class="text-muted">Ubah durasi proses & due default</div>
  </div>

  <div class="card p-3">
    <form method="POST" action="{{ route('processing-rules.update',$processingRule) }}">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-5">
          <label class="form-label">Machine Type <span class="text-danger">*</span></label>
          <select class="form-select" name="machine_type_id" required>
            @foreach($machineTypes as $m)
              <option value="{{ $m->machine_type_id }}"
                {{ old('machine_type_id', $processingRule->machine_type_id) == $m->machine_type_id ? 'selected' : '' }}>
                {{ $m->machine_type_id }} — {{ $m->machine_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Size <span class="text-danger">*</span></label>
          <select class="form-select" name="size_id" required>
            @foreach($machineSizes as $s)
              <option value="{{ $s->size_id }}"
                {{ old('size_id', $processingRule->size_id) == $s->size_id ? 'selected' : '' }}>
                {{ $s->size_id }} — {{ $s->size_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Process Days <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="process_days" min="1" max="365"
                 value="{{ old('process_days', $processingRule->process_days) }}" required>
        </div>

        <div class="col-md-2">
          <label class="form-label">Due Days <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="due_days" min="1" max="365"
                 value="{{ old('due_days', $processingRule->due_days) }}" required>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('processing-rules.index') }}">Kembali</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
@endsection
