<?php
require_once __DIR__ . '/helpers.php';

function obtener_cuento(int $id): ?array {
    $s = db()->prepare('SELECT * FROM cuentos WHERE id=?'); $s->execute([$id]);
    $c = $s->fetch();
    if (!$c) return null;
    $q = function (string $sql) use ($id) { $s = db()->prepare($sql); $s->execute([$id]); return $s->fetchAll(); };
    $c['texto'] = $q('SELECT * FROM cuentos_texto WHERE id_cuento=? ORDER BY fragmento_orden');
    $c['imagenes'] = $q('SELECT * FROM cuentos_imagenes WHERE id_cuento=? ORDER BY segundo_inicio, orden_escena, id');
    $c['preguntas'] = $q("SELECT * FROM preguntas_ia WHERE id_cuento=? ORDER BY FIELD(tipo,'literal','inferencial','reflexiva')");
    $a = $q('SELECT * FROM cuentos_audio WHERE id_cuento=? ORDER BY id DESC LIMIT 1');
    $c['audio'] = $a[0] ?? null;
    return $c;
}

function categorias(): array {
    return db()->query("SELECT DISTINCT categoria FROM cuentos WHERE categoria<>'' ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
}

function buscar_cuentos(string $q = '', string $cat = '', string $edad = '', string $orden = 'recientes'): array {
    $sql = "SELECT c.*, (SELECT ruta_archivo FROM cuentos_imagenes i WHERE i.id_cuento=c.id ORDER BY segundo_inicio, id LIMIT 1) AS portada
            FROM cuentos c WHERE 1=1";
    $p = [];
    if ($q !== '') {
        $sql .= " AND (c.titulo LIKE ? OR c.autor LIKE ? OR c.categoria LIKE ?
                  OR EXISTS (SELECT 1 FROM cuentos_texto t WHERE t.id_cuento=c.id AND t.contenido LIKE ?))";
        $like = '%' . $q . '%'; array_push($p, $like, $like, $like, $like);
    }
    if ($cat !== '') { $sql .= ' AND c.categoria=?'; $p[] = $cat; }
    if ($edad !== '') { $sql .= ' AND c.edad_recomendada=?'; $p[] = $edad; }
    $ordenes = ['recientes' => 'c.fecha_carga DESC', 'buscados' => 'c.vistas DESC',
                'duracion' => 'c.duracion_min ASC', 'titulo' => 'c.titulo ASC'];
    $sql .= ' ORDER BY ' . ($ordenes[$orden] ?? $ordenes['recientes']);
    $s = db()->prepare($sql); $s->execute($p);
    return $s->fetchAll();
}

function guardar_preguntas(int $id, array $p): void {
    $s = db()->prepare('REPLACE INTO preguntas_ia (id_cuento, tipo, pregunta) VALUES (?,?,?)');
    foreach (['literal', 'inferencial', 'reflexiva'] as $t) $s->execute([$id, $t, $p[$t]]);
}

function texto_plano(int $id): string {
    $s = db()->prepare('SELECT contenido FROM cuentos_texto WHERE id_cuento=? ORDER BY fragmento_orden'); $s->execute([$id]);
    return implode("\n\n", $s->fetchAll(PDO::FETCH_COLUMN));
}

function guardar_fragmentos(int $id, string $texto): void {
    db()->prepare('DELETE FROM cuentos_texto WHERE id_cuento=?')->execute([$id]);
    $s = db()->prepare('INSERT INTO cuentos_texto (id_cuento, contenido, fragmento_orden) VALUES (?,?,?)');
    foreach (dividir_fragmentos($texto) as $i => $f) $s->execute([$id, $f, $i + 1]);
}

function historial(string $termino = ''): array {
    $sid = session_id();
    if ($termino !== '') db()->prepare('INSERT INTO historial_busquedas (id_sesion, termino) VALUES (?,?)')->execute([$sid, mb_substr($termino, 0, 200)]);
    $s = db()->prepare('SELECT termino, MAX(fecha) f FROM historial_busquedas WHERE id_sesion=? GROUP BY termino ORDER BY f DESC LIMIT 8');
    $s->execute([$sid]);
    return $s->fetchAll(PDO::FETCH_COLUMN);
}
