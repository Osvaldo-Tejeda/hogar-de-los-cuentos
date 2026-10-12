<h1><?= e($titulo) ?></h1>
<form method="post" enctype="multipart/form-data" class="formulario">
  <?= csrf_campo() ?>
  <label for="titulo">Título</label><input id="titulo" name="titulo" required value="<?= e($c['titulo'] ?? '') ?>">
  <label for="autor">Autor</label><input id="autor" name="autor" value="<?= e($c['autor'] ?? '') ?>">
  <label for="categoria">Categoría</label><input id="categoria" name="categoria" value="<?= e($c['categoria'] ?? '') ?>">
  <label for="edad">Edad recomendada</label>
  <select id="edad" name="edad"><?php foreach (['3-5','6-8','9-12','13+'] as $a): ?><option <?= ($c['edad_recomendada'] ?? '') === $a ? 'selected' : '' ?>><?= $a ?></option><?php endforeach; ?></select>
  <label for="idioma">Idioma</label><input id="idioma" name="idioma" value="<?= e($c['idioma'] ?? 'es') ?>">
  <label for="nivel">Nivel de lectura</label><input id="nivel" name="nivel" value="<?= e($c['nivel_lectura'] ?? '') ?>">
  <label for="duracion">Duración aproximada (minutos)</label><input id="duracion" name="duracion" type="number" min="0" value="<?= (int)($c['duracion_min'] ?? 0) ?>">

  <label for="texto">Texto del cuento (separe los párrafos con una línea en blanco)</label>
  <textarea id="texto" name="texto"><?= e($textoActual) ?></textarea>
  <label for="archivo_texto">…o suba un archivo .txt (reemplaza el texto de arriba)</label>
  <input id="archivo_texto" name="archivo_texto" type="file" accept=".txt">

  <label for="audio">Audio propio (MP3, WAV u OGG)</label>
  <input id="audio" name="audio" type="file" accept=".mp3,.wav,.ogg">
  <label><input type="checkbox" name="audio_ia" value="1"> Generar el audio con IA (texto a voz) si no subo uno</label>
  <?php if (!empty($c['audio'])): ?><p>Audio actual: <?= e($c['audio']['origen']) ?>. Si sube otro, lo reemplaza.</p><?php endif; ?>
  <p><button class="boton">Guardar y continuar</button></p>
</form>
