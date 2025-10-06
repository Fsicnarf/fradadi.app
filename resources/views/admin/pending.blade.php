<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios pendientes</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .top {display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#1d4ed8; color:white;}
    .wrap {max-width:1000px; margin:24px auto; background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;}
    table {width:100%; border-collapse:collapse;}
    th, td {text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;}
    .btn {padding:8px 12px; border-radius:8px; border:1px solid transparent; background:#16a34a; color:white; font-weight:700; cursor:pointer;}
    form {display:inline;}
    .status {color:#065f46; background:#d1fae5; border:1px solid #a7f3d0; padding:8px 10px; border-radius:8px; margin:8px 0;}
    .link { padding:8px 10px; border-radius:8px; border:1px solid #e5e7eb; text-decoration:none; color:#0f172a; background:white; font-weight:600; }
  </style>
</head>
<body>
  <div class="top">
    <div><strong>Panel administrador</strong></div>
    <div>
      <a class="link" href="{{ route('admin.history.all') }}" style="margin-right:8px;">Historial general</a>
      <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button class="btn" style="background:#0f172a;">Cerrar sesión</button>
      </form>
    </div>
  </div>

  <div class="wrap">
    <h2 style="margin-top:0;">Registros pendientes de aprobación</h2>

    @if (session('status'))
      <div class="status">{{ session('status') }}</div>
    @endif

    @if ($pending->isEmpty())
      <p>No hay usuarios pendientes.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($pending as $u)
            <tr>
              <td>{{ $u->id }}</td>
              <td>{{ $u->name }}</td>
              <td>{{ $u->username }}</td>
              <td>{{ $u->created_at->format('Y-m-d H:i') }}</td>
              <td>
                <form method="POST" action="{{ route('admin.approve', $u) }}">
                  @csrf
                  <button class="btn" type="submit">Aprobar</button>
                </form>
                <a class="link" href="{{ route('admin.history', $u) }}" style="margin-left:6px;">Historial</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="wrap">
    <h2 style="margin-top:0;">Usuarios</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Usuario</th>
          <th>Aprobado</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ $u->approved ? 'Sí' : 'No' }}</td>
            <td>{{ $u->active ? 'Sí' : 'No' }}</td>
            <td>
              <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                @csrf
                <button class="btn" type="submit" style="background: {{ $u->active ? '#ef4444' : '#16a34a' }};">{{ $u->active ? 'Desactivar' : 'Activar' }}</button>
              </form>
              <form method="POST" action="{{ route('admin.users.killSessions', $u) }}" style="margin-left:6px; display:inline;">
                @csrf
                <button class="btn" type="submit" style="background:#dc2626;">Cerrar sesiones</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="wrap">
    <h2 style="margin-top:0;">Usuarios conectados</h2>
    @if($sessions->isEmpty())
      <p>No hay sesiones activas.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Usuario</th>
            <th>IP</th>
            <th>Navegador</th>
            <th>Última actividad</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sessions as $s)
            <tr>
              <td>{{ optional($connected[$s->user_id] ?? null)->username }}</td>
              <td>{{ $s->ip_address }}</td>
              <td>{{ $s->user_agent }}</td>
              <td>{{ \Carbon\Carbon::createFromTimestamp($s->last_activity)->format('Y-m-d H:i') }}</td>
              <td>
                @if(isset($connected[$s->user_id]))
                <form method="POST" action="{{ route('admin.users.killSessions', $connected[$s->user_id]) }}">
                  @csrf
                  <button class="btn" type="submit" style="background:#dc2626;">Cerrar sesiones</button>
                </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</body>
</html>
