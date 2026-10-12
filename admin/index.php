<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/cuentos.php';
exigir_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {   // baja de cuento
    csrf_validar();
    $c = obtener_cuento((int)$_POST['borrar']);
    if ($c) {
        foreach ($c['imagenes'] as $i) borrar_archivo($i['ruta_archivo']);
        if ($c['audio']) borrar_archivo($c['audio']['ruta_archivo']);
        db()->prepare('DELETE FROM cuentos WHERE id=?')->execute([$c['id']]);
        flash('Cuento eliminado.');
    }
    redirigir('admin/index.php');
}
$cuentos = buscar_cuentos();
$titulo = 'Panel de administración';
include __DIR__ . '/../html/head.php';
include __DIR__ . '/../html/admin_lista.php';
include __DIR__ . '/../html/foot.php';
