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
            $payload = [
                'contents' => [ [ 'role' => 'user', 'parts' => [ ['text' => $sys."\n\n".$prompt] ] ] ],
                'generationConfig' => [ 'temperature' => 0.4, 'maxOutputTokens' => 512 ],
            ];
            $preferred = env('GEMINI_MODEL');
            $candidates = array_values(array_filter([
                $preferred,
                'gemini-1.5-flash',
                'gemini-1.5-flash-001',
                'gemini-1.5-flash-latest',
                'gemini-1.5-pro',
                'gemini-1.5-pro-latest',
                'gemini-1.0-pro-001'
            ]));
            $lastError = null;
            foreach ($candidates as $model) {
                $endpoint = 'https://generativelanguage.googleapis.com/v1/models/'.$model.':generateContent?key='.$apiKey;
                $resp = Http::timeout(20)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($endpoint, $payload);
                if ($resp->ok()) {
                    $json = $resp->json();
                    $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if (!$text) { $text = 'No obtuve respuesta. Inténtalo de nuevo con otra redacción.'; }
                    return response()->json(['answer' => $text, 'model' => $model]);
                } else {
                    $detail = $resp->json();
                    $msg = $detail['error']['message'] ?? (is_string($detail) ? $detail : '');
                    $lastError = 'Model '.$model.': '.$msg;
                    // try next model
                }
            }
            return response()->json(['error' => 'Gemini request failed','message' => $lastError ?: 'All models failed'], 502);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gemini error','message'=>$e->getMessage()], 500);
        }
    }
}
