<h1>Crear cuenta de técnico</h1>
<?php if (!REGISTRO_ABIERTO): ?>
  <p class="aviso">El registro está cerrado. <a href="<?= url('admin/login.php') ?>">Iniciar sesión</a></p>
<?php else: ?>
<?php if ($error): ?><p class="aviso aviso-error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="formulario" style="max-width:420px">
  <?= csrf_campo() ?>
  <label for="usuario">Usuario</label><input id="usuario" name="usuario" required autocomplete="username" value="<?= e($_POST['usuario'] ?? '') ?>">
  <label for="clave">Contraseña (mínimo 8 caracteres)</label><input id="clave" name="clave" type="password" minlength="8" required autocomplete="new-password">
  <label for="clave2">Repetir contraseña</label><input id="clave2" name="clave2" type="password" minlength="8" required autocomplete="new-password">
  <?php if (REGISTRO_CODIGO !== ''): ?>
    <label for="codigo">Código de registro</label><input id="codigo" name="codigo" required autocomplete="off">
  <?php endif; ?>
  <p><button class="boton">Crear cuenta</button></p>
</form>
<p>¿Ya tiene cuenta? <a href="<?= url('admin/login.php') ?>">Iniciar sesión</a></p>
<?php endif; ?>
