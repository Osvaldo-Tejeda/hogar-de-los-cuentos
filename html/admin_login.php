<h1>Acceso del técnico</h1>
<?php if ($error): ?><p class="aviso aviso-error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="formulario" style="max-width:420px">
  <?= csrf_campo() ?>
  <label for="usuario">Usuario</label><input id="usuario" name="usuario" required autocomplete="username" autofocus>
  <label for="clave">Contraseña</label><input id="clave" name="clave" type="password" required autocomplete="current-password">
  <p><button class="boton">Entrar</button></p>
</form>
<?php if (REGISTRO_ABIERTO): ?><p>¿No tiene cuenta? <a href="<?= url('admin/registro.php') ?>">Crear cuenta</a></p><?php endif; ?>
