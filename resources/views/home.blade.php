<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fradadi | Cuidado integral</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; color:#0f172a; }
        .hero { background:#1d4ed8; color:white; padding:64px 24px; }
        .container { max-width:1100px; margin:0 auto; }
        .hero h1 { font-size:42px; margin:0 0 12px; }
        .hero p { max-width:700px; opacity:.95; }
        .hero .actions { margin-top:20px; display:flex; gap:12px; }
        .btn { padding:10px 16px; border-radius:8px; border:1px solid transparent; text-decoration:none; font-weight:600; }
        .btn.primary { background:white; color:#1d4ed8; }
        .btn.secondary { background:transparent; color:white; border-color:#93c5fd; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:16px; padding:32px 24px; }
        .card { background:white; border:1px solid #e5e7eb; border-radius:12px; padding:18px; box-shadow:0 1px 2px rgba(0,0,0,.04); }
        .topbar { display:flex; justify-content:space-between; align-items:center; padding:12px 24px; border-bottom:1px solid #e5e7eb; }
        .brand { font-weight:700; color:#1d4ed8; }
        form.logout { margin:0; }
        .map { padding:0 24px 32px; }
        #map { width:100%; height:360px; border-radius:12px; }
        .footer { border-top:1px solid #e5e7eb; color:#64748b; text-align:center; padding:16px; font-size:14px; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">FRADADI</div>
        <div>
            <a class="btn" href="{{ route('login') }}">Iniciar sesión</a>
            <a class="btn" href="{{ route('register') }}">Registrarse</a>
        </div>
    </div>

    <section class="hero">
        <div class="container">
            <h1>Cuidado integral de salud y sonrisa</h1>
            <p>Atención médica y odontológica profesional. Diagnóstico, tratamiento y prevención en un solo lugar.</p>
        </div>
    </section>

    <div id="servicios" class="container">
        <h2 style="margin:24px 0 8px;">Especialidades</h2>
    </div>
    <div class="grid container">
        <div class="card">
            <h3>Odontología</h3>
            <p>Cuidado de la salud bucal: limpiezas, restauraciones, ortodoncia y tratamientos estéticos.</p>
        </div>
    </div>

    <div class="container" style="margin-top:8px;">
        <h2 style="margin:0 0 12px;">Ubicación</h2>
        <p style="margin:0 0 12px; color:#475569;">Av. Colectora Urb. Santa Elena Lte 23 — Ref.: Frente Prd. de Fonavi II</p>
    </div>
    <div class="map container">
        <div id="map"></div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      const lat = -9.917730, lng = -76.229474;
      const map = L.map('map', { scrollWheelZoom: false }).setView([lat, lng], 17);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);
      L.marker([lat, lng]).addTo(map)
        .bindPopup('Fradadi Salud<br>Av. Colectora Urb. Santa Elena Lte 23<br>Ref.: Frente Prd. de Fonavi II')
        .openPopup();
    </script>
    <footer class="footer">© 2025 FRADADI · Huánuco, Perú</footer>
</body>
</html>

