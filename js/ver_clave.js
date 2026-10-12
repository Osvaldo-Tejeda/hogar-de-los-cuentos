// Agrega el "ojito" para mostrar u ocultar la contraseña en todos los campos de contraseña.
(() => {
  document.querySelectorAll('input[type=password]').forEach(campo => {
    const caja = document.createElement('div');
    caja.className = 'campo-clave';
    campo.parentNode.insertBefore(caja, campo);
    caja.append(campo);
    const btn = document.createElement('button');
    btn.type = 'button'; btn.className = 'ojito';
    const pintar = visible => {
      btn.textContent = visible ? '🙈' : '👁';
      btn.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
      btn.setAttribute('aria-pressed', visible);
    };
    pintar(false);
    btn.onclick = () => {
      const ver = campo.type === 'password';
      campo.type = ver ? 'text' : 'password';
      pintar(ver); campo.focus();
    };
    caja.append(btn);
  });
})();
