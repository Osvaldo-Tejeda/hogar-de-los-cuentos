<?php
$botonesTiempo = function (string $id) { ?>
  <button type="button" class="boton ahora" data-destino="<?= e($id) ?>">Ahora</button>
  <button type="button" class="boton ir" data-destino="<?= e($id) ?>" aria-label="Escuchar desde este tiempo">▶</button>
  <button type="button" class="boton ajuste" data-destino="<?= e($id) ?>" data-d="-1">−1 s</button>
  <button type="button" class="boton ajuste" data-destino="<?= e($id) ?>" data-d="1">+1 s</button>
<?php }; ?>
<h1>Sincronizar · <?= e($c['titulo']) ?></h1>
<p>Escriba los tiempos como <strong>min:seg</strong> (ejemplo <code>1:23</code>). La forma más fácil es <strong>marcar mientras escucha</strong>:
  elija «Párrafos» o «Imágenes», pulse «Empezar a marcar» y toque <strong>Marcar</strong> (o la tecla <kbd>M</kbd>) justo cuando empiece cada uno.</p>

<?php if ($c['audio']): ?>
<div class="panel-sync">
  <audio id="audio-sync" controls src="<?= e(url($c['audio']['ruta_archivo'])) ?>" style="width:100%"></audio>
  <div class="fila">
    <span>Tiempo: <strong id="reloj">0:00</strong></span>
    <fieldset class="modo"><legend class="sr-only">Qué se va a marcar</legend>
      <label><input type="radio" name="modo" value="texto" checked> Párrafos</label>
      <label><input type="radio" name="modo" value="imagen"> Imágenes</label>
    </fieldset>
    <button type="button" class="boton" id="btn-empezar">▶ Empezar a marcar</button>
    <button type="button" class="boton" id="btn-marcar">✔ Marcar (M)</button>
    <button type="button" class="boton" id="btn-deshacer">↩ Deshacer</button>
    <button type="button" class="boton" id="btn-repartir" title="Estima los tiempos según el largo de cada párrafo">Repartir automáticamente</button>
  </div>
  <p id="estado-marca" role="status" aria-live="polite">Elija qué marcar y pulse «Empezar a marcar».</p>
</div>
<?php else: ?><p class="aviso">Este cuento no tiene audio. Puede escribir los tiempos a mano, pero conviene subir el audio primero.</p><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_campo() ?>
  <h2>Imágenes</h2>
  <table class="tabla">
    <caption class="sr-only">Imágenes del cuento</caption>
    <thead><tr><th scope="col">Imagen</th><th scope="col">Texto alternativo (obligatorio)</th><th scope="col">Aparece en</th><th scope="col">Quitar</th></tr></thead>
    <tbody>
    <?php foreach ($c['imagenes'] as $i): ?>
    <tr class="fila-sync" data-tipo="imagen">
      <td><img src="<?= e(url($i['ruta_archivo'])) ?>" alt=""></td>
      <td><label class="sr-only" for="alt<?= $i['id'] ?>">Texto alternativo</label><textarea id="alt<?= $i['id'] ?>" name="alt_img[<?= $i['id'] ?>]" rows="3" required><?= e($i['texto_alternativo']) ?></textarea></td>
      <td><label class="sr-only" for="ti<?= $i['id'] ?>">Tiempo (min:seg)</label>
          <input id="ti<?= $i['id'] ?>" class="t" name="t_img[<?= $i['id'] ?>]" value="<?= e(fmt_tiempo((int)$i['segundo_inicio'])) ?>" inputmode="numeric" placeholder="0:00">
          <?php $botonesTiempo('ti' . $i['id']); ?></td>
      <td><label><input type="checkbox" name="borrar_img[<?= $i['id'] ?>]"> Quitar</label></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <h3>Agregar imágenes</h3>
  <div id="nuevas-imagenes"></div>
  <p><button type="button" class="boton" id="btn-mas-imagenes">＋ Agregar otra imagen</button></p>
  <template id="tpl-imagen">
    <div class="fila fila-nueva">
      <label for="ni__N__">Imagen</label><input id="ni__N__" type="file" name="nueva_img[]" accept="image/*">
      <label>Texto alternativo <input name="nueva_alt[]" size="40"></label>
      <label>Aparece en <input class="t" id="nt__N__" name="nueva_t[]" value="0:00" inputmode="numeric"></label>
      <?php $botonesTiempo('nt__N__'); ?>
      <button type="button" class="boton quitar-fila">Quitar fila</button>
    </div>
  </template>
  <div class="fila">
    <label>O generar con IA, describa la escena <input name="desc_ia" size="50"></label>
    <label>Aparece en <input class="t" name="t_ia" value="0:00" inputmode="numeric"></label>
  </div>

  <h2>Texto</h2>
  <table class="tabla">
    <caption class="sr-only">Párrafos del cuento y el momento en que empiezan</caption>
    <thead><tr><th scope="col">Empieza en</th><th scope="col">Párrafo</th></tr></thead>
    <tbody>
    <?php foreach ($c['texto'] as $t): ?>
    <tr class="fila-sync" data-tipo="texto">
      <td><label class="sr-only" for="tt<?= $t['id'] ?>">Tiempo del párrafo <?= (int)$t['fragmento_orden'] ?> (min:seg)</label>
          <input id="tt<?= $t['id'] ?>" class="t" name="t_texto[<?= $t['id'] ?>]" value="<?= e(fmt_tiempo((int)$t['segundo_inicio'])) ?>" inputmode="numeric" placeholder="0:00">
          <?php $botonesTiempo('tt' . $t['id']); ?></td>
      <td class="txt"><?= e($t['contenido']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <p><button class="boton">Guardar sincronización</button> <a href="<?= url('cuento.php?id=' . $c['id']) ?>" target="_blank" rel="noopener">Probar en el reproductor (abre otra pestaña)</a></p>
</form>
