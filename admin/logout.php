<?php
require_once __DIR__ . '/../php/auth.php';
cerrar_sesion();
header('Location: ' . url('index.php'));
