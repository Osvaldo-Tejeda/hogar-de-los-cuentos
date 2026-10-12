<h1>Panel de administración</h1>
<p><a class="boton" style="text-decoration:none;display:inline-block" href="<?= url('admin/cuento_form.php') ?>">＋ Nuevo cuento</a></p>
<table class="tabla">
  <caption class="sr-only">Cuentos cargados</caption>
  <thead><tr><th scope="col">Título</th><th scope="col">Categoría</th><th scope="col">Acciones</th></tr></thead>
  <tbody>
  <?php foreach ($cuentos as $c): ?>
  <tr>
    <th scope="row"><?= e($c['titulo']) ?></th><td><?= e($c['categoria']) ?></td>
    <td>
      <a href="<?= url('cuento.php?id=' . $c['id']) ?>">Ver</a> ·
      <a href="<?= url('admin/cuento_form.php?id=' . $c['id']) ?>">Editar</a> ·
      <a href="<?= url('admin/sincronizar.php?id=' . $c['id']) ?>">Imágenes y minutos</a> ·
      <a href="<?= url('admin/preguntas.php?id=' . $c['id']) ?>">Preguntas</a>
      <form method="post" style="display:inline" onsubmit="return confirm('¿Eliminar este cuento?')">
        <?= csrf_campo() ?><button class="boton" name="borrar" value="<?= $c['id'] ?>" aria-label="Eliminar <?= e($c['titulo']) ?>">Eliminar</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$cuentos): ?><tr><td colspan="3">Aún no hay cuentos.</td></tr><?php endif; ?>
  </tbody>
</table>
