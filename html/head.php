<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo ?? 'El Hogar de los Cuentos') ?> · El Hogar de los Cuentos</title>
<link rel="stylesheet" href="<?= url('css/estilos.css') ?>">
<script src="<?= url('js/accesibilidad.js') ?>" defer></script>
<script src="<?= url('js/ver_clave.js') ?>" defer></script>
<?php foreach (($scripts ?? []) as $js): ?><script src="<?= url('js/' . $js) ?>" defer></script><?php endforeach; ?>
</head>
<body class="<?= e($claseBody ?? '') ?>">
<a class="saltar" href="#contenido">Saltar al contenido</a>
<header class="cabecera">
  <a class="marca" href="<?= url('index.php') ?>">🏡 El Hogar de los Cuentos</a>
  <nav aria-label="Principal">
    <a href="<?= url('index.php') ?>">Cuentos</a>
    <a href="<?= url('buscar.php') ?>">Buscar</a>
    <?php if (!empty($_SESSION['tecnico'])): ?>
      <a href="<?= url('admin/index.php') ?>">Panel</a>
      <a href="<?= url('admin/logout.php') ?>">Salir</a>
    <?php else: ?>
      <a href="<?= url('admin/login.php') ?>">Técnico</a>
    <?php endif; ?>
  </nav>
  <div class="barra-acc" role="group" aria-label="Opciones de accesibilidad">
    <button type="button" data-acc="fuente-mas" aria-label="Aumentar tamaño de letra">A+</button>
    <button type="button" data-acc="fuente-menos" aria-label="Disminuir tamaño de letra">A−</button>
    <button type="button" data-acc="contraste" aria-pressed="false">Alto contraste</button>
    <button type="button" data-acc="espaciado" aria-pressed="false">Más espacio</button>
    <button type="button" data-acc="legible" aria-pressed="false">Letra legible</button>
  </div>
</header>
<main id="contenido" tabindex="-1">
<?php if ($f = flash()): ?><p class="aviso aviso-<?= e($f[1]) ?>" role="status"><?= e($f[0]) ?></p><?php endif; ?>
