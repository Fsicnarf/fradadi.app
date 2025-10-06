<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .wrap {display:grid; grid-template-columns:1fr 1fr; min-height:100vh;}
    .left {display:flex; align-items:center; justify-content:center; padding:32px;}
    .panel {width:100%; max-width:380px;}
    h1 {margin:0 0 8px; font-size:28px;}
    .muted {color:#64748b; margin-bottom:16px;}
    label {display:block; font-weight:600; margin:12px 0 6px;}
    input[type=text], input[type=password] {width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px;}
    .row {display:flex; justify-content:space-between; align-items:center; margin:10px 0; font-size:14px;}
    .btn {width:100%; padding:10px 16px; background:#0f172a; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;}
    .link {color:#0f172a; text-decoration:none; font-weight:600;}
    .right {background:url('https://images.unsplash.com/photo-1504199367641-aba8151af406?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;}
    .error {color:#b91c1c; background:#fee2e2; border:1px solid #fecaca; padding:8px 10px; border-radius:8px; margin:8px 0;}
    .status {color:#065f46; background:#d1fae5; border:1px solid #a7f3d0; padding:8px 10px; border-radius:8px; margin:8px 0;}
    .footer { border-top:1px solid #e5e7eb; color:#64748b; text-align:center; padding:12px; font-size:14px; background:white; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="left">
      <div class="panel">
        <div style="font-weight:700; color:#0f172a; margin-bottom:8px;">FRADADI</div>
        <h1>Bienvenido de nuevo</h1>
        <div class="muted">Por favor ingresa tus datos</div>

        @if (session('status'))
          <div class="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
          <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
          @csrf
          <label for="username">Usuario</label>
          <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>

          <label for="password">Contraseña</label>
          <input id="password" type="password" name="password" required>

          <div class="row" style="margin-top:12px;"></div>

          <button class="btn" type="submit">Iniciar sesión</button>
        </form>

        <div class="row" style="margin-top:12px;">
          <span>¿No tienes cuenta?</span>
          <a class="link" href="{{ route('register') }}">Regístrate</a>
        </div>
        <div class="row" style="margin-top:8px;">
          <a class="link" href="{{ route('home') }}">← Volver al inicio</a>
        </div>
        <footer class="footer">© 2025 FRADADI · Huánuco, Perú</footer>
      </div>
    </div>
    <div class="right" aria-hidden="true"></div>
  </div>
</body>
</html>
