// Editor de sincronización: tiempos min:seg, marcar mientras se escucha, repartir automático y vista previa en vivo.
(() => {
  const $ = id => document.getElementById(id);
  const audio = $('audio-sync'), reloj = $('reloj'), estado = $('estado-marca');

  const fmt = s => { s = Math.max(0, Math.floor(s)); const h = Math.floor(s / 3600), m = Math.floor(s % 3600 / 60), x = s % 60;
    return (h ? h + ':' + String(m).padStart(2, '0') : m) + ':' + String(x).padStart(2, '0'); };
  // "1:23" -> 83 ; "83" -> 83 ; inválido -> null
  const parse = v => { v = String(v).trim().replace(',', '.');
    if (v === '') return 0; if (/^\d+(\.\d+)?$/.test(v)) return Math.floor(parseFloat(v));
    const p = v.split(':'); if (p.length > 3 || p.some(x => !/^\d+$/.test(x))) return null;
    return p.reduce((a, b) => a * 60 + Number(b), 0); };
  const campo = el => $(el.dataset.destino);
  const modo = () => document.querySelector('input[name=modo]:checked')?.value || 'texto';
  const filas = () => [...document.querySelectorAll('.fila-sync[data-tipo="' + modo() + '"]')];
  const input = tr => tr.querySelector('.t');
  const ahora = () => audio ? audio.currentTime : 0;

  // Botones de cada fila
  document.addEventListener('click', e => {
    const b = e.target.closest('.ahora, .ir, .ajuste'); if (!b) return;
    const inp = campo(b);
    if (b.classList.contains('ahora')) inp.value = fmt(ahora());
    else if (b.classList.contains('ir') && audio) { audio.currentTime = Math.max(0, (parse(inp.value) || 0) - 1); audio.play(); }
    else if (b.classList.contains('ajuste')) inp.value = fmt(Math.max(0, (parse(inp.value) || 0) + Number(b.dataset.d)));
  });
  // Validar y normalizar lo que se escribe a mano (1:23, 01:23, 83…)
  document.addEventListener('change', e => {
    if (!e.target.classList.contains('t')) return;
    const s = parse(e.target.value);
    if (s === null) e.target.setCustomValidity('Escriba el tiempo como min:seg, por ejemplo 1:23');
    else { e.target.setCustomValidity(''); e.target.value = fmt(s); }
    e.target.reportValidity();
  });
  // Filas para agregar tantas imágenes como se necesiten
  const contenedor = $('nuevas-imagenes'), plantilla = $('tpl-imagen'); let n = 0;
  function agregarFila() {
    const fila = plantilla.content.cloneNode(true).firstElementChild;
    fila.innerHTML = fila.innerHTML.replaceAll('__N__', n++);
    contenedor.append(fila); fila.querySelector('input[type=file]').focus();
  }
  $('btn-mas-imagenes').onclick = agregarFila;
  contenedor.addEventListener('click', e => { if (e.target.closest('.quitar-fila')) e.target.closest('.fila-nueva').remove(); });
  agregarFila();

  if (!audio) return;

  // Marcar mientras se escucha
  let idx = 0, marcando = false;
  function resaltar() {
    document.querySelectorAll('.siguiente').forEach(x => x.classList.remove('siguiente'));
    const f = filas(); if (!marcando) return;
    if (idx >= f.length) { estado.textContent = 'Listo: se marcaron todos. Revise y pulse «Guardar sincronización».'; return; }
    f[idx].classList.add('siguiente'); f[idx].scrollIntoView({ block: 'center', behavior: 'smooth' });
    estado.textContent = 'Toque «Marcar» cuando empiece ' + (modo() === 'texto' ? 'el párrafo ' : 'la imagen ') + (idx + 1) + ' de ' + f.length + '.';
  }
  function marcar() {
    const f = filas(); if (!marcando || idx >= f.length) return;
    input(f[idx]).value = fmt(ahora()); idx++; resaltar();
  }
  $('btn-empezar').onclick = () => { marcando = true; idx = 0; audio.currentTime = 0; audio.play(); resaltar(); };
  $('btn-marcar').onclick = marcar;
  $('btn-deshacer').onclick = () => { idx = Math.max(0, idx - 1); resaltar(); };
  document.querySelectorAll('input[name=modo]').forEach(r => r.onchange = () => { idx = 0; marcando = false; resaltar(); estado.textContent = 'Pulse «Empezar a marcar».'; });
  document.addEventListener('keydown', e => {
    if ((e.key === 'm' || e.key === 'M') && !e.target.closest('input, textarea, select')) { e.preventDefault(); marcar(); }
  });

  // Repartir automáticamente según el largo del texto (punto de partida para afinar)
  $('btn-repartir').onclick = () => {
    const dur = audio.duration; if (!dur) { estado.textContent = 'Espere a que cargue el audio e intente de nuevo.'; return; }
    const f = filas(); if (!f.length) return;
    if (modo() === 'texto') {
      const largos = f.map(tr => tr.querySelector('.txt').textContent.length), total = largos.reduce((a, b) => a + b, 0); let acum = 0;
      f.forEach((tr, i) => { input(tr).value = fmt(acum / total * dur); acum += largos[i]; });
    } else f.forEach((tr, i) => input(tr).value = fmt(i * dur / f.length));
    estado.textContent = 'Tiempos estimados. Escuche y corrija con «Ahora», «−1 s» y «+1 s».';
  };

  // Vista previa en vivo: marca el párrafo que está sonando según los tiempos escritos
  audio.addEventListener('timeupdate', () => {
    const t = audio.currentTime; reloj.textContent = fmt(t);
    const f = [...document.querySelectorAll('.fila-sync[data-tipo="texto"]')]; let act = -1;
    f.forEach((tr, i) => { const s = parse(input(tr).value); if (s !== null && s <= t) act = i; });
    f.forEach((tr, i) => tr.classList.toggle('sonando', i === act));
  });
})();
