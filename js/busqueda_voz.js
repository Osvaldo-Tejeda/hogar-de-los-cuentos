// Búsqueda por voz (Web Speech API) con confirmación de lo entendido antes de buscar.
(() => {
  const btn = document.getElementById('btn-voz'), campo = document.getElementById('q'), estado = document.getElementById('estado-voz');
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!btn || !SR) return;
  btn.hidden = false;
  btn.addEventListener('click', () => {
    const r = new SR(); r.lang = document.documentElement.lang === 'es' ? 'es-ES' : navigator.language; r.interimResults = false;
    estado.hidden = false; estado.textContent = 'Escuchando… diga el nombre del cuento.';
    r.onresult = ev => {
      const texto = ev.results[0][0].transcript;
      campo.value = texto;
      estado.innerHTML = '';
      estado.append('Buscando: «' + texto + '». ');
      const ok = Object.assign(document.createElement('button'), { type: 'button', className: 'boton', textContent: 'Confirmar' });
      const no = Object.assign(document.createElement('button'), { type: 'button', className: 'boton', textContent: 'Corregir' });
      ok.onclick = () => campo.form.submit(); no.onclick = () => { estado.hidden = true; campo.focus(); };
      estado.append(ok, ' ', no); ok.focus();
    };
    r.onerror = () => { estado.textContent = 'No se pudo escuchar. Escriba la búsqueda con el teclado.'; };
    r.start();
  });
})();
