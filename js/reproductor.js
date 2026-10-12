// Reproductor: audio + texto + imágenes sincronizados POR MINUTO, con modo pantalla completa (estilo cine).
(() => {
  const raiz = document.getElementById('reproductor'); if (!raiz) return;
  const datos = JSON.parse(raiz.dataset.cuento);
  const $ = id => document.getElementById(id);
  const audio = $('audio'), escenario = $('escenario'), img = $('img-escena'), subs = $('subtitulos');
  const parrafos = [...document.querySelectorAll('.fragmento')], cajaTexto = $('texto'), anuncio = $('anuncio');
  const btnPlay = $('btn-play'), miniPlay = $('mini-play'), progreso = $('progreso'), tiempo = $('tiempo');
  let inicios = [], iniciosImg = [], idxFrag = -1, idxImg = 0, subsOn = true, ocultar;

  const fmt = s => Math.floor(s / 60) + ':' + String(Math.floor(s % 60)).padStart(2, '0');

  // Tiempos de inicio en segundos. Si el técnico aún no sincronizó nada, se estiman según el largo del texto.
  function calcularInicios() {
    const dur = (audio && audio.duration) || 0, n = datos.texto.length, ni = datos.imagenes.length;
    if (dur && n && datos.texto.every(f => !f.seg)) {
      const total = datos.texto.reduce((s, f) => s + f.texto.length, 0); let acum = 0;
      datos.texto.forEach((f, i) => { inicios[i] = acum / total * dur; acum += f.texto.length; });
    } else datos.texto.forEach((f, i) => inicios[i] = f.seg);
    if (dur && ni > 1 && datos.imagenes.every(x => !x.seg)) datos.imagenes.forEach((x, i) => iniciosImg[i] = i * dur / ni);
    else datos.imagenes.forEach((x, i) => iniciosImg[i] = x.seg);
  }

  function actualizar(t) {
    // imagen: la última cuyo minuto ya llegó
    let im = 0;
    datos.imagenes.forEach((x, i) => { if (iniciosImg[i] <= t) im = i; });
    if (datos.imagenes.length && im !== idxImg) { idxImg = im; img.src = datos.imagenes[im].src; img.alt = datos.imagenes[im].alt; }
    // texto
    let fr = 0;
    inicios.forEach((s, i) => { if (s <= t) fr = i; });
    if (fr !== idxFrag && parrafos.length) {
      parrafos[idxFrag]?.classList.remove('activo'); parrafos[idxFrag]?.removeAttribute('aria-current');
      idxFrag = fr; const p = parrafos[fr];
      p.classList.add('activo'); p.setAttribute('aria-current', 'true');
      cajaTexto.scrollTo({ top: p.offsetTop - cajaTexto.offsetTop - 16, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
      if (subs) subs.textContent = datos.texto[fr].texto;
    }
  }

  function estadoPlay() {
    const rep = audio && !audio.paused;
    if (btnPlay) btnPlay.textContent = rep ? '⏸ Pausar' : '▶ Reproducir';
    if (miniPlay) { miniPlay.textContent = rep ? '⏸ Pausar' : '▶ Reproducir'; miniPlay.setAttribute('aria-label', rep ? 'Pausar' : 'Reproducir'); }
    if (!rep && audio && audio.currentTime > 0 && anuncio) {
      const alt = datos.imagenes[idxImg]?.alt;
      anuncio.textContent = (datos.texto[idxFrag]?.texto || '') + (alt ? '. Imagen: ' + alt : '');
    }
    mostrarMini();
  }
  const alternar = () => audio && (audio.paused ? audio.play() : audio.pause());

  // ----- Pantalla completa -----
  const enPantalla = () => document.fullscreenElement === escenario || escenario?.classList.contains('pseudo-fs');
  function entrarPantalla() {
    if (escenario.requestFullscreen) escenario.requestFullscreen().catch(() => escenario.classList.add('pseudo-fs'));
    else escenario.classList.add('pseudo-fs');       // respaldo (p. ej. iPhone)
    escenario.classList.add('cine'); setTimeout(() => miniPlay?.focus(), 100); mostrarMini();
  }
  function salirPantalla() {
    if (document.fullscreenElement) document.exitFullscreen();
    escenario.classList.remove('pseudo-fs', 'cine'); $('btn-pantalla')?.focus();
  }
  function mostrarMini() {
    if (!escenario) return;
    escenario.classList.remove('ocultar-mini'); clearTimeout(ocultar);
    if (enPantalla() && audio && !audio.paused) ocultar = setTimeout(() => escenario.classList.add('ocultar-mini'), 3000);
  }

  if (audio) {
    audio.addEventListener('loadedmetadata', () => { calcularInicios(); progreso.max = Math.floor(audio.duration); actualizar(audio.currentTime); });
    audio.addEventListener('timeupdate', () => {
      progreso.value = Math.floor(audio.currentTime); tiempo.textContent = fmt(audio.currentTime) + ' / ' + fmt(audio.duration || 0);
      actualizar(audio.currentTime);
    });
    audio.addEventListener('play', estadoPlay); audio.addEventListener('pause', estadoPlay);
    btnPlay.onclick = alternar;
    $('btn-atras').onclick = () => audio.currentTime = Math.max(0, audio.currentTime - 10);
    $('btn-adelante').onclick = () => audio.currentTime = Math.min(audio.duration, audio.currentTime + 10);
    progreso.oninput = () => audio.currentTime = progreso.value;
    $('velocidad').onchange = e => audio.playbackRate = parseFloat(e.target.value);
  }
  calcularInicios(); actualizar(0);

  if (escenario) {
    $('btn-pantalla')?.addEventListener('click', entrarPantalla);
    miniPlay.onclick = alternar; $('mini-salir').onclick = salirPantalla;
    $('mini-subs').onclick = e => { subsOn = !subsOn; subs.hidden = !subsOn; e.target.setAttribute('aria-pressed', subsOn); };
    img.addEventListener('click', () => { if (enPantalla()) alternar(); });
    ['mousemove', 'touchstart', 'keydown'].forEach(ev => escenario.addEventListener(ev, mostrarMini));
    document.addEventListener('fullscreenchange', () => { if (!document.fullscreenElement) escenario.classList.remove('cine'); mostrarMini(); });
    document.addEventListener('keydown', e => {
      if (e.target.closest('input, select, textarea, button')) return;
      if (e.key === 'Escape' && escenario.classList.contains('pseudo-fs')) salirPantalla();
      if (e.key === 'f' || e.key === 'F') enPantalla() ? salirPantalla() : entrarPantalla();
      if (e.key === ' ' && enPantalla()) { e.preventDefault(); alternar(); }
    });
  }
})();
