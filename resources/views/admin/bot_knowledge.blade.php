<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Base de conocimiento del Bot</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .top {display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#1d4ed8; color:white;}
    .wrap {max-width:1000px; margin:24px auto; background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;}
    .btn { padding:8px 12px; border-radius:8px; border:1px solid #e5e7eb; background:white; color:#0f172a; text-decoration:none; font-weight:700; }
    table { width:100%; border-collapse:collapse; }
    th, td { text-align:left; padding:10px; border-bottom:1px solid #e5e7eb; }
    .muted { color:#64748b; }
  </style>
</head>
<body>
  <div class="top">
    <div><strong>Base de conocimiento</strong> · Bot‑FRADADI</div>
    <div>
      <a class="btn" href="{{ route('admin.pending') }}">← Panel admin</a>
    </div>
  </div>

  <div class="wrap">
    @if(session('ok'))
      <div class="muted">{{ session('ok') }}</div>
    @endif
    <h3 style="margin:0 0 8px;">Agregar PDF</h3>
    <form method="POST" action="{{ route('admin.bot.knowledge.store') }}" enctype="multipart/form-data" style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
      @csrf
      <div>
        <label>Título</label>
        <input type="text" name="title" required style="width:100%; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px;" />
      </div>
      <div>
        <label>Etiquetas (coma separadas)</label>
        <input type="text" name="tags" placeholder="caries, prevención, protocolo" style="width:100%; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px;" />
      </div>
      <div style="grid-column:1 / -1;">
        <label>Descripción</label>
        <input type="text" name="description" style="width:100%; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px;" />
      </div>
      <div style="grid-column:1 / -1;">
        <label>Archivo PDF</label>
        <input type="file" name="pdf" accept="application/pdf" required />
      </div>
      <div style="grid-column:1 / -1; text-align:right;">
        <button class="btn" style="background:#1d4ed8; color:white;">Subir</button>
      </div>
    </form>
  </div>

  <div class="wrap">
    <h3 style="margin:0 0 8px;">Documentos</h3>
    @if($docs->isEmpty())
      <p class="muted">Sin documentos aún.</p>
    @else
    <table>
      <thead>
        <tr>
          <th>Título</th>
          <th>Etiquetas</th>
          <th>Descripción</th>
          <th>Archivo</th>
          <th>Subido</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($docs as $d)
        <tr>
          <td>{{ $d->title }}</td>
          <td>{{ $d->tags }}</td>
          <td class="muted">{{ $d->description }}</td>
          <td><a class="btn" href="{{ asset('storage/'.$d->path) }}" target="_blank">Abrir</a></td>
          <td class="muted">{{ $d->created_at->format('Y-m-d H:i') }}</td>
          <td>
            <form method="POST" action="{{ route('admin.bot.knowledge.delete', $d) }}" onsubmit="return confirm('¿Eliminar documento?');">
              @csrf
              <button class="btn" style="background:#dc2626; color:white;">Eliminar</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div style="margin-top:8px;">{{ $docs->links() }}</div>
    @endif
  </div>

  @include('partials.bot_fradadi')
</body>
</html>
