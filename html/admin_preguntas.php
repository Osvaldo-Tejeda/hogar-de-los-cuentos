<h1>Preguntas · <?= e($c['titulo']) ?></h1>
<p>Siempre hay exactamente 3 preguntas, una de cada tipo. Corrija la que la IA haya hecho mal.</p>
<form method="post" class="formulario">
  <?= csrf_campo() ?>
  <?php foreach (['literal' => 'Literal (qué pasó)', 'inferencial' => 'Inferencial (por qué pasó)', 'reflexiva' => 'Reflexiva (qué opinas / qué harías)'] as $k => $r): ?>
    <label for="<?= $k ?>"><?= $r ?></label><textarea id="<?= $k ?>" name="<?= $k ?>" rows="3" style="min-height:0"><?= e($pp[$k] ?? '') ?></textarea>
  <?php endforeach; ?>
  <p><button class="boton">Guardar</button> <button class="boton" name="regenerar" value="1">Regenerar con IA</button></p>
</form>
