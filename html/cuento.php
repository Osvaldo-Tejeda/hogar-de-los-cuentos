<article>
<h1><?= e($c['titulo']) ?></h1>
<p class="meta"><?= e($c['autor']) ?> · <?= e($c['categoria']) ?> · Edad <?= e($c['edad_recomendada']) ?></p>

<section id="reproductor" class="reproductor" aria-label="Reproductor del cuento" data-cuento="<?= e(json_encode($datos, JSON_UNESCAPED_UNICODE)) ?>">
  <?php if ($c['imagenes']): ?>
  <div class="escenario" id="escenario">
    <img id="img-escena" src="<?= e($datos['imagenes'][0]['src']) ?>" alt="<?= e($datos['imagenes'][0]['alt']) ?>">
    <div class="subtitulos" id="subtitulos" aria-hidden="true"></div>
    <div class="mini-control" id="mini-control" role="group" aria-label="Controles de pantalla completa">
      <button type="button" id="mini-play" class="boton" aria-label="Pausar">⏸ Pausar</button>
      <button type="button" id="mini-subs" class="boton" aria-pressed="true">Subtítulos</button>
      <button type="button" id="mini-salir" class="boton" aria-label="Salir de pantalla completa">✕ Salir</button>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($c['audio']): ?>
  <audio id="audio" preload="metadata" src="<?= e($datos['audio']) ?>"></audio>
  <div class="controles" role="group" aria-label="Controles de audio">
    <button type="button" id="btn-play" class="boton">▶ Reproducir</button>
    <button type="button" id="btn-atras" class="boton" aria-label="Retroceder 10 segundos">⏪ 10 s</button>
    <button type="button" id="btn-adelante" class="boton" aria-label="Adelantar 10 segundos">10 s ⏩</button>
    <label class="progreso">Posición <input type="range" id="progreso" min="0" max="100" value="0" step="1"></label>
    <span id="tiempo" aria-hidden="true">0:00 / 0:00</span>
    <label>Velocidad <select id="velocidad">
      <?php foreach ([0.5, 0.75, 1, 1.25, 1.5, 2] as $v): ?><option value="<?= $v ?>" <?= $v == 1 ? 'selected' : '' ?>><?= $v ?>×</option><?php endforeach; ?></select></label>
    <?php if ($c['imagenes']): ?><button type="button" id="btn-pantalla" class="boton">⛶ Pantalla completa</button><?php endif; ?>
  </div>
  <?php endif; ?>
  <div id="anuncio" class="sr-only" aria-live="polite"></div>

  <section class="texto" id="texto" aria-label="Texto del cuento" tabindex="0">
    <?php foreach ($c['texto'] as $i => $t): ?><p class="fragmento" data-i="<?= $i ?>"><?= e($t['contenido']) ?></p><?php endforeach; ?>
  </section>
</section>

<?php if ($c['preguntas']): ?>
<section class="preguntas" aria-labelledby="h-preg">
  <h2 id="h-preg">Preguntas para pensar</h2>
  <?php foreach ($c['preguntas'] as $p): ?>
  <div class="pregunta">
    <p><strong><?= e(ucfirst($p['tipo'])) ?>:</strong> <span><?= e($p['pregunta']) ?></span></p>
    <button type="button" class="boton leer-pregunta">🔊 Escuchar</button>
  </div>
  <?php endforeach; ?>
</section>
<?php endif; ?>
</article>
