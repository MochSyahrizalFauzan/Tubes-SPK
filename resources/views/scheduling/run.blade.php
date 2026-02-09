@extends('layouts.app')
@section('title','Run ILP')

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Run Scheduling (ILP - OR-Tools)</h4>
    <div class="text-muted">Minimalkan keterlambatan (total tardiness) dengan kapasitas slot paralel</div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card p-3">
        <div class="text-muted">Order pending (NEW/HOLD)</div>
        <div class="fs-3 fw-bold">{{ $pendingCount }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <div class="text-muted">Slot aktif</div>
        <div class="fs-3 fw-bold">{{ $slots->count() }}</div>
        <div class="small text-muted mt-1">
          {{ $slots->pluck('slot_id')->join(', ') }}
        </div>
      </div>
    </div>
  </div>

  <div class="card p-3 mt-3">
    <form method="POST" action="{{ route('scheduling.run') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Base Date <span class="text-danger">*</span></label>
          <input type="date" class="form-control" name="base_date" value="{{ old('base_date', $baseDate) }}" required>
          <div class="form-text">Hari ke-0 untuk perhitungan start/finish.</div>
        </div>

        <div class="col-md-3">
          <label class="form-label">Time Limit (sec)</label>
          <input type="number" class="form-control" name="time_limit_sec" min="1" max="120"
                 value="{{ old('time_limit_sec', 10) }}">
          <div class="form-text">Naikkan jika order banyak.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Note</label>
          <input class="form-control" name="note" maxlength="255" value="{{ old('note') }}" placeholder="Run Januari, overload, dll">
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary" type="submit">Run ILP</button>
        <a class="btn btn-outline-secondary" href="{{ route('scheduling.runs') }}">Riwayat</a>
      </div>
    </form>
  </div>
@endsection
