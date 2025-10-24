<?php

namespace App\Http\Controllers;

use App\Models\BotDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BotKnowledgeController extends Controller
{
    public function index()
    {
        $docs = BotDoc::orderByDesc('created_at')->paginate(15);
        return view('admin.bot_knowledge', compact('docs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'pdf' => ['required','file','mimes:pdf','max:51200'], // 50MB
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string','max:2000'],
            'tags' => ['nullable','string','max:200'],
        ]);
        $file = $data['pdf'];
        $path = $file->store('bot_docs/'.$user->id, 'public');
        $content = null; $autoTitles = null;
        try {
            // Intentar extraer texto si existe un parser
            if (class_exists(\Smalot\PdfParser\Parser::class)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile(Storage::disk('public')->path($path));
                $content = trim((string)$pdf->getText());
                $autoTitles = $this->generateAutoTitles($content, $data['title']);
            }
        } catch (\Throwable $e) {
            // ignore extraction errors
        }
        BotDoc::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'tags' => $data['tags'] ?? null,
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'content' => $content,
            'auto_titles' => $autoTitles,
        ]);
        return redirect()->route('admin.bot.knowledge')->with('ok', 'PDF agregado');
    }

    public function destroy(BotDoc $doc)
    {
        if ($doc->path) Storage::disk('public')->delete($doc->path);
        $doc->delete();
        return redirect()->route('admin.bot.knowledge')->with('ok', 'PDF eliminado');
    }

    public function search(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        if ($q === '') return response()->json(['results'=>[]]);
        $results = BotDoc::query()
            ->when($q, function($qb) use ($q){
                $qb->where(function($qq) use ($q){
                    $qq->where('title','like','%'.$q.'%')
                       ->orWhere('description','like','%'.$q.'%')
                       ->orWhere('tags','like','%'.$q.'%')
                       ->orWhere('content','like','%'.$q.'%');
                });
            })
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($d) use ($q){
                // Build a snippet around the first occurrence of query terms
                $snippet = null;
                if (!empty($d->content)) {
                    $text = trim($d->content);
                    $pos = null;
                    $needles = array_filter(array_map('trim', preg_split('/\s+/', $q)));
                    foreach ($needles as $needle) {
                        $p = mb_stripos($text, $needle);
                        if ($p !== false) { $pos = $p; break; }
                    }
                    if ($pos === null) { $pos = 0; }
                    $start = max(0, $pos - 100);
                    $len = 260;
                    $snippet = ($start > 0 ? '…' : '') . mb_substr($text, $start, $len) . '…';
                    $snippet = preg_replace('/\s+/', ' ', $snippet);
                }
                return [
                    'id' => $d->id,
                    'title' => $d->title,
                    'description' => $d->description,
                    'tags' => $d->tags,
                    'auto_titles' => $d->auto_titles,
                    'snippet' => $snippet,
                    'url' => asset('storage/'.$d->path),
                ];
            });
        return response()->json(['results' => $results]);
    }

    private function generateAutoTitles(?string $content, string $fallbackTitle): array
    {
        if (!$content) return [$fallbackTitle];
        $lines = preg_split('/\r?\n/', $content);
        $candidates = [];
        foreach ($lines as $line) {
            $t = trim($line);
            if ($t === '' || mb_strlen($t) < 6) continue;
            // Titulares probables: líneas cortas tipo encabezado
            if (preg_match('/^(capítulo|sección|tema|resumen|introducción|conclusión)/i', $t) || mb_strlen($t) <= 60) {
                $candidates[] = $t;
            }
            if (count($candidates) >= 6) break;
        }
        if (empty($candidates)) $candidates[] = $fallbackTitle;
        // Unicos y recortados
        $candidates = array_values(array_unique(array_map(function($s){ return mb_substr($s,0,80); }, $candidates)));
        return $candidates;
    }

    public function topics(Request $request)
    {
        // Build a large pool of titles from auto_titles, titles and tags
        $docs = BotDoc::select(['title','auto_titles','tags'])->orderByDesc('created_at')->get();
        $pool = [];
        foreach ($docs as $d) {
            if (is_array($d->auto_titles)) {
                foreach ($d->auto_titles as $t) { if ($t) $pool[] = trim($t); }
            }
            if ($d->title) $pool[] = trim($d->title);
            if ($d->tags) {
                foreach (explode(',', $d->tags) as $tg) { $tg = trim($tg); if ($tg) $pool[] = $tg; }
            }
        }
        // Uniques, limit to 300
        $pool = array_values(array_unique(array_filter($pool)));
        // Keep strings not too short and not too long
        $pool = array_values(array_filter($pool, function($s){ $l = mb_strlen($s); return $l >= 4 && $l <= 120; }));
        // If not enough, add some default dental topics
        if (count($pool) < 220) {
            $defaults = [
                'Caries dental: diagnóstico y prevención','FDI: nomenclatura dental','Técnicas de cepillado dental','Índices CPO-D y CEO-D','Gingivitis y periodontitis','Radiología en odontología preventiva','Ortodoncia interceptiva','Selladores de fosas y fisuras','Cáncer bucal: factores de riesgo','Profilaxis y control de placa','Fluoruros tópicos','Higiene oral en niños','Bruxismo: manejo clínico','Hipoplasia del esmalte','Traumatismos dentales','Enfermedades pulpares','Endodoncia: indicaciones','Exodoncia: consideraciones','Materiales restauradores','Aislamiento absoluto','Anestesia local en odontología','Control del dolor dental','Halitosis: causas y manejo','Lesiones de mucosa oral','Candidiasis oral','Xerostomía','Desgaste dentario','Erosión dental','Maloclusiones: clasificación','Mantenedores de espacio','Dietas y caries','Educación para la salud bucal','Enjuagues bucales','Placa bacteriana y sarro','Bolsas periodontales','Tinciones dentales','Hipersensibilidad dentinaria','Rehabilitación oral básica','Anclaje ortodóncico','Teleodontología básica','Bioseguridad en clínica','Instrumental odontológico','Consentimiento informado','Historia clínica odontológica','Odontología preventiva en embarazadas','Periodoncia no quirúrgica','Raspado y alisado radicular','Profilaxis antibiótica en odontología','Lesiones blancas y rojas','Leucoplasia y eritroplasia','Úlceras orales','Aftas recurrentes','Medicina oral tópicos','HPV y cavidad oral','Tabaquismo y salud bucal','Diabetes y enfermedad periodontal','Farmacología en odontología','Antibióticos de uso odontológico','Analgesia en odontología','Emergencias odontológicas','Odontopediatría: manejo conductual','Erupción dental','Dentición mixta','Sellado de surcos en niños','Caries rampante','Sialometría y saliva','Microbiota bucal','Control químico de placa','Clorhexidina: indicaciones','Índice de higiene oral','PMA y Russell','Exámenes complementarios','Fotografía clínica en odontología','Radiografías bite-wing','Panorámica vs periapical','Protección radiológica','Prostodoncia parcial','Encerado diagnóstico','Brackets vs alineadores','Retenedores','Hábitos orales nocivos','Deleciones de espacio','Apiñamiento dental','Malposiciones dentarias','Lesiones no cariosas','Amalgama vs resina','Ionómero de vidrio','Cavitación inicial','Manejo mínimamente invasivo','Odontología basada en la evidencia','Plan de tratamiento','Pronóstico en odontología','Seguimiento y control','Registro odontológico','Odontología comunitaria','Epidemiología bucal','Vigilancia epidemiológica',
            ];
            $need = 240 - count($pool);
            if ($need > 0) $pool = array_merge($pool, array_slice($defaults, 0, max(0,$need)));
        }
        // Limit and shuffle
        shuffle($pool);
        $pool = array_slice($pool, 0, 300);
        return response()->json(['topics' => $pool]);
    }
}
