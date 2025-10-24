<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de usuario</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
  <style>
    body {font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; background:#f8fafc; color:#0f172a;}
    .top {display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#1d4ed8; color:white;}
    .wrap {max-width:1000px; margin:24px auto; background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;}
    .btn { padding:8px 12px; border-radius:8px; border:1px solid #e5e7eb; background:white; color:#0f172a; text-decoration:none; font-weight:700; }
    .tag { display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; font-weight:700; }
    .ok { background:#dcfce7; color:#166534; }
    .warn { background:#fee2e2; color:#991b1b; }
    #calendar { margin-top:16px; background:white; border:1px solid #e5e7eb; border-radius:12px; padding:8px; }
    /* Modal */
    .modal { position:fixed; inset:0; background:rgba(15,23,42,1); display:none; align-items:center; justify-content:center; z-index:1000; }
    .modal.show { display:flex; }
    .card { background:white; width:100%; max-width:720px; border-radius:12px; border:1px solid #e5e7eb; padding:16px; position:relative; }
    body.modal-open { overflow:hidden; }
    .grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    label { display:block; font-weight:600; font-size:12px; color:#475569; margin-bottom:4px; }
    input, select, textarea { width:100%; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; }
    .actions { display:flex; justify-content:flex-end; gap:8px; margin-top:12px; }
  </style>
</head>
<body>
  <div class="top">
    <div><strong>FRADADI</strong> · Panel de usuario</div>
    <div>
      <a class="btn" href="{{ route('appointments.registry') }}" style="margin-right:8px;">Registro de pacientes</a>
      <a class="btn" href="{{ route('materials.index') }}" style="margin-right:8px; position:relative;" id="invBtn">Inventariado
        <span id="invBadge" style="display:none; position:absolute; top:-6px; right:-6px; background:#dc2626; color:white; border-radius:999px; padding:2px 6px; font-size:12px;">0</span>
      </a>
      <a class="btn" href="{{ route('home') }}">Volver al inicio</a>
      <form method="POST" action="{{ route('logout') }}" style="display:inline; margin-left:8px;">
        @csrf
        <button class="btn" style="background:#0f172a; color:white;">Cerrar sesión</button>
      </form>
    </div>
  </div>

  <div class="wrap">
    <h2 style="margin-top:0;">Hola, {{ auth()->user()->name }}</h2>
    <p>Tu usuario: <strong>{{ auth()->user()->username }}</strong></p>
    <p>Estado de aprobación:
      @if(auth()->user()->approved)
        <span class="tag ok">Aprobado</span>
      @else
        <span class="tag warn">Pendiente</span>
      @endif
    </p>

    <p style="color:#64748b;">Selecciona una fecha en el calendario para registrar una cita. No se permiten fechas anteriores a hoy.</p>

    <div id="calendar"></div>
  </div>

  <!-- Modal nueva cita -->
  <div class="modal" id="modal">
    <div class="card">
      <h3 style="margin:0 0 8px;">Nueva cita</h3>
      <form id="apptForm">
        @csrf
        <div class="grid">
          <div>
            <label>DNI</label>
            <input type="text" name="dni" id="dni" maxlength="20" placeholder="Documento" />
          </div>
          <div>
            <label>Nombres</label>
            <input type="text" name="patient_first_name" id="patient_first_name" placeholder="Nombres" />
          </div>
          <div>
            <label>Apellidos</label>
            <input type="text" name="patient_last_name" id="patient_last_name" placeholder="Apellidos" />
          </div>
          <div>
            <label>Edad</label>
            <input type="number" name="patient_age" id="patient_age" min="0" max="120" />
          </div>
          <div>
            <label>Teléfono</label>
            <input type="text" name="phone" id="phone" maxlength="30" placeholder="Ej: 999123456" />
          </div>
          <div>
            <label>Fecha</label>
            <input type="date" name="date" id="date" required>
          </div>
          <div>
            <label>Hora</label>
            <input type="time" name="time" id="time" required>
          </div>
          <div>
            <label>Tipo de cita</label>
            <select name="appointment_type" id="appointment_type">
              <option value="">Selecciona</option>
              <option>Primera vez</option>
              <option>Control</option>
            </select>
          </div>
          <div>
            <label>Canal</label>
            <select name="channel" id="channel">
              <option value="">Selecciona</option>
              <option>Teléfono</option>
              <option selected>SMS</option>
            </select>
          </div>
          <div style="grid-column:1 / -1;">
            <label>Notas</label>
            <textarea name="notes" id="notes" rows="3" placeholder="Detalle opcional"></textarea>
          </div>
        </div>
        <div class="actions">
          <button type="button" class="btn" id="cancelBtn">Cancelar</button>
          <button type="submit" class="btn" style="background:#1d4ed8; color:white;">Guardar cita</button>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const today = new Date();
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth()+1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      const todayStr = `${yyyy}-${mm}-${dd}`;

      const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        selectable: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: true,
        validRange: { start: todayStr }, // Bloquear días pasados
        selectAllow: function(arg) {
          // Evita selección de días pasados también en vistas de semana/día
          return arg.start >= new Date(todayStr + 'T00:00:00');
        },
        events: '{{ route('appointments.events') }}',
        eventDidMount: function(info) {
          const start = info.event.start;
          const yyyy = start.getFullYear();
          const mm = String(start.getMonth()+1).padStart(2,'0');
          const dd = String(start.getDate()).padStart(2,'0');
          const hh = String(start.getHours()).padStart(2,'0');
          const mi = String(start.getMinutes()).padStart(2,'0');
          const type = info.event.extendedProps?.appointment_type || '';
          const channel = info.event.extendedProps?.channel || '';
          const notes = info.event.extendedProps?.notes || '';
          const lines = [
            info.event.title || 'Cita',
            `${yyyy}-${mm}-${dd} ${hh}:${mi}`,
            type ? `Tipo: ${type}` : '',
            channel ? `Canal: ${channel}` : '',
            notes ? `Notas: ${notes}` : ''
          ].filter(Boolean).join('\n');
          info.el.setAttribute('title', lines);
        },
        select: function(info) {
          // Abrir modal con fecha seleccionada
          openModal(info.startStr);
        },
        eventDrop: async function(info) {
          // Evitar mover a pasado
          const min = new Date(todayStr + 'T00:00:00');
          if (info.event.start < min) { info.revert(); return; }
          const start = info.event.start;
          const end = info.event.end || new Date(start.getTime() + 30*60000);
          const durationMin = Math.max(10, Math.round((end - start)/60000));
          const yyyy = start.getFullYear();
          const mm = String(start.getMonth()+1).padStart(2,'0');
          const dd = String(start.getDate()).padStart(2,'0');
          const hh = String(start.getHours()).padStart(2,'0');
          const mi = String(start.getMinutes()).padStart(2,'0');
          try {
            const resp = await fetch(`{{ url('/appointments') }}/${info.event.id}/update`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
              body: JSON.stringify({ date: `${yyyy}-${mm}-${dd}`, time: `${hh}:${mi}`, duration_min: durationMin })
            });
            if (!resp.ok) {
              info.revert();
              const err = await resp.json().catch(() => ({ message: 'No se pudo reprogramar la cita' }));
              alert(err.message || 'No se pudo reprogramar la cita');
            }
          } catch(e) {
            info.revert();
            alert('Error de red al reprogramar la cita');
          }
        },
        eventResize: async function(info) {
          // Evitar dejar inicio en pasado
          const min = new Date(todayStr + 'T00:00:00');
          if (info.event.start < min) { info.revert(); return; }
          const start = info.event.start;
          const end = info.event.end || new Date(start.getTime() + 30*60000);
          const durationMin = Math.max(10, Math.round((end - start)/60000));
          const yyyy = start.getFullYear();
          const mm = String(start.getMonth()+1).padStart(2,'0');
          const dd = String(start.getDate()).padStart(2,'0');
          const hh = String(start.getHours()).padStart(2,'0');
          const mi = String(start.getMinutes()).padStart(2,'0');
          try {
            const resp = await fetch(`{{ url('/appointments') }}/${info.event.id}/update`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
              body: JSON.stringify({ date: `${yyyy}-${mm}-${dd}`, time: `${hh}:${mi}`, duration_min: durationMin })
            });
            if (!resp.ok) {
              info.revert();
              const err = await resp.json().catch(() => ({ message: 'No se pudo actualizar la duración' }));
              alert(err.message || 'No se pudo actualizar la duración');
            }
          } catch(e) {
            info.revert();
            alert('Error de red al cambiar duración');
          }
        }
      });
      calendar.render();

      // Badge bajo stock
      async function refreshLow() {
        try {
          const resp = await fetch('{{ route('materials.low') }}');
          if (!resp.ok) return;
          const data = await resp.json();
          const badge = document.getElementById('invBadge');
          // set link to current month
          const now = new Date();
          const ym = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0');
          const invBtn = document.getElementById('invBtn');
          if (invBtn) invBtn.href = `{{ route('materials.index') }}?month=${ym}`;
          if (data && typeof data.low === 'number' && data.low > 0) {
            badge.textContent = data.low;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }
        } catch(e) { /* ignore */ }
      }
      refreshLow();
      setInterval(refreshLow, 30000);
      // Also navigate to current month when clicking the badge explicitly
      document.getElementById('invBadge')?.addEventListener('click', (e) => {
        e.preventDefault();
        const now = new Date();
        const ym = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0');
        window.location.href = `{{ route('materials.index') }}?month=${ym}`;
      });

      const modal = document.getElementById('modal');
      const dateInput = document.getElementById('date');
      const timeInput = document.getElementById('time');
      const cancelBtn = document.getElementById('cancelBtn');
      const form = document.getElementById('apptForm');
      const dniInput = document.getElementById('dni');
      const firstNameInput = document.getElementById('patient_first_name');
      const lastNameInput = document.getElementById('patient_last_name');
      const ageInput = document.getElementById('patient_age');
      const phoneInput = document.getElementById('phone');
      let editingId = null;

      function openModal(dateStr) {
        // Si la fecha es pasada (por seguridad)
        const d = new Date(dateStr);
        const min = new Date(todayStr + 'T00:00:00');
        if (d < min) return;
        dateInput.value = dateStr.slice(0,10);
        timeInput.value = '09:00';
        // Default channel to SMS
        const channelSel = document.getElementById('channel');
        if (channelSel) channelSel.value = 'SMS';
        if (phoneInput) phoneInput.value = '';
        if (dniInput) dniInput.value = '';
        if (firstNameInput) firstNameInput.value = '';
        if (lastNameInput) lastNameInput.value = '';
        if (ageInput) ageInput.value = '';
        modal.classList.add('show');
        editingId = null;
        document.body.classList.add('modal-open');
      }
      function closeModal() { modal.classList.remove('show'); document.body.classList.remove('modal-open'); }
      cancelBtn.addEventListener('click', closeModal);
      modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
      // Evitar que los clics dentro de la tarjeta se propaguen al backdrop
      modal.querySelector('.card').addEventListener('click', (e) => e.stopPropagation());

      // Eliminado: autorrelleno por DNI y llamadas a API.

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(form).entries());
        try {
          const url = editingId ? ('{{ url('/appointments') }}/' + editingId + '/update') : '{{ route('appointments.store') }}';
          const resp = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
          });
          if (!resp.ok) {
            const err = await resp.json().catch(() => ({ message: 'Error al crear la cita'}));
            alert(err.message || 'Error al crear la cita');
            return;
          }
          closeModal();
          calendar.refetchEvents();
        } catch (err) {
          alert('Error de red al registrar la cita');
        }
      });

      // Click en evento para editar/eliminar
      calendar.setOption('eventClick', function(info) {
        const ev = info.event;
        editingId = ev.id;
        const start = new Date(ev.start);
        const yyyy = start.getFullYear();
        const mm = String(start.getMonth()+1).padStart(2,'0');
        const dd = String(start.getDate()).padStart(2,'0');
        const hh = String(start.getHours()).padStart(2,'0');
        const mi = String(start.getMinutes()).padStart(2,'0');
        dateInput.value = `${yyyy}-${mm}-${dd}`;
        timeInput.value = `${hh}:${mi}`;
        // Populate basic fields if present
        const apptTypeSel = document.getElementById('appointment_type');
        if (apptTypeSel && ev.extendedProps.appointment_type) apptTypeSel.value = ev.extendedProps.appointment_type;
        const channelSel2 = document.getElementById('channel');
        if (channelSel2 && ev.extendedProps.channel) channelSel2.value = ev.extendedProps.channel;
        const notesEl = document.getElementById('notes');
        if (notesEl) notesEl.value = ev.extendedProps.notes || '';
        if (dniInput) dniInput.value = ev.extendedProps.dni || '';
        // Split patient_name into first/last best-effort
        const full = ev.extendedProps.patient_name || '';
        if (full && (firstNameInput || lastNameInput)) {
          const parts = full.trim().split(/\s+/);
          if (firstNameInput) firstNameInput.value = parts.slice(0, Math.max(1, Math.ceil(parts.length/2))).join(' ');
          if (lastNameInput) lastNameInput.value = parts.slice(Math.max(1, Math.ceil(parts.length/2))).join(' ');
        } else {
          if (firstNameInput) firstNameInput.value = '';
          if (lastNameInput) lastNameInput.value = '';
        }
        if (ageInput) ageInput.value = ev.extendedProps.patient_age ?? '';
        if (phoneInput) phoneInput.value = ev.extendedProps.phone || '';
        modal.classList.add('show');

        // Añadir botones de editar/eliminar en acciones si no existen
        let deleteBtn = document.getElementById('deleteBtn');
        if (!deleteBtn) {
          deleteBtn = document.createElement('button');
          deleteBtn.id = 'deleteBtn';
          deleteBtn.className = 'btn';
          deleteBtn.style.background = '#dc2626';
          deleteBtn.style.color = 'white';
          deleteBtn.type = 'button';
          deleteBtn.textContent = 'Eliminar';
          form.querySelector('.actions').prepend(deleteBtn);
          deleteBtn.addEventListener('click', async () => {
            if (!editingId) return;
            if (!confirm('¿Eliminar esta cita?')) return;
            try {
              const resp = await fetch('{{ url('/appointments') }}/' + editingId + '/delete', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
              });
              if (!resp.ok) {
                alert('No se pudo eliminar la cita');
                return;
              }
              closeModal();
              calendar.refetchEvents();
            } catch (e) {
              alert('Error de red al eliminar la cita');
            }
          });
        }
      });
    });
  </script>
@include('partials.bot_fradadi')
</body>
</html>
