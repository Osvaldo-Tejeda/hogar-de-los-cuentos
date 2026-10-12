<?php
// ===== CONFIGURACIÓN GENERAL (edite solo este archivo) =====
define('BASE_URL', '/hogar-de-los-cuentos');          // carpeta dentro de htdocs
define('RAIZ', dirname(__DIR__));
define('DB_HOST', 'localhost');
define('DB_NAME', 'hogar_cuentos');
define('DB_USER', 'root');                              // usuario por defecto de XAMPP
define('DB_PASS', '');                                  // contraseña por defecto de XAMPP

define('UPLOAD_DIR', RAIZ . '/uploads');
define('MAX_AUDIO_MB', 50);
define('MAX_IMG_MB', 8);
define('MAX_INTENTOS_LOGIN', 5);                        // por IP cada 15 minutos

// IA (opcional). Sin clave, las preguntas se generan con un método local simple.
define('IA_API_KEY', '');                               // ej. 'sk-...'
define('IA_BASE_URL', 'https://api.openai.com/v1');
define('IA_MODELO_TEXTO', 'gpt-4o-mini');
define('IA_MODELO_TTS', 'tts-1');
define('IA_MODELO_STT', 'whisper-1');
define('IA_MODELO_IMAGEN', 'dall-e-3');

// Registro de técnicos (quien se registra puede cargar y editar cuentos)
define('REGISTRO_ABIERTO', true);                       // false = nadie puede registrarse
define('REGISTRO_CODIGO', '');                          // si pone un código, se exigirá al registrarse (recomendado)
