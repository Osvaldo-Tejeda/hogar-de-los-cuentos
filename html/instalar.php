<h1>Instalación</h1>
<?php if ($msg): ?><p class="aviso" role="status"><?= e($msg) ?></p><?php endif; ?>
<?php if ($existe): ?>
  <p>Ya existe un usuario técnico. <a href="<?= url('admin/login.php') ?>">Ir al inicio de sesión</a>. Borre <code>instalar.php</code>.</p>
<?php else: ?>
<form method="post" class="formulario">
  <?= csrf_campo() ?>
  <label for="usuario">Usuario</label><input id="usuario" name="usuario" required autocomplete="username">
  <label for="clave">Contraseña (mínimo 8 caracteres)</label><input id="clave" name="clave" type="password" minlength="8" required autocomplete="new-password">
  <button class="boton">Crear usuario técnico</button>
</form>
<?php endif; ?>
