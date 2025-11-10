<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventariado</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .top {display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#1d4ed8; color:white;}
    .wrap {max-width:1100px; margin:24px auto;}
    .card {background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:16px;}
    .grid { display:grid; grid-template-columns:repeat(5, 1fr); gap:8px; }
    input, select, textarea { width:100%; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; }
    .btn { padding:8px 12px; border-radius:8px; border:1px solid #e5e7eb; background:white; color:#0f172a; text-decoration:none; font-weight:700; }
    table { width:100%; border-collapse:collapse; }
    th, td { text-align:left; padding:10px; border-bottom:1px solid #e5e7eb; }
    .muted { color:#64748b; }
    .toolbar { display:flex; gap:8px; align-items:center; margin-bottom:12px; }
    .editable { background:#fffceb; }
    .low { background:#fff1f2; } /* rojo claro */
    .folder { background:#e2e8f0; cursor:pointer; }
    .folder:hover { background:#cbd5e1; }
    .folder .name { display:flex; align-items:center; gap:8px; font-weight:700; text-transform:capitalize; }
    .chev { display:inline-block; transition:transform .15s ease; }
    /* Toast */
    .toast { position:fixed; bottom:16px; right:16px; background:#0f172a; color:white; padding:10px 14px; border-radius:10px; box-shadow:0 10px 20px rgba(2,6,23,.25); opacity:0; transform: translateY(10px); transition: all .25s ease; z-index:2000; }
    .toast.show { opacity:1; transform: translateY(0); }
    /* Fancy */
    .chip { transition:transform .15s ease, background .2s ease; }
    .chip:hover { transform: translateY(-1px); }
    .card { transition: box-shadow .2s ease; }
    .card:hover { box-shadow: 0 10px 24px rgba(2,6,23,.08); }
    /* Back icon */
    .icon-back { display:inline-block; width:12px; height:12px; vertical-align:-1px; margin-right:6px; }
    .icon-back svg { width:100%; height:100%; display:block; }
    /* Clamp common SVGs (pagination, links) to avoid huge icons */
    nav[role="navigation"] svg { width:14px; height:14px; display:inline-block; vertical-align:-2px; }
    ul.pagination svg { width:14px; height:14px; }
    a > svg { width:14px; height:14px; vertical-align:-2px; }
    table svg { width:14px; height:14px; }
    /* Page-wide safeguard */
    .page-inventory svg { width:16px !important; height:16px !important; display:inline-block; }
    .page-inventory .icon-back svg { width:12px !important; height:12px !important; }
  </style>
</head>
<body class="page-inventory">
  <div class="top">
    <div><strong>FRADADI</strong> · Inventariado</div>
    <div>
      <a class="btn" href="{{ route('dashboard') }}"><span class="icon-back" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Volver al panel</a>
    </div>
  </div>

  <div class="wrap">
    @if(session('ok'))
      <div id="invToast" class="toast show">{{ session('ok') }}</div>
    @endif

    <div class="card">
      <h3 style="margin:0 0 8px; display:flex; align-items:center; gap:8px;">Estadísticas por categoría
        <span class="muted" style="font-weight:400;">Interactivas</span>
      </h3>
      <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-bottom:8px;">
        <label>Año
          <select id="invYear" class="btn" style="margin-left:6px;">
            @for($y = now()->year; $y >= now()->year-4; $y--)
              <option value="{{ $y }}" {{ $y==now()->year?'selected':'' }}>{{ $y }}</option>
            @endfor
          </select>
        </label>
        <label>Mes
          <select id="invMonth" class="btn" style="margin-left:6px;">
            <option value="0">Todo el año</option>
            @for($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}" {{ $m==now()->month?'selected':'' }}>{{ \Carbon\Carbon::createFromDate(2000,$m,1)->locale('es')->isoFormat('MMMM') }}</option>
            @endfor
          </select>
        </label>
        <label>Categoría
          <select id="invCat" class="btn" style="margin-left:6px;">
            <option value="">Todas</option>
            @isset($categories)
              @foreach($categories as $c)
                <option value="{{ $c }}">{{ $c }}</option>
              @endforeach
            @endisset
          </select>
        </label>
      </div>
      <canvas id="invChart" height="120"></canvas>
    </div>

    <div class="card">
      <h3 style="margin:0 0 8px;">Añadir material</h3>
      <form method="POST" action="{{ route('materials.store') }}">
        @csrf
        <div class="grid">
          <div>
            <label>Nombre</label>
            <input name="name" required />
          </div>
          <div>
            <label>Categoría</label>
            <select name="category">
              <option value="">Selecciona</option>
              @foreach(($categories ?? []) as $c)
                <option value="{{ $c }}">{{ $c }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label>Cantidad</label>
            <input type="number" name="quantity" min="0" value="0" required />
          </div>
          <div>
            <label>Mínimo</label>
            <input type="number" name="min_quantity" min="0" value="0" />
          </div>
          <div>
            <label>Unidad</label>
            <input name="unit" placeholder="ej: unidades, cajas" />
          </div>
          <div>
            <label>Notas</label>
            <input name="notes" />
          </div>
        </div>
        <div style="margin-top:12px; text-align:right;">
          <button class="btn" style="background:#1d4ed8; color:white;">Guardar</button>
        </div>
      </form>
    </div>

    <div class="card">
      <h3 style="margin:0 0 8px;">Materiales</h3>
      <div style="text-align:right; margin-bottom:6px;">
        <a class="btn" href="{{ route('materials.export') }}">Exportar CSV</a>
      </div>
      <div class="toolbar">
        <form id="filterForm" method="GET" action="{{ route('materials.index') }}" style="display:flex; gap:8px; width:100%; align-items:center; flex-wrap:wrap;">
          <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Buscar por nombre, categoría o nota" style="flex:1; min-width:220px;" />
          <select name="category" id="categorySelect">
            <option value="">Todas las categorías</option>
            @isset($categories)
              @foreach($categories as $c)
                <option value="{{ $c }}" {{ (isset($cat) && $cat===$c)?'selected':'' }}>{{ $c }}</option>
              @endforeach
            @endisset
          </select>
          <input type="month" name="month" value="{{ $month ?? '' }}" />
          <button class="btn">Filtrar</button>
        </form>
      </div>
      @isset($categories)
      <div style="display:flex; flex-wrap:wrap; gap:8px; margin:8px 0 16px;">
        <a href="#" data-cat="" class="chip {{ empty($cat)?'active':'' }}" style="padding:6px 10px; border:1px solid #e5e7eb; border-radius:999px; text-decoration:none; color:#0f172a; {{ empty($cat)?'background:#1d4ed8;color:#fff;border-color:#1d4ed8;':'' }}">Todas</a>
        @foreach($categories as $c)
          <a href="#" data-cat="{{ $c }}" class="chip {{ (isset($cat) && $cat===$c)?'active':'' }}" style="padding:6px 10px; border:1px solid #e5e7eb; border-radius:999px; text-decoration:none; color:#0f172a; {{ (isset($cat) && $cat===$c)?'background:#1d4ed8;color:#fff;border-color:#1d4ed8;':'' }}">{{ $c }}</a>
        @endforeach
      </div>
      @endisset
      @if($materials->isEmpty())
        <p class="muted">No hay materiales registrados.</p>
      @else
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Cantidad</th>
            <th>Mín.</th>
            <th>Unidad</th>
            <th>Notas</th>
            <th>Registrado por</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @if(isset($grouped))
            @php
              $currentYm = \Carbon\Carbon::now()->format('Y-m');
            @endphp
            @foreach($grouped as $ym => $items)
              @php
                $dt = \Carbon\Carbon::createFromFormat('Y-m', $ym)->startOfMonth();
                $title = $dt->locale('es')->isoFormat('MMMM YYYY');
                $expanded = ($ym === $currentYm);
              @endphp
              <tr class="folder" data-group="{{ $ym }}" data-expanded="{{ $expanded ? '1' : '0' }}">
                <td colspan="7">
                  <div class="name">
                    <span class="chev" style="transform: rotate({{ $expanded ? '90' : '0' }}deg);">▶</span>
                    {{ ucfirst($title) }} <span class="muted">({{ count($items) }})</span>
                  </div>
                </td>
              </tr>
              @foreach($items as $m)
              <tr class="group-item {{ ($m->quantity < ($m->min_quantity ?? 0)) ? 'low' : '' }}" data-group="{{ $ym }}" style="display: {{ $expanded ? 'table-row' : 'none' }};">
                <td><span class="inline" data-id="{{ $m->id }}" data-field="name">{{ $m->name }}</span></td>
                <td><span class="inline" data-id="{{ $m->id }}" data-field="category">{{ $m->category }}</span></td>
                <td><span class="inline" data-id="{{ $m->id }}" data-field="quantity">{{ $m->quantity }}</span></td>
                <td><span class="inline" data-id="{{ $m->id }}" data-field="min_quantity">{{ $m->min_quantity }}</span></td>
                <td><span class="inline" data-id="{{ $m->id }}" data-field="unit">{{ $m->unit }}</span></td>
                <td><span class="inline" data-id="{{ $m->id }}" data-field="notes">{{ $m->notes }}</span></td>
                <td>{{ optional($m->user)->name ?? '—' }}</td>
                <td>
                  <form method="POST" action="{{ route('materials.destroy', $m) }}" onsubmit="return confirm('¿Eliminar material?');">
                    @csrf
                    <button class="btn" style="background:#dc2626; color:white;">Eliminar</button>
                  </form>
                </td>
              </tr>
              @endforeach
            @endforeach
          @else
            @foreach($materials as $m)
            <tr class="{{ ($m->quantity < ($m->min_quantity ?? 0)) ? 'low' : '' }}">
              <td><span class="inline" data-id="{{ $m->id }}" data-field="name">{{ $m->name }}</span></td>
              <td><span class="inline" data-id="{{ $m->id }}" data-field="category">{{ $m->category }}</span></td>
              <td><span class="inline" data-id="{{ $m->id }}" data-field="quantity">{{ $m->quantity }}</span></td>
              <td><span class="inline" data-id="{{ $m->id }}" data-field="min_quantity">{{ $m->min_quantity }}</span></td>
              <td><span class="inline" data-id="{{ $m->id }}" data-field="unit">{{ $m->unit }}</span></td>
              <td><span class="inline" data-id="{{ $m->id }}" data-field="notes">{{ $m->notes }}</span></td>
              <td>{{ optional($m->user)->name ?? '—' }}</td>
              <td>
                <form method="POST" action="{{ route('materials.destroy', $m) }}" onsubmit="return confirm('¿Eliminar material?');">
                  @csrf
                  <button class="btn" style="background:#dc2626; color:white;">Eliminar</button>
                </form>
              </td>
            </tr>
            @endforeach
          @endif
        </tbody>
      </table>
      <div style="margin-top:8px;">{{ $materials->links() }}</div>
      @endif
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    // Toast auto-hide
    (function(){ const t=document.getElementById('invToast'); if(t){ setTimeout(()=>t.classList.remove('show'), 2500); } })();
    const token = '{{ csrf_token() }}';
    // chips de categoría
    const form = document.getElementById('filterForm');
    const sel = document.getElementById('categorySelect');
    const monthInput = document.querySelector('input[type="month"][name="month"]');
    document.querySelectorAll('.chip').forEach(ch => {
      ch.addEventListener('click', (e) => {
        e.preventDefault();
        const val = ch.getAttribute('data-cat') || '';
        if (sel) sel.value = val;
        form?.submit();
      });
    });

    // carpetas por mes
    document.querySelectorAll('tr.folder').forEach(row => {
      row.addEventListener('click', () => {
        const ym = row.getAttribute('data-group');
        const expanded = row.getAttribute('data-expanded') === '1';
        const next = !expanded;
        row.setAttribute('data-expanded', next ? '1' : '0');
        const chev = row.querySelector('.chev');
        if (chev) chev.style.transform = next ? 'rotate(90deg)' : 'rotate(0deg)';
        document.querySelectorAll(`tr.group-item[data-group="${ym}"]`).forEach(r => {
          r.style.display = next ? 'table-row' : 'none';
        });
      });
    });
    function makeEditable(el) {
      const field = el.dataset.field;
      const id = el.dataset.id;
      const oldVal = el.textContent.trim();
      let input;
      if (field === 'category') {
        input = document.createElement('select');
        input.className = 'editable';
        input.style.width = '100%';
        const opts = ['Materiales Preventivos','Materiales Restaurativos','Materiales Auxiliares','Metálicos','Poliméricos','Cerámicos','Composites'];
        const def = document.createElement('option'); def.value=''; def.textContent='Selecciona'; input.appendChild(def);
        opts.forEach(v => { const o=document.createElement('option'); o.value=v; o.textContent=v; if (v===oldVal) o.selected=true; input.appendChild(o); });
      } else {
        input = document.createElement(field === 'notes' ? 'textarea' : 'input');
        if (field === 'quantity' || field === 'min_quantity') { input.type = 'number'; input.min = '0'; }
        input.value = oldVal;
        input.className = 'editable';
        input.style.width = '100%';
      }
      el.replaceWith(input);
      input.focus();
      const save = async () => {
        const value = input.value;
        try {
          const resp = await fetch(`{{ url('/inventory') }}/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-HTTP-Method-Override': 'PUT' },
            body: JSON.stringify({ field, value })
          });
          if (!resp.ok) throw new Error('Error al guardar');
          const span = document.createElement('span');
          span.className = 'inline';
          span.dataset.field = field; span.dataset.id = id;
          span.textContent = value;
          input.replaceWith(span);
          span.addEventListener('click', () => makeEditable(span));
          // actualizar resaltado de fila si cambió cantidad o mínimo
          if (field === 'quantity' || field === 'min_quantity') {
            const tr = span.closest('tr');
            const qty = field === 'quantity' ? parseInt(value || '0') : parseInt(tr.querySelector('[data-field="quantity"]').textContent.trim() || '0');
            const min = field === 'min_quantity' ? parseInt(value || '0') : parseInt(tr.querySelector('[data-field="min_quantity"]').textContent.trim() || '0');
            if (qty < min) tr.classList.add('low'); else tr.classList.remove('low');
          }
        } catch(e) {
          alert('No se pudo guardar el cambio');
          input.focus();
        }
      };
      input.addEventListener('blur', save);
      input.addEventListener('keydown', (e) => { if (e.key === 'Enter' && field !== 'notes') { e.preventDefault(); input.blur(); } });
    }
    document.querySelectorAll('.inline').forEach(el => {
      el.addEventListener('click', () => makeEditable(el));
    });

    // Inventory chart
    (function(){
      const canvas = document.getElementById('invChart');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      let chart;
      async function loadInvStats(){
        const y = document.getElementById('invYear').value;
        const m = document.getElementById('invMonth').value;
        const cat = document.getElementById('invCat').value;
        const url = `{{ route('materials.stats') }}?year=${y}&month=${m}&category=${encodeURIComponent(cat)}`;
        const resp = await fetch(url);
        const data = await resp.json();
        const labels = data.labels;
        const values = data.values;
        const grad = ctx.createLinearGradient(0,0,0,200);
        grad.addColorStop(0, 'rgba(59,130,246,0.55)');
        grad.addColorStop(1, 'rgba(59,130,246,0.05)');
        const conf = {
          type: 'bar',
          data: { labels, datasets: [{ label: 'Cantidad', data: values, backgroundColor: grad, borderColor: '#3b82f6' }]},
          options: { responsive:true, animation:{ duration:700 }, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false }, tooltip:{ mode:'index' } } }
        };
        if (chart) { chart.data = conf.data; chart.update(); } else { chart = new Chart(ctx, conf); }
      }
      ['invYear','invMonth','invCat'].forEach(id => document.getElementById(id)?.addEventListener('change', loadInvStats));
      loadInvStats();
    })();
  </script>
@include('partials.bot_fradadi')
</body>
</html>
