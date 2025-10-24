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
      <div id="bf-suggests" style="display:flex; flex-wrap:wrap; gap:6px;">
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
  const suggests = document.getElementById('bf-suggests');
  const TOPIC_POOL = [
    // 📚 Historia y Fundamentos
    'Orígenes de la Odontología','Evolución de la práctica dental','Personajes históricos en odontología','Avances tecnológicos en odontología moderna',
    // 🧠 Promoción y Prevención
    'Conceptos clave de promoción de salud bucal','Prevención: niveles y estrategias','Educación para la salud en odontología','Diferencias entre promoción y prevención',
    // 🔬 Epidemiología y Salud Pública
    'Introducción a la epidemiología bucal','Tipos de investigaciones epidemiológicas','Perfil epidemiológico bucodental','Clasificación y vigilancia epidemiológica',
    // 🦷 Enfermedades y Prevención
    'Caries dental: causas y prevención','Índices odontológicos (CPO-D, CEO-D, etc.)','Saliva y microflora bucal','Uso del ozono y fluoruros en caries',
    // 🪥 Higiene y Técnicas
    'Técnicas de cepillado dental','Índices de higiene bucal','Selladores y edulcorantes: usos y contraindicaciones',
    // 🩺 Enfermedades Periodontales
    'Gingivitis: factores de riesgo y tratamiento','Periodontitis: prevención y manejo','Índices periodontales (Russell, PMA, etc.)',
    // 😬 Maloclusiones y Ortodoncia
    'Prevención de maloclusiones','Ortodoncia interceptiva','Niveles de prevención en ortodoncia',
    // 🧪 Diagnóstico y Cáncer Bucal
    'Factores de riesgo del cáncer bucal','Diagnóstico diferencial en odontología',
    // 📷 Radiología
    'Radiología en odontología preventiva'
  ];
  function shuffle(arr){ for (let i=arr.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [arr[i],arr[j]]=[arr[j],arr[i]]; } return arr; }
  function renderRandomSuggests(n=6){
    if (!suggests) return;
    suggests.innerHTML = '';
    const picks = shuffle(TOPIC_POOL.slice()).slice(0, n);
    picks.forEach(label => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'bf-suggest';
      b.textContent = label;
      b.style.border = '1px solid #e5e7eb';
      b.style.background = '#fff';
      b.style.borderRadius = '999px';
      b.style.padding = '6px 10px';
      b.style.cursor = 'pointer';
      b.addEventListener('click', ()=>{ sendUser(label); reply(label); });
      suggests.appendChild(b);
    });
  }
  function open(){ panel.style.display='block'; panel.setAttribute('aria-hidden','false'); toggle.setAttribute('aria-expanded','true'); setTimeout(()=>input.focus(),100); }
  function close(){ panel.style.display='none'; panel.setAttribute('aria-hidden','true'); toggle.setAttribute('aria-expanded','false'); }
  toggle.addEventListener('click', ()=>{ const show = panel.style.display!=='block'; if (show) { renderRandomSuggests(); } show?open():close(); });
  closeBtn.addEventListener('click', close);
  box.addEventListener('click', (e)=>{ const s = e.target.closest('.bf-suggest'); if(!s) return; sendUser(s.textContent); reply(s.textContent); });
  form.addEventListener('submit', (e)=>{ e.preventDefault(); const t = input.value.trim(); if(!t) return; sendUser(t); input.value=''; reply(t); });
  // inicial
  renderRandomSuggests();
  function addMsg(text, cls){ const d=document.createElement('div'); d.className='bf-msg '+cls; d.textContent=text; box.appendChild(d); box.scrollTop=box.scrollHeight; }
  function sendUser(t){ addMsg(t, 'bf-user'); }
  async function reply(t){
    // Respuesta corta por intents locales
    const local = answer(t);
    if (local) addMsg(local, 'bf-bot');
    // Consultar a Gemini vía backend
    try {
      const r = await fetch('{{ route('bot.ask') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ q: t })
      });
      if (r.ok) {
        const data = await r.json();
        if (data && data.answer) addMsg(data.answer, 'bf-bot');
        else addMsg('No obtuve respuesta. Inténtalo de nuevo.', 'bf-bot');
      } else {
        addMsg('No se pudo consultar la IA (Gemini).', 'bf-bot');
      }
    } catch(e) {
      addMsg('Error de red al consultar la IA.', 'bf-bot');
    }
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
    if (/(periodontal|encia|encía|sangrado)/.test(s)) return 'Sangrado de encía suele indicar gingivitis. Mejora higiene y consulta para evaluación periodontal.';
    // Temas curados adicionales
    if (/(cancer|cáncer).*bucal|factores de riesgo.*cancer|factores de riesgo.*cáncer/.test(s)) return 'Cáncer bucal: factores de riesgo → tabaco, alcohol, HPV, exposición solar (labio), mala higiene, irritación crónica. Signos de alarma: úlceras que no cicatrizan, leucoplasias/eritroplasias.';
    if (/(diagnostico diferencial|diagnóstico diferencial)/.test(s)) return 'Diagnóstico diferencial: comparar signos/síntomas con entidades similares para descartar. Historia clínica, examen intra/extraoral y pruebas complementarias orientan la decisión.';
    if (/(radiologia|radiología).*preventiva/.test(s)) return 'Radiología preventiva: bite-wings para caries interproximales, periapicales para lesiones apicales, panorámica para visión general. Usar cuando cambie la conducta clínica.';
    if (/(promocion|promoción).*prevencion|prevencion: niveles|prevención: niveles/.test(s)) return 'Promoción: capacitar y empoderar para salud bucal. Prevención: acciones específicas (primaria, secundaria, terciaria). Primaria evita aparición; secundaria detecta precoz; terciaria limita secuelas.';
    if (/(epidemiologia|epidemiología).*bucal|perfil epidemiologico|perfil epidemiológico/.test(s)) return 'Epidemiología bucal: estudia distribución/determinantes de enfermedades orales. Perfil: prevalencia de caries, enfermedad periodontal, maloclusiones, hábitos y acceso a servicios.';
    if (/(indices odontologicos|índices odontológicos|cpo-d|ceo-d|cpod|ceod)/.test(s)) return 'Índices odontológicos: CPO-D (permanentes) y CEO-D (temporales) miden piezas cariadas, perdidas y obturadas. Útiles para vigilancia y planificación.';
    if (/(saliva|microflora|microbiota).*bucal/.test(s)) return 'Saliva: tampón, remineralización y defensa. Microflora: equilibrio entre bacterias acidogénicas y defensas del huésped. Flujo salival bajo aumenta riesgo de caries.';
    if (/(ozono|fluoruros|flúor).*caries/.test(s)) return 'Prevención con ozono y fluoruros: el flúor favorece remineralización y resistencia del esmalte; el ozono puede reducir carga bacteriana en lesiones iniciales (uso adyuvante).';
    if (/(indices de higiene|índices de higiene|opl|simplificado|green).*higiene/.test(s)) return 'Índices de higiene: OHI-S (Greene y Vermillion) valora detritus y cálculo; Silness y Löe miden placa. Útiles para educación y seguimiento.';
    if (/(selladores|edulcorantes)/.test(s)) return 'Selladores: protegen fosas y fisuras en molares en riesgo. Edulcorantes como xilitol reducen cariogenicidad; evitar abuso de azúcares fermentables.';
    if (/(gingivitis)/.test(s)) return 'Gingivitis: inflamación sin pérdida de inserción. Manejo: higiene mecánica, profilaxis, control de placa y factores locales.';
    if (/(periodontitis)/.test(s)) return 'Periodontitis: pérdida de inserción/soporte óseo. Prevención/manejo: control de placa, raspado/alizado radicular y control de factores de riesgo (tabaco, diabetes).';
    if (/(maloclusion|maloclusión|oclusion|oclusión).*prevencion/.test(s)) return 'Prevención de maloclusiones: control de hábitos orales, manejo de espacio en dentición mixta, detección temprana de discrepancias óseo-dentarias.';
    if (/(ortodoncia interceptiva)/.test(s)) return 'Ortodoncia interceptiva: intervenciones tempranas para corregir problemas en crecimiento (expansión, mantenedores de espacio, control de hábitos).';
    return 'Puedo ayudarte con caries, FDI, cepillado, dolor, ortodoncia, periodoncia y más. Intenta preguntar con palabras clave.';
  }
})();
</script>
