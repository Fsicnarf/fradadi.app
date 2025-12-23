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
    <form id="bf-form" style="display:flex; gap:6px; padding:10px; background:#f8fafc; border-top:1px solid #e5e7eb;" novalidate action="javascript:void(0);">
      <input id="bf-input" type="text" placeholder="Escribe tu pregunta…" aria-label="Mensaje" style="flex:1; border:1px solid #e2e8f0; border-radius:10px; padding:10px;" />
      <button id="bf-send" class="bf-send" type="button" onclick="return (window.bfSend?window.bfSend():false)" style="background:#1d4ed8; color:white; border:1px solid #93c5fd; border-radius:10px; padding:10px 12px; font-weight:800; cursor:pointer;">Enviar</button>
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
  const sendBtn = (function(){ try { return document.getElementById('bf-send') || document.querySelector('#bf-form .bf-send'); } catch(_) { return null; } })();
  const box = document.getElementById('bf-messages');
  const suggests = document.getElementById('bf-suggests');
  const greet = document.getElementById('bf-greet');
  const CSRF = '{{ csrf_token() }}';
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
    { k: /(diagnostico diferencial|diagnóstico diferencial)/i, a: 'Diagnóstico diferencial: comparar signos y síntomas con entidades similares usando historia, examen y pruebas complementarias.' },
    { k: /(ortodoncia interceptiva|mantenedor de espacio|habitos|hábitos)/i, a: 'Ortodoncia interceptiva: intervenciones tempranas para guiar crecimiento, controlar hábitos y mantener espacios.' },
    { k: /(halitosis)/i, a: 'Halitosis: suele relacionarse con placa lingual, encías o caries. Manejo: higiene de lengua, control de placa y evaluar causas sistémicas.' },
    { k: /(hipersensibilidad|sensibilidad) dent/i, a: 'Hipersensibilidad dentinaria: dolor breve ante frío/calor por exposición dentinaria. Manejo: desensibilizantes y control de hábitos erosivos.' },
    { k: /(trauma|traumatismo) dent/i, a: 'Trauma dentoalveolar: conservar fragmentos, reimplantar avulsiones si es posible y acudir urgente. Radiografías y control pulpar.' },
    { k: /(nutrici|dieta|azucar|azúcar)/i, a: 'Reducir frecuencia de azúcares fermentables entre comidas; preferir agua y xilitol en chicles para disminuir riesgo de caries.' },
    { k: /(caries|c1|c2|c3)/i, a: 'Caries: daño del diente causado por bacterias. C1 afecta el esmalte, C2 llega a la dentina y C3 presenta cavidad visible.' },
    { k: /(higiene oral|cepillado|cepillo dental|pasta dental|hilo dental)/i, a: 'Higiene oral: cepillarse tres veces al día, usar hilo dental y pasta con flúor para prevenir caries y enfermedades de encías.' },
    { k: /(fluor|fluoruro|fluoracion)/i, a: 'El flúor fortalece el esmalte y previene la caries. Se aplica en pastas, enjuagues o barnices dentales bajo supervisión.' },
    { k: /(sellador|selladores de fosas|fisuras)/i, a: 'Selladores de fosas y fisuras: capa protectora en molares jóvenes para evitar la acumulación de bacterias y caries.' },
    { k: /(gingivitis|inflamacion de encias)/i, a: 'Gingivitis: inflamación y sangrado de las encías causada por placa bacteriana. Se revierte con buena higiene y limpieza profesional.' },
    { k: /(periodontitis|bolsa periodontal|piorrea)/i, a: 'Periodontitis: infección profunda que destruye hueso y encías. Requiere tratamiento profesional y control de placa.' },
    { k: /(placa bacteriana|biofilm|sarro)/i, a: 'Placa bacteriana: capa pegajosa que se forma sobre los dientes. Si no se elimina, se endurece formando sarro.' },
    { k: /(profilaxis|limpieza dental)/i, a: 'Profilaxis: limpieza profesional que elimina sarro y manchas, manteniendo las encías sanas.' },
    { k: /(halitosis|mal aliento)/i, a: 'Halitosis: mal olor bucal causado por placa, encías inflamadas o lengua sucia. Se controla con buena higiene y revisión dental.' },
    { k: /(nutricion|dieta|azucar)/i, a: 'La dieta influye en la salud bucal. Reducir el consumo de azúcares y alimentos pegajosos disminuye el riesgo de caries.' },
    { k: /(indice cpo|ceo|ceod|cpod)/i, a: 'Índices CPO-D y CEO-D: miden dientes cariados, perdidos y obturados, útiles para conocer la experiencia de caries.' },
    { k: /(indice higiene|ohi|silness|loe)/i, a: 'Índices de higiene: evalúan cantidad de placa y cálculo en dientes, importantes para programas preventivos.' },
    { k: /(educacion para la salud|promocion de la salud|prevencion)/i, a: 'Educación para la salud: enseña hábitos de higiene y alimentación para mantener una boca sana.' },
    { k: /(control de placa|placa bacteriana)/i, a: 'Control de placa: eliminación mecánica diaria mediante cepillado y uso de hilo dental.' },
    { k: /(fluor topico|gel fluor|barniz fluor)/i, a: 'Flúor tópico: se aplica directamente en dientes para reforzar el esmalte y reducir la desmineralización.' },
    { k: /(colutorio|enjuague bucal)/i, a: 'Enjuague bucal: complemento al cepillado que reduce bacterias y refresca el aliento. Debe contener flúor o antisépticos.' },
    { k: /(erosion dental|abrasion|attricion)/i, a: 'Erosión dental: pérdida del esmalte por ácidos o desgaste mecánico. Evitar bebidas ácidas y cepillado agresivo.' },
    { k: /(hipersensibilidad dental|dientes sensibles)/i, a: 'Hipersensibilidad: dolor breve ante frío o calor. Se trata con pastas desensibilizantes y controlando la causa.' },
    { k: /(fluorosis|exceso de fluor)/i, a: 'Fluorosis: exceso de flúor durante la formación dental. Produce manchas blancas o marrones, prevenir con dosis adecuadas.' },
    { k: /(bruxismo|apretar dientes|rechinamiento)/i, a: 'Bruxismo: hábito de apretar o rechinar los dientes. Puede causar desgaste dental y dolor muscular.' },
    { k: /(ferula|ferula de descarga)/i, a: 'Férula de descarga: dispositivo acrílico que protege los dientes del desgaste causado por el bruxismo.' },
    { k: /(diagnostico diferencial|examen clinico|historia clinica)/i, a: 'Diagnóstico diferencial: comparación de signos y síntomas para determinar la causa real del problema dental.' },
    { k: /(radiografia|bite wing|periapical|panoramica)/i, a: 'Radiografía dental: imagen que muestra estructuras internas del diente para diagnóstico y planificación.' },
    { k: /(examen oral|inspeccion|palpacion)/i, a: 'Examen oral: incluye observación, palpación y registro de hallazgos para identificar alteraciones.' },
    { k: /(cancer bucal|lesion blanca|ulcera oral)/i, a: 'Cáncer bucal: lesiones que no cicatrizan en lengua o mejillas. Requiere diagnóstico temprano y control profesional.' },
    { k: /(habitos orales|succion digital|morder lapices)/i, a: 'Hábitos orales como morder objetos o chuparse el dedo pueden alterar la posición de los dientes.' },
    { k: /(ortodoncia interceptiva|mantenedor de espacio)/i, a: 'Ortodoncia interceptiva: corrige hábitos o problemas tempranos para guiar el crecimiento dental correcto.' },
    { k: /(habitos higienicos|rutina diaria dental)/i, a: 'Hábitos higiénicos: cepillarse tres veces al día, usar hilo dental y visitar al odontólogo cada seis meses.' },
    { k: /(instruccion de cepillado|educacion oral)/i, a: 'Instrucción de cepillado: enseñar técnica correcta adaptada a cada paciente y edad.' },
    { k: /(tecnica de cepillado|bass modificada|circular)/i, a: 'Técnica de cepillado Bass: movimientos suaves en ángulo de 45° hacia el margen gingival para limpiar placa.' },
    { k: /(hilo dental|limpieza interdental)/i, a: 'Hilo dental: elimina placa entre dientes donde el cepillo no llega. Usar al menos una vez al día.' },
    { k: /(cepillo electrico|cepillo manual)/i, a: 'Cepillo eléctrico: facilita el cepillado en personas con poca destreza manual; tan eficaz como el manual si se usa bien.' },
    { k: /(lengua|limpiador lingual)/i, a: 'La lengua acumula bacterias. Limpiarla a diario ayuda a evitar mal aliento y mantener la boca sana.' },
    { k: /(control dietetico|dieta saludable)/i, a: 'Control dietético: limitar azúcares entre comidas y preferir frutas, verduras y agua.' },
    { k: /(prevencion primaria|secundaria|terciaria)/i, a: 'Prevención primaria evita la enfermedad; secundaria la detecta temprano; terciaria evita complicaciones.' },
    { k: /(educacion comunitaria|salud bucal escolar)/i, a: 'Programas escolares enseñan cepillado, uso de flúor y control de dieta para prevenir caries.' },
    { k: /(cepillado infantil|ninos|odontopediatria preventiva)/i, a: 'Cepillado infantil: los padres deben supervisar hasta que el niño aprenda la técnica correcta.' },
    { k: /(cepillado nocturno|antes de dormir)/i, a: 'El cepillado antes de dormir es el más importante, ya que reduce la acción bacteriana durante la noche.' },
    { k: /(cepillo duro|cepillo suave|reemplazo del cepillo)/i, a: 'Usar cepillo de cerdas suaves y cambiarlo cada tres meses o tras una enfermedad.' },
    { k: /(pasta fluorada|dentifrico con fluor)/i, a: 'Pasta fluorada: protege el esmalte y previene caries; usar una cantidad del tamaño de un grano de arroz en niños.' },
    { k: /(fluor barniz|gel profesional)/i, a: 'Barniz de flúor: aplicación profesional que forma una capa protectora en el esmalte durante varias horas.' },
    { k: /(campanas preventivas|jornadas de salud)/i, a: 'Campañas preventivas promueven revisiones periódicas y enseñanza de higiene bucal a la comunidad.' },
    { k: /(indice de sangrado|indice gingival)/i, a: 'Índice gingival: mide inflamación y sangrado de encías, útil para seguimiento de tratamientos periodontales.' },
    { k: /(indice de placa|acumulacion de placa)/i, a: 'Índice de placa: cuantifica la cantidad de placa bacteriana presente en dientes.' },
    { k: /(plano de higiene|instrucciones personalizadas)/i, a: 'Plan de higiene: conjunto de recomendaciones individuales para mejorar la limpieza bucal.' },
    { k: /(determinacion de riesgo|riesgo de caries)/i, a: 'Determinación del riesgo: analiza factores como dieta, higiene y flúor para establecer un plan preventivo.' },
    { k: /(promocion de salud bucal|educacion preventiva)/i, a: 'Promoción de salud bucal: acciones dirigidas a crear hábitos saludables en la población.' },
    { k: /(odontologia preventiva|prevencion bucal)/i, a: 'Odontología preventiva: enfoque que busca evitar enfermedades antes de que aparezcan.' },
    { k: /(control de placa revelador|tabletas reveladoras)/i, a: 'Revelador de placa: tiñe la placa bacteriana para enseñar zonas donde el cepillado fue insuficiente.' },
    { k: /(salud bucal|bienestar oral)/i, a: 'Salud bucal: equilibrio entre dientes, encías y hábitos saludables que permiten comer y sonreír sin dolor.' },
    { k: /(visita dental|chequeo|control semestral)/i, a: 'Visita dental: debe realizarse cada seis meses para detectar problemas a tiempo.' },
    { k: /(educacion familiar|padres responsables)/i, a: 'Los padres deben reforzar la higiene y el control de dieta de los niños para evitar caries tempranas.' },
    { k: /(caries temprana|caries del biberon)/i, a: 'Caries del biberón: aparece por exposición prolongada a líquidos azucarados. Se previene limpiando dientes después de comer.' },
    { k: /(odontograma|registro dental)/i, a: 'Odontograma: dibujo o esquema donde se registran los hallazgos clínicos de cada diente.' },
    { k: /(historia clinica|registro odontologico)/i, a: 'Historia clínica: documento que recopila información del paciente, antecedentes y plan de tratamiento.' },
    { k: /(educacion continua|capacitacion dental)/i, a: 'Educación continua: actualización constante del profesional para ofrecer tratamientos seguros y eficaces.' },
    { k: /(bioseguridad|control de infecciones)/i, a: 'Bioseguridad: normas para evitar contagios cruzados, incluye uso de guantes, mascarillas y esterilización.' },
    { k: /(esterilizacion|autoclave|instrumental)/i, a: 'Esterilización: proceso que destruye microorganismos mediante calor o vapor en autoclave.' },
    { k: /(desinfeccion|limpieza de superficies)/i, a: 'Desinfección: eliminación de bacterias en superficies y equipos con productos químicos apropiados.' },
    { k: /(lavado de manos|higiene del operador)/i, a: 'Lavado de manos: antes y después de atender a cada paciente, usando jabón antiséptico por 40 segundos.' },
    { k: /(vacunacion|hepatitis b|tetanica)/i, a: 'Vacunación: protege al personal contra enfermedades transmisibles como hepatitis B y tétanos.' },
    { k: /(residuos biologicos|basura clinica|desechos)/i, a: 'Residuos biológicos: deben separarse y eliminarse según normas de bioseguridad para evitar contagios.' },
    { k: /(control de infeccion cruzada|proteccion cruzada)/i, a: 'Control cruzado: cambio de guantes y barreras entre pacientes evita transmisión de infecciones.' },
    { k: /(proteccion personal|guantes|mascarilla)/i, a: 'Equipo de protección personal: guantes, mascarilla, gafas y bata protegen al profesional y al paciente.' },
    { k: /(manejo de residuos|clasificacion de residuos)/i, a: 'Manejo de residuos: clasificación por tipo y color; agujas en contenedores rígidos, orgánicos en bolsas rojas.' },
    { k: /(ergonomia dental|postura laboral)/i, a: 'Ergonomía: mantener postura adecuada y pausas activas para prevenir lesiones musculares.' },
    { k: /(iluminacion dental|luz del gabinete)/i, a: 'Iluminación: luz blanca dirigida al área de trabajo mejora la visibilidad y precisión en los procedimientos.' },
    { k: /(control ambiental|ventilacion consultorio)/i, a: 'Ventilación adecuada reduce aerosoles y mejora la seguridad del entorno clínico.' },
    { k: /(educacion paciente|orientacion personalizada)/i, a: 'Educación al paciente: explicar el procedimiento y dar recomendaciones claras mejora la cooperación.' },
    { k: /(comunicacion paciente|trato amable)/i, a: 'Comunicación efectiva: escuchar y hablar con empatía genera confianza y mejora los resultados del tratamiento.' },
    { k: /(ansiedad dental|miedo al dentista)/i, a: 'Ansiedad dental: puede manejarse con información clara, música relajante o técnicas de respiración.' },
    { k: /(salud general|enfermedades sistemicas)/i, a: 'La salud bucal influye en la salud general; infecciones dentales pueden afectar corazón o diabetes.' },
    { k: /(diabetes|hipertension|paciente sistemico)/i, a: 'Pacientes con enfermedades sistémicas requieren control médico y cuidados dentales especiales.' },
    { k: /(embarazo y salud bucal|gestante)/i, a: 'Durante el embarazo se recomienda mantener buena higiene y evitar tratamientos invasivos sin necesidad urgente.' },
    { k: /(educacion escolar|programa educativo dental)/i, a: 'Programas educativos en escuelas fomentan la prevención mediante juegos, charlas y demostraciones.' },
    { k: /(riesgo de caries alto|evaluacion de riesgo)/i, a: 'Pacientes con alto riesgo de caries deben tener controles más frecuentes y flúor adicional.' },
    { k: /(agua fluorada|fluor en agua potable)/i, a: 'Fluoración del agua: medida comunitaria efectiva para prevenir caries en la población general.' },
    { k: /(cepillado en grupo|clinica escolar)/i, a: 'El cepillado grupal en escuelas refuerza hábitos de higiene y motiva el cuidado dental.' },
    { k: /(tecnica horizontal|cepillado incorrecto)/i, a: 'La técnica horizontal puede causar desgaste y daño en encías, se recomienda cepillado suave vertical.' },
    { k: /(prevencion de caries|control de placa)/i, a: 'Prevención de caries: higiene adecuada, dieta saludable y visitas regulares al odontólogo.' },
    { k: /(indice de sangrado|control periodontal)/i, a: 'El índice de sangrado mide la respuesta inflamatoria de las encías y orienta la necesidad de tratamiento.' },
    { k: /(evaluacion de higiene|control en casa)/i, a: 'Evaluar la higiene motiva al paciente y permite ajustar las recomendaciones personalizadas.' },
    { k: /(educacion continua en salud|formacion profesional)/i, a: 'La formación constante mejora la calidad de atención y actualiza las técnicas preventivas.' },
    { k: /(salud publica|programas de salud oral)/i, a: 'Salud pública: estrategias para reducir enfermedades bucales en comunidades a través de educación y prevención.' },
    { k: /(odontologia comunitaria|brigadas dentales)/i, a: 'Odontología comunitaria: atención y educación en poblaciones rurales o escolares con enfoque preventivo.' },
    { k: /(proyecto educativo bucal|promocion escolar)/i, a: 'Los proyectos escolares de salud bucal fortalecen hábitos y fomentan la visita regular al dentista.' },
    { k: /(control de flúor|monitoreo del agua)/i, a: 'El control del nivel de flúor en el agua garantiza una prevención segura sin causar fluorosis.' },
    { k: /(prevencion en adultos mayores|cuidado geriatrico dental)/i, a: 'En adultos mayores se prioriza la higiene, prótesis limpias y control de enfermedades periodontales.' },
    { k: /(instrumental basico|bandeja basica|set dental)/i, a: 'Instrumental básico: espejo, pinza, explorador y sonda; usados para examinar y detectar caries o placa.' },
    { k: /(espejo dental|espejo bucal)/i, a: 'Espejo dental: permite ver zonas difíciles de la boca y reflejar la luz durante el examen clínico.' },
    { k: /(pinza dental|pinza algodonera|pinza de algodon)/i, a: 'Pinza algodonera: sujeta algodón, gasas u objetos pequeños sin tocar directamente la zona bucal.' },
    { k: /(explorador dental|sonda exploradora)/i, a: 'Explorador dental: instrumento delgado con punta fina que detecta caries, rugosidades o restauraciones defectuosas.' },
    { k: /(sonda periodontal|medidor de bolsas|sonda de williams)/i, a: 'Sonda periodontal: mide la profundidad de las bolsas gingivales y evalúa la salud periodontal.' },
    { k: /(bisturi|mango de bisturi|hoja de bisturi)/i, a: 'Bisturí: se usa para realizar incisiones precisas en tejidos blandos durante procedimientos quirúrgicos.' },
    { k: /(tijeras quirurgicas|tijeras iris|tijeras de sutura)/i, a: 'Tijeras quirúrgicas: cortan suturas o tejidos blandos; deben mantenerse limpias y afiladas.' },
    { k: /(pinza mosquito|pinza hemostatica|hemostato)/i, a: 'Pinza mosquito: sirve para sujetar tejidos o detener sangrados pequeños mediante compresión.' },
    { k: /(espatula|espatula de cemento|espatula de modelar)/i, a: 'Espátula dental: mezcla materiales como cementos o alginato hasta lograr consistencia uniforme.' },
    { k: /(porta amalgama|amalgamador manual)/i, a: 'Porta amalgama: transporta y deposita la amalgama en la cavidad dental preparada.' },
    { k: /(condensador|bruñidor|instrumentos de condensacion)/i, a: 'Condensador: compacta materiales restauradores dentro de la cavidad dental para mejorar su adaptación.' },
    { k: /(carver|bruñidor|tallador amalgama)/i, a: 'Carver o tallador: da forma anatómica final a las restauraciones de amalgama o resina.' },
    { k: /(cucharilla|cucharilla de dentina|excavador)/i, a: 'Cucharilla: elimina dentina reblandecida y restos de caries del interior de la cavidad dental.' },
    { k: /(jeringa triple|aire agua|spray dental)/i, a: 'Jeringa triple: emite aire, agua o ambos para limpiar o secar la zona de trabajo.' },
    { k: /(turbina dental|pieza de alta|fresa)/i, a: 'Turbina dental: instrumento rotatorio de alta velocidad para cortar tejidos duros del diente.' },
    { k: /(pieza de baja|micromotor|contraangulo)/i, a: 'Pieza de baja velocidad o contraángulo: usada para pulido, ajuste o eliminación de caries con menor velocidad.' },
    { k: /(fresas dentales|tipos de fresa|fresa redonda|fresa cono invertido)/i, a: 'Fresas dentales: cortan y desgastan el diente; hay de formas variadas según el procedimiento.' },
    { k: /(pulidor|disco de pulido|goma de pulido)/i, a: 'Pulidor dental: suaviza y da brillo a restauraciones o dientes tras el tratamiento.' },
    { k: /(eyector de saliva|succion|canula)/i, a: 'Eyector de saliva: mantiene seca la cavidad bucal durante los procedimientos odontológicos.' },
    { k: /(bomba de succion|aspirador dental)/i, a: 'Bomba de succión: elimina líquidos y restos del campo operatorio para mantener la visibilidad.' },
    { k: /(bandeja de instrumental|charola|porta instrumental)/i, a: 'Bandeja: mantiene los instrumentos organizados y accesibles durante la atención clínica.' },
    { k: /(autoclave|esterilizacion|esterilizador)/i, a: 'Autoclave: equipo que esteriliza instrumental mediante vapor a presión y alta temperatura.' },
    { k: /(selladora de bolsas|bolsas de esterilizacion)/i, a: 'Selladora: cierra herméticamente bolsas de esterilización para mantener los instrumentos estériles.' },
    { k: /(ultrasonido|limpieza ultrasonica|baño ultrasónico)/i, a: 'Ultrasonido: limpia el instrumental mediante vibraciones que eliminan residuos antes de esterilizar.' },
    { k: /(lupa dental|lentes magnificadores)/i, a: 'Lupa dental: amplía la visión del campo operatorio y mejora la precisión en los procedimientos.' },
    { k: /(lampara de fotocurado|curado de resina|luz led dental)/i, a: 'Lámpara de fotocurado: endurece resinas compuestas mediante luz azul de alta intensidad.' },
    { k: /(resina compuesta|composite|restauracion estetica)/i, a: 'Resina compuesta: material estético para restaurar dientes afectados por caries o fracturas.' },
    { k: /(amalgama dental|restauracion metalica)/i, a: 'Amalgama: material metálico resistente usado en molares; mezcla de mercurio y aleaciones de plata.' },
    { k: /(ionomero de vidrio|cemento ionomero)/i, a: 'Ionómero de vidrio: material restaurador con flúor que se adhiere químicamente al diente.' },
    { k: /(cemento dental|cementado|cemento fosfato|cemento de oxido)/i, a: 'Cementos dentales: fijan coronas, puentes y bases; cada tipo tiene propiedades específicas.' },
    { k: /(barniz dental|forro cavitario|base dental)/i, a: 'Barniz o base: capa protectora que aísla la pulpa del calor o agentes químicos de los materiales restauradores.' },
    { k: /(matriz dental|banda matriz|portamatriz)/i, a: 'Matriz: reproduce la pared del diente durante restauraciones para evitar desbordes del material.' },
    { k: /(cuña dental|cuña de madera|separador interdental)/i, a: 'Cuña dental: mantiene la matriz ajustada y separa ligeramente los dientes para mejor contacto.' },
    { k: /(articulador|montaje en articulador|modelo de estudio)/i, a: 'Articulador: simula el movimiento de la mandíbula para estudiar o diseñar restauraciones y prótesis.' },
    { k: /(modelo de yeso|vaciado de yeso|modelo dental)/i, a: 'Modelo de yeso: copia exacta de la boca obtenida a partir de impresiones con alginato o silicona.' },
    { k: /(alginato|material de impresion|mezcla de alginato)/i, a: 'Alginato: material de impresión elástico usado para obtener moldes de dientes y encías.' },
    { k: /(silicona dental|impresion de precision)/i, a: 'Silicona: material de impresión de alta precisión usado en prótesis o coronas.' },
    { k: /(vibrador de yeso|mesa vibradora)/i, a: 'Vibrador de yeso: elimina burbujas al vaciar moldes de yeso, asegurando un modelo preciso.' },
    { k: /(piedra pomez|pulido|abrasivo dental)/i, a: 'Piedra pómez: se usa con cepillo o copa para eliminar manchas o pulir restauraciones.' },
    { k: /(pasta profilaxis|pasta abrasiva)/i, a: 'Pasta de profilaxis: limpia y pule los dientes después del raspado y eliminación del sarro.' },
    { k: /(cavitador ultrasónico|detartrador|limpieza profesional)/i, a: 'Cavitador ultrasónico: rompe el sarro mediante vibración y agua, usado en limpiezas profesionales.' },
    { k: /(anestesia local|lidocaina|infiltracion|bloqueo)/i, a: 'Anestesia local: bloquea el dolor en zonas específicas; aplicada mediante infiltración o bloqueo troncular.' },
    { k: /(carpule|jeringa carpule|jeringa dental)/i, a: 'Jeringa carpule: administra anestesia mediante cartuchos desechables con precisión y control.' },
    { k: /(aguja dental|aguja corta|aguja larga)/i, a: 'Agujas dentales: varían según el tipo de anestesia; las cortas para infiltración y largas para bloqueos.' },
    { k: /(bloqueo alveolar inferior|troncular|mandibular)/i, a: 'Bloqueo alveolar inferior: anestesia todo el lado mandibular mediante inyección en el nervio dental inferior.' },
    { k: /(aspiracion anestesia|reflujo|inyeccion segura)/i, a: 'Siempre aspirar antes de inyectar anestesia para evitar introducirla en un vaso sanguíneo.' },
    { k: /(complicaciones anestesia|reaccion alergica|toxicidad)/i, a: 'Complicaciones: reacciones leves o alergias. Usar dosis correctas y revisar antecedentes del paciente.' },
    { k: /(radiologia dental|radiografia intraoral|radiografia panoramica)/i, a: 'Radiología dental: herramienta diagnóstica que muestra estructuras dentales y óseas internas.' },
    { k: /(radiografia bite wing|interproximal)/i, a: 'Bite-wing: radiografía que muestra coronas y espacios interdentales para detectar caries entre dientes.' },
    { k: /(radiografia periapical|lesion apical)/i, a: 'Radiografía periapical: permite observar toda la raíz dental y tejidos adyacentes.' },
    { k: /(radiografia panoramica|ortopantomografia)/i, a: 'Panorámica: muestra toda la boca en una sola imagen, útil para planificación general.' },
    { k: /(proteccion radiologica|delantal plomado|colimador)/i, a: 'Protección radiológica: uso de delantal plomado y colimador para minimizar exposición a radiación.' },
    { k: /(exposicion radiografica|tiempo de exposicion|sensor digital)/i, a: 'El tiempo de exposición debe ser mínimo necesario; los sensores digitales reducen dosis al paciente.' },
    { k: /(revelado radiografico|procesado de imagen)/i, a: 'Revelado: transforma la imagen latente en visible; puede ser químico o digital.' },
    { k: /(radiovisiografia|imagen digital dental)/i, a: 'Radiovisiografía: sistema digital inmediato que facilita diagnóstico y almacenamiento electrónico.' },
    { k: /(rx oclusal|radiografia oclusal)/i, a: 'Radiografía oclusal: muestra el piso bucal o palatino, útil en diagnóstico de cuerpos extraños o dientes retenidos.' },
    { k: /(ergonomia dental|postura correcta|posicion de trabajo)/i, a: 'Ergonomía dental: busca prevenir lesiones manteniendo postura recta y brazos cercanos al cuerpo.' },
    { k: /(silla odontologica|unidad dental|equipo dental)/i, a: 'Unidad dental: conjunto de silla, lámpara, mangueras y controles usados durante la atención odontológica.' },
    { k: /(lampara dental|iluminacion del campo)/i, a: 'Lámpara dental: proporciona luz intensa y focalizada para visualizar claramente la zona de trabajo.' },
    { k: /(aspiracion quirurgica|succion de alto volumen)/i, a: 'Aspiración quirúrgica: retira líquidos y restos durante procedimientos quirúrgicos o restauradores.' },
    { k: /(autoclave control biologico|test de esporas)/i, a: 'Control biológico del autoclave: garantiza que la esterilización fue efectiva mediante indicadores de esporas.' },
    { k: /(lubricacion instrumental|mantenimiento dental)/i, a: 'Lubricar el instrumental rotatorio prolonga su vida útil y evita sobrecalentamiento.' },
    { k: /(control de infecciones cruzadas|protocolos de limpieza)/i, a: 'El control de infecciones requiere desinfección de superficies y cambio de guantes entre pacientes.' },
    { k: /(proteccion ocular|gafas de seguridad)/i, a: 'Las gafas de seguridad protegen contra salpicaduras de fluidos o fragmentos durante tratamientos.' },
    { k: /(bata clinica|ropa de proteccion)/i, a: 'La bata clínica protege la ropa del profesional y evita la contaminación cruzada.' },
    { k: /(mascarilla dental|tapabocas|respirador n95)/i, a: 'Mascarilla: evita la inhalación de aerosoles y protege tanto al paciente como al profesional.' },
    { k: /(guantes de latex|guantes de nitrilo|proteccion manual)/i, a: 'Guantes: crean una barrera física contra microorganismos. Cambiar entre pacientes siempre.' },
    { k: /(lavado quirurgico|antisepsia de manos)/i, a: 'Lavado quirúrgico: realizado antes de procedimientos invasivos con jabón antiséptico y técnica estéril.' },
    { k: /(barrera plastica|proteccion cruzada|film de superficie)/i, a: 'Barreras plásticas: cubren superficies difíciles de desinfectar y se cambian entre pacientes.' },
    { k: /(residuos peligrosos|manejo de desechos|biocontaminados)/i, a: 'Los residuos peligrosos deben colocarse en bolsas o contenedores según color y tipo.' },
    { k: /(infeccion ocupacional|accidente biologico)/i, a: 'Si ocurre una punción o exposición a sangre, lavar con agua y jabón, reportar y seguir protocolo médico.' },
    { k: /(instrumental cortopunzante|desecho de agujas)/i, a: 'Agujas y bisturís usados se desechan en contenedores rígidos resistentes a perforaciones.' },
    { k: /(vacunas odontologos|hepatitis b|tetanica)/i, a: 'Todo odontólogo debe tener esquema de vacunación completo contra hepatitis B y tétanos.' },
    { k: /(ergonomia del asistente|posicion de cuatro manos)/i, a: 'Trabajo a cuatro manos: coordinación entre odontólogo y asistente para mayor eficiencia y ergonomía.' },
    { k: /(control de calidad|evaluacion de esterilizacion)/i, a: 'Control de calidad: verifica que todos los procesos cumplan normas de bioseguridad y esterilización.' },
    { k: /(descarte de instrumental|reutilizacion prohibida)/i, a: 'Instrumental desechable no debe reutilizarse; usar siempre productos estériles o nuevos.' },
    { k: /(reporte de accidentes|protocolo de bioseguridad)/i, a: 'Los accidentes laborales deben registrarse y seguir protocolo institucional de atención inmediata.' },
    { k: /(control de aerosoles|succion de alto volumen|barreras faciales)/i, a: 'El control de aerosoles se logra con succión eficiente, barreras faciales y ventilación adecuada.' },
    { k: /(iluminacion adecuada|ergonomia visual)/i, a: 'La luz correcta reduce fatiga visual y mejora la precisión durante el tratamiento dental.' },
    { k: /(mantenimiento del equipo dental|limpieza diaria)/i, a: 'El mantenimiento preventivo del equipo dental evita fallas y prolonga su funcionamiento seguro.' },
    { k: /(sistema de agua dental|purga de lineas)/i, a: 'Purga diaria de las líneas de agua dental previene la acumulación de bacterias.' },
    { k: /(esterilizacion por calor seco|horno pasteur)/i, a: 'El calor seco esteriliza instrumentos metálicos a altas temperaturas, aunque tarda más que el vapor.' },
    { k: /(indicadores quimicos|control de esterilizacion)/i, a: 'Los indicadores químicos cambian de color cuando se alcanzan las condiciones adecuadas de esterilización.' },
    { k: /(botiquin dental|emergencias medicas)/i, a: 'Todo consultorio debe contar con botiquín que incluya oxígeno, epinefrina y antihistamínicos.' },
    { k: /(control de emergencias|capacitacion rcp)/i, a: 'El personal odontológico debe conocer RCP básica y manejo de emergencias médicas.' },
    { k: /(comunicacion efectiva|trato al paciente)/i, a: 'Una comunicación clara y empática mejora la cooperación y reduce la ansiedad del paciente.' },
    { k: /(operatoria dental|restauracion dental|tratamiento de caries)/i, a: 'Operatoria dental: elimina la caries y restaura la forma y función del diente afectado.' },
    { k: /(preparacion cavitaria|cavidad dental|clase i|clase ii|clase iii|clase iv|clase v)/i, a: 'Preparación cavitaria: conformación de la cavidad según su ubicación y extensión para recibir el material restaurador.' },
    { k: /(resina compuesta|composite dental)/i, a: 'Resina compuesta: material estético usado para restauraciones; se adhiere al esmalte y dentina mediante adhesivos.' },
    { k: /(adhesivo dental|bonding|sistema adhesivo)/i, a: 'Adhesivo dental: une químicamente la resina al diente creando un sellado hermético y duradero.' },
    { k: /(curado de resina|fotocurado|luz led dental)/i, a: 'Fotocurado: proceso de endurecimiento de la resina mediante luz azul de alta intensidad.' },
    { k: /(pulido dental|acabado de restauraciones)/i, a: 'Pulido: suaviza la superficie de las restauraciones, mejora la estética y evita la acumulación de placa.' },
    { k: /(aislamiento absoluto|dique de hule|grapa|portagrapa)/i, a: 'Aislamiento absoluto: separa el diente del campo salival, manteniendo el área seca y limpia durante el procedimiento.' },
    { k: /(aislamiento relativo|rollos de algodon|eyector de saliva)/i, a: 'Aislamiento relativo: control de humedad mediante rollos de algodón y succión continua.' },
    { k: /(ionomero de vidrio|base de cavidad)/i, a: 'Ionómero de vidrio: material que libera flúor, usado como base o restauración temporal.' },
    { k: /(forro cavitario|protector pulpar)/i, a: 'Forro cavitario: protege la pulpa dental frente a irritantes térmicos o químicos.' },
    { k: /(matriz y cuña|banda matriz|tofflemire)/i, a: 'Matriz y cuña: permiten reconstruir la pared del diente durante una restauración y mantener contacto adecuado.' },
    { k: /(fresas de diamante|fresas de tungsteno)/i, a: 'Fresas: instrumentos cortantes para eliminar caries o tallar dientes según el tipo de material y velocidad.' },
    { k: /(instrumental de operatoria|condensador|carver|bruñidor)/i, a: 'Instrumental de operatoria: herramientas manuales que modelan y pulen materiales restauradores.' },
    { k: /(selladores de fosas y fisuras|prevencion de caries)/i, a: 'Selladores: capa protectora que evita la acumulación de bacterias en surcos profundos de los molares.' },
    { k: /(amalgama dental|material metalico)/i, a: 'Amalgama: material resistente usado en molares, mezcla de mercurio y aleaciones metálicas.' },
    { k: /(polimerizacion|curado incompleto|fotopolimerizacion)/i, a: 'Polimerización: proceso por el cual la resina pasa de estado líquido a sólido gracias a la luz de curado.' },
    { k: /(terminos cavitarios|margen cavosuperficial|pared axial)/i, a: 'Los términos cavitarios indican las partes anatómicas de una cavidad dental, como paredes y márgenes.' },
    { k: /(sensibilidad postoperatoria|dolor despues de restauracion)/i, a: 'Sensibilidad postoperatoria: puede deberse a deshidratación dentinaria o contracción de la resina; suele ser temporal.' },
    { k: /(endodoncia|tratamiento de conductos|pulpa dental)/i, a: 'Endodoncia: tratamiento que elimina la pulpa dañada y sella los conductos radiculares para conservar el diente.' },
    { k: /(acceso endodontico|camara pulpar|localizacion de conductos)/i, a: 'Acceso endodóntico: apertura en el diente para localizar y tratar los conductos radiculares.' },
    { k: /(instrumentacion|limas|ensanchado de conductos)/i, a: 'Instrumentación: limpieza y modelado de los conductos con limas manuales o rotatorias.' },
    { k: /(irrigacion endodontica|hipoclorito de sodio)/i, a: 'Irrigación: elimina restos orgánicos y bacterias dentro de los conductos usando soluciones desinfectantes.' },
    { k: /(obturacion de conductos|gutapercha|sellador endodontico)/i, a: 'Obturación: sellado hermético del sistema de conductos con gutapercha y sellador.' },
    { k: /(radiografia de conducto|control endodontico)/i, a: 'Las radiografías verifican la longitud de trabajo y la calidad del sellado durante la endodoncia.' },
    { k: /(retratamiento endodontico|fallo del tratamiento)/i, a: 'Retratamiento endodóntico: se realiza cuando la obturación anterior presenta filtración o infección persistente.' },
    { k: /(instrumentos endodonticos|limas k|limas h)/i, a: 'Limas endodónticas: instrumentos metálicos que eliminan tejido pulpar y dan forma al conducto.' },
    { k: /(apex locator|localizador apical)/i, a: 'Apex locator: dispositivo electrónico que mide la longitud del conducto radicular con precisión.' },
    { k: /(obturacion lateral|tecnica de condensacion)/i, a: 'Técnica de obturación lateral: método de sellado que introduce conos accesorios de gutapercha lateralmente.' },
    { k: /(biopulpectomia|pulpotomia|necrosis pulpar)/i, a: 'Biopulpectomía: extracción completa de la pulpa viva; se diferencia de pulpotomía, que conserva la parte radicular.' },
    { k: /(periapicitis|absceso apical|lesion periapical)/i, a: 'Periapicitis: inflamación del ápice dental por infección; puede causar dolor intenso y requiere endodoncia o drenaje.' },
    { k: /(apicectomia|cirugia periapical)/i, a: 'Apicectomía: resección del extremo de la raíz y eliminación de tejido inflamado cuando falla la endodoncia.' },
    { k: /(rehabilitacion post endodoncia|perno muñon|corona)/i, a: 'Rehabilitación postendodóntica: reconstrucción del diente tratado con perno, muñón y corona para devolver función.' },
    { k: /(fractura radicular|fisura dental)/i, a: 'Fractura radicular: grieta en la raíz del diente que puede requerir extracción o tratamiento especializado.' },
    { k: /(blanqueamiento interno|dientes oscurecidos)/i, a: 'Blanqueamiento interno: aclara dientes endodonciados con productos oxidantes dentro de la cámara pulpar.' },
    { k: /(protesis dental|rehabilitacion oral)/i, a: 'Prótesis dental: reemplaza dientes ausentes para restaurar función masticatoria y estética.' },
    { k: /(protesis fija|corona|puente dental)/i, a: 'Prótesis fija: coronas o puentes cementados permanentemente sobre dientes o implantes.' },
    { k: /(protesis removible|placa dental)/i, a: 'Prótesis removible: sustituye varios dientes y puede retirarse para limpieza y descanso.' },
    { k: /(impresion protesica|silicona por adicion|material de precision)/i, a: 'Impresión protésica: obtiene una copia exacta de la boca para fabricar coronas o prótesis.' },
    { k: /(prueba de metal|ajuste de protesis)/i, a: 'Prueba de metal: verificación del ajuste y adaptación antes de colocar la prótesis definitiva.' },
    { k: /(cementacion|cemento definitivo|ionomero de vidrio)/i, a: 'Cementación: fijación de una prótesis o corona con un cemento que garantice sellado y retención.' },
    { k: /(ajuste oclusion|mordida|contacto oclusal)/i, a: 'Ajuste oclusal: equilibrar la mordida para evitar puntos de presión o desgaste desigual.' },
    { k: /(protesis total|dentadura completa)/i, a: 'Prótesis total: sustituye todos los dientes de una arcada; requiere adaptación gradual del paciente.' },
    { k: /(protesis parcial removible|ganchos|estructura metalica)/i, a: 'Prótesis parcial removible: reemplaza dientes ausentes con una base acrílica o metálica y retenedores.' },
    { k: /(rebase de protesis|ajuste protesis)/i, a: 'Rebase: renovación de la base de la prótesis para mejorar su adaptación a la encía.' },
    { k: /(prueba de dientes|montaje en cera)/i, a: 'Prueba de dientes: permite evaluar estética y oclusión antes de fabricar la prótesis final.' },
    { k: /(laboratorio dental|tecnico dental|modelo maestro)/i, a: 'El laboratorio dental fabrica restauraciones siguiendo las indicaciones del odontólogo tratante.' },
    { k: /(periodoncia|tratamiento de encias|bolsas periodontales)/i, a: 'Periodoncia: trata enfermedades que afectan las encías y el soporte óseo de los dientes.' },
    { k: /(raspado y alisado radicular|curetaje|limpieza profunda)/i, a: 'Raspado y alisado radicular: elimina cálculo subgingival y suaviza la raíz para permitir la cicatrización de la encía.' },
    { k: /(instrumental periodontal|curetas gracey|curetas universales)/i, a: 'Curetas periodontales: instrumentos curvos que eliminan cálculo de las raíces y bolsas periodontales.' },
    { k: /(sonda periodontal|profundidad de bolsa)/i, a: 'La sonda periodontal mide la profundidad de las bolsas gingivales y detecta pérdida de inserción.' },
    { k: /(enfermedad periodontal|piorrea|encía retraida)/i, a: 'La enfermedad periodontal destruye hueso y encías, puede causar movilidad dental si no se trata.' },
    { k: /(cirugia periodontal|colgajo periodontal|injerto de encia)/i, a: 'Cirugía periodontal: corrige defectos de encía o hueso para mejorar la salud y estética gingival.' },
    { k: /(recesion gingival|retraccion de encia)/i, a: 'Recesión gingival: desplazamiento de la encía que expone la raíz; puede corregirse con injertos.' },
    { k: /(higiene periodontal|control de placa|mantenimiento periodontal)/i, a: 'El mantenimiento periodontal evita la reaparición de la enfermedad mediante limpiezas regulares.' },
    { k: /(placa bacteriana subgingival|bolsas profundas)/i, a: 'La placa subgingival es causa principal de periodontitis; se elimina con curetas especializadas.' },
    { k: /(oclusion traumatica|fuerzas excesivas|movilidad dental)/i, a: 'La oclusión traumática puede agravar problemas periodontales; debe equilibrarse la mordida.' },
    { k: /(regeneracion tisular guiada|membranas|injertos oseos)/i, a: 'Regeneración tisular guiada: técnica que usa membranas o injertos para recuperar el hueso perdido.' },
    { k: /(ferulizacion dental|union de dientes moviles)/i, a: 'Ferulización: une dientes móviles entre sí para estabilizarlos y distribuir mejor las fuerzas.' },
    { k: /(profilaxis periodontal|mantenimiento profesional)/i, a: 'Profilaxis periodontal: limpieza profesional periódica para mantener los tejidos saludables.' },
    { k: /(cirugia mucogingival|injerto libre|colgajo desplazado)/i, a: 'Cirugía mucogingival: corrige defectos en el margen gingival mediante injertos o colgajos.' },
    { k: /(piorrea avanzada|movilidad dental|perdida de dientes)/i, a: 'Piorrea avanzada: fase severa de periodontitis con pérdida de soporte óseo y movilidad dental.' },
    { k: /(control de placa en casa|educacion periodontal)/i, a: 'La educación en higiene es clave para el éxito del tratamiento periodontal.' },
    { k: /(irrigador bucal|limpieza interdental con agua)/i, a: 'El irrigador bucal elimina restos de comida y placa en zonas difíciles de alcanzar con el cepillo.' },
    { k: /(cepillo interdental|espacios interdentales)/i, a: 'Cepillo interdental: limpia espacios amplios entre dientes y aparatos de ortodoncia.' },
    { k: /(enjuague antiséptico|clorhexidina|colutorio)/i, a: 'La clorhexidina es un antiséptico eficaz para reducir bacterias y controlar la inflamación gingival.' },
    { k: /(periodontitis cronica|agresiva)/i, a: 'Periodontitis crónica: progresa lentamente; la agresiva afecta rápidamente a pacientes jóvenes.' },
    { k: /(tratamiento de mantenimiento|control post tratamiento)/i, a: 'El mantenimiento regular tras la terapia periodontal previene recaídas y pérdida de dientes.' },
    { k: /(hueso alveolar|reabsorcion osea)/i, a: 'La reabsorción ósea es consecuencia de inflamación prolongada o pérdida dental sin reemplazo.' },
    { k: /(microbiota oral|bacterias patogenas)/i, a: 'La microbiota oral incluye bacterias beneficiosas y dañinas; su equilibrio mantiene la salud bucal.' },
    { k: /(biofilm subgingival|patogenos periodontales)/i, a: 'El biofilm subgingival contiene bacterias específicas responsables de la periodontitis avanzada.' },
    { k: /(cirugia regenerativa|enfermedad periodontal avanzada)/i, a: 'La cirugía regenerativa busca reconstruir tejido perdido por la enfermedad periodontal.' },
    { k: /(cepillo electrico sonico|ultrasonico)/i, a: 'El cepillo sónico mejora la eliminación de placa mediante vibraciones de alta frecuencia.' },
    { k: /(periodonto sano|tejidos de soporte dental)/i, a: 'El periodonto incluye encía, ligamento periodontal, cemento y hueso alveolar, esenciales para soporte dental.' },
    { k: /(placa supragingival|placa subgingival)/i, a: 'La placa supragingival se forma sobre el margen gingival, y la subgingival debajo del mismo.' },
    { k: /(cepillado profesional|educacion periodontal)/i, a: 'El cepillado profesional enseña técnicas correctas de higiene adaptadas al estado periodontal del paciente.' },
    { k: /(control de tabaco|factor de riesgo periodontal)/i, a: 'El tabaco es un factor que empeora la periodontitis y retrasa la cicatrización.' },
    { k: /(diabetes y periodontitis|relacion sistemica)/i, a: 'La diabetes mal controlada aumenta el riesgo y severidad de la enfermedad periodontal.' },
    { k: /(antibioticos en periodoncia|tratamiento complementario)/i, a: 'Los antibióticos pueden usarse como complemento en casos severos, siempre con control profesional.' },
    { k: /(microcirugia periodontal|instrumentos finos)/i, a: 'La microcirugía periodontal utiliza lupas o microscopios para procedimientos más precisos y menos invasivos.' },
    { k: /(cuidados postoperatorios periodontales|control en casa)/i, a: 'Después del tratamiento periodontal, se recomienda evitar cepillado fuerte y usar enjuague antiséptico.' },
    { k: /(educacion en higiene oral|instruccion personalizada)/i, a: 'Cada paciente recibe instrucciones personalizadas para mantener la salud de sus encías y dientes.' },
    { k: /(motivacion del paciente|refuerzo positivo)/i, a: 'Motivar al paciente es esencial para mantener la constancia en la higiene y el cuidado periodontal.' },
    { k: /(revisiones periodicas|seguimiento periodontal)/i, a: 'Las revisiones periódicas permiten detectar signos tempranos de inflamación o recidiva de enfermedad.' },
    { k: /(exodoncia|extraccion dental|extraccion de muela)/i, a: 'Exodoncia: extracción controlada de un diente; requiere anestesia, técnica cuidadosa y control posterior.' },
    { k: /(luxador|elevador dental|botador)/i, a: 'Luxador o elevador: instrumento que separa y moviliza el diente del hueso antes de extraerlo.' },
    { k: /(forceps|pinzas de extraccion)/i, a: 'Fórceps: pinzas usadas para sujetar y extraer dientes; su diseño varía según el tipo de diente.' },
    { k: /(alveolitis|alveolo seco|complicacion post extraccion)/i, a: 'Alveolitis: dolor tras extracción por pérdida del coágulo; se trata con limpieza y medicación local.' },
    { k: /(hemostasia|sangrado post extraccion)/i, a: 'Hemostasia: detener el sangrado con presión, sutura o agentes hemostáticos después de una cirugía dental.' },
    { k: /(suturas|sutura reabsorbible|sutura seda)/i, a: 'Suturas: unen bordes de una herida; pueden ser reabsorbibles o de seda, según la zona tratada.' },
    { k: /(anestesia troncular alveolar inferior|bloqueo mandibular)/i, a: 'Anestesia troncular: bloquea el nervio mandibular para procedimientos en el lado inferior de la boca.' },
    { k: /(biopsia oral|muestra tisular|lesion sospechosa)/i, a: 'Biopsia: toma de tejido para análisis histológico en casos de lesiones bucales persistentes o sospechosas.' },
    { k: /(drenaje absceso|absceso dental|incision y drenaje)/i, a: 'Drenaje de absceso: elimina pus acumulado y reduce la presión; se acompaña de antibióticos y control clínico.' },
    { k: /(antibioticos dentales|amoxicilina|clindamicina)/i, a: 'Antibióticos: controlan infecciones bucales; su uso debe ser indicado por el odontólogo según el caso.' },
    { k: /(analgesicos|ibuprofeno|paracetamol|dolor dental)/i, a: 'Analgésicos: alivian el dolor dental o postoperatorio; deben tomarse según prescripción médica.' },
    { k: /(control postoperatorio|cuidados despues de extraccion)/i, a: 'Cuidados postoperatorios: no fumar ni enjuagar las primeras horas, dieta blanda y reposo.' },
    { k: /(quistes maxilares|quiste dentigero|quiste radicular)/i, a: 'Quistes maxilares: cavidades patológicas que contienen líquido; requieren diagnóstico y tratamiento quirúrgico.' },
    { k: /(fractura mandibular|maxilar|traumatismo facial)/i, a: 'Fracturas faciales: lesiones óseas que pueden afectar función y estética; requieren fijación y control.' },
    { k: /(colgajo quirurgico|cirugia de colgajo)/i, a: 'Colgajo quirúrgico: permite acceder al hueso o raíz; se reposiciona con suturas tras el procedimiento.' },
    { k: /(injerto oseo|biomaterial|regeneracion guiada)/i, a: 'Injerto óseo: rellena defectos del hueso con material natural o sintético para favorecer su regeneración.' },
    { k: /(osteotomia|osteoplastia|cirugia osea)/i, a: 'Osteotomía: corte controlado del hueso para extraer raíces o remodelar rebordes óseos.' },
    { k: /(implante dental|tornillo de titanio|osteointegracion)/i, a: 'Implante dental: tornillo de titanio colocado en el hueso que reemplaza la raíz de un diente perdido.' },
    { k: /(apicectomia|cirugia periapical)/i, a: 'Apicectomía: eliminación del extremo de la raíz dental y tejido inflamado tras un fallo endodóntico.' },
    { k: /(odontoseccion|extraccion quirurgica)/i, a: 'Odontosección: división del diente multirradicular para facilitar su extracción controlada.' },
    { k: /(ortodoncia|brackets|alineadores)/i, a: 'Ortodoncia: especialidad que corrige la posición de los dientes y mejora la mordida.' },
    { k: /(brackets metalicos|brackets ceramicos|autoligables)/i, a: 'Brackets: elementos adheridos a los dientes que guían su movimiento con alambres o ligaduras.' },
    { k: /(arco ortodontico|ligaduras|elastico)/i, a: 'Arco ortodóntico: alambre que aplica fuerzas sobre los dientes para moverlos progresivamente.' },
    { k: /(alineadores invisibles|alineadores dentales)/i, a: 'Alineadores: férulas transparentes removibles que alinean los dientes sin usar brackets visibles.' },
    { k: /(retenedor|retencion post ortodoncia)/i, a: 'Retenedor: mantiene los dientes en su nueva posición después del tratamiento ortodóntico.' },
    { k: /(expansor palatino|disyuntor|ortodoncia interceptiva)/i, a: 'Expansor palatino: ensancha el paladar en niños para corregir mordidas cruzadas.' },
    { k: /(maloclusion|clase i|clase ii|clase iii)/i, a: 'Maloclusión: desalineación dental; clase II mandíbula corta, clase III mandíbula adelantada.' },
    { k: /(apiñamiento dental|dientes torcidos|espacios)/i, a: 'Apiñamiento: falta de espacio que causa dientes encimados; se corrige con ortodoncia.' },
    { k: /(diastema|espacio interdental)/i, a: 'Diastema: separación entre dientes, común entre incisivos; puede cerrarse con ortodoncia o estética.' },
    { k: /(mordida abierta|mordida cruzada|mordida profunda)/i, a: 'Tipos de mordida: abierta (sin contacto), cruzada (arcadas desalineadas) o profunda (superior cubre inferior).' },
    { k: /(odontopediatria|odontologia infantil)/i, a: 'Odontopediatría: atiende la salud bucal de niños desde la erupción del primer diente hasta la adolescencia.' },
    { k: /(caries infantil|caries del biberon)/i, a: 'Caries del biberón: aparece por contacto prolongado con líquidos azucarados durante el sueño.' },
    { k: /(selladores pediatricos|sellador infantil)/i, a: 'Selladores pediátricos: protegen los molares recién erupcionados del riesgo de caries temprana.' },
    { k: /(fluor infantil|fluor topico ninos)/i, a: 'Flúor infantil: se aplica en forma de gel o barniz para fortalecer los dientes de los niños.' },
    { k: /(trauma dental infantil|avulsion diente de leche)/i, a: 'Trauma dental infantil: en dientes permanentes reimplantar; en temporales no se reimplantan.' },
    { k: /(pulpotomia|pulpectomia|tratamiento pulpar infantil)/i, a: 'Pulpotomía: elimina la pulpa afectada de la corona conservando la raíz; pulpectomía la retira completamente.' },
    { k: /(comportamiento infantil|manejo del nino|tecnicas de conducta)/i, a: 'Manejo de conducta: técnicas como decir-mostrar-hacer y refuerzo positivo ayudan a la cooperación del niño.' },
    { k: /(educacion infantil|ensenar cepillado ninos)/i, a: 'Educación infantil: enseñar cepillado y hábitos saludables desde pequeños forma adultos con buena salud bucal.' },
    { k: /(odontologia para discapacitados|paciente especial)/i, a: 'Odontología para pacientes especiales: adapta el entorno y las técnicas según las capacidades del paciente.' },
    { k: /(sedacion consciente|oxido nitroso|anestesia general)/i, a: 'Sedación consciente: reduce ansiedad y miedo sin pérdida de conciencia, usada con control profesional.' },
    { k: /(bioseguridad dental|control de infecciones|normas de seguridad)/i, a: 'Bioseguridad: medidas para prevenir transmisión de enfermedades en el consultorio dental.' },
    { k: /(esterilizacion instrumental|autoclave dental)/i, a: 'Esterilización: destruye microorganismos mediante vapor o calor seco, esencial para evitar infecciones cruzadas.' },
    { k: /(desinfeccion de superficies|limpieza de gabinete)/i, a: 'Desinfección: elimina bacterias de superficies con alcohol o cloro; debe hacerse entre cada paciente.' },
    { k: /(lavado de manos clinico|antisepsia)/i, a: 'Lavado clínico: limpiar las manos con jabón antiséptico antes y después de cada atención.' },
    { k: /(control de aerosoles|barreras plasticas|proteccion facial)/i, a: 'Control de aerosoles: usar succión potente, barreras plásticas y mascarilla N95 durante procedimientos.' },
    { k: /(residuos biologicos|basura hospitalaria)/i, a: 'Residuos biológicos: separar en bolsas de color; agujas y bisturís en contenedores resistentes.' },
    { k: /(vacunacion del personal|hepatitis b|tetanica)/i, a: 'Vacunación del personal: protege contra hepatitis B, tétanos e influenza, fundamentales en la práctica clínica.' },
    { k: /(accidente biologico|puncion accidental|exposicion sangre)/i, a: 'Accidente biológico: lavar con agua y jabón, reportar y seguir el protocolo de atención inmediata.' },
    { k: /(ventilacion consultorio|control ambiental)/i, a: 'Ventilación: mantener aire limpio y renovado reduce el riesgo de transmisión aérea.' },
    { k: /(gestionar residuos|reciclaje dental)/i, a: 'Gestión de residuos: separar desechos reciclables, biológicos y cortopunzantes siguiendo normas sanitarias.' },
    { k: /(instrumental esteril|bolsas esterilizacion)/i, a: 'El instrumental debe mantenerse en su bolsa cerrada hasta el momento del uso.' },
    { k: /(historia clinica dental|registro del paciente)/i, a: 'Historia clínica: documento legal que registra los datos, diagnósticos y tratamientos del paciente.' },
    { k: /(consentimiento informado|autorizacion del paciente)/i, a: 'Consentimiento informado: permiso firmado por el paciente tras recibir información sobre su tratamiento.' },
    { k: /(urgencias dentales|dolor intenso|absceso dental)/i, a: 'Urgencias dentales: dolor severo, infecciones o traumatismos que requieren atención inmediata.' },
    { k: /(emergencia medica|paro cardiorrespiratorio|rcp)/i, a: 'Emergencia médica: aplicar RCP básica y activar servicios de emergencia sin demora.' },
    { k: /(botiquin de emergencia|kit de urgencia)/i, a: 'Botiquín dental: contiene medicamentos básicos, oxígeno y material para atender emergencias.' },
    { k: /(manejo de ansiedad|paciente nervioso)/i, a: 'Manejo de ansiedad: se logra con comunicación empática y técnicas de relajación.' },
    { k: /(comunicacion profesional|empatia|trato humano)/i, a: 'La empatía y comunicación clara fortalecen la confianza entre odontólogo y paciente.' },
    { k: /(educacion continua|actualizacion profesional)/i, a: 'Educación continua: mantiene actualizado al odontólogo en técnicas, materiales y normas de seguridad.' },
    { k: /(salud ocupacional|ergonomia laboral)/i, a: 'Salud ocupacional: previene lesiones musculares mediante pausas activas y postura correcta.' },
    { k: /(estres laboral|fatiga profesional)/i, a: 'Estrés laboral: frecuente en odontología; se previene con descanso, organización y apoyo emocional.' },
    { k: /(control de calidad dental|auditoria clinica)/i, a: 'Control de calidad: asegura cumplimiento de normas y satisfacción del paciente.' },
    { k: /(documentacion clinica|registro adecuado)/i, a: 'La documentación adecuada respalda la atención clínica y evita errores o repeticiones.' },
    { k: /(odontologia digital|escaneo intraoral|cad cam)/i, a: 'Odontología digital: integra escáneres 3D y diseño asistido por computadora para restauraciones precisas.' },
    { k: /(radiologia digital|cbct|tomografia cone beam)/i, a: 'Radiología digital: permite imágenes tridimensionales precisas con menor exposición a radiación.' },
    { k: /(laser dental|terapia laser|cirugia laser)/i, a: 'Láser dental: corta, coagula o desinfecta tejidos con mínima invasión y mejor recuperación.' },
    { k: /(ozonoterapia dental|ozono)/i, a: 'Ozono dental: desinfecta y estimula la cicatrización en caries incipientes o tratamientos periodontales.' },
    { k: /(fotografia intraoral|documentacion fotografica)/i, a: 'Fotografía intraoral: registra el estado inicial y final de los tratamientos; útil para seguimiento y educación.' },
    { k: /(control de infecciones cruzadas|protocolos sanitarios)/i, a: 'El control de infecciones cruzadas se logra con esterilización rigurosa y barreras desechables.' },
    { k: /(odontologia estetica|carillas|blanqueamiento)/i, a: 'Odontología estética: mejora la apariencia de los dientes mediante carillas, resinas o blanqueamiento.' },
    { k: /(blanqueamiento dental|peroxido de hidrogeno)/i, a: 'Blanqueamiento dental: aclara el color del esmalte con agentes oxidantes aplicados por el odontólogo.' },
    { k: /(microabrasion|manchas de esmalte)/i, a: 'Microabrasión: elimina manchas superficiales del esmalte con una mezcla abrasiva y ácida controlada.' },
    { k: /(recontorneado dental|ajuste estetico)/i, a: 'Recontorneado: desgaste leve del esmalte para mejorar forma y simetría de los dientes.' },
    { k: /(carillas de porcelana|laminados esteticos)/i, a: 'Carillas: finas láminas de porcelana o resina que mejoran la estética del frente dental.' },
    { k: /(fluor topico profesional|aplicacion de gel)/i, a: 'Flúor tópico profesional: refuerza el esmalte y previene caries, aplicado cada seis meses.' },
    { k: /(educacion comunitaria|programas escolares)/i, a: 'Educación comunitaria: promueve hábitos saludables y revisiones regulares en la población.' },
    { k: /(odontologia preventiva comunitaria|brigadas dentales)/i, a: 'Odontología comunitaria: acerca la atención y prevención a zonas rurales o escolares.' },
    { k: /(prevencion en adultos mayores|cuidado protesis)/i, a: 'Prevención en adultos mayores: incluye limpieza de prótesis, revisiones y control de sequedad bucal.' },
    { k: /(salud oral y general|enfermedades sistemicas)/i, a: 'La salud oral influye directamente en la salud general; las infecciones bucales pueden afectar otros órganos.' },
    { k: /(educacion para la salud oral|promocion integral)/i, a: 'La educación para la salud fomenta hábitos sostenibles de higiene y prevención de enfermedades bucales.' }
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
      b.addEventListener('click', ()=>{ input.value = label; input.focus(); });
      suggests.appendChild(b);
      if (used.size >= n) break;
    }
  }
  // Cargar muchos temas desde backend
  fetch('{{ route('bot.topics') }}').then(r=>r.ok?r.json():null).then(j=>{ if(j&&Array.isArray(j.topics)) { TOPIC_POOL = j.topics; renderRandomSuggests(); } }).catch(()=>{});
  // Greeting aleatorio único
  if (greet) { const g = GREETINGS[Math.floor(Math.random()*GREETINGS.length)]; greet.textContent = g; }
  function open(){ 
    panel.style.display='block'; 
    panel.removeAttribute('aria-hidden');
    try { panel.inert = false; } catch(_) {}
    toggle.setAttribute('aria-expanded','true');
    setTimeout(()=>input.focus(),100); 
  }
  function close(){ 
    panel.style.display='none'; 
    panel.setAttribute('aria-hidden','true');
    try { panel.inert = true; } catch(_) {}
    toggle.setAttribute('aria-expanded','false'); 
  }
  // Ensure initial closed state is inert
  try { if (panel.getAttribute('aria-hidden') !== 'false') panel.inert = true; } catch(_) {}
  toggle.addEventListener('click', (e)=>{ e.stopPropagation(); const show = panel.style.display!=='block'; if (show) { renderRandomSuggests(); } show?open():close(); });
  closeBtn.addEventListener('click', (e)=>{ e.stopPropagation(); close(); });
  form.addEventListener('submit', (e)=>{ e.preventDefault(); e.stopPropagation(); const t = input.value.trim(); if(!t) return; sendUser(t); input.value=''; reply(t); });
  // Capturing listener as fallback in case other scripts stop bubbling
  form.addEventListener('submit', (e)=>{ e.preventDefault(); e.stopPropagation(); const t = input.value.trim(); if(!t) return; sendUser(t); input.value=''; reply(t); }, true);
  // Enter to send from input
  input.addEventListener('keydown', (e)=>{ if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); const t = input.value.trim(); if(!t) return; sendUser(t); input.value=''; reply(t); } });
  if (sendBtn) {
    sendBtn.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); const t = input.value.trim(); if(!t) return; sendUser(t); input.value=''; reply(t); });
  }
  // Delegated fallback (capturing) to ensure clicks are handled even if direct binding fails
  document.addEventListener('click', (e)=>{
    const btn = e.target && (e.target.id === 'bf-send' ? e.target : (e.target.closest ? e.target.closest('#bf-send') : null));
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    const t = input.value.trim();
    if (!t) return;
    sendUser(t);
    input.value = '';
    reply(t);
  }, true);
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
      const sUrl = '{{ route('bot.search') }}' + '?q=' + encodeURIComponent(t);
      const r = await fetch(sUrl);
      if (r.ok) {
        const data = await r.json();
        const items = Array.isArray(data.results) ? data.results.slice(0, 3) : [];
        for (const it of items) {
          const title = it.title || 'Documento relacionado';
          const snippet = (it.snippet || '').toString().trim();
          addMsg(title + (snippet ? ' · ' + snippet : ''), 'bf-bot');
        }
      }
      const aiUrl = '{{ route('bot.ask') }}';
      const aiResp = await fetch(aiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ q: t })
      });
      
    } catch(e) {
      addMsg('Ocurrió un error al procesar tu consulta.', 'bf-bot');
    }
  }
  function answer(q){ return ''; }
})();
</script>
