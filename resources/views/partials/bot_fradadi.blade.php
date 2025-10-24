<div id="bot-fradadi" style="position:fixed; right:18px; bottom:18px; z-index:4000; font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;">
  <button id="bf-toggle" type="button" aria-expanded="false" style="display:flex; align-items:center; gap:10px; background:#1d4ed8; color:white; border:2px solid #93c5fd; border-radius:999px; padding:10px 14px; box-shadow:0 12px 24px rgba(29,78,216,.35); cursor:pointer;">
    <span id="bf-avatar" style="width:28px; height:28px; border-radius:999px; background:linear-gradient(135deg,#22c55e,#16a34a); display:inline-flex; align-items:center; justify-content:center; font-weight:900; animation: bf-bounce 2s ease-in-out infinite;">🦷</span>
    <span style="font-weight:800;">Bot‑FRADADI</span>
  </button>
  <div id="bf-panel" aria-hidden="true" style="display:none; position:absolute; right:0; bottom:62px; width:340px; max-height:70vh; background:white; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 20px 40px rgba(2,6,23,.35);">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; background:#f8fafc; border-bottom:1px solid #e5e7eb;">
      <div style="display:flex; align-items:center; gap:8px;">
        <div style="width:28px; height:28px; border-radius:999px; background:linear-gradient(135deg,#22c55e,#16a34a); display:flex; align-items:center; justify-content:center; color:white; font-weight:900;">🦷</div>
        <div>
          <div style="font-weight:800; line-height:1;">Bot‑FRADADI</div>
          <div style="font-size:12px; color:#64748b;">Asistente odontológico</div>
        </div>
      </div>
      <button id="bf-close" type="button" aria-label="Cerrar" style="background:#ef4444; color:white; border:0; width:30px; height:30px; border-radius:999px; font-weight:900; cursor:pointer;">×</button>
    </div>
    <div id="bf-messages" style="padding:12px; display:flex; flex-direction:column; gap:10px; overflow:auto; max-height:50vh; background:white;">
      <div style="align-self:flex-start; max-width:88%; background:#f1f5f9; color:#0f172a; padding:8px 10px; border-radius:12px;">Hola, soy Bot‑FRADADI. Pregúntame sobre prevención, caries, cepillado, citas, dolor, piezas FDI, etc.</div>
      <div style="display:flex; flex-wrap:wrap; gap:6px;">
        <button class="bf-suggest" type="button" style="border:1px solid #e5e7eb; background:#fff; border-radius:999px; padding:6px 10px; cursor:pointer;">¿Qué es caries c1, c2, c3?</button>
        <button class="bf-suggest" type="button" style="border:1px solid #e5e7eb; background:#fff; border-radius:999px; padding:6px 10px; cursor:pointer;">Números FDI 11/21/31/41</button>
        <button class="bf-suggest" type="button" style="border:1px solid #e5e7eb; background:#fff; border-radius:999px; padding:6px 10px; cursor:pointer;">Dolor de muela</button>
        <button class="bf-suggest" type="button" style="border:1px solid #e5e7eb; background:#fff; border-radius:999px; padding:6px 10px; cursor:pointer;">Cepillado correcto</button>
      </div>
    </div>
    <form id="bf-form" style="display:flex; gap:6px; padding:10px; background:#f8fafc; border-top:1px solid #e5e7eb;">
      <input id="bf-input" type="text" placeholder="Escribe tu pregunta…" aria-label="Mensaje" style="flex:1; border:1px solid #e2e8f0; border-radius:10px; padding:10px;" />
      <button class="bf-send" type="submit" style="background:#1d4ed8; color:white; border:1px solid #93c5fd; border-radius:10px; padding:10px 12px; font-weight:800; cursor:pointer;">Enviar</button>
    </form>
  </div>
