<?php
require_once __DIR__ . '/php/cuentos.php';
$c = obtener_cuento((int)($_GET['id'] ?? 0));
if (!$c) { http_response_code(404); exit('Cuento no encontrado.'); }
db()->prepare('UPDATE cuentos SET vistas = vistas + 1 WHERE id=?')->execute([$c['id']]);
$datos = [
    'audio' => $c['audio'] ? url($c['audio']['ruta_archivo']) : null,
    'texto' => array_map(fn($t) => ['texto' => $t['contenido'], 'seg' => (int)$t['segundo_inicio']], $c['texto']),
    'imagenes' => array_map(fn($i) => ['src' => url($i['ruta_archivo']), 'alt' => $i['texto_alternativo'], 'seg' => (int)$i['segundo_inicio']], $c['imagenes']),
];
$titulo = $c['titulo']; $scripts = ['reproductor.js'];
include __DIR__ . '/html/head.php';
include __DIR__ . '/html/cuento.php';
include __DIR__ . '/html/foot.php';
