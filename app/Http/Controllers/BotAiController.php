<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotAiController extends Controller
{
    public function ask(Request $request)
    {
        $data = $request->validate([
            'q' => ['required','string','max:4000']
        ]);
        $apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API key not configured'], 500);
        }
        $prompt = trim($data['q']);
        // System style instruction to keep domain dental and concise
        $sys = "Eres Bot‑FRADADI, asistente odontológico. Responde de forma breve, clara y con enfoque clínico preventivo. Si no estás seguro, recomienda evaluación profesional.";
        try {
            $resp = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key='.$apiKey, [
                    'contents' => [
                        ['role' => 'user', 'parts' => [ ['text' => $sys."\n\nPregunta: ".$prompt ] ]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 512,
                    ],
                    'safetySettings' => []
                ]);
            if (!$resp->ok()) {
                return response()->json(['error' => 'Gemini request failed','detail'=>$resp->body()], 502);
            }
            $json = $resp->json();
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if (!$text) { $text = 'No obtuve respuesta. Inténtalo de nuevo con otra redacción.'; }
            return response()->json(['answer' => $text]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gemini error','detail'=>$e->getMessage()], 500);
        }
    }
}
