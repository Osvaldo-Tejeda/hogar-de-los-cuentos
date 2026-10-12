<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/cuentos.php';
require_once __DIR__ . '/../php/ia.php';
exigir_admin();
$id = (int)($_GET['id'] ?? 0);
$c = $id ? obtener_cuento($id) : null;
if ($id && !$c) redirigir('admin/index.php');
$textoActual = $c ? texto_plano($id) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    try {
        $texto = trim($_POST['texto'] ?? '');
        if (!empty($_FILES['archivo_texto']['name'])) $texto = trim(file_get_contents($_FILES['archivo_texto']['tmp_name']));
        if (trim($_POST['titulo'] ?? '') === '' || $texto === '') throw new RuntimeException('El título y el texto son obligatorios.');
        $campos = [trim($_POST['titulo']), trim($_POST['autor'] ?? ''), trim($_POST['categoria'] ?? ''), $_POST['edad'] ?? '',
                   $_POST['idioma'] ?? 'es', $_POST['nivel'] ?? '', max(0, (int)($_POST['duracion'] ?? 0))];
        if ($c) {
            db()->prepare('UPDATE cuentos SET titulo=?, autor=?, categoria=?, edad_recomendada=?, idioma=?, nivel_lectura=?, duracion_min=? WHERE id=?')
                ->execute([...$campos, $id]);
        } else {
            db()->prepare('INSERT INTO cuentos (titulo, autor, categoria, edad_recomendada, idioma, nivel_lectura, duracion_min, id_tecnico) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([...$campos, admin_actual()['id']]);
            $id = (int)db()->lastInsertId();
        }
        // El texto solo se reemplaza si cambió (así no se pierden los minutos ya sincronizados)
        $nuevoTexto = !$c || preg_replace('/\s+/', ' ', $texto) !== preg_replace('/\s+/', ' ', $textoActual);
        if ($nuevoTexto) { guardar_fragmentos($id, $texto); guardar_preguntas($id, ia_preguntas($campos[0], $texto)); }

        // Audio: subido o generado con IA
        $rutaAudio = null; $origen = 'subido';
        if (!empty($_FILES['audio']['name'])) $rutaAudio = subir_archivo($_FILES['audio'], 'audio');
        elseif (($_POST['audio_ia'] ?? '') === '1') { $rutaAudio = ia_tts($texto); $origen = 'generado_ia'; }
        if ($rutaAudio) {
            if ($c && $c['audio']) { borrar_archivo($c['audio']['ruta_archivo']); db()->prepare('DELETE FROM cuentos_audio WHERE id_cuento=?')->execute([$id]); }
            db()->prepare('INSERT INTO cuentos_audio (id_cuento, ruta_archivo, origen) VALUES (?,?,?)')->execute([$id, $rutaAudio, $origen]);
        }
        flash('Cuento guardado. Ahora agregue las imágenes y asigne los minutos.');
        redirigir('admin/sincronizar.php?id=' . $id);
    } catch (Throwable $ex) { flash($ex->getMessage(), 'error'); }
}
$titulo = $c ? 'Editar cuento' : 'Nuevo cuento';
include __DIR__ . '/../html/head.php';
include __DIR__ . '/../html/admin_form.php';
include __DIR__ . '/../html/foot.php';
