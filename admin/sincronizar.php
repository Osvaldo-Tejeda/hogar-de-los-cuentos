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
    try {
        $pdo = db();
        // tiempos del texto (formato min:seg)
        $u = $pdo->prepare('UPDATE cuentos_texto SET segundo_inicio=? WHERE id=? AND id_cuento=?');
        foreach (($_POST['t_texto'] ?? []) as $tid => $t) $u->execute([parse_tiempo($t), (int)$tid, $id]);
        // tiempos / alt / baja de imágenes existentes
        foreach (($_POST['t_img'] ?? []) as $iid => $t) {
            $iid = (int)$iid;
            if (isset($_POST['borrar_img'][$iid])) {
                foreach ($c['imagenes'] as $im) if ($im['id'] == $iid) borrar_archivo($im['ruta_archivo']);
                $pdo->prepare('DELETE FROM cuentos_imagenes WHERE id=? AND id_cuento=?')->execute([$iid, $id]);
                continue;
            }
            $alt = trim($_POST['alt_img'][$iid] ?? '');
            if ($alt === '') throw new RuntimeException('Toda imagen necesita su texto alternativo.');
            $pdo->prepare('UPDATE cuentos_imagenes SET segundo_inicio=?, texto_alternativo=? WHERE id=? AND id_cuento=?')->execute([parse_tiempo($t), $alt, $iid, $id]);
        }
        // imágenes nuevas (subidas) o generadas con IA
        $ins = $pdo->prepare('INSERT INTO cuentos_imagenes (id_cuento, ruta_archivo, origen, texto_alternativo, segundo_inicio, orden_escena) VALUES (?,?,?,?,?,?)');
        $nuevas = $_FILES['nueva_img'] ?? null;
        if ($nuevas && is_array($nuevas['name'])) foreach ($nuevas['name'] as $k => $nombre) {
            if ($nombre === '') continue;
            $alt = trim($_POST['nueva_alt'][$k] ?? '');
            if ($alt === '') throw new RuntimeException("Escriba el texto alternativo de la imagen «$nombre».");
            $f = ['name' => $nombre, 'tmp_name' => $nuevas['tmp_name'][$k], 'size' => $nuevas['size'][$k], 'error' => $nuevas['error'][$k]];
            $ins->execute([$id, subir_archivo($f, 'imagenes'), 'subido', $alt, parse_tiempo($_POST['nueva_t'][$k] ?? ''), $k]);
        }
        if (trim($_POST['desc_ia'] ?? '') !== '') {
            $ins->execute([$id, ia_imagen(trim($_POST['desc_ia'])), 'generado_ia', trim($_POST['desc_ia']), parse_tiempo($_POST['t_ia'] ?? ''), 0]);
        }
        flash('Sincronización guardada.');
    } catch (Throwable $ex) { flash($ex->getMessage(), 'error'); }
    redirigir('admin/sincronizar.php?id=' . $id);
}
$titulo = 'Sincronizar';
$scripts = ['sincronizar.js'];
include __DIR__ . '/../html/head.php';
include __DIR__ . '/../html/admin_sync.php';
include __DIR__ . '/../html/foot.php';
