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
}