</div>
<style>
@keyframes bf-bounce { 0%,100%{ transform:translateY(0) } 50%{ transform:translateY(-4px) } }
.bf-bot{align-self:flex-start; background:#f1f5f9; color:#0f172a}
.bf-user{align-self:flex-end; background:#1d4ed8; color:#fff}
.bf-msg{max-width:88%; padding:8px 10px; border-radius:12px}
</style>
<script>
(function(){
  const root = document.getElementById('bot-fradadi');
  if (!root) return;
  const toggle = document.getElementById('bf-toggle');
  const panel = document.getElementById('bf-panel');
  const closeBtn = document.getElementById('bf-close');
  const form = document.getElementById('bf-form');
  const input = document.getElementById('bf-input');
  const box = document.getElementById('bf-messages');
  function open(){ panel.style.display='block'; panel.setAttribute('aria-hidden','false'); toggle.setAttribute('aria-expanded','true'); setTimeout(()=>input.focus(),100); }
  function close(){ panel.style.display='none'; panel.setAttribute('aria-hidden','true'); toggle.setAttribute('aria-expanded','false'); }
  toggle.addEventListener('click', ()=>{ const show = panel.style.display!=='block'; show?open():close(); });
  closeBtn.addEventListener('click', close);
  box.addEventListener('click', (e)=>{ const s = e.target.closest('.bf-suggest'); if(!s) return; sendUser(s.textContent); reply(s.textContent); });
  form.addEventListener('submit', (e)=>{ e.preventDefault(); const t = input.value.trim(); if(!t) return; sendUser(t); input.value=''; reply(t); });
  function addMsg(text, cls){ const d=document.createElement('div'); d.className='bf-msg '+cls; d.textContent=text; box.appendChild(d); box.scrollTop=box.scrollHeight; }
  function sendUser(t){ addMsg(t, 'bf-user'); }
  async function reply(t){
    const ans = answer(t);
    addMsg(ans, 'bf-bot');
    try {
      const url = '{{ route('bot.search') }}' + '?q=' + encodeURIComponent(t);
      const r = await fetch(url);
      if (r.ok) {
        const data = await r.json();
        const items = (data && data.results) ? data.results : [];
        if (items.length) {
          addResults(items);
        }
      }
    } catch(e) { /* ignore */ }
  }
  function addResults(items){
    const wrap = document.createElement('div');
    wrap.className = 'bf-msg bf-bot';
    const title = document.createElement('div');
    title.textContent = 'Material relacionado:';
    title.style.fontWeight = '700';
    wrap.appendChild(title);
    const list = document.createElement('div');
    list.style.display = 'flex';
    list.style.flexDirection = 'column';
    list.style.gap = '8px';
    items.forEach(it => {
      const row = document.createElement('div');
      const a = document.createElement('a');
      a.href = it.url || '#';
      a.target = '_blank';
      a.rel = 'noopener';
      a.textContent = it.title || 'Documento';
      a.style.color = '#1d4ed8';
      a.style.fontWeight = '700';
      a.style.textDecoration = 'none';
      const sn = document.createElement('div');
      sn.textContent = it.snippet ? (it.snippet + '…') : (it.description || '');
      sn.style.color = '#475569';
      sn.style.fontSize = '12px';
      row.appendChild(a);
      row.appendChild(sn);
      // topic chips from auto_titles
      if (Array.isArray(it.auto_titles) && it.auto_titles.length) {
        const chips = document.createElement('div');
        chips.style.display = 'flex';
        chips.style.flexWrap = 'wrap';
        chips.style.gap = '6px';
        chips.style.marginTop = '4px';
        it.auto_titles.slice(0,4).forEach(tt => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'bf-suggest';
          btn.textContent = tt;
          btn.style.border = '1px solid #e5e7eb';
          btn.style.background = '#fff';
          btn.style.borderRadius = '999px';
          btn.style.padding = '6px 10px';
          btn.style.cursor = 'pointer';
          btn.addEventListener('click', ()=>{ sendUser(tt); reply(tt); });
          chips.appendChild(btn);
        });
        row.appendChild(chips);
      }
      list.appendChild(row);
    });
    wrap.appendChild(list);
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
  }
  function answer(q){
    const s = q.toLowerCase();
    if (/(caries|c1|c2|c3)/.test(s)) return 'Caries: c1 inicial esmalte, c2 en dentina, c3 cavitada. Atiéndelo pronto para evitar dolor e infección.';
    if (/(fdi|11|21|31|41|nomenclatura|numer|pieza)/.test(s)) return 'FDI: dos dígitos. 11 incisivo central sup. derecho, 21 sup. izquierdo, 31 inf. izquierdo, 41 inf. derecho. 51–85 son temporales.';
    if (/(dolor|muela|odontalgia)/.test(s)) return 'Si hay dolor intenso o con inflamación, evita automedicación y solicita atención. Enjuague tibio con sal puede aliviar temporalmente.';
    if (/(cepillado|higiene|cepillo|pasta)/.test(s)) return 'Cepilla 2–3 veces/día por 2 minutos. Técnica barrido suave, pasta con flúor, hilo dental diario y control semestral.';
    if (/(sarro|placa|profilaxis)/.test(s)) return 'La placa es una biopelícula que se calcifica en sarro. La profilaxis profesional remueve depósitos y previene gingivitis.';
    if (/(bruxismo|apretar|ferula)/.test(s)) return 'Bruxismo: apretar o rechinar dientes. Puede causar desgaste y dolor. Una férula de descarga nocturna suele ayudar.';
    if (/(ortodoncia|frenos|alineadores)/.test(s)) return 'Ortodoncia alinea dientes y mordida. Hay brackets metálicos/estéticos y alineadores. Una valoración determina el plan.';
    if (/(cita|turno|agendar|horario)/.test(s)) return 'Para agendar o ver tu registro, usa la sección de citas en el sistema (Registro).';
    if (/(periodontal|encía|sangrado)/.test(s)) return 'Sangrado de encía suele indicar gingivitis. Mejora higiene y consulta para evaluación periodontal.';
    return 'Puedo ayudarte con caries, FDI, cepillado, dolor, ortodoncia, periodoncia y más. Intenta preguntar con palabras clave.';
  }
})();
</script>
