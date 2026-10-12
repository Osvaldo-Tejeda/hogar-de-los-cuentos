-- Ejecutar UNA SOLA VEZ si ya había importado la versión anterior (sincronización por minutos).
USE hogar_cuentos;
ALTER TABLE cuentos_texto CHANGE minuto_inicio segundo_inicio INT NOT NULL DEFAULT 0;
ALTER TABLE cuentos_imagenes CHANGE minuto_inicio segundo_inicio INT NOT NULL DEFAULT 0;
UPDATE cuentos_texto SET segundo_inicio = segundo_inicio * 60;
UPDATE cuentos_imagenes SET segundo_inicio = segundo_inicio * 60;
