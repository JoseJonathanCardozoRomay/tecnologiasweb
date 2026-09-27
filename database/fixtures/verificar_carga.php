<?php
/**
 * VERIFICACION POST CARGA
 * ---------------------------------------------------------------------
 * Comprueba que los archivos que quedaron en uploads/ superan el mismo
 * verify_stored_file() que usa la aplicacion antes de servirlos, y que
 * las rutas registradas en la base apuntan a archivos reales.
 *
 *     docker exec tutorias_web php /var/www/html/database/fixtures/verificar_carga.php
 */

$raiz = dirname(__DIR__, 2);
require_once $raiz . '/includes/archivos.php';
require_once $raiz . '/config/conexion.php';

$ok = 0;
$mal = 0;

function revisar(string $ruta, string $carpeta, string $contexto): void
{
    global $ok, $mal;
    $v = verify_stored_file($ruta, $carpeta, 1);
    if (!empty($v['ok'])) {
        printf("  [OK  ] %-11s %-62s %7d bytes\n", $contexto, $ruta, $v['bytes']);
        $ok++;
    } else {
        printf("  [FALLA] %-9s %-62s %s\n", $contexto, $ruta, $v['motivo']);
        $mal++;
    }
}

echo "ARCHIVOS ALMACENADOS QUE SIRVE LA APLICACION\n";
echo str_repeat('=', 92) . "\n";

$comprobantes = $pdo->query(
    'SELECT id_comprobante, ruta_archivo FROM comprobantes_pago_mg
      WHERE ruta_archivo LIKE "%comprobante_mg_%" ORDER BY id_comprobante'
)->fetchAll();
foreach ($comprobantes as $c) {
    revisar($c['ruta_archivo'], 'uploads', 'comprobante');
}

$reuniones = $pdo->query(
    'SELECT id_reunion, evidencia_url FROM reuniones
      WHERE evidencia_url LIKE "%evidencia_1790525%" ORDER BY id_reunion'
)->fetchAll();
foreach ($reuniones as $r) {
    revisar((string) $r['evidencia_url'], 'uploads', 'reunion');
}

$documentos = $pdo->query(
    'SELECT id_documento, ruta_archivo FROM documentos_expediente
      WHERE ruta_archivo LIKE "%_1790525%" ORDER BY id_documento'
)->fetchAll();
foreach ($documentos as $d) {
    revisar((string) $d['ruta_archivo'], 'uploads', 'expediente');
}

echo str_repeat('=', 92) . "\n";
printf("Archivos verificados: %d correctos, %d con error\n\n", $ok, $mal);

echo "PADRON IMPORTADO (cohorte 2026-I)\n";
echo str_repeat('-', 92) . "\n";
$padron = $pdo->query(
    "SELECT e.id_estudiante, e.registro_universitario, u.nombre, u.apellido,
            u.correo, c.nombre_carrera, e.semestre, e.materias_completadas, e.acceso_mg_desbloqueado
       FROM estudiantes e
       JOIN usuarios u ON u.id_usuario = e.id_usuario
       LEFT JOIN carreras c ON c.id_carrera = e.id_carrera
      WHERE e.registro_universitario LIKE '900001%'
      ORDER BY e.registro_universitario"
)->fetchAll();
foreach ($padron as $p) {
    printf(
        "  %-10s %-26s %-34s %-26s sem=%-2d mat=%-2d mg=%d\n",
        $p['registro_universitario'],
        $p['nombre'] . ' ' . $p['apellido'],
        $p['correo'],
        (string) $p['nombre_carrera'],
        (int) $p['semestre'],
        (int) $p['materias_completadas'],
        (int) $p['acceso_mg_desbloqueado']
    );
}
echo str_repeat('-', 92) . "\n";
printf("Estudiantes creados por el padron: %d\n\n", count($padron));

echo "NOTIFICACIONES GENERADAS PARA LOS COMPROBANTES\n";
echo str_repeat('-', 92) . "\n";
$notis = $pdo->query(
    "SELECT n.id_notificacion, n.tipo, u.usuario AS destinatario, LEFT(n.mensaje, 62) AS mensaje
       FROM notificaciones n JOIN usuarios u ON u.id_usuario = n.id_destinatario
      WHERE n.tipo = 'COMPROBANTE_MG' ORDER BY n.id_notificacion DESC LIMIT 3"
)->fetchAll();
foreach ($notis as $n) {
    printf("  #%d  %-14s -> %-10s %s\n", $n['id_notificacion'], $n['tipo'], $n['destinatario'], $n['mensaje']);
}
echo str_repeat('=', 92) . "\n";

exit($mal === 0 ? 0 : 1);
