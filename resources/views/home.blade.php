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
        /* Odontología tiles */
        .tiles { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px; margin-top:8px; }
        @media (min-width: 900px) { .tiles { grid-template-columns: repeat(4, minmax(0,1fr)); } }
        .tile { position:relative; padding:0 0 12px; border-radius:12px; border:1px solid #e5e7eb; background:linear-gradient(180deg,#ffffff,#f8fafc); overflow:hidden; cursor:pointer; transition: transform .25s ease, box-shadow .25s ease, opacity .2s ease; }
        .tile:hover { transform: scale(1.04); box-shadow:0 10px 24px rgba(2,6,23,.08); }
        .tile.zoom { transform: scale(1.06); z-index:2; box-shadow:0 14px 32px rgba(2,6,23,.12); }
        .tile h4 { margin:8px 12px 4px; font-size:16px; }
        .tile p { margin:0 12px; color:#475569; font-size:13px; }
        .tile .thumb { width:100%; aspect-ratio: 16 / 10; overflow:hidden; }
        .tile .thumb img { width:100%; height:100%; object-fit:cover; display:block; transition: transform .35s ease; }
        .tile:hover .thumb img, .tile.zoom .thumb img { transform: scale(1.08); }
        .tile .badge { position:absolute; right:10px; top:10px; font-size:12px; background:#1d4ed8; color:white; padding:4px 8px; border-radius:999px; }
        .tile .icon { font-size:22px; margin-right:8px; }
        .tile .row { display:flex; align-items:center; margin:8px 12px 6px; }
        .tile .more { display:none; padding:8px 12px 0; color:#334155; font-size:13px; }
        .tile.zoom .more { display:block; }
        .tiles .tile.dim { opacity:.55; }
        @media (min-width: 900px) {
          .tiles .tile.zoom { grid-column: span 2; grid-row: span 2; }
          .tiles .tile.zoom h4 { font-size:18px; }
          .tiles .tile.zoom p { font-size:15px; }
        }
        /* Contacto */
        .contact { display:flex; align-items:center; gap:14px; flex-wrap: wrap; }
        .wa { display:inline-flex; align-items:center; gap:8px; background:#22c55e; color:white; padding:10px 14px; border-radius:10px; text-decoration:none; font-weight:700; border:1px solid #16a34a; box-shadow:0 6px 14px rgba(34,197,94,.25); }
        .wa:hover { transform: translateY(-1px); }
        .wa .icon { width:22px; height:22px; display:block; }
        .call { display:inline-flex; align-items:center; gap:8px; background:#1d4ed8; color:white; padding:10px 14px; border-radius:10px; text-decoration:none; font-weight:700; border:1px solid #1e40af; }
        .mail { display:inline-flex; align-items:center; gap:8px; background:#0ea5e9; color:white; padding:10px 14px; border-radius:10px; text-decoration:none; font-weight:700; border:1px solid #0284c7; }
        .mail .icon { width:20px; height:20px; display:block; }
        .email-text { color:#0f172a; font-weight:600; }
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
            <p>Atención odontológica profesional.Tratamiento y prevención en un solo lugar.</p>
        </div>
    </section>

    <div id="servicios" class="container">
        <h2 style="margin:24px 0 8px;">Especialidades</h2>
    </div>
    <div class="grid container">
        <div class="card">
            <h3>Odontología</h3>
            <div class="tiles" id="odoTiles">
                <div class="tile" tabindex="0" aria-label="Limpiezas dentales">
                    <span class="badge">Nuevo</span>
                    <div class="thumb">
                        <img src="/img/1.jpg" alt="Limpiezas dentales" loading="lazy">
                    </div>
                    <div class="row"><span class="icon">🪥</span><h4>Limpiezas</h4></div>
                    <p>Profilaxis y control de placa para una sonrisa sana.</p>
                    <div class="more">Incluye tartrectomía y pulido dental. Recomendado cada 6 meses para prevenir caries y enfermedad periodontal.</div>
                </div>
                <div class="tile" tabindex="0" aria-label="Restauraciones">
                    <div class="thumb">
                        <img src="/img/2.webp" alt="Restauraciones" loading="lazy">
                    </div>
                    <div class="row"><span class="icon">🧩</span><h4>Restauraciones</h4></div>
                    <p>Resinas y reconstrucciones conservadoras.</p>
                    <div class="more">Tratamientos estéticos y funcionales para devolver la forma y la función a dientes dañados por caries o fracturas.</div>
                </div>
                <div class="tile" tabindex="0" aria-label="Ortodoncia">
                    <div class="thumb">
                        <img src="/img/3.jpg" alt="Ortodoncia" loading="lazy">
                    </div>
                    <div class="row"><span class="icon">😬</span><h4>Ortodoncia</h4></div>
                    <p>Alineación dental con brackets o alineadores.</p>
                    <div class="more">Corrección de maloclusiones y apiñamientos para mejorar función masticatoria y estética de la sonrisa.</div>
                </div>
                <div class="tile" tabindex="0" aria-label="Estética dental">
                    <div class="thumb">
                        <img src="/img/4.jpg" alt="Estética dental" loading="lazy">
                    </div>
                    <div class="row"><span class="icon">✨</span><h4>Estética</h4></div>
                    <p>Blanqueamiento y diseño de sonrisa.</p>
                    <div class="more">Opciones para armonizar color, forma y tamaño dental: blanqueamientos, carillas y microdiseño de sonrisa.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top:8px;">
        <h2 style="margin:0 0 12px;">Ubicación</h2>
        <p style="margin:0 0 12px; color:#475569;">Av. Colectora Urb. Santa Elena Lte 23 — Ref.: Frente Prd. de Fonavi II</p>
    </div>
    <div class="map container">
        <div id="map"></div>
    </div>
    <div class="grid container">
        <div class="card">
            <h3>Contáctenos</h3>
            <p style="color:#475569; margin:6px 0 12px;">¿Tienes dudas? Escríbenos o llámanos al <strong>962 074 140</strong>.</p>
            <div class="contact">
                <a class="wa" href="https://wa.me/51962074140" target="_blank" rel="noopener" aria-label="WhatsApp">
                    <svg class="icon" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor" aria-hidden="true"><path d="M19.11 17.19c-.29-.14-1.7-.84-1.96-.93-.26-.1-.45-.14-.65.14-.19.29-.75.93-.92 1.12-.17.19-.34.22-.63.07-.29-.14-1.21-.45-2.3-1.42-.85-.76-1.42-1.7-1.59-1.99-.17-.29-.02-.45.12-.6.12-.12.29-.31.43-.46.14-.15.19-.26.29-.43.1-.19.05-.36-.02-.5-.07-.14-.65-1.56-.9-2.14-.24-.58-.48-.5-.65-.51l-.55-.01c-.19 0-.5.07-.77.36-.26.29-1.01.98-1.01 2.39s1.03 2.77 1.17 2.96c.14.19 2.03 3.1 4.92 4.35.69.3 1.22.48 1.63.61.68.22 1.3.19 1.79.12.55-.08 1.7-.69 1.94-1.36.24-.67.24-1.24.17-1.36-.07-.12-.26-.19-.55-.34z"/><path d="M27.26 4.74A13.47 13.47 0 0 0 4.7 23.93L3 29l5.2-1.64a13.45 13.45 0 0 0 6.45 1.64h.01c7.43 0 13.48-6.04 13.49-13.46a13.39 13.39 0 0 0-3.89-9.8zM14.65 26.02h-.01a11.4 11.4 0 0 1-5.81-1.59l-.42-.25-3.09.98.99-3.01-.27-.44a11.39 11.39 0 1 1 21.1-5.72 11.37 11.37 0 0 1-11.49 11.03z"/></svg>
                    WhatsApp
                </a>
                <span class="email-text">marceladeisyortegaiturri@gmail.com</span>
                <a class="call" href="tel:+51962074140" aria-label="Llamar 962074140">Llamar 962 074 140</a>
            </div>
        </div>
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

      // Odontología tiles: click-to-zoom for mobile and keyboard toggle
      (function(){
        const cont = document.getElementById('odoTiles');
        if (!cont) return;
        const tiles = Array.from(cont.querySelectorAll('.tile'));
        function clear(){ tiles.forEach(t=>{ t.classList.remove('zoom'); t.classList.remove('dim'); }); }
        tiles.forEach(t=>{
          t.addEventListener('click', (e)=>{
            if (t.classList.contains('zoom')) { clear(); }
            else { clear(); t.classList.add('zoom'); tiles.filter(x=>x!==t).forEach(x=>x.classList.add('dim')); }
          });
          t.addEventListener('keydown', (e)=>{
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); t.click(); }
            if (e.key === 'Escape') { clear(); }
          });
        });
        document.addEventListener('click', (e)=>{
          if (!cont.contains(e.target)) clear();
        });
      })();
    </script>
    <footer class="footer">© 2025 FRADADI · Huánuco, Perú</footer>
</body>
</html>

