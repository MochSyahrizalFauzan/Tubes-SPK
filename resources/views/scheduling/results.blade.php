@extends('layouts.app')
@section('title','Hasil Run')

<style>
  .gantt-wrap { border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px; background: #fff; }
  .gantt-row { display: grid; grid-template-columns: 70px 1fr; gap: 12px; align-items: center; margin-bottom: 10px; }
  .gantt-slot { font-weight: 700; color: #0f172a; }
  .gantt-track { position: relative; height: 58px; border-radius: 10px; border: 1px solid #e5e7eb; background: #f8fafc; overflow: hidden; }

  .gantt-bar{
    position:absolute; top:5px; height:46px; border-radius:10px;
    border:1px solid rgba(0,0,0,.08);
    padding:6px 10px;
    display:flex; flex-direction:column; justify-content:center;
    gap:2px;
    font-size:11px; line-height:1.1;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }

  .gantt-bar.ontime { background:#dcfce7; border-color:#86efac; }
  .gantt-bar.late   { background:#fee2e2; border-color:#fca5a5; }

  .bar-top{ display:flex; align-items:center; gap:6px; overflow:hidden; }
  .oid{ font-weight:800; overflow:hidden; text-overflow:ellipsis; max-width:220px; }
  .bar-bottom{ display:flex; justify-content:space-between; gap:10px; }
  .meta{ opacity:.9; overflow:hidden; text-overflow:ellipsis; }
  .range{ font-weight:800; white-space:nowrap; }

  .gantt-scale { display:flex; gap:8px; flex-wrap:wrap; font-size:12px; color:#64748b; margin-bottom: 10px; }
  .tick { padding: 2px 8px; border:1px dashed #cbd5e1; border-radius: 999px; background:#fff; }

  .badge-pill{
    border-radius:999px; padding:2px 8px;
    font-size:10px; font-weight:800;
    border:1px solid transparent;
  }
  .badge-due{ background:#e0f2fe; color:#075985; border-color:#bae6fd; }
  .badge-ok{ background:#16a34a; color:#fff; }
  .badge-late{ background:#ef4444; color:#fff; }
</style>



@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Hasil Scheduling Run #{{ $run->run_id }}</h4>
    <div class="text-muted">Run Date: {{ $run->run_date }} • Slots: {{ $run->capacity_slots }} • Orders: {{ $run->total_orders }}</div>
    @if($run->note)
      <div class="text-muted">Note: {{ $run->note }}</div>
    @endif
  </div>

  <div class="gantt-wrap mb-3">
  <div class="d-flex justify-content-between align-items-start mb-2">
    <div>
      <div class="fw-semibold">Gantt Chart (Hari relatif)</div>
      <div class="text-muted small">
        Timeline dari Day 0 sampai Day {{ $horizon }}
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 align-items-center mb-2 small text-muted">
    <span class="badge-pill badge-ok">On-time</span>
    <span class="badge-pill badge-late">Late</span>
    <span class="badge-pill badge-due">Due Dx</span>
    <span>Range: Dstart–Dfinish</span>
  </div>


  <div class="gantt-scale">
    @php
      // tampilkan tick tiap 2 hari biar rapi
      $step = $horizon <= 14 ? 1 : 2;
    @endphp
    @for($d=0; $d <= $horizon; $d += $step)
      <span class="tick">Day {{ $d }}</span>
    @endfor
  </div>

  @foreach($grouped as $slotId => $items)
    <div class="gantt-row">
      <div class="gantt-slot">{{ $slotId }}</div>

      <div class="gantt-track">
        @foreach($items as $it)
          @php
            $start = (int)$it->start_day;
            $finish = (int)$it->finish_day;
            $dur = max(1, $finish - $start);

            $leftPct = ($start / $horizon) * 100;
            $widthPct = ($dur / $horizon) * 100;

            $tard = (int)$it->tardiness_days;
            $isLate = $tard > 0;

            // butuh due_day dari controller (nanti aku jelaskan)
            $dueDay = (int)($it->due_day ?? 0);
          @endphp

          <div class="gantt-bar {{ $isLate ? 'late' : 'ontime' }}"
            style="left: {{ $leftPct }}%; width: {{ $widthPct }}%;"
            title="Order: {{ $it->order_id }} | {{ $it->machine_name }} | {{ $it->customer_name }} | Start: D{{ $start }} | Finish: D{{ $finish }} | Due: D{{ $dueDay }} | Tardiness: {{ $tard }} hari">
            <div class="bar-top">
              <span class="oid">{{ $it->order_id }}</span>
              <span class="badge-pill badge-due">Due D{{ $dueDay }}</span>
            
              @if($isLate)
                <span class="badge-pill badge-late">Late {{ $tard }}</span>
              @else
                <span class="badge-pill badge-ok">On-time</span>
              @endif
            </div>
          
            <div class="bar-bottom">
              <span class="meta">{{ $it->machine_name }} — {{ $it->customer_name }}</span>
              <span class="range">D{{ $start }}–D{{ $finish }}</span>
            </div>
          </div>

        @endforeach
      </div>
    </div>
  @endforeach
</div>


  <div class="card p-3">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Slot</th>
            <th>Order</th>
            <th>Start Day</th>
            <th>Finish Day</th>
            <th>Tardiness</th>
            <th>Decision</th>
          </tr>
        </thead>
        <tbody>
        @foreach($results as $r)
          <tr>
            <td class="fw-semibold">{{ $r->slot_id }}</td>
            <td class="fw-semibold">{{ $r->order_id }}</td>
            <td>{{ $r->start_day }}</td>
            <td>{{ $r->finish_day }}</td>
            <td>
              @if($r->tardiness_days > 0)
                <span class="badge text-bg-danger">{{ $r->tardiness_days }} hari</span>
              @else
                <span class="badge text-bg-success">0</span>
              @endif
            </td>
            <td><span class="badge text-bg-secondary">{{ $r->decision }}</span></td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <div class="d-flex gap-2 mt-3">
      <a class="btn btn-outline-secondary" href="{{ route('scheduling.runs') }}">Kembali</a>
      <a class="btn btn-primary" href="{{ route('scheduling.runForm') }}">Run Baru</a>
    </div>
  </div>
@endsection
