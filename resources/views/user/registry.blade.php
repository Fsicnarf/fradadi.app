<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de usuarios</title>
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
  </style>
</head>
<body>
  <div class="top">
    <div><strong>FRADADI</strong> · Registro de usuarios</div>
    <div>
      <a class="btn" href="{{ route('dashboard') }}">← Volver al panel</a>
    </div>
  </div>

  <div class="wrap">
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
          </tr>
          @endforeach
        </tbody>
      </table>
      <div style="margin-top:8px;">{{ $past->links() }}</div>
      @endif
    </div>
  </div>
</body>
</html>
