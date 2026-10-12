<?php
// Integración con IA vía cURL (compatible con APIs estilo OpenAI). Todo es opcional.
require_once __DIR__ . '/helpers.php';

function ia_disponible(): bool { return IA_API_KEY !== ''; }

function ia_curl(string $ruta, ?array $json = null, ?array $multipart = null): string {
    $ch = curl_init(IA_BASE_URL . $ruta);
    $headers = ['Authorization: Bearer ' . IA_API_KEY];
    if ($json !== null) { $headers[] = 'Content-Type: application/json'; curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json)); }
    if ($multipart !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $multipart);
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120]);
    $r = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($r === false || $code >= 400) throw new RuntimeException("Error de IA ($code) $err");
    return $r;
}

// Devuelve ['literal'=>..., 'inferencial'=>..., 'reflexiva'=>...] (siempre exactamente 3)
function ia_preguntas(string $titulo, string $texto): array {
    if (ia_disponible()) {
        try {
            $prompt = "Lee este cuento y genera EXACTAMENTE 3 preguntas de comprensión basadas solo en el texto: "
                . "una literal (qué pasó), una inferencial (por qué pasó) y una reflexiva (qué opinas/qué harías). "
                . "Responde solo JSON: {\"literal\":\"...\",\"inferencial\":\"...\",\"reflexiva\":\"...\"}\n\nTítulo: $titulo\n\n" . mb_substr($texto, 0, 12000);
            $r = json_decode(ia_curl('/chat/completions', [
                'model' => IA_MODELO_TEXTO,
                'response_format' => ['type' => 'json_object'],
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]), true);
            $p = json_decode($r['choices'][0]['message']['content'] ?? '', true);
            if (!empty($p['literal']) && !empty($p['inferencial']) && !empty($p['reflexiva'])) {
                return ['literal' => $p['literal'], 'inferencial' => $p['inferencial'], 'reflexiva' => $p['reflexiva']];
            }
        } catch (Throwable $ex) { /* cae al método local */ }
    }
    return [
        'literal' => "¿Qué sucede en el cuento «$titulo»? Cuenta con tus palabras lo que pasó.",
        'inferencial' => "¿Por qué crees que los personajes actuaron como lo hicieron en «$titulo»?",
        'reflexiva' => "¿Qué opinas del final de «$titulo»? ¿Qué habrías hecho tú en su lugar?",
    ];
}

// Texto a voz: genera un MP3 y devuelve la ruta relativa
function ia_tts(string $texto): string {
    if (!ia_disponible()) throw new RuntimeException('Configure IA_API_KEY en php/config.php para generar audio con IA.');
    $mp3 = '';
    foreach (dividir_fragmentos($texto) as $frag) {
        foreach (mb_str_split($frag, 3800) as $trozo) {
            $mp3 .= ia_curl('/audio/speech', ['model' => IA_MODELO_TTS, 'voice' => 'alloy', 'input' => $trozo, 'response_format' => 'mp3']);
        }
    }
    $rel = 'uploads/audio/' . bin2hex(random_bytes(8)) . '.mp3';
    file_put_contents(RAIZ . '/' . $rel, $mp3);
    return $rel;
}

// Voz a texto: transcribe un audio ya subido
function ia_stt(string $rutaRel): string {
    if (!ia_disponible()) throw new RuntimeException('Configure IA_API_KEY para transcribir.');
    $r = json_decode(ia_curl('/audio/transcriptions', null, [
        'model' => IA_MODELO_STT, 'file' => new CURLFile(RAIZ . '/' . $rutaRel),
    ]), true);
    return $r['text'] ?? '';
}

// Genera una ilustración a partir de una descripción
function ia_imagen(string $descripcion): string {
    if (!ia_disponible()) throw new RuntimeException('Configure IA_API_KEY para generar imágenes.');
    $r = json_decode(ia_curl('/images/generations', [
        'model' => IA_MODELO_IMAGEN, 'prompt' => $descripcion, 'size' => '1792x1024', 'response_format' => 'b64_json',
    ]), true);
    $rel = 'uploads/imagenes/' . bin2hex(random_bytes(8)) . '.png';
    file_put_contents(RAIZ . '/' . $rel, base64_decode($r['data'][0]['b64_json'] ?? ''));
    return $rel;
}
