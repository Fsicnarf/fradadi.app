<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de pacientes</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .top {display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#1d4ed8; color:white;}
    .wrap {max-width:1100px; margin:24px auto;}
    .card {background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:16px;}
    table {width:100%; border-collapse:collapse;}
    th, td {text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;}
    .btn { padding:8px 12px; border-radius:8px; border:1px solid #e5e7eb; background:white; color:#0f172a; text-decoration:none; font-weight:700; }
    .muted { color:#64748b; }
    .grid2 { display:grid; grid-template-columns:1fr; gap:16px; }
    @media (min-width: 900px) { .grid2 { grid-template-columns: 1.2fr 0.8fr; } }
    /* Patients cards */
    .cards { display:grid; grid-template-columns: repeat(2, 1fr); gap:12px; }
    @media (min-width: 900px) { .cards { grid-template-columns: repeat(2, 1fr); } }
    .p-card { position:relative; background:linear-gradient(180deg,#f1f5f9, #fff); border:1px solid #e5e7eb; border-radius:16px; padding:12px; cursor:pointer; overflow:hidden; }
    .p-illus { position:absolute; right:-8px; bottom:-8px; width:90px; opacity:0.15; }
    .p-title { font-weight:700; }
    .p-sub { font-size:12px; color:#64748b; }
    /* Modal */
    .modal { position:fixed; inset:0; background:rgba(2,6,23,0.7); display:none; align-items:center; justify-content:center; z-index:1000; }
    .modal.show { display:flex; }
    .mcard { background:white; width:100%; max-width:560px; border-radius:12px; border:1px solid #e5e7eb; padding:16px; }
  </style>
</head>
<body>
  <div class="top">
    <div><strong>FRADADI</strong> · Registro de pacientes</div>
    <div>
      <a class="btn" href="{{ route('dashboard') }}">← Volver al panel</a>
    </div>
  </div>

  <div class="wrap">
    <div class="card">
      <h3 style="margin:0 0 8px; display:flex; align-items:center; gap:8px;">Pacientes por día
        <span class="muted" style="font-weight:400;">Interactivo</span>
      </h3>
      <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-bottom:8px;">
        <label>Año
          <select id="yearSel" class="btn" style="margin-left:6px;">
            @for($y = now()->year; $y >= now()->year-4; $y--)
              <option value="{{ $y }}" {{ $y==now()->year?'selected':'' }}>{{ $y }}</option>
            @endfor
          </select>
        </label>
        <label>Mes
          <select id="monthSel" class="btn" style="margin-left:6px;">
            <option value="0">Todo el año</option>
            @for($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}" {{ $m==now()->month?'selected':'' }}>{{ \Carbon\Carbon::createFromDate(2000,$m,1)->locale('es')->isoFormat('MMMM') }}</option>
            @endfor
          </select>
        </label>
      </div>
      <canvas id="patientsChart" height="120"></canvas>
    </div>
    <div class="card">
      <h3 style="margin:0 0 8px;">Próximas citas</h3>
      @if($upcoming->isEmpty())
        <p class="muted">No hay próximas citas.</p>
      @else
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Tipo</th>
            <th>Canal</th>
            <th>Notas</th>
            <th>Registrado por</th>
          </tr>
        </thead>
        <tbody>
          @foreach($upcoming as $a)
          <tr>
            <td>{{ $a->start_at->format('Y-m-d') }}</td>
            <td>{{ $a->start_at->format('H:i') }}</td>
            <td>{{ $a->dni }}</td>
            <td>{{ $a->patient_name }}</td>
            <td>{{ $a->patient_age }}</td>
            <td>{{ $a->appointment_type }}</td>
            <td>{{ $a->channel }}</td>
            <td>{{ $a->notes }}</td>
            <td>{{ optional($a->user)->name ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div style="margin-top:8px;">{{ $upcoming->links() }}</div>
      @endif
    </div>

    <div class="card">
      <h3 style="margin:0 0 8px;">Citas pasadas</h3>
      @if($past->isEmpty())
        <p class="muted">No hay citas pasadas.</p>
      @else
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Tipo</th>
            <th>Canal</th>
            <th>Notas</th>
            <th>Registrado por</th>
          </tr>
        </thead>
        <tbody>
          @foreach($past as $a)
          <tr>
            <td>{{ $a->start_at->format('Y-m-d') }}</td>
            <td>{{ $a->start_at->format('H:i') }}</td>
            <td>{{ $a->dni }}</td>
            <td>{{ $a->patient_name }}</td>
            <td>{{ $a->patient_age }}</td>
            <td>{{ $a->appointment_type }}</td>
            <td>{{ $a->channel }}</td>
            <td>{{ $a->notes }}</td>
            <td>{{ optional($a->user)->name ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div style="margin-top:8px;">{{ $past->links() }}</div>
      @endif
    </div>
    <div class="card">
      <h3 style="margin:0 0 8px;">Pacientes</h3>
      @if(($patients ?? collect())->isEmpty())
        <p class="muted">Sin registros.</p>
      @else
      <div class="cards">
        @foreach($patients as $p)
          @php($key = $p->dni ?: ('NAME:'.$p->patient_name))
          <div class="p-card" data-key="{{ $key }}" data-name="{{ $p->patient_name }}" data-age="{{ $p->patient_age }}" data-dni="{{ $p->dni }}">
            <div class="p-title">{{ $p->patient_name ?: 'Sin nombre' }}</div>
            <div class="p-sub">DNI: {{ $p->dni ?: '—' }} · Edad: {{ $p->patient_age ?: '—' }}</div>
            <img class="p-illus" src="https://cdn.jsdelivr.net/gh/tabler/tabler-icons/icons/brand-tailwind.svg" alt="" />
          </div>
        @endforeach
      </div>
      @endif
    </div>
  </div>

  <div class="modal" id="pModal">
    <div class="mcard">
      <h3 id="pmTitle" style="margin:0 0 8px;">Paciente</h3>
      <p class="muted" id="pmMeta">—</p>
      <div style="display:flex; gap:8px;">
        <a id="pmHistory" class="btn" href="#" style="background:#1d4ed8; color:white;">Ver historial clínico</a>
      </div>
      <div style="text-align:right; margin-top:12px;">
        <button id="pmClose" class="btn">Cerrar</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    // Chart
    (function(){
      const canvas = document.getElementById('patientsChart');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      let chart;
      async function loadStats(){
        const y = document.getElementById('yearSel').value;
        const m = document.getElementById('monthSel').value;
        const resp = await fetch(`{{ route('appointments.stats') }}?year=${y}&month=${m}`);
        const data = await resp.json();
        const labels = data.labels;
        const values = data.values;
        const gradient = ctx.createLinearGradient(0,0,0,200);
        gradient.addColorStop(0, 'rgba(234,179,8,0.6)');
        gradient.addColorStop(1, 'rgba(234,179,8,0.05)');
        const conf = {
          type: 'line',
          data: { labels, datasets: [{ label: 'Pacientes', data: values, fill: true, borderColor: '#eab308', backgroundColor: gradient, tension: 0.35, pointRadius: 3, pointBackgroundColor: '#eab308' }]},
          options: { responsive: true, animation: { duration: 700 }, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
        };
        if (chart) { chart.data = conf.data; chart.update(); } else { chart = new Chart(ctx, conf); }
      }
      document.getElementById('yearSel').addEventListener('change', loadStats);
      document.getElementById('monthSel').addEventListener('change', loadStats);
      loadStats();
    })();

    // Patients modal
    (function(){
      const modal = document.getElementById('pModal');
      const close = document.getElementById('pmClose');
      function openCard(card){
        const name = card.getAttribute('data-name') || 'Paciente';
        const age = card.getAttribute('data-age') || '—';
        const dni = card.getAttribute('data-dni') || '—';
        const key = card.getAttribute('data-key');
        document.getElementById('pmTitle').textContent = name;
        document.getElementById('pmMeta').textContent = `Edad: ${age} · DNI: ${dni}`;
        document.getElementById('pmHistory').href = `{{ url('/patients') }}/${encodeURIComponent(key)}/history`;
        modal.classList.add('show');
      }
      document.querySelectorAll('.p-card').forEach(c => c.addEventListener('click', () => openCard(c)));
      close?.addEventListener('click', () => modal.classList.remove('show'));
      modal?.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('show'); });
    })();
  </script>
</body>
</html>
