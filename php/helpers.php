<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $ruta = ''): string { return BASE_URL . '/' . ltrim($ruta, '/'); }
function redirigir(string $ruta): void { header('Location: ' . url($ruta)); exit; }

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_campo(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_validar(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403); exit('Token de seguridad inválido. Recargue la página.');
    }
}

function flash(?string $msg = null, string $tipo = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $tipo]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}

// Divide el texto en fragmentos (párrafos separados por línea en blanco)
function dividir_fragmentos(string $texto): array {
    $partes = preg_split('/\R{2,}/u', trim($texto));
    return array_values(array_filter(array_map('trim', $partes), fn($p) => $p !== ''));
}

// Valida y guarda un archivo subido. $tipo: audio | imagenes | texto. Devuelve ruta relativa.
function subir_archivo(array $f, string $tipo): string {
    $reglas = [
        'audio'    => ['ext' => ['mp3','wav','ogg'], 'mb' => MAX_AUDIO_MB],
        'imagenes' => ['ext' => ['jpg','jpeg','png','webp','gif'], 'mb' => MAX_IMG_MB],
        'texto'    => ['ext' => ['txt'], 'mb' => 2],
    ][$tipo];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Error al subir el archivo.');
    if ($f['size'] > $reglas['mb'] * 1048576) throw new RuntimeException("El archivo supera {$reglas['mb']} MB.");
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $reglas['ext'], true)) throw new RuntimeException('Formato no permitido: .' . $ext);
    $dir = UPLOAD_DIR . '/' . $tipo;
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $nombre = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], "$dir/$nombre")) throw new RuntimeException('No se pudo guardar el archivo.');
    return "uploads/$tipo/$nombre";
}

function borrar_archivo(string $ruta): void {
    $real = realpath(RAIZ . '/' . $ruta);
    if ($real && strpos($real, realpath(UPLOAD_DIR)) === 0) @unlink($real);
}

// Tiempo escrito como "1:23", "01:23", "1:02:03" o solo segundos ("83") -> segundos
function parse_tiempo($v): int {
    $v = trim((string)$v);
    if ($v === '') return 0;
    if (preg_match('/^\d+([.,]\d+)?$/', $v)) return (int)floor((float)str_replace(',', '.', $v));
    if (preg_match('/^(?:(\d+):)?(\d{1,2}):(\d{1,2})$/', $v, $m)) return ((int)$m[1]) * 3600 + ((int)$m[2]) * 60 + (int)$m[3];
    return 0;
}
function fmt_tiempo(int $s): string {
    $h = intdiv($s, 3600); $m = intdiv($s % 3600, 60); $x = $s % 60;
    return $h ? sprintf('%d:%02d:%02d', $h, $m, $x) : sprintf('%d:%02d', $m, $x);
}
