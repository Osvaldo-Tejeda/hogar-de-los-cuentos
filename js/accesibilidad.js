// Barra de accesibilidad: tamaño de letra, alto contraste, espaciado, fuente legible. Se guarda en el navegador.
(() => {
  const KEY = 'hc_acc';
  const est = Object.assign({ fuente: 0, contraste: false, espaciado: false, legible: false }, JSON.parse(localStorage.getItem(KEY) || '{}'));
  const html = document.documentElement;

  function aplicar() {
    html.style.setProperty('--fs', (1 + est.fuente * 0.125) + 'rem');
    html.dataset.contraste = est.contraste; html.dataset.espaciado = est.espaciado; html.dataset.legible = est.legible;
    document.querySelectorAll('[data-acc]').forEach(b => {
      const k = b.dataset.acc; if (est[k] !== undefined && typeof est[k] === 'boolean') b.setAttribute('aria-pressed', est[k]);
    });
    localStorage.setItem(KEY, JSON.stringify(est));
  }
  document.addEventListener('click', ev => {
    const b = ev.target.closest('[data-acc]'); if (!b) return;
    const k = b.dataset.acc;
    if (k === 'fuente-mas') est.fuente = Math.min(est.fuente + 1, 6);
    else if (k === 'fuente-menos') est.fuente = Math.max(est.fuente - 1, -2);
    else est[k] = !est[k];
    aplicar();
  });
  aplicar();

  // Preguntas en audio con la voz del navegador
  document.addEventListener('click', ev => {
    const b = ev.target.closest('.leer-pregunta'); if (!b || !('speechSynthesis' in window)) return;
    speechSynthesis.cancel();
    const u = new SpeechSynthesisUtterance(b.parentElement.querySelector('span').textContent);
    u.lang = 'es-ES'; speechSynthesis.speak(u);
  });
})();
