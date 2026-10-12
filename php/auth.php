<?php
require_once __DIR__ . '/helpers.php';

function admin_actual(): ?array { return $_SESSION['tecnico'] ?? null; }

function exigir_admin(): void {
    if (!admin_actual()) redirigir('admin/login.php');
}

function login_bloqueado(): bool {
    $s = db()->prepare("SELECT COUNT(*) FROM intentos_login WHERE ip=? AND fecha > (NOW() - INTERVAL 15 MINUTE)");
    $s->execute([$_SERVER['REMOTE_ADDR'] ?? '']);
    return (int)$s->fetchColumn() >= MAX_INTENTOS_LOGIN;
}

function intentar_login(string $usuario, string $clave): bool {
    if (login_bloqueado()) return false;
    $s = db()->prepare('SELECT * FROM usuarios_tecnicos WHERE usuario=?');
    $s->execute([$usuario]);
    $u = $s->fetch();
    if ($u && password_verify($clave, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['tecnico'] = ['id' => $u['id'], 'usuario' => $u['usuario'], 'rol' => $u['rol']];
        db()->prepare('DELETE FROM intentos_login WHERE ip=?')->execute([$_SERVER['REMOTE_ADDR'] ?? '']);
        return true;
    }
    db()->prepare('INSERT INTO intentos_login (ip) VALUES (?)')->execute([$_SERVER['REMOTE_ADDR'] ?? '']);
    return false;
}

function cerrar_sesion(): void {
    $_SESSION = [];
    session_destroy();
}

// Crea un técnico y abre su sesión. Devuelve null si todo va bien o un mensaje de error.
function registrar_usuario(string $usuario, string $clave, string $clave2, string $codigo): ?string {
    if (!REGISTRO_ABIERTO) return 'El registro está cerrado.';
    if (REGISTRO_CODIGO !== '') {
        if (login_bloqueado()) return 'Demasiados intentos. Espere 15 minutos.';
        if (!hash_equals(REGISTRO_CODIGO, $codigo)) {
            db()->prepare('INSERT INTO intentos_login (ip) VALUES (?)')->execute([$_SERVER['REMOTE_ADDR'] ?? '']);
            return 'El código de registro no es correcto.';
        }
    }
    if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $usuario)) return 'El usuario debe tener de 3 a 40 caracteres (letras, números, punto, guion o guion bajo).';
    if (strlen($clave) < 8) return 'La contraseña debe tener al menos 8 caracteres.';
    if ($clave !== $clave2) return 'Las contraseñas no coinciden.';
    try {
        db()->prepare("INSERT INTO usuarios_tecnicos (usuario, password_hash, rol) VALUES (?,?,'tecnico')")
            ->execute([$usuario, password_hash($clave, PASSWORD_DEFAULT)]);
    } catch (PDOException $ex) {
        if ($ex->getCode() === '23000') return 'Ese nombre de usuario ya existe.';
        throw $ex;
    }
    intentar_login($usuario, $clave);
    return null;
}
