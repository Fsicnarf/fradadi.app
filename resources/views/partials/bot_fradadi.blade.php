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
      <div id="bf-greet" style="align-self:flex-start; max-width:88%; background:#f1f5f9; color:#0f172a; padding:8px 10px; border-radius:12px;">Bienvenid@, soy tu asistente virtual Bot-FRADADI, para ayudarte con conocimientos odontológicos.</div>
      <div id="bf-suggests" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
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
  const greet = document.getElementById('bf-greet');
  let TOPIC_POOL = [];
  const GREETINGS = [
    'Bienvenid@, soy tu asistente virtual Bot-FRADADI, listo para apoyarte con información odontológica.',
    'Bienvenid@, Bot-FRADADI está aquí como tu asistente virtual para brindarte conocimientos sobre odontología.',
    'Bienvenid@, soy tu asistente virtual Bot-FRADADI, para ayudarte con conocimientos odontológicos.',
    'Bienvenid@, soy Bot-FRADADI, tu asistente virtual especializado en temas odontológicos.',
    'Bienvenid@, Bot-FRADADI te acompaña como asistente virtual para resolver tus dudas odontológicas.',
    'Bienvenid@, soy tu asistente virtual Bot-FRADADI, preparado para ayudarte en el campo de la odontología.',
    'Bienvenid@, Bot-FRADADI es tu asistente virtual para guiarte con conocimientos odontológicos.',
    'Bienvenid@, Bot-FRADADI está aquí para ayudarte con información odontológica.',
    'Bienvenid@, soy Bot-FRADADI, tu asistente virtual enfocado en ayudarte con temas de odontología.'
  ];
  function shuffle(arr){ for (let i=arr.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [arr[i],arr[j]]=[arr[j],arr[i]]; } return arr; }
  function uniqueBy(arr, key){ const seen=new Set(); const out=[]; for(const it of arr){ const k=key(it); if(seen.has(k)) continue; seen.add(k); out.push(it);} return out; }
  const QA = [
    { k: /(caries|c1|c2|c3)/i, a: 'Caries: c1 inicial esmalte, c2 afecta dentina, c3 cavitada. Control: higiene, dieta baja en azúcares y aplicación de flúor.' },
    { k: /(fdi|nomenclatura|pieza|11|21|31|41)/i, a: 'FDI: dos dígitos. 11 incisivo central sup. derecho; 21 sup. izquierdo; 31 inf. izquierdo; 41 inf. derecho. 51–85 dentición temporal.' },
    { k: /(cepillad|higiene|cepillo|pasta)/i, a: 'Cepillado 2–3 veces al día por 2 minutos, técnica de barrido suave, pasta con flúor e hilo dental diario.' },
    { k: /(dolor|muela|odontalgia)/i, a: 'Para dolor dental evita automedicación; enjuague tibio con sal puede aliviar. Requiere evaluación clínica si persiste.' },
    { k: /(bruxismo|apretar|rechinar|férula|ferula)/i, a: 'Bruxismo: apretar o rechinar dientes. Manejo con férula de descarga y control de estrés; revisar desgastes y dolor muscular.' },
    { k: /(sarro|placa|profilaxis)/i, a: 'Placa bacteriana puede calcificarse en sarro. La profilaxis profesional previene gingivitis y periodontitis.' },
    { k: /(gingivitis)/i, a: 'Gingivitis: inflamación de encía sin pérdida de inserción. Tratamiento: higiene, profilaxis y control de placa.' },
    { k: /(periodontitis|bolsa periodontal)/i, a: 'Periodontitis: pérdida de soporte. Manejo: raspado y alisado radicular, control de placa y factores de riesgo (tabaco, diabetes).'},
    { k: /(indices|índices).*(cpo|ceo|cpod|ceod)/i, a: 'Índices CPO-D/CEO-D miden piezas cariadas, perdidas y obturadas para estimar experiencia de caries.' },
    { k: /(índice|indice).*higiene|ohi|silness|loe|löe/i, a: 'Índices de higiene: OHI-S valora detritus y cálculo; Silness-Löe mide placa. Útiles en educación y seguimiento.' },
    { k: /(radiolog|bite.?wing|periapical|panoramica|panorámica)/i, a: 'Bite-wings para caries interproximales; periapical para lesiones apicales; panorámica para visión general. Usar con criterio clínico.' },
    { k: /(sellador|selladores)/i, a: 'Selladores de fosas y fisuras reducen caries en molares con surcos profundos; requieren campo seco y control periódico.' },
    { k: /(fluor|flúor|fluoruros)/i, a: 'El flúor favorece remineralización y aumenta resistencia del esmalte. Uso tópico supervisado según riesgo.' },
    { k: /(cancer|cáncer).*bucal|factores de riesgo.*c(a|á)ncer/i, a: 'Cáncer bucal: riesgo por tabaco, alcohol, HPV y exposición solar. Alarma: úlceras que no cicatrizan, leucoplasias y eritroplasias.' },
    { k: /(diagnostico diferencial|diagnóstico diferencial)/i, a: 'Diagnóstico diferencial: comparar signos y síntomas con entidades similares usando historia, examen y pruebas complementarias.' },
    { k: /(ortodoncia interceptiva|mantenedor de espacio|habitos|hábitos)/i, a: 'Ortodoncia interceptiva: intervenciones tempranas para guiar crecimiento, controlar hábitos y mantener espacios.' },
    { k: /(halitosis)/i, a: 'Halitosis: suele relacionarse con placa lingual, encías o caries. Manejo: higiene de lengua, control de placa y evaluar causas sistémicas.' },
    { k: /(hipersensibilidad|sensibilidad) dent/i, a: 'Hipersensibilidad dentinaria: dolor breve ante frío/calor por exposición dentinaria. Manejo: desensibilizantes y control de hábitos erosivos.' },
    { k: /(trauma|traumatismo) dent/i, a: 'Trauma dentoalveolar: conservar fragmentos, reimplantar avulsiones si es posible y acudir urgente. Radiografías y control pulpar.' },
    { k: /(nutrici|dieta|azucar|azúcar)/i, a: 'Reducir frecuencia de azúcares fermentables entre comidas; preferir agua y xilitol en chicles para disminuir riesgo de caries.' },
    { k: /(caries|c1|c2|c3|caries dental)/i, a: 'Caries: destrucción del tejido duro por ácidos bacterianos. c1 esmalte, c2 dentina, c3 cavitación. Prevención: higiene, flúor y selladores.' },
    { k: /(fdi|nomenclatura|pieza|11|21|31|41|sistema fdi)/i, a: 'FDI: sistema de dos dígitos para identificar piezas. 11=incisivo central sup. derecho; 21=sup. izq.; 31=inf. izq.; 41=inf. der.' },
    { k: /(cepillad|higiene|cepillo|pasta|técnica de cepillado)/i, a: 'Cepillado: 2–3 veces/día por 2 minutos con técnica de barrido suave. Usar pasta con flúor y complementar con hilo dental diario.' },
    { k: /(dolor|muela|odontalgia|dolor dental)/i, a: 'Dolor dental: identificar origen (pulpar, periodontal, muscular). Evitar automedicación prolongada; evaluar clínicamente y tratar según causa.' },
    { k: /(bruxismo|apretar|rechinar|férula|ferula|desgaste dental)/i, a: 'Bruxismo: hábito de apretar o rechinar. Manejo: férula de descarga, control de estrés y seguimiento por signos de desgaste y dolor.' },
    { k: /(sarro|placa|profilaxis|cálculo dental)/i, a: 'Placa: biofilm bacteriano; si calcifica forma sarro. Profilaxis profesional y control de higiene previenen gingivitis y periodontitis.' },
    { k: /(gingivitis|encías inflamadas|gingivitis clínica)/i, a: 'Gingivitis: inflamación gingival reversible sin pérdida de inserción. Tratamiento: higiene, profilaxis y educación al paciente.' },
    { k: /(periodontitis|bolsa periodontal|pérdida de inserción)/i, a: 'Periodontitis: infección que destruye soporte dental. Manejo: raspado y alisado radicular, control de placa y factores de riesgo (tabaco, diabetes).' },
    { k: /(cpod|ceo|cpo|índice cpo|índice ceo|ceod|cpod|índices de caries)/i, a: 'Índices CPO/CEO miden piezas cariadas, perdidas y obturadas para estimar experiencia de caries en poblaciones.' },
    { k: /(ohi|sílness|sílness-löe|silness|índice higiene|indice de higiene)/i, a: 'Índices de higiene: OHI-S y Silness-Löe evalúan placa y detritus; útiles para programas preventivos y seguimiento.' },
    { k: /(bite.?wing|periapical|panoramica|panorámica|radiografía dental)/i, a: 'Radiografías: bite-wings para caries interprox., periapical para ápices, panorámica para visión global; usar criterio y protección radiológica.' },
    { k: /(sellador|selladores|fosas y fisuras|pit.*fissure|sellado)/i, a: 'Selladores de fosas y fisuras: barrera preventiva en molares con surcos profundos; requieren campo seco y controles periódicos.' },
    { k: /(fluor|flúor|fluoruros|aplicación de flúor)/i, a: 'Flúor: favorece remineralización y reduce riesgo de caries. Uso tópico (barnices, geles) según riesgo carioso y edad.' },
    { k: /(cáncer|cáncer bucal|cancer bucal|lesión oral sospechosa)/i, a: 'Cáncer bucal: factores de riesgo tabaco, alcohol, HPV, exposición solar. Derivar lesiones que no cicatrizan o leucoplasias para biopsia.' },
    { k: /(diagnostico diferencial|diagnóstico diferencial|dxd)/i, a: 'Diagnóstico diferencial: comparar signos y síntomas para distinguir entidades similares; usar historia, examen y pruebas complementarias.' },
    { k: /(ortodoncia interceptiva|mantenedor de espacio|mantenedor|hábitos|habitos)/i, a: 'Ortodoncia interceptiva: intervenciones tempranas para guiar crecimiento, controlar hábitos (succión) y mantener espacios.' },
    { k: /(halitosis|mal aliento|aliento)/i, a: 'Halitosis: habitualmente por biofilm lingual, encías o caries. Manejo: higiene lingual, control de placa y evaluar causas sistémicas o medicamentosas.' },
    { k: /(hipersensibilidad|sensibilidad dentinaria|sensibilidad)/i, a: 'Hipersensibilidad dentinaria: dolor breve ante estímulos (frío, dulce) por exposición dentinaria. Tratamiento: desensibilizantes y control de erosión/abrasión.' },
    { k: /(trauma|traumatismo dentoalveolar|avulsión|fractura dental)/i, a: 'Trauma dentoalveolar: conservar fragmentos, reimplantar avulsiones (si posible) y acudir urgente. Radiografías y seguimiento pulpar son claves.' },
    { k: /(nutrici|dieta|azucar|azúcar|alimentación)/i, a: 'Nutrición y caries: reducir frecuencia de azúcares fermentables entre comidas; preferir agua y alimentos no cariogénicos; educación alimentaria.' },
    { k: /(profilaxis|limpieza profesional|curetaje)/i, a: 'Profilaxis: limpieza profesional para eliminar placa y cálculo; indicada en higiene insuficiente y como parte del tratamiento periodontal inicial.' },
    { k: /(ultrasonidos|detartraje|tartrectomía|puntas de ultrasonido)/i, a: 'Ultrasonidos: instrumental para detartraje eficiente de cálculo; uso según sensibilidad y control de aerosoles con succión adecuada.' },
    { k: /(barniz de flúor|barniz|barnices)/i, a: 'Barniz de flúor: aplicación tópica protectora de baja concentración sistémica y alta efectividad en reducción de caries en niños y adultos.' },
    { k: /(pasta profilaxis|prophylaxis paste|pasta de pulido)/i, a: 'Pasta de profilaxis: utilizada en limpieza profesional para eliminar manchas y pulir superficies; combinar con técnicas de higiene domiciliaria.' },
    { k: /(detector de caries|fluorescencia|cariosidad|detector)/i, a: 'Detectores de caries: herramientas auxiliares (fluorescencia, explorador) que complementan examen clínico y radiográfico para detectar lesiones iniciales.' },
    { k: /(sellador puntos fisuras|pit.*fissure sealant|sellado de fisuras)/i, a: 'Selladores: prevenir caries en surcos profundos; aplicar en molares permanentes y revisar anualmente su retención.' },
    { k: /(cubeta flúor|cubeta para flúor|aplicación en cubeta)/i, a: 'Cubeta para flúor: método de aplicación domiciliaria o clínica de gel/espuma de flúor; usar con supervisión según edad y riesgo.' },
    { k: /(pastillero|xilitol|edulcorante|edulcorantes|goma de mascar)/i, a: 'Edulcorantes: xilitol reduce riesgo de caries al inhibir bacterias cariogénicas; preferir estos frente a azúcares fermentables entre comidas.' },
    { k: /(sellador contraindicaciones|contraindicaciones selladores|no aplicar sellador)/i, a: 'Contraindicaciones selladores: cavitación extensa, mala cooperación del paciente o imposibilidad de campo seco; en casos usar restauración directa.' },
    { k: /(educación para la salud|promoción de la salud|educación sanitaria)/i, a: 'Educación para la salud: proceso continuo para cambiar conocimientos y actitudes; clave en programas comunitarios de prevención bucal.' },
    { k: /(promoción de salud|promoción|estrategias comunitarias)/i, a: 'Promoción de salud: acciones intersectoriales para mejorar determinantes sociales y hábitos; incluye políticas, educación y entornos saludables.' },
    { k: /(vigilancia epidemiológica|epidemiología dental|perfil epidemiológico)/i, a: 'Vigilancia epidemiológica: monitoreo de la salud bucal de poblaciones para planear intervenciones preventivas y evaluar impacto.' },
    { k: /(índice higiene bucal simplificado|ihs|índice higiene|índice de higiene bucal)/i, a: 'Índice de higiene bucal simplificado (IHS): mide detritus y cálculo en grupos poblacionales, útil en campañas y seguimiento.' },
    { k: /(índice gingival|ig|índice de gingivitis|gingival index)/i, a: 'Índice gingival: evalúa inflamación gingival (color, edema, sangrado); útil para valorar respuesta a higiene y tratamientos.' },
    { k: /(índice placa|plaque index|índice de placa)/i, a: 'Índice de placa: cuantifica presencia de placa bacteriana; herramienta para educación y control en pacientes y estudios poblacionales.' },
    { k: /(índice pma|pma index|índice de russell)/i, a: 'Índices PMA/Russell: miden extensión y severidad de gingivitis; sirven para evaluar poblaciones y efectividad de programas preventivos.' },
    { k: /(periodo prepatogénico|prepatogénico|etapa prepatogénica)/i, a: 'Periodo prepatogénico: fase donde actúan determinantes y factores de riesgo antes de la aparición de la enfermedad; momento ideal para promoción.' },
    { k: /(etapa de la enfermedad|patogénico|fase de la enfermedad)/i, a: 'Etapa de la enfermedad: desde la exposición a los agentes hasta la aparición de signos y síntomas; importante para diagnóstico y prevención secundaria.' },
    { k: /(investigación en salud|tipos de investigación|epidemiología)/i, a: 'Investigación en salud: incluye estudios descriptivos y analíticos para entender distribución y determinantes de enfermedades bucodentales.' },
    { k: /(perfil epidemiológico|perfil bucodental|estudio epidemiológico)/i, a: 'Perfil epidemiológico: descripción de problemas y factores de riesgo en una comunidad para planificar programas de prevención y recursos.' },
    { k: /(caries radicular|índice caries radicular|radicular caries)/i, a: 'Caries radicular: caries en superficie radicular expuesta, frecuente en edades avanzadas o recesiones gingivales. Prevención: higiene y flúor tópico.' },
    { k: /(saliva|composición saliva|flujo salival)/i, a: 'Saliva: papel en defensa (buffer, minerales). Hiposialia aumenta riesgo de caries y enfermedades mucosas; valorar medicamentos y causas sistémicas.' },
    { k: /(microflora|microbiota oral|flora oral|bacterias)/i, a: 'Microbiota oral: conjunto de microorganismos; desequilibrio favorece caries y periodontal. Control con higiene y reducción de sustratos fermentables.' },
    { k: /(colonización bacteriana|adherencia bacteriana|biofilm)/i, a: 'Colonización bacteriana: formación de biofilm sobre dientes; eliminar con cepillado mecánico + agentes químicos cuando sea necesario.' },
    { k: /(índice cpo-s|ceo-s|cpo s|ceo s)/i, a: 'Índice CPO-S/CEO-S: mide superficies cariadas, obturadas o perdidas en dentición permanente y temporal; útil en estudios poblacionales.' },
    { k: /(índice pmr|índice periodontitis|índice de necesidad de tratamiento comunitario)/i, a: 'Índices comunitarios: evalúan necesidad de tratamiento y prioridades en salud pública para asignación de recursos.' },
    { k: /(técnica del cepillado|técnica de cepillado infantil|cepillado infantil)/i, a: 'Técnica de cepillado: adaptar según edad. En niños supervisar hasta los 7–8 años; enseñar barrido suave y cubrir todas las superficies.' },
    { k: /(mecanismo de acción de los fluoruros|acción del flúor)/i, a: 'Mecanismo del flúor: remineraliza el esmalte, inhibe metabolismo bacteriano y reduce solubilidad del esmalte frente a ácidos.' },
    { k: /(efectos de los fluoruros|efectos fluor|fluorosis)/i, a: 'Efectos del flúor: protector a nivel dental; exceso en desarrollo dental puede causar fluorosis. Dosificar según edad y riesgo.' },
    { k: /(ozono|uso del ozono|ozonoterapia dental)/i, a: 'Ozono: alternativa en manejo de lesiones iniciales por su actividad antimicrobiana; complementario, no sustituye medidas preventivas básicas.' },
    { k: /(edulcorantes no cariogénicos|xilitol|sacarina|aspartamo)/i, a: 'Edulcorantes: sustitutos no fermentables (xilitol) disminuyen riesgo de caries; preferirlos frente a azúcares simples entre comidas.' },
    { k: /(selladores contraindicaciones|contraindicaciones sellador)/i, a: 'Contraindicaciones selladores: dientes con cavitación, falta de aislamiento, o mala colaboración del paciente; elegir restauración cuando procede.' },
    { k: /(enfermedad periodontal prevención|prevención periodontal|periodoncia preventiva)/i, a: 'Prevención periodontal: control de placa, raspado profesional, abandono de tabaco y control de condiciones sistémicas como diabetes.' },
    { k: /(índice pma|índice pma anterior|índice pma completo)/i, a: 'Índice PMA: mide porcentaje de dientes con inflamación o destrucción; útil en estudios de prevalencia de gingivitis.' },
    { k: /(índice de russell|russell index|índice periodoncia russell)/i, a: 'Índice de Russell: evalúa estado periodontal basándose en sangrado y cálculo; utilizado en estudios epidemiológicos.' },
    { k: /(índice periodontal oms|índice periodontal organización mundial salud|oms periodontal)/i, a: 'Índice periodontal OMS: herramienta estandarizada para evaluar salud periodontal en poblaciones y comparar resultados.' },
    { k: /(índice gingival sif|índice de higiene simplificado|ihs)/i, a: 'Índice gingival simplificado: valoración rápida de inflamación gingival y placa para uso en programas comunitarios.' },
    { k: /(gingivitis factores de riesgo|factores gingivitis)/i, a: 'Factores de riesgo gingivitis: mala higiene, tabaco, cambios hormonales, algunos medicamentos y enfermedades sistémicas.' },
    { k: /(tratamiento de la gingivitis|tratamiento gingivitis)/i, a: 'Tratamiento gingivitis: higiene oral intensiva, profilaxis, instrucción al paciente y revaluación; raramente requiere cirugía.' },
    { k: /(medidas preventivas periodontitis|prevención periodontitis)/i, a: 'Medidas preventivas periodontitis: control de placa, mantenimiento periodontal periódico y manejo de factores de riesgo (diabetes, tabaquismo).' },
    { k: /(periodontitis manejo|tratamiento periodontitis|raspado alisado)/i, a: 'Tratamiento periodontitis: raspado y alisado radicular, terapias antibióticas cuando está indicado, y cirugía en casos avanzados.' },
    { k: /(mala oclusión prevención|mala oclusión|prevención maloclusión)/i, a: 'Prevención de maloclusiones: cribado temprano, manejo de hábitos (succión digital), y ortodoncia interceptiva cuando proceda.' },
    { k: /(niveles de prevención|prevención primaria|prevención secundaria|prevención terciaria)/i, a: 'Niveles de prevención: primaria (evitar aparición), secundaria (diagnóstico precoz) y terciaria (rehabilitación y evitar secuelas).' },
    { k: /(ortodoncia interceptiva cuándo|indicaciones ortodoncia interceptiva)/i, a: 'Ortodoncia interceptiva indicada en problemas de crecimiento, pérdida prematura de piezas y hábitos que afectan evolución dental.' },
    { k: /(cuarto nivel prevención|quinto nivel prevención|prevención cuaternaria)/i, a: 'Niveles adicionales: prevención cuaternaria evita intervenciones innecesarias; la educación comunitaria reduce daños iatrogénicos.' },
    { k: /(factores de riesgo cáncer bucal|riesgo cáncer bucal|hpv)/i, a: 'Factores cáncer bucal: tabaco, alcohol, HPV (VPH), exposición solar (lábios) y precarias condiciones de higiene; educación y cribado son esenciales.' },
    { k: /(diagnóstico diferencial lesiones orales|dx diferencial oral)/i, a: 'Diagnóstico diferencial: distinguir entre lesiones ulceradas, infecciosas, traumáticas o neoplásicas; ante duda derivar para biopsia.' },
    { k: /(radiología dental prevención|uso de radiografías|indicación radiográfica)/i, a: 'Radiología: usar exámenes radiográficos según indicación clínica para diagnóstico de caries interproximal, lesión periapical o planificación.' },
    { k: /(programa comunitario oral|programa escolar dental|salud bucal comunitaria)/i, a: 'Programas comunitarios: educación escolar, aplicaciones de flúor, selladores y campañas de promoción con evaluación epidemiológica.' },
    { k: /(control de aerosoles|aerosoles dentales|protección contra aerosoles)/i, a: 'Control de aerosoles: succión de alto volumen, barreras, y medidas de protección personal; importante en procedimientos con ultrasonidos y turbina.' },
    { k: /(vacunación hpv|hpv y cáncer oral|vacuna vph)/i, a: 'Vacuna contra HPV: reduce riesgo de infecciones por VPH asociadas a cáncer orofaríngeo; recomendada según programas de salud pública.' },
    { k: /(promocion escolar|educación escolar salud bucal|programas escolares)/i, a: 'Promoción escolar: enseñar técnicas de higiene, evaluar índices y aplicar flúor/selladores en programas preventivos escolares.' },
    { k: /(tamizaje oral|cribado oral|screening bucal)/i, a: 'Tamizaje oral: búsqueda temprana de lesiones sospechosas en población de riesgo (fumadores, bebedores) para derivación y diagnóstico precoz.' },
    { k: /(consulta preventiva|chequeo dental|revisión dental)/i, a: 'Consulta preventiva: evaluación de riesgos, higiene, educación y planificación de medidas preventivas individualizadas.' },
    { k: /(consejería en salud bucal|consejería paciente|motivación higiene)/i, a: 'Consejería: informar, motivar y entrenar al paciente en técnicas de higiene y hábitos saludables para mejorar adherencia a la prevención.' },
    { k: /(fluoruración comunitaria|fluoruro en agua|fluoración comunitaria)/i, a: 'Fluoración comunitaria: medida poblacional (agua o sal) que reduce prevalencia de caries; se evalúa según contexto epidemiológico.' },
    { k: /(control del tabaco|cesación tabaquica|tabaquismo y salud bucal)/i, a: 'Control del tabaco: fundamental en prevención periodontal y cáncer bucal; incluir consejería y derivación a programas de cesación.' },
    { k: /(programa de salud oral para embarazadas|embarazo y salud bucal|embarazadas)/i, a: 'Salud bucal en embarazo: controlar gingivitis gestacional, educar sobre higiene y programar tratamiento dental seguro durante gestación.' },
    { k: /(salud bucal ancianos|geriatría dental|ancianos)/i, a: 'Salud bucal en ancianos: revisar xerostomía, caries radicular y prótesis; adaptar higiene y control de factores sistémicos.' },
    { k: /(evaluación de riesgo carioso|caries riesgo|riesgo caries)/i, a: 'Evaluación de riesgo: considerar historia de caries, dieta, flujo salival y higiene para planificar medidas preventivas individualizadas.' },
    { k: /(anestesia local|anestésico local|lidocaína|mepivacaína)/i, a: 'Anestesia local: bloqueo reversible de conducción nerviosa; lidocaína y mepivacaína son las más usadas en odontología.' },
    { k: /(jeringa carpule|jeringa aspirante|carpule|jeringa dental)/i, a: 'Jeringa carpule: instrumento metálico reutilizable para cartuchos de anestésico; debe esterilizarse entre pacientes.' },
    { k: /(aguja dental|aguja anestesia|agujas cortas|agujas largas)/i, a: 'Agujas dentales: cortas (infiltración) y largas (bloqueo); uso individual y desecho seguro tras cada aplicación.' },
    { k: /(bloqueo alveolar|bloqueo mandibular|bloqueo maxilar)/i, a: 'Bloqueo nervioso: anestesia de trayecto nervioso completo; indicado para procedimientos amplios en mandíbula o maxilar.' },
    { k: /(infiltración|anestesia infiltrativa|anestesia supraperióstica)/i, a: 'Infiltración: técnica anestésica local en zona apical; útil en maxilar por hueso esponjoso y buena difusión del anestésico.' },
    { k: /(anestesia troncular|bloqueo troncular|bloqueo alveolar inferior)/i, a: 'Anestesia troncular: técnica para bloquear nervios principales; requiere conocimiento anatómico preciso para evitar fallas.' },
    { k: /(articaína|bupivacaína|prilocaína|anestésicos locales)/i, a: 'Articaína: anestésico con alta difusión ósea; prilocaína y bupivacaína se usan según duración y sensibilidad del procedimiento.' },
    { k: /(adrenalina|epinefrina|vasoconstrictor)/i, a: 'Adrenalina: vasoconstrictor que prolonga duración y reduce toxicidad del anestésico; evitar en hipertensos no controlados.' },
    { k: /(aspiración|prueba de aspiración|inyección segura)/i, a: 'Aspiración: paso previo a inyección para evitar punción intravascular; indispensable para seguridad del procedimiento anestésico.' },
    { k: /(complicaciones anestesia|reacción adversa|toxicidad anestésica)/i, a: 'Complicaciones: reacciones alérgicas, toxicidad, hematoma o parálisis temporal; prevenir con dosis adecuadas y técnica correcta.' },
    { k: /(parálisis facial transitoria|bloqueo nervio facial)/i, a: 'Parálisis facial transitoria: posible al infiltrar anestésico cerca del nervio facial; remite en pocas horas, evitar masaje inmediato.' },
    { k: /(isquemia local|blanqueamiento temporal|efecto adrenalina)/i, a: 'Isquemia local: palidez temporal por efecto del vasoconstrictor; desaparece espontáneamente en minutos.' },
    { k: /(trismus|contractura muscular post anestesia)/i, a: 'Trismus: dificultad de apertura por trauma muscular o inflamación; aplicar calor local y ejercicios suaves.' },
    { k: /(hematoma|hematoma post anestesia)/i, a: 'Hematoma: sangrado por punción vascular; prevenir con aspiración y compresión inmediata si ocurre.' },
    { k: /(anestesia intraligamentaria|intraligamentosa)/i, a: 'Anestesia intraligamentaria: inyección en el ligamento periodontal para anestesia localizada; útil en dientes aislados.' },
    { k: /(anestesia intrapulpar|intrapulpar)/i, a: 'Anestesia intrapulpar: técnica usada durante endodoncia cuando persiste sensibilidad pulpar; produce efecto inmediato y breve.' },
    { k: /(anestesia tópica|gel anestésico|spray anestésico)/i, a: 'Anestesia tópica: usada para reducir dolor de punción o procedimientos superficiales; aplicar con hisopo en mucosa seca.' },
    { k: /(reanimación|lipotimia|síncope)/i, a: 'Síncope: reacción vasovagal por ansiedad; colocar al paciente en posición supina con piernas elevadas y administrar oxígeno si es necesario.' },
    { k: /(ergonomía dental|postura operador|posición operador)/i, a: 'Ergonomía: postura adecuada previene fatiga y lesiones; espalda recta, brazos cerca del cuerpo, visión indirecta con espejo.' },
    { k: /(campo operatorio|aislamiento absoluto|aislamiento relativo|dique de goma)/i, a: 'Aislamiento: uso de dique de goma o rollos de algodón para mantener campo seco y limpio durante procedimientos restauradores.' },
    { k: /(dique de goma|dique dental|grapas|portagrapa)/i, a: 'Dique de goma: barrera de aislamiento que mejora visibilidad y control de humedad; se fija con grapas adaptadas al diente.' },
    { k: /(eyector|succión|aspirador quirúrgico)/i, a: 'Eyector de saliva y aspirador: mantienen campo seco y eliminan aerosoles; esenciales en control de infección cruzada.' },
    { k: /(lámpara de fotocurado|fotopolimerizadora|curado|luz azul)/i, a: 'Lámpara de fotocurado: activa materiales resinosos mediante luz azul (450–470 nm); revisar intensidad y tiempo de exposición.' },
    { k: /(turbina|pieza de alta|alta velocidad)/i, a: 'Turbina: pieza de mano de alta velocidad para tallado dental; requiere enfriamiento por spray de agua y esterilización tras cada uso.' },
    { k: /(micromotor|pieza de baja|baja velocidad|contrángulo)/i, a: 'Micromotor y contrángulo: piezas de baja velocidad para pulido, caries superficiales y profilaxis; esterilizar y lubricar regularmente.' },
    { k: /(piedra de afilar|afilado|afiladores)/i, a: 'Afilado: mantener instrumentos cortantes eficaces (curetas, cinceles); piedra de Arkansas o afiladores específicos.' },
    { k: /(cureta|curetas gracey|universal|periodontal)/i, a: 'Curetas: instrumentos periodontales para raspado y alisado radicular; las Gracey se adaptan a áreas específicas.' },
    { k: /(escaler|scaler|instrumento ultrasónico)/i, a: 'Scalers: eliminan cálculo supragingival; deben usarse con presión moderada y movimientos controlados.' },
    { k: /(explorador|sonda exploradora|n° 23|odontoscopio)/i, a: 'Explorador dental: punta fina para detectar irregularidades o caries; uso cuidadoso para evitar dañar esmalte.' },
    { k: /(sonda periodontal|sonda milimetrada|sonda de williams)/i, a: 'Sonda periodontal: mide profundidad de bolsa gingival; instrumento básico para diagnóstico periodontal.' },
    { k: /(espejo bucal|espejo dental|visión indirecta)/i, a: 'Espejo bucal: permite visión indirecta y retracción de tejidos; debe mantenerse limpio y sin empañamiento.' },
    { k: /(pinza algodonera|pinza algodón|pinza porta algodones)/i, a: 'Pinza algodonera: transporta materiales pequeños o algodones al campo operatorio; debe mantenerse esterilizada.' },
    { k: /(pinza mosquito|hemostática|pinzas quirúrgicas)/i, a: 'Pinza mosquito: hemostática usada para sujetar o pinzar tejidos pequeños durante cirugía oral.' },
    { k: /(porta agujas|porta aguja mayo hegar|porta agujas castroviejo)/i, a: 'Porta agujas: sostiene la aguja de sutura durante cierre quirúrgico; los modelos varían según precisión requerida.' },
    { k: /(tijera iris|tijeras quirúrgicas|tijera recta)/i, a: 'Tijeras quirúrgicas: para cortar tejidos blandos o suturas; mantener afiladas y esterilizadas.' },
    { k: /(elevador periostal|despegador mucoperióstico)/i, a: 'Elevador periostal: separa tejido gingival y periostio del hueso; usado en cirugía y exodoncia.' },
    { k: /(bisturí|mango bisturí|hojas bisturí|hoja 15|hoja 12)/i, a: 'Bisturí: instrumento cortante con mango y hoja desechable; las más comunes: n°15 (incisión general) y 12 (curva).' },
    { k: /(fresas|fresa redonda|fresa troncocónica|fresa periforme)/i, a: 'Fresas: instrumentos rotatorios para corte y tallado; se eligen por forma y grano según tarea (caries, pulido, ajuste).' },
    { k: /(piedra de arkansas|pulido|acabado)/i, a: 'Piedra de Arkansas: usada para afilar y dar acabado fino a instrumentos cortantes y superficies metálicas.' },
    { k: /(espátula cemento|espátula de inserción|espátula de mezcla)/i, a: 'Espátula de cemento: mezcla materiales (ionómero, alginato); limpiar y secar inmediatamente tras su uso.' },
    { k: /(loseta de vidrio|loseta de mezcla|vidrio reloj)/i, a: 'Loseta de vidrio: superficie inerte para mezclar cementos o materiales; mantener limpia y seca para evitar contaminación.' },
    { k: /(porta amalgama|amalgamador|amalgama)/i, a: 'Porta amalgama: transporta amalgama al diente preparado; amalgamador mezcla polvo y mercurio hasta lograr consistencia homogénea.' },
    { k: /(condensador|atacador amalgama|plugger|consolidador)/i, a: 'Condensador: compacta material restaurador en cavidad; aplicar presión firme y controlada para evitar porosidades.' },
    { k: /(bruñidor|bruñido amalgama|burnisher)/i, a: 'Bruñidor: alisa y da brillo a restauraciones metálicas; mejora sellado marginal y apariencia.' },
    { k: /(carver|recortador|tallador amalgama)/i, a: 'Carver: recorta exceso de material restaurador; modelo Hollenback es común en operatoria dental.' },
    { k: /(matriz|porta matriz|tofflemire|banda matriz)/i, a: 'Matriz: forma temporal de pared proximal en restauraciones; tofflemire ajusta banda metálica a la pieza dental.' },
    { k: /(cuña de madera|cuñas plásticas|cuña interdental)/i, a: 'Cuñas: sellan espacio gingival durante restauraciones; facilitan contacto y contorno adecuado.' },
    { k: /(espátula resina|espátula plástica|instrumento resina)/i, a: 'Espátula para resina: adapta material compuesto sin adherirse; punta lisa y flexible.' },
    { k: /(lámpara halógena|led curado|fotopolimerizadora led)/i, a: 'Lámparas LED: fotopolimerizan resinas; control de intensidad y tiempo es clave para resistencia final.' },
    { k: /(detector de caries|láser fluorescencia|examen visual)/i, a: 'Detección de caries: se complementa examen visual con tecnología láser o fluorescencia para mayor precisión.' },
    { k: /(protección ocular|gafas protección|gafas fotocurado)/i, a: 'Protección ocular: uso obligatorio para operador y paciente durante procedimientos con luz intensa o aerosoles.' },
    { k: /(guantes|batas|mascarilla|protección personal)/i, a: 'EPP: guantes, mascarilla, bata y gafas; previenen infección cruzada; deben cambiarse entre pacientes.' },
    { k: /(autoclave|esterilización|esterilizador vapor)/i, a: 'Autoclave: esteriliza instrumentos a vapor a 121°C o más; verificar indicadores biológicos y físicos regularmente.' },
    { k: /(paquete estéril|bolsa esterilización|indicador químico)/i, a: 'Paquetes estériles: deben sellarse y marcar fecha; indicadores químicos confirman exposición adecuada al proceso.' },
    { k: /(limpieza ultrasonido|lavadora ultrasónica)/i, a: 'Limpieza ultrasónica: elimina restos antes de esterilizar; usar soluciones específicas y enjuague posterior.' },
    { k: /(radiología|rx dental|radiografía periapical)/i, a: 'Radiología dental: permite diagnóstico de lesiones internas; requiere protección plomada y colimación del haz.' },
    { k: /(colimador|cono plomado|protección radiológica)/i, a: 'Colimador: reduce dispersión de radiación; mejora calidad de imagen y reduce exposición al paciente.' },
    { k: /(película radiográfica|sensor digital|placa fosforada)/i, a: 'Radiografía digital: sustituye película tradicional; menor dosis y procesamiento rápido.' },
    { k: /(procesadora radiográfica|cuarto oscuro|revelado manual)/i, a: 'Procesadora: usada en radiografía convencional para revelar imágenes; evitar contaminaciones químicas.' },
    { k: /(radiografía panorámica|ortopantomografía)/i, a: 'Panorámica: muestra toda la arcada dental y estructuras óseas; útil en planificación quirúrgica o ortodóntica.' },
    { k: /(radiografía cefalométrica|cefalometría)/i, a: 'Cefalométrica: permite analizar relaciones craneofaciales; base en ortodoncia y cirugía ortognática.' },
    { k: /(radiografía bite wing|interproximal)/i, a: 'Bite wing: radiografía interproximal para detección de caries entre dientes y evaluar nivel óseo alveolar.' },
    { k: /(posición operador|posición paciente|ergonomía)/i, a: 'Ergonomía clínica: operador entre 9 y 12 horas (posición reloj); paciente con cabeza al nivel de codo del operador.' },
    { k: /(iluminación dental|luz operatoria|lámpara de unidad)/i, a: 'Iluminación: debe orientarse sin deslumbrar al paciente; luz blanca neutra favorece visión del color dental.' },
    { k: /(unidad dental|sillón dental|equipo dental)/i, a: 'Unidad dental: sistema integrado con silla, compresor, piezas de mano y aspiración; mantenimiento regular evita fallos clínicos.' },
    { k: /(compresor|aire comprimido|sistema neumático)/i, a: 'Compresor: suministra aire a instrumentos; debe tener filtro y drenaje de humedad diario.' },
    { k: /(aspirador quirúrgico|sistema de aspiración|succión dental)/i, a: 'Aspirador: elimina fluidos durante cirugía; debe limpiarse y desinfectarse al final de cada jornada.' },
    { k: /(bandeja clínica|bandeja metálica|bandeja quirúrgica)/i, a: 'Bandeja: superficie de trabajo estéril donde se colocan instrumentos; mantener orden y secuencia lógica.' },
    { k: /(pinza college|pinza dental recta|pinza curva)/i, a: 'Pinza College: sujeta algodones o gasas; su versión curva facilita manipulación intraoral.' },
    { k: /(gasas|rollos algodón|material absorbente)/i, a: 'Gasas y rollos: mantienen campo seco y absorben fluidos; deben ser estériles y cambiados frecuentemente.' },
    { k: /(boca de succión|tubo evacuador|eyector quirúrgico)/i, a: 'Boca de succión: boquilla desechable conectada a sistema de aspiración; previene salpicaduras y contaminación cruzada.' },
    { k: /(batea de acero|batea de goma|recipiente esterilizable)/i, a: 'Batea: recipiente para enjuague o transporte de instrumentos; se esteriliza tras cada uso.' },
    { k: /(algodón|torundas|pellets)/i, a: 'Algodón: material absorbente para aislamiento relativo y aplicación de medicamentos; desechar tras un solo uso.' },
    { k: /(jeringa triple|aire agua spray|pistola triple)/i, a: 'Jeringa triple: expulsa aire, agua o spray para limpieza y secado del campo operatorio; desinfectar boquillas.' },
    { k: /(lupas|magnificación|óptica dental)/i, a: 'Lupas: aumentan precisión visual en procedimientos; mejora postura y ergonomía del operador.' },
    { k: /(retractor labial|retractor mejilla|retractor lingual)/i, a: 'Retractores: separan tejidos blandos para mejorar acceso y visibilidad; usar con cuidado para evitar lesiones.' },
    { k: /(espejo frontal|luz frontal|visor)/i, a: 'Espejo o luz frontal: ayuda en visión en zonas difíciles; especialmente útil en procedimientos quirúrgicos.' },
    { k: /(aspirador de saliva|eyector de saliva|succión baja)/i, a: 'Aspirador de saliva: mantiene campo libre de humedad durante restauraciones o endodoncia.' },
    { k: /(cubeta de impresión|cubeta metálica|cubeta perforada)/i, a: 'Cubeta de impresión: sostiene material de impresión; elegir tamaño adecuado y perforada para retención mecánica.' },
    { k: /(material de impresión|alginato|silicona)/i, a: 'Material de impresión: alginato para modelos de estudio; siliconas para prótesis o restauraciones precisas.' },
    { k: /(espátula alginato|mezclador|cuenco goma)/i, a: 'Espátula y cuenco: mezcla de alginato hasta consistencia homogénea; limpieza inmediata tras su uso.' },
    { k: /(modelo de estudio|vaciado yeso|yeso dental)/i, a: 'Modelos de yeso: reproducción de estructuras orales para diagnóstico y planificación de tratamiento.' },
    { k: /(yeso tipo II|yeso tipo III|yeso tipo IV)/i, a: 'Yesos dentales: tipo II (modelos), tipo III (trabajo), tipo IV (precisión); mezclar agua-polvo según indicación.' },
    { k: /(vibrador yeso|mesa vibradora)/i, a: 'Vibrador: elimina burbujas al vaciar modelos; vibrar suavemente para no alterar detalles.' },
    { k: /(piedra pómez|pomez|piedra para pulir)/i, a: 'Piedra pómez: abrasivo para pulir prótesis o eliminar manchas superficiales; aplicar con agua para evitar calor excesivo.' },
    { k: /(pasta profilaxis|pulido final|pasta abrasiva)/i, a: 'Pasta de profilaxis: mejora brillo y suaviza superficie dental tras limpieza profesional.' },
    { k: /(instrumental básico|bandeja básica|set dental básico)/i, a: 'Instrumental básico: espejo, pinza, explorador y sonda; indispensables en todo examen clínico.' },
    { k: /(barreras de protección|film plástico|protección cruzada)/i, a: 'Barreras: cubren superficies difíciles de desinfectar; reemplazarlas entre pacientes evita contaminación cruzada.' },
    { k: /(desinfección superficies|limpieza consultorio|control infecciones)/i, a: 'Desinfección: aplicar soluciones aprobadas en superficies entre pacientes; control de infecciones es prioridad clínica.' },
    { k: /(residuos biológicos|basura infecciosa|segregación residuos)/i, a: 'Residuos biológicos: deben colocarse en bolsas rojas o amarillas; agujas y bisturís en contenedores rígidos.' },
    { k: /(lavado de manos|higiene de manos|antiséptico|jabón antiséptico)/i, a: 'Lavado de manos: antes y después de cada procedimiento; usar jabón antiséptico y técnica correcta durante 40 segundos.' },
    { k: /(resina compuesta|resina|composite|material estético)/i, a: 'Resina compuesta: material restaurador estético fotopolimerizable; requiere campo seco y adhesión adecuada.' },
    { k: /(ionómero de vidrio|ionomero|glass ionomer)/i, a: 'Ionómero de vidrio: libera flúor y se adhiere químicamente al esmalte y dentina; ideal en restauraciones cervicales y pediatría.' },
    { k: /(amalgama dental|obturación metálica|restauración metálica)/i, a: 'Amalgama: mezcla de mercurio con aleaciones metálicas; duradera y económica, requiere retención mecánica.' },
    { k: /(cemento temporal|hidróxido de calcio|forro protector)/i, a: 'Cemento temporal: protege cavidad provisionalmente; el hidróxido de calcio estimula dentina reparadora.' },
    { k: /(cemento fosfato de zinc|fosfato zinc)/i, a: 'Cemento fosfato de zinc: material de base o cementado; alta resistencia pero pH ácido inicial, usar aislante si hay pulpa cercana.' },
    { k: /(cemento policarboxilato|policarboxilato de zinc)/i, a: 'Cemento policarboxilato: adhesión química al esmalte; menos irritante que el fosfato, útil para cementar coronas metálicas.' },
    { k: /(adhesivo dental|sistema adhesivo|bonding)/i, a: 'Adhesivo: une resina al diente; limpieza, grabado ácido y aplicación por capas garantizan mejor retención.' },
    { k: /(grabado ácido|ácido fosfórico|etching)/i, a: 'Grabado ácido: elimina barrera superficial del esmalte y dentina para mejorar adhesión; usar ácido fosfórico al 35%. ' },
    { k: /(liner|base cavitaria|protector pulpar)/i, a: 'Liner o base: capa delgada entre restauración y dentina; reduce sensibilidad y protege la pulpa de agentes irritantes.' },
    { k: /(matriz tofflemire|banda matriz|porta matriz)/i, a: 'Matriz Tofflemire: sistema metálico que forma pared temporal; esencial en restauraciones clase II.' },
    { k: /(cuñas de madera|cuña interdental|cuña plástica)/i, a: 'Cuñas: sellan el margen gingival y separan dientes durante restauraciones; mejoran punto de contacto proximal.' },
    { k: /(instrumento de tallado|carver|hollenback)/i, a: 'Carver: modela y da forma a restauraciones metálicas; remover excesos y definir contornos anatómicos.' },
    { k: /(pulido|acabado restauración|disco de pulido)/i, a: 'Pulido: reduce rugosidad y mejora brillo en restauraciones; usar discos, cepillos o puntas de goma con pasta fina.' },
    { k: /(microfiltrado|filtración marginal|sellado marginal)/i, a: 'Microfiltrado: ingreso de fluidos entre diente y restauración; prevenir con buena adhesión y control de humedad.' },
    { k: /(fractura dental|fractura coronaria|tratamiento fractura)/i, a: 'Fractura dental: depende de extensión; restauración directa, poste o corona según daño estructural.' },
    { k: /(caries recurrente|caries secundaria|margen defectuoso)/i, a: 'Caries recurrente: aparece junto a restauraciones por sellado deficiente; control con exploración y radiografía.' },
    { k: /(desgaste dental|abrasión|erosión|attrición)/i, a: 'Desgaste dental: pérdida de esmalte por fricción o ácidos; manejo con férulas y control dietético.' },
    { k: /(sellado de fisuras|selladores|profilaxis previa)/i, a: 'Selladores de fisuras: barrera plástica protectora en molares jóvenes; aplicar tras limpieza y secado.' },
    { k: /(fotopolimerización|curado|resina fotoactivada)/i, a: 'Fotopolimerización: endurecimiento de resina con luz azul; respetar tiempo e intensidad adecuados.' },
    { k: /(composite híbrido|microhíbrido|nanohíbrido)/i, a: 'Resinas híbridas: combinan resistencia y estética; recomendadas en sectores anteriores y posteriores.' },
    { k: /(cemento de resina|resin cement|cementado adhesivo)/i, a: 'Cementos de resina: alta adhesión y estética; usados en carillas, incrustaciones y coronas cerámicas.' },
    { k: /(ionómero modificado con resina|ionómero híbrido)/i, a: 'Ionómero modificado: combina liberación de flúor y mayor resistencia; útil en restauraciones pediátricas y bases cavitarias.' },
    { k: /(resinas fluidas|flow|flowable)/i, a: 'Resinas fluidas: baja viscosidad, ideales en cavidades pequeñas o sellado de fisuras finas.' },
    { k: /(resinas bulk fill|alta viscosidad|incremento único)/i, a: 'Resinas bulk-fill: permiten relleno en capas gruesas con baja contracción; útil en cavidades profundas.' },
    { k: /(postes de fibra|poste colado|reconstrucción endodoncia)/i, a: 'Postes: refuerzan estructura dental endodonciada; fibra de vidrio por su estética y adhesión.' },
    { k: /(endodoncia|tratamiento de conducto|conducto radicular)/i, a: 'Endodoncia: elimina pulpa infectada, limpia y sella conductos para conservar la pieza dental.' },
    { k: /(limas k|limas h|instrumental endodoncia)/i, a: 'Limas endodónticas: K (flexibles) y H (agresivas); se usan para instrumentar y limpiar conductos radiculares.' },
    { k: /(irrigación endodoncia|hipoclorito|edta)/i, a: 'Irrigación: limpieza del conducto con hipoclorito de sodio y EDTA; elimina residuos y desinfecta.' },
    { k: /(obturación conductos|gutapercha|sellador endodóntico)/i, a: 'Obturación: relleno hermético del conducto con gutapercha y sellador; evita reinfecciones.' },
    { k: /(localizador apical|medición conducto|longitud de trabajo)/i, a: 'Localizador apical: mide longitud exacta del conducto para instrumentar y obturar correctamente.' },
    { k: /(pulpitis|necrosis pulpar|dolor pulpar)/i, a: 'Pulpitis: inflamación pulpar reversible o irreversible; diagnóstico clínico y radiográfico guían tratamiento.' },
    { k: /(retracción gingival|cordón retráctil)/i, a: 'Retracción gingival: separación temporal del margen gingival para impresiones o restauraciones; usar cordón impregnado.' },
    { k: /(prótesis total|dentadura completa|reposición total)/i, a: 'Prótesis total: reemplaza todos los dientes; requiere buena retención, estabilidad y ajuste oclusal.' },
    { k: /(prótesis parcial removible|parcial acrílica|estructura metálica)/i, a: 'Prótesis parcial removible: sustituye piezas faltantes con base acrílica o metálica; ajusta con retenedores y apoyos.' },
    { k: /(corona metal porcelana|corona cerámica|corona total)/i, a: 'Coronas: restauraciones completas; metal-porcelana por durabilidad, cerámica por estética en sector anterior.' },
    { k: /(incrustación|onlay|inlay|restauración indirecta)/i, a: 'Incrustaciones: restauraciones parciales de laboratorio en resina, cerámica o metal; cementadas adhesivamente.' },
    { k: /(carilla|laminado|veneers)/i, a: 'Carillas: láminas finas de cerámica o resina que cubren la cara frontal del diente para mejorar estética.' },
    { k: /(ajuste oclusal|mordida alta|contacto prematuro)/i, a: 'Ajuste oclusal: corrige interferencias o contactos prematuros que causan dolor o desgaste; esencial tras restauraciones grandes.' },
    { k: /(prótesis inmediata|postexodoncia)/i, a: 'Prótesis inmediata: colocada tras extracciones para mantener estética y función mientras cicatriza el hueso.' },
    { k: /(rebase prótesis|relineado|ajuste base)/i, a: 'Rebase: adaptación de prótesis a cambios del reborde alveolar; se hace con materiales acrílicos blandos o duros.' },
    { k: /(oclusión|relación céntrica|guía anterior)/i, a: 'Oclusión: relación funcional entre dientes superiores e inferiores; base para restauraciones equilibradas.' },
    { k: /(articulador|montaje en articulador|relación intermaxilar)/i, a: 'Articulador: simula movimientos mandibulares; facilita confección precisa de prótesis.' },
    { k: /(impresión|material de impresión|silicona de adición)/i, a: 'Impresión: registro de tejidos orales con alginato o silicona; debe ser exacta para buena adaptación protésica.' },
    { k: /(acrílico autopolimerizable|acrílico termocurable)/i, a: 'Acrílico: material base de prótesis; autopolimerizable para reparaciones, termocurable para mayor resistencia.' },
    { k: /(cerámica dental|porcelana|cerámica feldespática)/i, a: 'Cerámica: material estético, biocompatible y duradero; requiere técnicas de cementado adhesivo.' },
    { k: /(aleaciones metálicas|cromo cobalto|niquel cromo)/i, a: 'Aleaciones metálicas: base de estructuras protésicas; el cromo-cobalto es resistente a la corrosión.' },
    { k: /(periodoncia|tratamiento periodontal|bolsas periodontales)/i, a: 'Periodoncia: trata enfermedades de encías y soporte óseo; incluye raspado, cirugía y mantenimiento.' },
    { k: /(sondaje periodontal|profundidad de bolsa)/i, a: 'Sondaje: mide profundidad de bolsa gingival; permite diagnóstico de gingivitis o periodontitis.' },
    { k: /(raspado y alisado|tratamiento básico periodontal)/i, a: 'Raspado y alisado radicular: elimina cálculo subgingival y alisa raíces para mejorar cicatrización gingival.' },
    { k: /(cirugía periodontal|colgajo|injerto de encía)/i, a: 'Cirugía periodontal: procedimientos para acceso o regeneración; colgajos e injertos mejoran soporte y estética.' },
    { k: /(mantenimiento periodontal|control periódico)/i, a: 'Mantenimiento periodontal: visitas periódicas tras terapia inicial; evita recidiva de la enfermedad.' },
    { k: /(pérdida ósea|defecto óseo|regeneración ósea guiada)/i, a: 'Pérdida ósea: consecuencia de periodontitis; regeneración ósea guiada usa membranas y biomateriales.' },
    { k: /(biomateriales|injertos óseos|membranas reabsorbibles)/i, a: 'Biomateriales: favorecen regeneración ósea o tisular; incluyen injertos autólogos, aloplásticos y membranas reabsorbibles.' },
    { k: /(placa bacteriana|biofilm|control de placa)/i, a: 'Placa bacteriana: principal causa de caries y enfermedad periodontal; eliminar mediante cepillado mecánico diario.' },
    { k: /(cepillo interdental|hilo dental|limpiador interproximal)/i, a: 'Cepillo interdental: limpia espacios donde no llega el cepillo convencional; complemento esencial del hilo dental.' },
    { k: /(colutorio|enjuague bucal|antiséptico oral)/i, a: 'Colutorio: reduce placa y halitosis; los de clorhexidina se usan por periodos cortos bajo supervisión.' },
    { k: /(profilaxis periodontal|limpieza profunda)/i, a: 'Profilaxis periodontal: elimina placa y cálculo; esencial para prevenir gingivitis y periodontitis.' },
    { k: /(recesión gingival|retracción encías|encía retraída)/i, a: 'Recesión gingival: exposición radicular por cepillado agresivo o periodontitis; puede requerir injerto gingival.' },
    { k: /(higiene oral|educación higiene|control domiciliario)/i, a: 'Higiene oral: pilar fundamental de prevención; cepillado, hilo dental y control profesional regular.' },
    { k: /(gingivoplastia|gingivectomía|cirugía de encías)/i, a: 'Gingivoplastia: remodela encía por estética o salud; gingivectomía elimina tejido inflamado o bolsas falsas.' },
    { k: /(alargamiento coronario|cirugía ósea resectiva)/i, a: 'Alargamiento coronario: expone más estructura dental para restauración o estética; requiere control periodontal.' },
    { k: /(oclusión traumática|trauma oclusal|fuerzas excesivas)/i, a: 'Trauma oclusal: fuerzas excesivas sobre dientes o periodonto; causa movilidad o pérdida ósea localizada.' },
    { k: /(movilidad dental|grado movilidad|tratamiento movilidad)/i, a: 'Movilidad dental: se evalúa por grados; puede ser reversible si se controla la causa periodontal o oclusal.' },
    { k: /(ferulización|férula periodontal|unión de dientes móviles)/i, a: 'Ferulización: une dientes móviles mediante resina o alambre; estabiliza durante tratamiento periodontal.' },
    { k: /(injerto óseo|injerto gingival|regeneración tisular)/i, a: 'Injerto: técnica para recuperar tejido óseo o gingival perdido; requiere asepsia y control postoperatorio.' },
    { k: /(exudado gingival|líquido crevicular|inflamación encía)/i, a: 'Exudado gingival: fluido inflamatorio; su aumento indica gingivitis o periodontitis activa.' },
    { k: /(placa subgingival|biofilm profundo|raspado subgingival)/i, a: 'Placa subgingival: responsable de enfermedad periodontal avanzada; requiere eliminación profesional.' },
    { k: /(periodontograma|registro periodontal|gráfico periodontal)/i, a: 'Periodontograma: registro visual de sondaje y pérdida ósea; guía planificación del tratamiento.' },
    { k: /(control de placa química|clorhexidina|triclosán|fluoruro estanoso)/i, a: 'Control químico: colutorios con clorhexidina o fluoruro estanoso complementan higiene mecánica.' },
    { k: /(curetaje cerrado|tratamiento no quirúrgico|terapia básica)/i, a: 'Curetaje cerrado: eliminación de cálculo sin levantar colgajo; primera fase del tratamiento periodontal.' },
    { k: /(aliento|mal olor|halitosis periodontal)/i, a: 'Halitosis periodontal: causada por placa subgingival y compuestos sulfurosos; control con higiene y terapia periodontal.' },
    { k: /(control de placa profesional|profilaxis control|educación paciente)/i, a: 'Control profesional: refuerzo educativo y profilaxis cada 3–6 meses según riesgo individual.' },
    { k: /(tinciones dentales|manchas extrínsecas|pigmentación dental)/i, a: 'Tinciones: pigmentos superficiales por tabaco o café; eliminar con profilaxis y pulido.' },
    { k: /(abrasión cervical|lesión no cariosa|abfracción)/i, a: 'Lesiones no cariosas: desgaste mecánico o estrés; restaurar con resina y modificar técnica de cepillado.' },
    { k: /(fluorosis|manchas blancas|exceso de flúor)/i, a: 'Fluorosis: alteración por exceso de flúor durante formación dental; leves son estéticas, severas requieren microabrasión o carillas.' },
    { k: /(enfermedad periodontal avanzada|bolsas profundas|pérdida severa)/i, a: 'Periodontitis avanzada: pérdida ósea y movilidad; requiere cirugía y mantenimiento intensivo.' },
    { k: /(periimplantitis|mucositis periimplantaria|implante infectado)/i, a: 'Periimplantitis: inflamación del tejido que rodea implantes; control de placa y cirugía son esenciales.' },
    { k: /(osteointegración|implante dental|implante de titanio)/i, a: 'Implante dental: estructura de titanio que sustituye raíz dental; osteointegración garantiza estabilidad.' },
    { k: /(pilar protésico|conexión implante|pilar de cicatrización)/i, a: 'Pilar protésico: elemento que conecta implante y corona; debe ajustarse sin generar tensión.' },
    { k: /(torquímetro|atornillado|par de apriete)/i, a: 'Torquímetro: instrumento que controla la fuerza de apriete de tornillos protésicos para evitar fracturas o aflojamiento.' },
    { k: /(mantenimiento de implantes|higiene de implantes|control implantes)/i, a: 'Mantenimiento de implantes: limpieza cuidadosa con instrumentos de teflón y visitas periódicas.' },
    { k: /(cemento quirúrgico|curativo periodontal|protector)/i, a: 'Cemento quirúrgico: cubre zonas intervenidas para protección y confort postoperatorio.' },
    { k: /(exodoncia|extracción dental|extracción de muela)/i, a: 'Exodoncia: extracción controlada de un diente; requiere anestesia adecuada, técnica atraumática y control postoperatorio.' },
    { k: /(luxador|elevador recto|botador|elevadores de Winter)/i, a: 'Elevadores: separan y movilizan el diente del alvéolo antes de la extracción; usar con cuidado para evitar fracturas.' },
    { k: /(forceps|fórceps|pinzas de extracción)/i, a: 'Fórceps: instrumento para sujetar y extraer dientes; diferentes modelos para cada grupo dental.' },
    { k: /(alveolitis|alveolo seco|complicación post extracción)/i, a: 'Alveolitis: inflamación dolorosa tras extracción por pérdida de coágulo; tratar con limpieza y apósitos medicados.' },
    { k: /(hemostasia|coágulo|sangrado post extracción)/i, a: 'Hemostasia: detener sangrado postoperatorio con presión, sutura o agentes hemostáticos locales.' },
    { k: /(suturas|sutura reabsorbible|sutura seda|nudos quirúrgicos)/i, a: 'Suturas: mantienen bordes de herida unidos; seda para uso general, reabsorbibles para tejidos internos.' },
    { k: /(anestesia troncular alveolar inferior|bloqueo mandibular)/i, a: 'Bloqueo alveolar inferior: anestesia del hemimaxilar inferior; referencia: escotadura sigmoidea y pterigomandibular.' },
    { k: /(biopsia oral|lesión sospechosa|muestra tisular)/i, a: 'Biopsia: toma de tejido para diagnóstico histopatológico; esencial en lesiones persistentes o de origen incierto.' },
    { k: /(drenaje absceso|absceso dental|incisión drenaje)/i, a: 'Drenaje de absceso: libera pus y reduce presión; combinar con antibióticos y tratamiento causal.' },
    { k: /(antibióticos dentales|amoxicilina|clindamicina)/i, a: 'Antibióticos: usados en infecciones agudas o cirugía; elección según paciente y sensibilidad bacteriana.' },
    { k: /(analgésicos|ibuprofeno|paracetamol|dolor postoperatorio)/i, a: 'Analgésicos: controlan dolor tras procedimientos; ibuprofeno es primera elección salvo contraindicaciones.' },
    { k: /(control postoperatorio|cuidados después de extracción)/i, a: 'Cuidados postoperatorios: no enjuagar, no fumar, dieta blanda y aplicar frío local las primeras 24h.' },
    { k: /(quistes maxilares|quiste dentígero|quiste radicular)/i, a: 'Quistes maxilares: cavidades patológicas con contenido líquido; requieren diagnóstico radiográfico y extirpación quirúrgica.' },
    { k: /(fractura mandibular|fractura maxilar|traumatismo facial)/i, a: 'Fracturas faciales: pueden afectar función y estética; tratamiento con reducción y fijación adecuada.' },
    { k: /(cirugía de colgajo|colgajo mucoperióstico|levantamiento de colgajo)/i, a: 'Colgajo: permite acceso a zonas profundas del hueso; conservar irrigación y reposicionar con sutura.' },
    { k: /(injerto óseo|biomateriales quirúrgicos|regeneración guiada)/i, a: 'Injerto óseo: rellena defectos óseos; puede ser autógeno o sintético, favorece regeneración tisular.' },
    { k: /(osteotomía|osteoplastia|cirugía ósea)/i, a: 'Osteotomía: corte controlado del hueso para extraer raíces o remodelar rebordes alveolares.' },
    { k: /(implante dental|tornillo titanio|osteointegración)/i, a: 'Implante dental: tornillo de titanio integrado al hueso que sirve de base para una prótesis fija.' },
    { k: /(cirugía periapical|apicectomía|resección apical)/i, a: 'Apicectomía: extirpación del ápice radicular y tejido inflamado; indicada tras fracaso endodóntico.' },
    { k: /(odontosección|separación radicular|extracción quirúrgica)/i, a: 'Odontosección: división del diente multirradicular para extracción controlada y mínima pérdida ósea.' },
    { k: /(ortodoncia|brackets|alineadores)/i, a: 'Ortodoncia: corrige maloclusiones con brackets o alineadores; mejora función y estética dental.' },
    { k: /(arco ortodóntico|ligadura|elástico)/i, a: 'Arco ortodóntico: alambre que genera fuerzas sobre dientes; se fija con ligaduras metálicas o elásticas.' },
    { k: /(brackets metálicos|brackets cerámicos|brackets autoligables)/i, a: 'Brackets: elementos adheridos al diente; los metálicos son más resistentes, los cerámicos más estéticos.' },
    { k: /(alineadores invisibles|ortodoncia estética|alineadores)/i, a: 'Alineadores: férulas transparentes removibles que mueven gradualmente los dientes; cómodos y discretos.' },
    { k: /(retención ortodóntica|retenedor|ferula post ortodoncia)/i, a: 'Retención: mantiene dientes en posición tras ortodoncia; puede ser removible o fija según caso.' },
    { k: /(expansor palatino|disyuntor|ortodoncia interceptiva)/i, a: 'Expansor palatino: separa sutura media del paladar para corregir mordidas cruzadas; uso en crecimiento.' },
    { k: /(maloclusión|clase I|clase II|clase III)/i, a: 'Maloclusiones: alteraciones en relación de arcadas; clase II retrognatismo, clase III prognatismo.' },
    { k: /(apiñamiento dental|espacios|alineación)/i, a: 'Apiñamiento: falta de espacio para la erupción correcta; se corrige con ortodoncia o extracción selectiva.' },
    { k: /(diastema|espacio interdental|separación dental)/i, a: 'Diastema: espacio entre dientes; puede cerrarse con ortodoncia o restauraciones estéticas.' },
    { k: /(mordida abierta|mordida cruzada|mordida profunda)/i, a: 'Mordidas: alteraciones verticales u horizontales; requieren diagnóstico cefalométrico y tratamiento ortodóntico personalizado.' },
    { k: /(ortodoncia fija|ortodoncia removible)/i, a: 'Ortodoncia fija usa brackets y alambres; la removible emplea aparatos acrílicos para correcciones simples.' },
    { k: /(hábitos orales|succión digital|deglución infantil)/i, a: 'Hábitos orales: succión digital, respiración bucal o deglución infantil alteran desarrollo maxilar; requieren corrección temprana.' },
    { k: /(odontopediatría|odontología infantil|niños)/i, a: 'Odontopediatría: atención dental en niños; énfasis en prevención, educación y manejo del comportamiento.' },
    { k: /(fluorosis infantil|caries rampante|caries del biberón)/i, a: 'Caries del biberón: aparece por exposición prolongada a líquidos azucarados; prevención con higiene desde erupción del primer diente.' },
    { k: /(selladores pediátricos|sellador de fisuras infantil)/i, a: 'Selladores pediátricos: protegen molares recién erupcionados; aplicación clínica con aislamiento absoluto.' },
    { k: /(trauma dental infantil|fractura diente temporal|avulsión niño)/i, a: 'Trauma infantil: reimplantar dientes permanentes avulsionados si es posible; dientes temporales no se reimplantan.' },
    { k: /(pulpotomía|pulpotomía en molares temporales|tratamiento pulpar infantil)/i, a: 'Pulpotomía: elimina pulpa coronal afectada preservando la radicular; común en molares temporales con caries extensa.' },
    { k: /(pulpectomía|tratamiento de conductos en temporales)/i, a: 'Pulpectomía: eliminación completa de pulpa en dientes temporales; se obtura con pasta reabsorbible.' },
    { k: /(fluoración infantil|flúor tópico niños)/i, a: 'Flúor tópico infantil: aplicación profesional de barniz o gel; prevención eficaz de caries en dentición mixta.' },
    { k: /(comportamiento infantil|manejo de conducta|técnicas psicológicas)/i, a: 'Manejo de conducta: técnicas como decir-mostrar-hacer y refuerzo positivo facilitan cooperación infantil.' },
    { k: /(educación dental infantil|enseñanza higiene niños)/i, a: 'Educación dental: enseñar cepillado y dieta saludable desde edad temprana; refuerzo en escuela y hogar.' },
    { k: /(odontología preventiva infantil|control de caries niños)/i, a: 'Prevención infantil: visitas periódicas, selladores y control dietético; base de salud bucodental futura.' },
    { k: /(odontología para discapacitados|paciente especial|manejo especial)/i, a: 'Paciente con discapacidad: adaptar técnicas y tiempos; posible uso de anestesia general o apoyo multidisciplinario.' },
    { k: /(anestesia general odontológica|sedación consciente|óxido nitroso)/i, a: 'Sedación consciente: óxido nitroso reduce ansiedad sin pérdida de conciencia; segura bajo supervisión profesional.' },
    { k: /(bioseguridad|precauciones universales|control de infecciones)/i, a: 'Bioseguridad: medidas que previenen transmisión de enfermedades; guantes, mascarillas y esterilización rigurosa.' },
    { k: /(esterilización|autoclave|calor seco|instrumental estéril)/i, a: 'Esterilización: proceso que elimina microorganismos; preferir autoclave, verificar indicadores biológicos.' },
    { k: /(desinfección|superficies|antisépticos)/i, a: 'Desinfección: limpieza química de superficies; usar hipoclorito, alcohol o compuestos cuaternarios.' },
    { k: /(lavado de manos clínico|antiséptico|manos del operador)/i, a: 'Lavado clínico: antes y después de cada paciente; jabón antiséptico y secado con toalla desechable.' },
    { k: /(control de aerosoles|barreras plásticas|protección facial)/i, a: 'Control de aerosoles: usar succión de alto volumen, barreras y mascarilla N95 en procedimientos generadores.' },
    { k: /(residuos hospitalarios|bioresiduos|segregación de residuos)/i, a: 'Residuos hospitalarios: clasificar por color y tipo; punzocortantes en contenedores rígidos, orgánicos en bolsas rojas.' },
    { k: /(vacunación del personal|hepatitis b|inmunización odontólogo)/i, a: 'Vacunación: todo profesional debe inmunizarse contra hepatitis B, tétanos e influenza.' },
    { k: /(accidente biológico|punción accidental|exposición a sangre)/i, a: 'Accidente biológico: lavar con agua y jabón, reportar y seguir protocolo post exposición.' },
    { k: /(ventilación consultorio|control ambiental|aire limpio)/i, a: 'Ventilación: mantener flujo de aire y filtros adecuados; evita acumulación de aerosoles contaminantes.' },
    { k: /(gestión de residuos|manejo ambiental|reciclaje clínico)/i, a: 'Gestión ambiental: reducción y reciclaje de materiales cuando sea posible bajo normas de bioseguridad.' },
    { k: /(instrumental estéril|paquete quirúrgico|bolsa esterilización)/i, a: 'Instrumental estéril: debe mantenerse cerrado hasta el momento de uso; revisar indicadores químicos.' },
    { k: /(control cruzado|contaminación cruzada|bioseguridad clínica)/i, a: 'Prevención cruzada: uso correcto de EPP, barreras desechables y limpieza entre pacientes.' },
    { k: /(historia clínica|ficha dental|anamnesis)/i, a: 'Historia clínica: documento legal y diagnóstico; incluye antecedentes, examen y plan de tratamiento.' },
    { k: /(consentimiento informado|autorización del paciente)/i, a: 'Consentimiento informado: documento donde el paciente acepta tratamiento tras recibir información clara.' },
    { k: /(urgencias dentales|tratamiento de urgencia|emergencia oral)/i, a: 'Urgencias dentales: control de dolor, hemorragias o infecciones; priorizar estabilización del paciente.' },
    { k: /(hemorragia postoperatoria|sangrado|hemostático local)/i, a: 'Hemorragia postoperatoria: aplicar presión, sutura o agentes hemostáticos como esponjas de colágeno.' },
    { k: /(fractura coronaria|reimplante dental|avulsión)/i, a: 'Avulsión: reimplantar diente permanente de inmediato o conservar en leche; acudir urgente al odontólogo.' },
    { k: /(quemadura química|accidente clínico|irritación mucosa)/i, a: 'Quemaduras químicas: enjuagar con agua y aplicar tratamiento sintomático; registrar el incidente.' },
    { k: /(desinfección instrumental|prelavado|autoclave)/i, a: 'Desinfección instrumental: prelavado, ultrasonido y esterilización; controlar indicadores biológicos.' },
    { k: /(odontología digital|escáner intraoral|cad cam)/i, a: 'Odontología digital: escaneo 3D, diseño y fresado asistido por computadora (CAD/CAM) para restauraciones precisas.' },
    { k: /(radiología digital|sensor intraoral|cbct|tomografía cone beam)/i, a: 'CBCT: tomografía de haz cónico; proporciona imágenes 3D para diagnóstico endodóntico y quirúrgico.' },
    { k: /(láser dental|terapia láser|cirugía láser)/i, a: 'Láser dental: corta y coagula tejidos con mínima invasión; reduce dolor y mejora cicatrización.' },
    { k: /(ozonoterapia|ozono dental|desinfección con ozono)/i, a: 'Ozono dental: agente antimicrobiano usado en caries incipientes y periodoncia; complementario, no sustitutivo.' },
    { k: /(endodoncia rotatoria|motor endodóntico|limas de níquel titanio)/i, a: 'Endodoncia rotatoria: uso de limas NiTi motorizadas; reduce tiempo y mejora conformación del conducto.' },
    { k: /(microscopio dental|magnificación quirúrgica|endodoncia microscópica)/i, a: 'Microscopio: mejora precisión y visibilidad en endodoncia y microcirugía periodontal.' },
    { k: /(fotografía intraoral|documentación clínica|cámara dental)/i, a: 'Fotografía intraoral: registra casos clínicos y evolución; herramienta educativa y legal.' },
    { k: /(educación continua|actualización profesional|formación odontológica)/i, a: 'Educación continua: mantiene competencia y actualización científica del profesional odontólogo.' },
    { k: /(ética profesional|responsabilidad legal|juramento hipocrático)/i, a: 'Ética profesional: actuar con integridad, respeto y confidencialidad hacia el paciente y colegas.' },
    { k: /(salud ocupacional|ergonomía laboral|postura dental)/i, a: 'Salud ocupacional: prevenir lesiones musculoesqueléticas mediante pausas activas y buena ergonomía.' },
    { k: /(estres laboral|síndrome burnout|fatiga profesional)/i, a: 'Estrés ocupacional: frecuente en odontología; prevenir con pausas, ejercicios y apoyo emocional.' },
    { k: /(comunicación con el paciente|empatía|trato humano)/i, a: 'Comunicación: base de confianza con el paciente; escuchar, informar y resolver dudas mejora adherencia al tratamiento.' },
    { k: /(control de calidad|evaluación clínica|auditoría interna)/i, a: 'Control de calidad: asegura cumplimiento de normas clínicas, bioseguridad y satisfacción del paciente.' },
    { k: /(emergencia médica|paro cardiorrespiratorio|rcp)/i, a: 'Emergencia médica: aplicar RCP básica y activar sistema de emergencias; todo consultorio debe estar preparado.' },
    { k: /(botiquin dental|equipo de emergencia|kit de urgencia)/i, a: 'Botiquín dental: debe incluir oxígeno, antihistamínicos, epinefrina y material de primeros auxilios.' },
    { k: /(manejo de ansiedad|paciente nervioso|relajación)/i, a: 'Manejo de ansiedad: técnicas de respiración, distracción o sedación ligera mejoran experiencia del paciente.' },
    { k: /(calidad en odontología|seguridad del paciente)/i, a: 'Calidad en odontología: enfoque en seguridad, eficacia y satisfacción; requiere protocolos estandarizados.' },
    { k: /(educación para la salud|prevención integral|promoción bucal)/i, a: 'Prevención integral: combina educación, control de placa, flúor y revisiones periódicas.' }
  ];
  function localAnswer(q){ const s=(q||'').toString().toLowerCase(); for(const it of QA){ if(it.k.test(s)) return it.a; } return ''; }
  function renderRandomSuggests(n=12){
    if (!suggests) return;
    suggests.innerHTML = '';
    const base = (TOPIC_POOL.length?TOPIC_POOL:['Caries dental','Gingivitis','FDI','Cepillado dental','Índices CPO-D','Radiología preventiva','Ortodoncia interceptiva','Selladores de fosas y fisuras','Bruxismo','Hipersensibilidad']).slice();
    const picks = shuffle(base);
    const used = new Set();
    for (const label of picks) {
      const norm = String(label).trim().toLowerCase();
      if (!norm || used.has(norm)) continue;
      used.add(norm);
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
      if (used.size >= n) break;
    }
  }
  // Cargar muchos temas desde backend
  fetch('{{ route('bot.topics') }}').then(r=>r.ok?r.json():null).then(j=>{ if(j&&Array.isArray(j.topics)) { TOPIC_POOL = j.topics; renderRandomSuggests(); } }).catch(()=>{});
  // Greeting aleatorio único
  if (greet) { const g = GREETINGS[Math.floor(Math.random()*GREETINGS.length)]; greet.textContent = g; }
  function open(){ panel.style.display='block'; panel.setAttribute('aria-hidden','false'); toggle.setAttribute('aria-expanded','true'); setTimeout(()=>input.focus(),100); }
  function close(){ panel.style.display='none'; panel.setAttribute('aria-hidden','true'); toggle.setAttribute('aria-expanded','false'); }
  toggle.addEventListener('click', ()=>{ const show = panel.style.display!=='block'; if (show) { renderRandomSuggests(); } show?open():close(); });
  closeBtn.addEventListener('click', close);
  form.addEventListener('submit', (e)=>{ e.preventDefault(); const t = input.value.trim(); if(!t) return; sendUser(t); input.value=''; reply(t); });
  // inicial
  renderRandomSuggests();
  let lastBotText = '';
  function addMsg(text, cls){
    if (cls==='bf-bot') {
      const norm = String(text||'').trim();
      if (!norm) return;
      if (norm === lastBotText) return; // avoid duplicate bot message
      lastBotText = norm;
    }
    const d=document.createElement('div'); d.className='bf-msg '+cls; d.textContent=text; box.appendChild(d); box.scrollTop=box.scrollHeight;
  }
  let lastUserSent = { text:'', at:0 };
  function sendUser(t){
    const now = Date.now();
    const norm = String(t||'').trim();
    if (norm && norm===lastUserSent.text && (now - lastUserSent.at) < 700) return; // debounce rapid duplicates
    lastUserSent = { text:norm, at:now };
    addMsg(t, 'bf-user');
  }
  async function reply(t){
    try {
      const la = localAnswer(t);
      if (la) addMsg(la, 'bf-bot');
      const url = '{{ route('bot.search') }}' + '?q=' + encodeURIComponent(t);
      const r = await fetch(url);
      if (!r.ok) throw new Error('search_failed');
      const data = await r.json();
      let items = Array.isArray(data.results) ? data.results.slice() : [];
      if (!items.length) { addMsg('formule su pregunta de otra manera', 'bf-bot'); return; }
      // dedupe by title+snippet y tomar solo 1 aleatorio
      items = uniqueBy(items, it => (String(it.title||'').toLowerCase()+'|'+String(it.snippet||it.description||'').toLowerCase()));
      const pick = shuffle(items)[0];
      const msg = pick ? ((pick.snippet||'').trim() || (pick.description||'').trim()) : '';
      addMsg(msg || 'formule su pregunta de otra manera', 'bf-bot');
      box.scrollTop = box.scrollHeight;
    } catch(e) {
      addMsg('formule su pregunta de otra manera', 'bf-bot');
    }
  }
  function answer(q){ return ''; }
})();
</script>
