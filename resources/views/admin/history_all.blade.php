<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historial general</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .top {display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#1d4ed8; color:white;}
    .wrap {max-width:1100px; margin:24px auto; background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;}
    table {width:100%; border-collapse:collapse;}
    th, td {text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;}
    .badge {display:inline-block; padding:4px 8px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-weight:700; font-size:12px;}
    .muted {color:#64748b;}
    .btn { padding:8px 12px; border-radius:8px; border:1px solid #e5e7eb; text-decoration:none; color:#0f172a; background:white; }
    .ua { max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    input, select { padding:6px 8px; border:1px solid #e5e7eb; border-radius:8px; }
  </style>
</head>
<body>
  <div class="top">
    <div><strong>Historial general</strong></div>
    <div>
      <a class="btn" href="{{ route('admin.pending') }}">Pendientes</a>
    </div>
  </div>

  <div class="wrap">
    <form method="GET" action="{{ route('admin.history.all') }}" style="display:flex; gap:12px; align-items:flex-end; margin-bottom:12px; flex-wrap:wrap;">
      <div>
        <label style="display:block; font-weight:600; font-size:12px; color:#475569;">Usuario</label>
        <select name="user_id">
          <option value="">Todos</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->username }} — {{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label style="display:block; font-weight:600; font-size:12px; color:#475569;">Desde</label>
        <input type="date" name="from" value="{{ request('from') }}">
      </div>
      <div>
        <label style="display:block; font-weight:600; font-size:12px; color:#475569;">Hasta</label>
        <input type="date" name="to" value="{{ request('to') }}">
      </div>
      <div>
        <label style="display:block; font-weight:600; font-size:12px; color:#475569;">Buscar</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Texto, IP, navegador..." style="min-width:240px;">
      </div>
      <div>
        <label style="display:block; font-weight:600; font-size:12px; color:#475569;">Acción</label>
        <select name="action">
          <option value="">Todas</option>
          @foreach($actions as $a)
            <option value="{{ $a }}" @selected(request('action')===$a)>{{ $a }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <button type="submit" class="btn">Filtrar</button>
        <a class="btn" href="{{ route('admin.history.all') }}" style="margin-left:6px;">Limpiar</a>
        <a class="btn" href="{{ route('admin.history.all.export', request()->query()) }}" style="margin-left:6px;">Exportar CSV</a>
      </div>
    </form>

    @if($logs->isEmpty())
      <p class="muted">Sin actividades registradas.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>IP</th>
            <th>Navegador</th>
            <th>Detalles</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
            <tr>
              <td>{{ $log->created_at->format('d/m/Y') }}</td>
              <td>{{ $log->created_at->format('H:i') }}</td>
              <td>{{ optional($log->user)->username }}</td>
              <td><span class="badge">{{ $log->action }}</span></td>
              <td>{{ $log->ip ?? '—' }}</td>
              <td class="ua" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '—' }}</td>
              <td>
                {{ $log->description }}
                @if($log->changes)
                  <pre style="margin:6px 0 0; background:#f8fafc; padding:8px; border-radius:8px; border:1px solid #e5e7eb; font-size:12px;">{{ json_encode($log->changes, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="margin-top:12px;">{{ $logs->links() }}</div>
    @endif
  </div>
@include('partials.bot_fradadi')
</body>
</html>
