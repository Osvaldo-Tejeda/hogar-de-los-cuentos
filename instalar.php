<?php
// Crea el primer usuario técnico. Se bloquea solo cuando ya existe un usuario. BORRE este archivo después.
require_once __DIR__ . '/php/helpers.php';
$existe = (int)db()->query('SELECT COUNT(*) FROM usuarios_tecnicos')->fetchColumn() > 0;
$msg = '';
if (!$existe && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $u = trim($_POST['usuario'] ?? ''); $c = $_POST['clave'] ?? '';
    if ($u === '' || strlen($c) < 8) { $msg = 'Usuario obligatorio y contraseña de al menos 8 caracteres.'; }
    else {
        db()->prepare("INSERT INTO usuarios_tecnicos (usuario, password_hash, rol) VALUES (?,?,'admin')")
            ->execute([$u, password_hash($c, PASSWORD_DEFAULT)]);
        $existe = true; $msg = 'Usuario creado. Ya puede iniciar sesión y debe borrar instalar.php.';
    }
}
$titulo = 'Instalación'; $esAdmin = true;
include __DIR__ . '/html/head.php';
include __DIR__ . '/html/instalar.php';
include __DIR__ . '/html/foot.php';
