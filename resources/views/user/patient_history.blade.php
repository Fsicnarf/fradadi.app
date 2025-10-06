<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historial clínico</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .top {display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#1d4ed8; color:white;}
    .wrap {max-width:1100px; margin:24px auto;}
    .card {background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:16px;}
    .muted { color:#64748b; }
    .grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; }
    @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    .file { background:white; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; }
    .file .media { position:relative; background:#f1f5f9; aspect-ratio: 16/9; display:flex; align-items:center; justify-content:center; }
    .file .media img, .file .media video { width:100%; height:100%; object-fit:cover; }
    .file .body { padding:10px; }
    /* Toast */
    .toast { position:fixed; bottom:16px; right:16px; background:#0f172a; color:white; padding:10px 14px; border-radius:10px; box-shadow:0 10px 20px rgba(2,6,23,.25); opacity:0; transform: translateY(10px); transition: all .25s ease; z-index:2000; }
    .toast.show { opacity:1; transform: translateY(0); animation: fadeOut .5s ease forwards; }
    @keyframes fadeOut {
      from { opacity:1; }
      to { opacity:0; }
    }
    /* Profile */
    .profile { display:flex; gap:14px; align-items:center; padding:12px; border:1px solid #e5e7eb; border-radius:12px; background:linear-gradient(180deg,#fff,#f8fafc); box-shadow:0 6px 18px rgba(2,6,23,.06); animation: pop .35s ease; }
    .avatar { width:56px; height:56px; border-radius:999px; background:linear-gradient(135deg,#1d4ed8,#60a5fa); display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:20px; box-shadow:0 10px 20px rgba(29,78,216,.25); }
    .pname { font-weight:800; font-size:18px; }
    .pmeta { color:#64748b; font-size:12px; }
    /* Floating back button */
    .fab-back { position:fixed; right:16px; bottom:16px; width:52px; height:52px; border-radius:999px; background:#1d4ed8; color:white; display:flex; align-items:center; justify-content:center; box-shadow:0 12px 24px rgba(29,78,216,.35); text-decoration:none; font-weight:800; border:2px solid #93c5fd; animation: floaty 2.2s ease-in-out infinite; }
    .fab-back:hover { transform: translateY(-2px) scale(1.03); }
    @keyframes floaty { 0% { transform: translateY(0); } 50% { transform: translateY(-4px); } 100% { transform: translateY(0); } }
    /* Edit mode */
    .edit-only { display:none !important; }
    .editing .edit-only { display:block; }
    .editing .hide-on-edit { display:none !important; }
    /* Edit button active state */
    .editing #toggleEdit { background:#f59e0b !important; border-color:#fbbf24; color:#111827; }
  </style>
</head>
<body>
  <div class="top">
    <div><strong>FRADADI</strong> · Historial clínico</div>
    <div>
      <a class="btn" href="{{ route('appointments.registry') }}">← Volver al registro</a>
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
        <div class="pname">{{ $p_name ?? 'Paciente' }}</div>
        <div class="pmeta">DNI: {{ $p_dni ?? ($key && !str_starts_with($key,'NAME:') ? $key : '—') }} · Edad: {{ $p_age ?? '—' }} · Última visita: {{ $p_last ? $p_last->format('Y-m-d H:i') : '—' }}</div>
      </div>
    </div>
      <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin:12px 0 8px;">
        <h3 style="margin:0;">Agregar al historial</h3>
        <div style="display:flex; gap:8px;">
          <button id="toggleEdit" type="button" class="btn" style="background:#0ea5e9; color:white;" aria-pressed="false">Editar</button>
          <button id="saveAll" type="button" class="btn edit-only" style="background:#16a34a; color:white;">Guardar cambios</button>
        </div>
      </div>
      <form method="POST" action="{{ route('patients.history.store', ['key' => $key]) }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
          <div>
            <label>Archivo</label>
            <input type="file" name="file" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" required />
          </div>
          <div>
            <label>Descripción (opcional)</label>
            <input type="text" name="description" maxlength="1000" placeholder="Breve detalle" />
          </div>
        </div>
        <div style="text-align:right; margin-top:12px;">
          <button class="btn" style="background:#1d4ed8; color:white;">Subir</button>
        </div>
      </form>
    </div>

    <div class="card">
      <h3 style="margin:0 0 8px;">Archivos</h3>
      @if($files->isEmpty())
        <p class="muted">No hay archivos en el historial.</p>
      @else
      <div class="grid">
        @foreach($files as $f)
          @php($url = asset('storage/'.$f->path))
          @php($ext = strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)))
          @php($label = (str_starts_with($f->mime ?? '', 'image/') ? 'IMG' : (str_starts_with($f->mime ?? '', 'video/') ? 'VID' : (($f->mime ?? '') === 'application/pdf' ? 'PDF' : strtoupper($ext ?: 'FILE')))))
          <div class="file">
            <div class="media">
              <span class="badge">{{ $label }}</span>
              @if(str_starts_with($f->mime ?? '', 'image/'))
                <img src="{{ $url }}" alt="{{ $f->original_name }}" />
              @elseif(str_starts_with($f->mime ?? '', 'video/'))
                <video src="{{ $url }}" controls></video>
              @elseif(($f->mime ?? '') === 'application/pdf')
                <iframe src="{{ $url }}" style="width:100%; height:100%; border:0;"></iframe>
              @else
                <a class="btn" href="{{ $url }}" target="_blank">Ver archivo</a>
              @endif
            </div>
            <div class="body">
              <div style="font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $f->original_name }}">{{ $f->original_name }}</div>
              <div class="muted" style="font-size:12px;">{{ $f->mime }} · {{ number_format(($f->size ?? 0)/1024, 1) }} KB</div>
              <div class="muted" style="font-size:12px; margin-top:2px;">Subido por: {{ optional($f->user)->name ?? '—' }} · {{ optional($f->created_at)->format('Y-m-d H:i') }}</div>
              <form class="desc-form edit-only" method="POST" action="{{ route('patients.history.update', ['key' => $key, 'file' => $f->id]) }}" style="margin-top:8px;" onsubmit="return false;">
                @csrf
                <label>Descripción</label>
                <input type="text" name="description" value="{{ old('description', $f->description) }}" maxlength="1000" />
              </form>
              <div style="margin-top:6px; display:flex; gap:8px;">
                <a class="btn" href="{{ $url }}" target="_blank">Abrir</a>
                <a class="btn" href="{{ $url }}" download>Descargar</a>
                <form class="edit-only" method="POST" action="{{ route('patients.history.delete', ['key' => $key, 'file' => $f->id]) }}" onsubmit="return confirm('¿Eliminar archivo?');" style="display:inline;">
                  @csrf
                  <button class="btn" style="background:#dc2626; color:white;" type="submit">Eliminar</button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <div style="margin-top:8px;">{{ $files->links() }}</div>
      @endif
    </div>

    @if(isset($deleted) && $deleted->count())
    <div class="card edit-only">
      <h3 style="margin:0 0 8px;">Eliminados recientemente</h3>
      <div class="grid">
        @foreach($deleted as $f)
          @php($url = asset('storage/'.$f->path))
          @php($ext = strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)))
          @php($label = (str_starts_with($f->mime ?? '', 'image/') ? 'IMG' : (str_starts_with($f->mime ?? '', 'video/') ? 'VID' : (($f->mime ?? '') === 'application/pdf' ? 'PDF' : strtoupper($ext ?: 'FILE')))))
          <div class="file">
            <div class="media">
              <span class="badge">{{ $label }}</span>
              <div class="muted" style="padding:8px;">Eliminado</div>
            </div>
            <div class="body">
              <div style="font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $f->original_name }}">{{ $f->original_name }}</div>
              <div class="muted" style="font-size:12px;">{{ $f->mime }} · {{ number_format(($f->size ?? 0)/1024, 1) }} KB</div>
              <div class="muted" style="font-size:12px; margin-top:2px;">Subido por: {{ optional($f->user)->name ?? '—' }} · {{ optional($f->created_at)->format('Y-m-d H:i') }}</div>
              <form method="POST" action="{{ route('patients.history.restore', ['key' => $key, 'file' => $f->id]) }}" onsubmit="return confirm('¿Restaurar archivo?');" style="margin-top:8px;">
                @csrf
                <button class="btn" style="background:#059669; color:white;">Restaurar</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
      <div style="margin-top:8px;">{{ $deleted->links() }}</div>
    </div>
    @endif
  </div>
  <script>
    // Auto-hide toast
    (function(){ const t=document.getElementById('flashOk'); if(t){ setTimeout(()=>t.classList.remove('show'), 2500); } })();

    // Edit mode toggle and batch save
    (function(){
      const root = document.body;
      const toggleBtn = document.getElementById('toggleEdit');
      const saveAllBtn = document.getElementById('saveAll');
      if (!toggleBtn) return;
      toggleBtn.addEventListener('click', ()=>{
        const editing = root.classList.toggle('editing');
        if (saveAllBtn) saveAllBtn.style.display = editing ? 'inline-block' : 'none';
        toggleBtn.textContent = editing ? 'Salir de edición' : 'Editar';
        toggleBtn.setAttribute('aria-pressed', editing ? 'true' : 'false');
        toggleBtn.classList.toggle('active', editing);
      });
      if (saveAllBtn) {
        saveAllBtn.style.display = 'none';
        saveAllBtn.addEventListener('click', async ()=>{
          const forms = Array.from(document.querySelectorAll('.desc-form'));
          let ok = 0; let fail = 0;
          for (const f of forms) {
            const fd = new FormData(f);
            try {
              const res = await fetch(f.action, { method:'POST', body: fd, headers: { 'X-Requested-With':'XMLHttpRequest' } });
              if (res.ok) { ok++; } else { fail++; }
            } catch(e) { fail++; }
          }
          // feedback
          const t = document.createElement('div');
          t.className = 'toast show';
          t.textContent = `Cambios guardados: ${ok} ✓, errores: ${fail}`;
          document.body.appendChild(t);
          setTimeout(()=>t.classList.remove('show'), 2500);
          if (fail === 0) {
            // salir de edición y refrescar para ver descripciones
            root.classList.remove('editing');
            if (saveAllBtn) saveAllBtn.style.display = 'none';
            toggleBtn.textContent = 'Editar';
            setTimeout(()=>window.location.reload(), 400);
          }
        });
      }
    })();
  </script>
  <a href="{{ route('appointments.registry') }}" class="fab-back" title="Volver al registro" aria-label="Volver al registro">←</a>
</body>
</html>
