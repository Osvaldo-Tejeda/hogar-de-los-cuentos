# El Hogar de los Cuentos (XAMPP)

## Instalación
1. Copie esta carpeta a `C:\xampp\htdocs\hogar-de-los-cuentos`.
2. Inicie **Apache** y **MySQL** en el panel de XAMPP.
3. Abra `http://localhost/phpmyadmin` > pestaña **Importar** > elija `sql/schema.sql` > Continuar (crea la base `hogar_cuentos`).
4. Abra `http://localhost/hogar-de-los-cuentos/instalar.php`, cree el usuario técnico y luego **borre `instalar.php`**.
5. Entre a `http://localhost/hogar-de-los-cuentos/admin/login.php`.
6. Para audios grandes suba en `php.ini` (XAMPP > Config > php.ini): `upload_max_filesize=64M` y `post_max_size=70M`; reinicie Apache.

## Registro de usuarios
Desde el login hay un enlace «Crear cuenta» (`admin/registro.php`). Quien se registra queda con sesión iniciada y puede cargar, editar,
sincronizar y borrar cuentos. En `php/config.php`: `REGISTRO_ABIERTO` (true/false) y `REGISTRO_CODIGO` (código que se exige al registrarse;
muy recomendable si el sitio estará en internet, porque si no cualquiera podría crear una cuenta y subir archivos).

## Estructura (cada lenguaje en su carpeta)
- `php/` lógica: `config.php` (ajustes), `db.php`, `auth.php`, `helpers.php`, `cuentos.php`, `ia.php`
- `html/` vistas (solo marcado): `head.php`, `foot.php`, `buscar.php`, `cuento.php`, `admin_*.php`
- `js/` `reproductor.js` (sincronía y pantalla completa), `accesibilidad.js`, `busqueda_voz.js`, `sincronizar.js`
- `css/estilos.css` (paleta en variables `:root`)
- `admin/` páginas privadas · `sql/schema.sql` · `uploads/` audio, imágenes, texto

## Sincronización (min:seg)
En **Imágenes y minutos** → **Sincronizar** los tiempos se escriben como `1:23`. Tres formas, de la más fácil a la más fina:
1. **Marcar mientras escucha:** elija Párrafos o Imágenes, «Empezar a marcar» y toque **Marcar** (tecla **M**) cuando empiece cada uno.
2. **Repartir automáticamente:** estima los tiempos según el largo de cada párrafo.
3. **Afinar:** «Ahora», «−1 s», «+1 s» y «▶» (escucha desde ese punto). El párrafo que suena se resalta en la tabla.
Si no se sincroniza nada, el reproductor estima solo los tiempos por el largo del texto.

> Si ya importó la versión anterior, ejecute una vez `sql/migracion_segundos.sql` en phpMyAdmin.

## Pantalla completa
Botón «Pantalla completa» (o tecla **F**): solo imagen + subtítulos abajo. Mini barra con Pausar, Subtítulos y Salir
(se oculta sola a los 3 s; vuelve al mover el mouse). **Espacio** pausa, **Esc** sale.

## IA (opcional)
Ponga su clave en `IA_API_KEY` de `php/config.php` para preguntas, audio (TTS), transcripción e imágenes. Sin clave todo funciona; las preguntas usan un modelo local simple.
