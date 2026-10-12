<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/cuentos.php';
require_once __DIR__ . '/../php/ia.php';
exigir_admin();
$id = (int)($_GET['id'] ?? 0);
$c = obtener_cuento($id);
if (!$c) redirigir('admin/index.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    if (isset($_POST['regenerar'])) { guardar_preguntas($id, ia_preguntas($c['titulo'], texto_plano($id))); flash('Preguntas regeneradas.'); }
    else {
        $p = [];
        foreach (['literal', 'inferencial', 'reflexiva'] as $t) $p[$t] = trim($_POST[$t] ?? '');
        if (in_array('', $p, true)) flash('Las 3 preguntas son obligatorias.', 'error');
        else { guardar_preguntas($id, $p); flash('Preguntas guardadas.'); }
    }
    redirigir('admin/preguntas.php?id=' . $id);
}
$pp = array_column($c['preguntas'], 'pregunta', 'tipo');
$titulo = 'Preguntas de comprensión';
include __DIR__ . '/../html/head.php';
include __DIR__ . '/../html/admin_preguntas.php';
include __DIR__ . '/../html/foot.php';
