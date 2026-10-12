<h1><?= $esInicio ? 'Cuentos para leer y escuchar' : 'Buscar cuentos' ?></h1>
<form class="buscador" method="get" action="<?= url('buscar.php') ?>" role="search" aria-label="Buscar cuentos">
  <div class="fila">
    <label for="q">Título, autor, categoría o palabra</label>
    <input id="q" name="q" type="search" value="<?= e($q) ?>" autocomplete="off">
    <button type="button" id="btn-voz" class="boton" aria-label="Buscar por voz" hidden>🎤 Voz</button>
    <button class="boton">Buscar</button>
  </div>
  <div id="estado-voz" class="aviso" role="status" aria-live="polite" hidden></div>
  <div class="fila filtros">
    <label>Categoría <select name="categoria"><option value="">Todas</option>
      <?php foreach ($categorias as $c): ?><option <?= $c === $cat ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></label>
    <label>Edad <select name="edad"><option value="">Todas</option>
      <?php foreach (['3-5','6-8','9-12','13+'] as $a): ?><option <?= $a === $edad ? 'selected' : '' ?>><?= $a ?></option><?php endforeach; ?></select></label>
    <label>Ordenar por <select name="orden">
      <?php foreach (['recientes'=>'Más recientes','buscados'=>'Más vistos','duracion'=>'Duración','titulo'=>'Título'] as $k => $v): ?>
        <option value="<?= $k ?>" <?= $k === $orden ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></label>
  </div>
</form>
<?php if ($recientes): ?>
<p class="historial">Búsquedas recientes:
  <?php foreach ($recientes as $h): ?><a href="<?= url('buscar.php?q=' . urlencode($h)) ?>"><?= e($h) ?></a> <?php endforeach; ?></p>
<?php endif; ?>
<?php if ($q !== ''): ?><p role="status">Resultados para «<?= e($q) ?>»: <?= count($resultados) ?></p><?php endif; ?>
<?php if (!$resultados): ?>
  <p class="aviso" role="status">No encontramos cuentos con esa búsqueda. Pruebe con otra palabra o quite los filtros.</p>
<?php else: ?>
<ul class="tarjetas">
  <?php foreach ($resultados as $c): ?>
  <li class="tarjeta">
    <a href="<?= url('cuento.php?id=' . $c['id']) ?>">
      <?php if ($c['portada']): ?><img src="<?= url($c['portada']) ?>" alt="" loading="lazy"><?php else: ?><div class="sin-portada" aria-hidden="true">📖</div><?php endif; ?>
      <h2><?= e($c['titulo']) ?></h2>
      <p><?= e($c['autor']) ?> · <?= e($c['categoria']) ?> · <?= e($c['edad_recomendada']) ?> años<?= $c['duracion_min'] ? ' · ' . (int)$c['duracion_min'] . ' min' : '' ?></p>
    </a>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
