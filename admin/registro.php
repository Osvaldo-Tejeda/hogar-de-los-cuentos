<?php
require_once __DIR__ . '/../php/auth.php';
if (admin_actual()) redirigir('admin/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $error = registrar_usuario(trim($_POST['usuario'] ?? ''), $_POST['clave'] ?? '', $_POST['clave2'] ?? '', $_POST['codigo'] ?? '');
    if ($error === null) { flash('Cuenta creada. ¡Bienvenido!'); redirigir('admin/index.php'); }
}
$titulo = 'Crear cuenta';
include __DIR__ . '/../html/head.php';
include __DIR__ . '/../html/admin_registro.php';
include __DIR__ . '/../html/foot.php';
