<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ficha odontológica</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --blue:#1d4ed8; --muted:#64748b; --bg:#f8fafc; --ok:#22c55e; --warn:#f59e0b; --bad:#ef4444; }
    *{box-sizing:border-box}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:var(--bg);color:#0f172a}
    .top{display:flex;justify-content:space-between;align-items:center;padding:16px 24px;background:var(--blue);color:white}
    .wrap{max-width:1100px;margin:18px auto;padding:0 12px}
    .card{background:white;border:1px solid #e5e7eb;border-radius:12px;padding:14px;margin-bottom:14px}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:10px;border:1px solid #c7d2fe;background:white;color:#111827;text-decoration:none;font-weight:700;cursor:pointer}
    .btn.primary{background:var(--blue);color:white;border-color:#93c5fd}
    .btn.success{background:var(--ok);color:white;border-color:#86efac}
    /* Back icon */
    .icon-back{display:inline-block;width:16px;height:16px;vertical-align:-2px;margin-right:6px}
    .icon-back svg{width:100%;height:100%;display:block}
    .muted{color:var(--muted)}
    .profile{display:flex;gap:14px;align-items:center;padding:10px;border:1px solid #e5e7eb;border-radius:12px;background:linear-gradient(180deg,#fff,#f8fafc)}
    .avatar{width:52px;height:52px;border-radius:999px;background:linear-gradient(135deg,#1d4ed8,#60a5fa);display:flex;align-items:center;justify-content:center;color:white;font-weight:800}
    .grid-legend{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
    .chip{border:1px solid #e5e7eb;border-radius:999px;padding:4px 8px;font-size:12px;display:inline-flex;align-items:center;gap:6px}
    .dot{width:10px;height:10px;border-radius:999px}
    /* Odontograma */
    .odonto{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:12px}
    .hemi{border-top:1px solid #e5e7eb;padding-top:10px}
    .hemi + .hemi{border-left:1px solid #e5e7eb;padding-left:14px}
    .row{display:grid;gap:8px;margin:8px 0}
    .row.r8{grid-template-columns:repeat(8,1fr)}
    .row.r5{grid-template-columns:repeat(5,1fr)}
    .tooth{aspect-ratio:1/1;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;position:relative;cursor:pointer;transition:transform .07s ease}
    .tooth:hover{transform:translateY(-2px)}
    .tooth svg{width:85%;height:85%}
    .tooth .num{position:absolute;top:4px;left:6px;font-size:11px;color:#475569}
    .tooth .code{position:absolute;bottom:4px;right:6px;font-size:11px;font-weight:700}
    .state-S{--c:#10b981}.state-c1{--c:#f59e0b}.state-c2{--c:#f97316}.state-c3{--c:#ef4444}.state-K{--c:#111827}
    .tooth .code{color:var(--c,#334155)}
    .editing .hint{display:block}
    .hint{display:none;margin-top:4px;color:var(--muted);font-size:12px}
    .toolbar{display:flex;gap:8px;align-items:center}
    .toast{position:fixed;bottom:16px;right:16px;background:#0f172a;color:white;padding:10px 14px;border-radius:10px;opacity:0;transform:translateY(8px);transition:all .25s ease}
    .toast.show{opacity:1;transform:translateY(0)}
  </style>
</head>
<body>
  <div class="top">
    <div><strong>FRADADI</strong> · Ficha odontológica</div>
    <div>
      <a class="btn" href="{{ route('patients.history', ['key' => $key]) }}"><span class="icon-back" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Historial clínico</a>
      <a class="btn" href="{{ route('appointments.registry') }}">Registro</a>
    </div>
  </div>
  @if(session('ok'))
    <div id="flashOk" class="toast show">{{ session('ok') }}</div>
  @endif
  <div class="wrap">
    <div class="card">
      <div class="profile">
        <div class="avatar">{{ ($p_name ? strtoupper(substr($p_name,0,1)) : 'P') }}</div>
        <div>
          <div style="font-weight:800">{{ $p_name ?? 'Paciente' }}</div>
          <div class="muted" style="font-size:12px">DNI: {{ $p_dni ?? ($key && !str_starts_with($key,'NAME:') ? $key : '—') }} · Edad: {{ $p_age ?? '—' }} · Última visita: {{ $p_last ? $p_last->format('Y-m-d H:i') : '—' }}</div>
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px">
        <div class="toolbar">
          <button id="toggleEdit" class="btn">Modo edición</button>
          <form id="saveForm" method="POST" action="{{ route('patients.dental.save', ['key' => $key]) }}">
            @csrf
            <input type="hidden" name="data" id="dataField" />
            <input type="hidden" name="note" id="noteField" />
            <button id="saveBtn" class="btn success" type="submit">Guardar</button>
          </form>
        </div>
        <div class="grid-legend">
          <span class="chip"><span class="dot" style="background:#10b981"></span>Sano (S)</span>
          <span class="chip"><span class="dot" style="background:#f59e0b"></span>Caries inicial (c1)</span>
          <span class="chip"><span class="dot" style="background:#f97316"></span>Caries en dentina (c2)</span>
          <span class="chip"><span class="dot" style="background:#ef4444"></span>Caries con cavidad (c3)</span>
          <span class="chip"><span class="dot" style="background:#111827"></span>Ausente/Obturado (K)</span>
        </div>
      </div>
      <div class="hint">Toque una pieza para cambiar su estado: S → c1 → c2 → c3 → K → vacío.</div>
    </div>

    <div class="card">
      <h3 style="margin:0 0 8px">Odontograma</h3>
      <div id="odonto" class="odonto" aria-live="polite">
        <div class="hemi" id="hemi-top-left"></div>
        <div class="hemi" id="hemi-top-right"></div>
        <div class="hemi" id="hemi-bottom-left"></div>
        <div class="hemi" id="hemi-bottom-right"></div>
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:6px">
        <span class="muted" style="font-weight:700">DER</span>
        <span class="muted" style="font-weight:700">IZQ</span>
      </div>
      <textarea id="note" placeholder="Observaciones" style="width:100%;min-height:90px;margin-top:10px;border:1px solid #e2e8f0;border-radius:8px;padding:8px;resize:vertical">{{ ($record && ($record->data['note'] ?? null)) ? $record->data['note'] : '' }}</textarea>
    </div>

    <div class="card">
      <h3 style="margin:0 0 8px">Guía de lectura</h3>
      <div class="muted" style="font-size:14px">
        <p><strong>Números de dientes</strong></p>
        <ul>
          <li>La numeración (11, 12, 13, …) corresponde a la nomenclatura internacional de la <strong>FDI</strong> (Federación Dental Internacional).</li>
          <li>Ejemplos:
            <ul>
              <li><strong>11</strong>: incisivo central superior derecho</li>
              <li><strong>21</strong>: incisivo central superior izquierdo</li>
              <li><strong>31</strong>: incisivo central inferior izquierdo</li>
              <li><strong>41</strong>: incisivo central inferior derecho</li>
            </ul>
          </li>
          <li>Los números <strong>51 a 85</strong> indican dientes <strong>temporales</strong> (de leche), usados en odontopediatría.</li>
        </ul>
        <p style="margin-top:10px"><strong>Círculos divididos en secciones</strong></p>
        <ul>
          <li>Cada círculo representa un diente y se divide en áreas: <em>vestibular</em>, <em>lingual</em>, <em>mesial</em>, <em>distal</em> y <em>oclusal</em>.</li>
          <li>Se marcan estas áreas para indicar <em>caries</em>, <em>obturaciones</em>, <em>fracturas</em>, <em>tratamientos</em>, etc.</li>
        </ul>
      </div>
    </div>
  </div>

<script>
  // Datos iniciales desde PHP
  const initialData = @json($record?($record->data ?? []):[]);
  // Definición exacta por cuadrantes (según imagen)
  const Q = {
    topLeft: {
      rows: [ [18,17,16,15,14,13,12,11], [55,54,53,52,51] ]
    },
    topRight: {
      rows: [ [21,22,23,24,25,26,27,28], [61,62,63,64,65] ]
    },
    bottomLeft: {
      // etiqueta DER
      rows: [ [85,84,83,82,81], [48,47,46,45,44,43,42,41] ]
    },
    bottomRight: {
      // etiqueta IZQ
      rows: [ [71,72,73,74,75], [31,32,33,34,35,36,37,38] ]
    }
  };
  const cycle = ["S","c1","c2","c3","K","" ];
  const grid = document.getElementById('odonto');
  const note = document.getElementById('note');
  const dataField = document.getElementById('dataField');
  const noteField = document.getElementById('noteField');
  const toggleBtn = document.getElementById('toggleEdit');
  const root = document.body;
  let editing = false;

  function toothState(num){
    return (initialData && initialData[num]) || '';
  }
  function stateClass(s){
    return s ? ('state-' + s) : '';
  }
  function nextState(s){
    const i = cycle.indexOf(s);
    return cycle[(i+1) % cycle.length];
  }
  function svgTooth(color){
    return `<svg viewBox='0 0 100 100' aria-hidden='true'>
      <circle cx='50' cy='50' r='42' fill='white' stroke='${color||"#94a3b8"}' stroke-width='6' />
      <circle cx='50' cy='50' r='30' fill='none' stroke='${color||"#94a3b8"}' stroke-width='4' stroke-dasharray='6 4'/>
    </svg>`
  }
  function colorFor(s){
    return s==="S"?"#10b981": s==="c1"?"#f59e0b": s==="c2"?"#f97316": s==="c3"?"#ef4444": s==="K"?"#111827":"#94a3b8";
  }
  function makeTooth(num){
    const s = toothState(num);
    const el = document.createElement('button');
    el.type = 'button';
    el.className = `tooth ${stateClass(s)}`;
    el.setAttribute('data-num', num);
    el.setAttribute('aria-label', `Pieza ${num} estado ${s||'vacío'}`);
    el.innerHTML = `<span class='num'>${num}</span>${svgTooth(colorFor(s))}<span class='code'>${s}</span>`;
    el.addEventListener('click', ()=>{
      if (!editing) return;
      const current = toothState(num);
      const ns = nextState(current);
      if (!initialData || typeof initialData !== 'object') { window.initialData = {}; }
      initialData[num] = ns;
      render();
    });
    return el;
  }

  function render(){
    const tl = document.getElementById('hemi-top-left');
    const tr = document.getElementById('hemi-top-right');
    const bl = document.getElementById('hemi-bottom-left');
    const br = document.getElementById('hemi-bottom-right');
    [tl,tr,bl,br].forEach(c=>c.innerHTML='');

    function renderHemi(container, rows){
      rows.forEach(arr=>{
        const r = document.createElement('div');
        r.className = 'row ' + (arr.length===8?'r8':'r5');
        arr.forEach(n=> r.appendChild(makeTooth(n)) );
        container.appendChild(r);
      });
    }
    renderHemi(tl, Q.topLeft.rows);
    renderHemi(tr, Q.topRight.rows);
    renderHemi(bl, Q.bottomLeft.rows);
    renderHemi(br, Q.bottomRight.rows);
  }
  render();

  toggleBtn.addEventListener('click', ()=>{
    editing = !editing;
    root.classList.toggle('editing', editing);
    toggleBtn.textContent = editing ? 'Salir de edición' : 'Modo edición';
  });

  // Guardado
  document.getElementById('saveForm').addEventListener('submit', (e)=>{
    dataField.value = JSON.stringify(initialData||{});
    noteField.value = note.value || '';
  });

  // Auto-hide toast
  (function(){ const t=document.getElementById('flashOk'); if(t){ setTimeout(()=>t.classList.remove('show'), 2200); } })();
</script>
@include('partials.bot_fradadi')
</body>
</html>
