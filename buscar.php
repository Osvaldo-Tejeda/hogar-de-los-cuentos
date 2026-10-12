<?php
require_once __DIR__ . '/php/cuentos.php';
$q = trim($_GET['q'] ?? '');
$cat = $_GET['categoria'] ?? ''; $edad = $_GET['edad'] ?? ''; $orden = $_GET['orden'] ?? 'recientes';
$resultados = buscar_cuentos($q, $cat, $edad, $orden);
$recientes = historial($q);
$categorias = categorias();
$esInicio = basename($_SERVER['SCRIPT_NAME']) === 'index.php';
$titulo = $esInicio ? 'Biblioteca de cuentos' : 'Buscar cuentos';
$scripts = ['busqueda_voz.js'];
include __DIR__ . '/html/head.php';
include __DIR__ . '/html/buscar.php';
include __DIR__ . '/html/foot.php';
