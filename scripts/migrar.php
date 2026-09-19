<?php
/**
 * scripts/migrar.php — Ejecutor de migraciones para el Sistema de Tutorías UPDS.
 *
 * Uso:
 *   php scripts/migrar.php                  Aplica todas las migraciones pendientes.
 *   php scripts/migrar.php --marcar-aplicadas  Registra las migraciones existentes
 *                                              SIN ejecutarlas (para bases ya migradas).
 *
 * Solo funciona por línea de comandos. No debe ejecutarse desde el navegador.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Este script solo puede ejecutarse por línea de comandos (CLI).');
}

$marcarAplicadas = in_array('--marcar-aplicadas', $argv ?? [], true);

require_once __DIR__ . '/../config/conexion.php';

$directorioMigraciones = __DIR__ . '/../database/migrations';

/**
 * Salida con salto de línea.
 */
function salir(string $mensaje, int $codigo = 0): void
{
    fwrite($codigo === 0 ? STDOUT : STDERR, $mensaje . PHP_EOL);
    exit($codigo);
}

// 1) Crear la tabla de control de migraciones si no existe.
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        nombre_archivo VARCHAR(150) PRIMARY KEY,
        fecha_aplicada DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

// 2) Listar los archivos .sql en orden alfabético.
$archivos = glob($directorioMigraciones . '/*.sql') ?: [];
sort($archivos, SORT_STRING);

if (empty($archivos)) {
    salir('No se encontraron archivos de migración en ' . $directorioMigraciones);
}

// 3) Averiguar cuáles ya están registradas.
$aplicadas = $pdo->query('SELECT nombre_archivo FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
$aplicadas = array_flip($aplicadas);

$pendientes = [];
foreach ($archivos as $ruta) {
    $nombre = basename($ruta);
    if (!isset($aplicadas[$nombre])) {
        $pendientes[] = $nombre;
    }
}

if ($marcarAplicadas) {
    $registrar = $pdo->prepare('INSERT IGNORE INTO schema_migrations (nombre_archivo) VALUES (:nombre)');
    $marcadas = 0;
    foreach ($pendientes as $nombre) {
        $registrar->execute([':nombre' => $nombre]);
        fwrite(STDOUT, "  - Marcada como aplicada: {$nombre}" . PHP_EOL);
        $marcadas++;
    }
    salir($marcadas === 0
        ? 'No había migraciones por marcar.'
        : "Se marcaron {$marcadas} migración(es) como aplicadas (sin ejecutarlas).");
}

if (empty($pendientes)) {
    salir('No hay migraciones pendientes. La base de datos está al día.');
}

/**
 * Divide un archivo SQL en sentencias individuales, respetando comillas simples,
 * comillas dobles y comentarios (-- y /* *\/). Evita depender del multi-statement
 * del driver PDO y hace la ejecución determinista.
 */
function dividirSentencias(string $sql): array
{
    $sentencias = [];
    $actual = '';
    $largo = strlen($sql);
    $enComillaSimple = false;
    $enComillaDoble = false;
    $enComentarioLinea = false;
    $enComentarioBloque = false;

    for ($i = 0; $i < $largo; $i++) {
        $c = $sql[$i];
        $siguiente = $i + 1 < $largo ? $sql[$i + 1] : '';

        if ($enComentarioLinea) {
            if ($c === "\n") {
                $enComentarioLinea = false;
                $actual .= $c;
            }
            continue;
        }
        if ($enComentarioBloque) {
            if ($c === '*' && $siguiente === '/') {
                $enComentarioBloque = false;
                $i++;
            }
            continue;
        }
        if ($enComillaSimple) {
            $actual .= $c;
            if ($c === '\\') { $actual .= $siguiente; $i++; continue; }
            if ($c === "'") { $enComillaSimple = false; }
            continue;
        }
        if ($enComillaDoble) {
            $actual .= $c;
            if ($c === '\\') { $actual .= $siguiente; $i++; continue; }
            if ($c === '"') { $enComillaDoble = false; }
            continue;
        }

        if ($c === '-' && $siguiente === '-') { $enComentarioLinea = true; $i++; continue; }
        if ($c === '/' && $siguiente === '*') { $enComentarioBloque = true; $i++; continue; }
        if ($c === "'") { $enComillaSimple = true; $actual .= $c; continue; }
        if ($c === '"') { $enComillaDoble = true; $actual .= $c; continue; }

        if ($c === ';') {
            $sentencia = trim($actual);
            if ($sentencia !== '') { $sentencias[] = $sentencia; }
            $actual = '';
            continue;
        }
        $actual .= $c;
    }

    $sentencia = trim($actual);
    if ($sentencia !== '') { $sentencias[] = $sentencia; }

    return $sentencias;
}

/**
 * Errores que significan "ya estaba aplicado" y que no deben abortar el runner.
 */
function esErrorIdempotente(Throwable $e): bool
{
    $codigo = $e->getCode();
    // 1050: table already exists, 1060: duplicate column name, 1061: duplicate key name.
    if (in_array($codigo, [1050, 1060, 1061], true)) {
        return true;
    }
    return (bool) preg_match('/already exists|Duplicate column|Duplicate key/i', $e->getMessage());
}

// 4) Aplicar cada migración pendiente, sentencia por sentencia. Si una falla, se detiene.
$registrar = $pdo->prepare('INSERT INTO schema_migrations (nombre_archivo) VALUES (:nombre)');

foreach ($pendientes as $nombre) {
    $ruta = $directorioMigraciones . '/' . $nombre;
    $sql = file_get_contents($ruta);
    if ($sql === false || trim($sql) === '') {
        salir("ERROR: no se pudo leer o el archivo está vacío: {$nombre}", 1);
    }

    try {
        foreach (dividirSentencias($sql) as $sentencia) {
            try {
                $pdo->exec($sentencia);
            } catch (Throwable $e) {
                if (!esErrorIdempotente($e)) {
                    throw $e;
                }
            }
        }
        $registrar->execute([':nombre' => $nombre]);
        fwrite(STDOUT, "  OK  {$nombre}" . PHP_EOL);
    } catch (Throwable $e) {
        salir('ERROR aplicando ' . $nombre . ': ' . $e->getMessage() . PHP_EOL
            . 'Se detiene la ejecución. Corrige el problema y vuelve a ejecutar.', 1);
    }
}

salir('Migraciones aplicadas correctamente: ' . count($pendientes) . '.');
