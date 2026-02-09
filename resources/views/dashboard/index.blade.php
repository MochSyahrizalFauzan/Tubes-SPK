@extends('layouts.app')
@section('title','Dashboard')

@section('styles')

<style>
  /* ===== Dashboard UI tokens ===== */
  :root{
    --bd:#e5e7eb;
    --muted:#64748b;
    --text:#0f172a;
    --bg:#f6f7fb;
    --card:#ffffff;

    --new:#0ea5e9;
    --hold:#f59e0b;
    --pro:#22c55e;
    --done:#64748b;
    --late:#ef4444;
  }

  /* Head */
  .dash-head h4{ color: var(--text); }
  .dash-sub{ color: var(--muted); }

  /* KPI cards */
  .kpi-card{
    border:1px solid var(--bd);
    border-radius:14px;
    background:var(--card);
    padding:14px 16px;
    height:100%;
  }
  .kpi-title{ color:var(--muted); font-size:.9rem; }
  .kpi-value{ color:var(--text); font-size:1.75rem; font-weight:800; line-height:1.1; }
  .kpi-foot{ color:var(--muted); font-size:.82rem; margin-top:6px; }
  .kpi-badge{
    display:inline-flex; align-items:center; gap:6px;
    padding:2px 10px; border-radius:999px;
    border:1px solid var(--bd);
    font-size:.78rem; color:var(--text); background:#fff;
  }
  .dot{ width:8px; height:8px; border-radius:999px; display:inline-block; }

  /* Panel cards */
  .panel-card{
    border:1px solid var(--bd);
    border-radius:14px;
    background:var(--card);
    height:100%;
  }
  .panel-card .panel-inner{ padding:14px 16px; }
  .panel-title{ font-weight:700; color:var(--text); }
  .panel-actions .btn{ border-radius:10px; }

  /* Order status bar */
  .st-row{ display:grid; grid-template-columns:70px 1fr 42px; gap:10px; align-items:center; margin-bottom:10px; }
  .st-label{ font-weight:700; color:var(--text); font-size:.9rem; }
  .st-track{
    height:12px; border:1px solid var(--bd); border-radius:999px;
    background:#f8fafc; overflow:hidden;
  }
  .st-fill{ height:100%; border-radius:999px; }
  .st-val{ text-align:right; color:var(--text); font-weight:700; font-size:.9rem; }

  /* Scheduling mini cards */
  .mini-metric{
    border:1px solid var(--bd);
    border-radius:12px;
    padding:10px 12px;
    background:#fff;
    height:100%;
  }
  .mini-metric .lbl{ color:var(--muted); font-size:.78rem; }
  .mini-metric .val{ color:var(--text); font-weight:800; font-size:1.05rem; margin-top:2px; }

  /* ===== Gantt ===== */
  .gantt-card{
    border:1px solid var(--bd);
    border-radius:14px;
    background:#fff;
  }
  .gantt-inner{ padding:14px 16px; }

  /* Make gantt horizontally scrollable */
  .gantt-scroll{
    overflow-x:auto;
    overflow-y:hidden;
    padding-bottom:6px;
  }

  /* Keep labels visible and align with track width */
  .gantt-grid{
    min-width: 980px; /* ensures not squished, will scroll if viewport smaller */
    display:flex;
    flex-direction:column;
    gap:10px;
  }

  .gantt-scale{
    display:flex;
    gap:8px;
    flex-wrap:nowrap;
    white-space:nowrap;
    color:var(--muted);
    font-size:12px;
    margin-bottom:10px;
  }
  .tick{
    padding:2px 10px;
    border:1px dashed #cbd5e1;
    border-radius:999px;
    background:#fff;
  }

  .gantt-row{
    display:grid;
    grid-template-columns:78px 1fr;
    gap:12px;
    align-items:center;
  }
  .gantt-slot{
    font-weight:800;
    color:var(--text);
  }
  .gantt-track{
    position:relative;
    height:40px;
    border-radius:12px;
    border:1px solid var(--bd);
    background:linear-gradient(180deg,#f8fafc,#ffffff);
    overflow:hidden;
  }

  /* subtle vertical grid lines (optional) */
  .gantt-track::before{
    content:"";
    position:absolute; inset:0;
    background-image: repeating-linear-gradient(
      to right,
      rgba(148,163,184,.22),
      rgba(148,163,184,.22) 1px,
      transparent 1px,
      transparent 80px
    );
    opacity:.35;
    pointer-events:none;
  }

  .gantt-bar{
    position:absolute;
    top:7px;
    height:26px;
    border-radius:10px;
    border:1px solid rgba(15,23,42,.10);
    display:flex;
    align-items:center;
    gap:8px;
    padding:0 10px;
    font-size:11px;
    font-weight:800;
    color:var(--text);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    box-shadow: 0 1px 0 rgba(0,0,0,.03);
    background:#dbeafe;
  }
  .gantt-bar.pro{ background:#dcfce7; }
  .gantt-bar.late{ background:#fee2e2; }

  .gantt-badge{
    font-size:10px;
    font-weight:900;
    padding:2px 7px;
    border-radius:999px;
    border:1px solid rgba(0,0,0,.08);
    background:#fff;
  }

  /* Orders 14d mini chart */
  .spark-wrap{
    border:1px solid var(--bd);
    border-radius:14px;
    padding:14px 16px;
    background:#fff;
  }
  .spark-bars{
    height:120px;
    display:flex;
    align-items:flex-end;
    gap:6px;
  }
  .spark-bar{
    flex:1;
    border:1px solid var(--bd);
    border-radius:10px;
    background:#e2e8f0;
    min-width:6px;
  }
</style>
@endsection

@section('content')
  {{-- HEADER --}}
  <div class="dash-head mb-3">
    <h4 class="mb-0">Dashboard</h4>
    <div class="dash-sub">Ringkasan produksi & penjadwalan (ILP)</div>
  </div>

  {{-- KPI --}}
  <div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="kpi-title">Customers</div>
            <div class="kpi-value">{{ $totalCustomers }}</div>
          </div>
          <span class="kpi-badge"><span class="dot" style="background:var(--new)"></span>Master</span>
        </div>
        <div class="kpi-foot">Total pelanggan terdaftar</div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="kpi-title">Orders</div>
            <div class="kpi-value">{{ $totalOrders }}</div>
          </div>
          <span class="kpi-badge"><span class="dot" style="background:var(--pro)"></span>Transaksi</span>
        </div>
        <div class="kpi-foot">Pending (NEW/HOLD): <span class="fw-bold text-dark">{{ $pendingOrders }}</span></div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="kpi-title">Late Orders</div>
            <div class="kpi-value">{{ $lateOrders }}</div>
          </div>
          <span class="kpi-badge"><span class="dot" style="background:var(--late)"></span>Risk</span>
        </div>
        <div class="kpi-foot">In Progress: <span class="fw-bold text-dark">{{ $inProgress }}</span></div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="kpi-title">Active Slots</div>
            <div class="kpi-value">{{ $activeSlots }}</div>
          </div>
          <span class="kpi-badge"><span class="dot" style="background:#8b5cf6"></span>Kapasitas</span>
        </div>
        <div class="kpi-foot">Kapasitas paralel produksi</div>
      </div>
    </div>
  </div>

  {{-- PANELS --}}
  <div class="row g-3">
    {{-- Order Status --}}
    <div class="col-12 col-lg-6">
      <div class="panel-card">
        <div class="panel-inner">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="panel-title">Order Status</div>
            <div class="panel-actions">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('orders.index') }}">Lihat Orders</a>
            </div>
          </div>

          @php
            $new  = $statusCounts['NEW'] ?? 0;
            $hold = $statusCounts['HOLD'] ?? 0;
            $pro  = $statusCounts['PRO'] ?? 0;
            $done = $statusCounts['DONE'] ?? 0;
            $late = $statusCounts['LATE'] ?? 0;
            $maxV = max(1, $new,$hold,$pro,$done,$late);

            $palette = [
              'NEW' => 'var(--new)',
              'HOLD' => 'var(--hold)',
              'PRO' => 'var(--pro)',
              'DONE' => 'var(--done)',
              'LATE' => 'var(--late)',
            ];
          @endphp

          @foreach([['NEW',$new],['HOLD',$hold],['PRO',$pro],['DONE',$done],['LATE',$late]] as [$label,$val])
            @php
              $w = ($val / $maxV) * 100;
              $color = $palette[$label] ?? '#94a3b8';
            @endphp
            <div class="st-row">
              <div class="st-label">{{ $label }}</div>
              <div class="st-track" aria-label="bar {{ $label }}">
                <div class="st-fill" style="width:{{ $w }}%; background:{{ $color }};"></div>
              </div>
              <div class="st-val">{{ $val }}</div>
            </div>
          @endforeach

          <div class="small text-muted mt-2">
            Tip: fokus monitoring <span class="fw-bold">LATE</span> dan <span class="fw-bold">PRO</span> untuk kontrol produksi berjalan.
          </div>
        </div>
      </div>
    </div>

    {{-- Scheduling --}}
    <div class="col-12 col-lg-6">
      <div class="panel-card">
        <div class="panel-inner">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="panel-title">Scheduling (ILP)</div>
            <div class="panel-actions d-flex gap-2">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('scheduling.runs') }}">Riwayat</a>
              <a class="btn btn-sm btn-primary" href="{{ route('scheduling.runForm') }}">Run Baru</a>
            </div>
          </div>

          <div class="text-muted small">Total Run: <span class="fw-bold text-dark">{{ $totalRuns }}</span></div>

          @if($lastRunSummary)
            <hr class="my-3">
            <div class="fw-bold mb-1">Run Terakhir #{{ $lastRunSummary['run_id'] }}</div>
            <div class="small text-muted">Tanggal: {{ $lastRunSummary['run_date'] }}</div>
            <div class="small text-muted">Metode: {{ $lastRunSummary['method'] }}</div>

            <div class="row mt-3 g-2">
              <div class="col-6">
                <div class="mini-metric">
                  <div class="lbl">Orders dijadwalkan</div>
                  <div class="val">{{ $lastRunSummary['total_orders'] }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="mini-metric">
                  <div class="lbl">Total Tardiness</div>
                  <div class="val">{{ $lastRunSummary['sum_tardiness'] }} hari</div>
                </div>
              </div>
              <div class="col-6">
                <div class="mini-metric">
                  <div class="lbl">Job terlambat</div>
                  <div class="val">{{ $lastRunSummary['late_jobs'] }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="mini-metric">
                  <div class="lbl">Slot kapasitas</div>
                  <div class="val">{{ $lastRunSummary['capacity_slots'] }}</div>
                </div>
              </div>
            </div>

            <div class="mt-3">
              <a class="btn btn-sm btn-outline-primary"
                 href="{{ route('scheduling.results', $lastRunSummary['run_id']) }}">
                Lihat hasil run
              </a>
            </div>
          @else
            <hr class="my-3">
            <div class="text-muted">Belum ada run ILP. Jalankan dari menu “Run ILP”.</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Gantt --}}
    <div class="col-12">
      <div class="gantt-card">
        <div class="gantt-inner">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <div class="panel-title">Gantt Produksi Berjalan (PRO/LATE)</div>
              <div class="text-muted small">
                @if($gantt['run_id'])
                  Sumber: Run terakhir #{{ $gantt['run_id'] }} • Timeline Day 0 – Day {{ $gantt['horizon'] }}
                @else
                  Belum ada data run ILP.
                @endif
              </div>
            </div>

            @if($gantt['run_id'])
              <a class="btn btn-sm btn-outline-primary" href="{{ route('scheduling.results', $gantt['run_id']) }}">
                Lihat detail run
              </a>
            @endif
          </div>

          @if(!$gantt['run_id'])
            <div class="text-muted">Jalankan “Run ILP” untuk menghasilkan jadwal dan menampilkan gantt.</div>

          @elseif($gantt['grouped']->isEmpty())
            <div class="text-muted">Tidak ada order yang sedang berjalan (status PRO/LATE).</div>

          @else
            @php
              $horizon = (int) $gantt['horizon'];
              // penting: pakai denom horizon+1 biar bar tidak “nabrak” ujung kanan (start==horizon)
              $denom = max(1, $horizon + 1);
              $step = $horizon <= 14 ? 1 : 2;
            @endphp

            <div class="gantt-scroll">
              <div class="gantt-grid">
                {{-- Scale --}}
                <div class="gantt-scale">
                  @for($d=0; $d <= $horizon; $d += $step)
                    <span class="tick">Day {{ $d }}</span>
                  @endfor
                </div>

                {{-- Rows per slot --}}
                @foreach($gantt['grouped'] as $slotId => $items)
                  <div class="gantt-row">
                    <div class="gantt-slot">{{ $slotId }}</div>

                    <div class="gantt-track">
                      @foreach($items as $it)
                        @php
                          $start = (int) $it->start_day;
                          $finish = (int) $it->finish_day;

                          // dur minimal 1 biar bar tetap keliatan
                          $dur = max(1, $finish - $start);

                          $leftPct  = ($start / $denom) * 100;
                          $widthPct = ($dur   / $denom) * 100;

                          $isLate = ((int)$it->tardiness_days) > 0 || $it->status === 'LATE';
                          $cls = $isLate ? 'late' : 'pro';
                        @endphp

                        <div class="gantt-bar {{ $cls }}"
                             style="left: {{ $leftPct }}%; width: {{ $widthPct }}%;"
                             title="Order: {{ $it->order_id }} | {{ $it->machine_name }} | {{ $it->customer_name }} | Start: {{ $start }} | Finish: {{ $finish }} | Status: {{ $it->status }} | Tardiness: {{ $it->tardiness_days }}">
                          <span>{{ $it->order_id }}</span>
                          <span class="text-muted">{{ $it->machine_name }} — {{ $it->customer_name }}</span>
                          <span class="text-muted">({{ $start }}–{{ $finish }})</span>

                          @if($isLate)
                            <span class="gantt-badge" style="border-color:rgba(239,68,68,.35)">LATE</span>
                          @else
                            <span class="gantt-badge" style="border-color:rgba(34,197,94,.35)">PRO</span>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="small text-muted mt-2">
              Scroll ke kanan jika timeline panjang. Hover bar untuk detail.
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Orders 14 Hari Terakhir --}}
    <div class="col-12">
      <div class="spark-wrap">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="panel-title">Orders 14 Hari Terakhir</div>
          <div class="small text-muted">{{ $labels[0] }} → {{ $labels[count($labels)-1] }}</div>
        </div>

        @php $max = max(1, ...$values); @endphp
        <div class="spark-bars">
          @foreach($values as $i => $v)
            @php $h = ($v / $max) * 100; @endphp
            <div class="spark-bar"
                 title="{{ $labels[$i] }}: {{ $v }}"
                 style="height:{{ $h }}%;">
            </div>
          @endforeach
        </div>

        <div class="d-flex justify-content-between mt-2 small text-muted">
          <span>{{ $labels[0] }}</span>
          <span>{{ $labels[count($labels)-1] }}</span>
        </div>
      </div>
    </div>
  </div>
@endsection
