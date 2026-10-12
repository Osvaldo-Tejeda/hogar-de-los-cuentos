<?php
require_once __DIR__ . '/../php/auth.php';
if (admin_actual()) redirigir('admin/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    if (login_bloqueado()) $error = 'Demasiados intentos. Espere 15 minutos.';
    elseif (intentar_login(trim($_POST['usuario'] ?? ''), $_POST['clave'] ?? '')) redirigir('admin/index.php');
    else $error = 'Usuario o contraseña incorrectos.';
}
$titulo = 'Acceso técnico';
include __DIR__ . '/../html/head.php';
include __DIR__ . '/../html/admin_login.php';
include __DIR__ . '/../html/foot.php';
