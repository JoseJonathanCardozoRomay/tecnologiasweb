 <?php
/**
 * Listado de Reuniones de Seguimiento
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor','estudiante']);
require_once __DIR__ . '/../config/conexion.php';

$rol_actual = $_SESSION['rol_nombre'] ?? '';
$reuniones = [];

global $conexion;

if ($rol_actual === 'administrador' || $rol_actual === 'coordinador_mg') {
    $stmt = $conexion->query("
        SELECT r.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo
        FROM reuniones_seguimiento r
        LEFT JOIN usuarios u ON r.id_usuario_creo = u.id_usuario
        ORDER BY r.fecha DESC, r.hora DESC
    ");
} elseif ($rol_actual === 'tutor') {
    $id_tutor = $_SESSION['id_usuario'];
    $stmt = $conexion->prepare("
        SELECT r.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo
        FROM reuniones_seguimiento r
        LEFT JOIN usuarios u ON r.id_usuario_creo = u.id_usuario
        WHERE r.id_usuario_creo = :id_usuario
        ORDER BY r.fecha DESC, r.hora DESC
    ");
    $stmt->bindParam(':id_usuario', $id_tutor);
} else {
    $stmt = $conexion->query("
        SELECT r.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo
        FROM reuniones_seguimiento r
        LEFT JOIN usuarios u ON r.id_usuario_creo = u.id_usuario
        ORDER BY r.fecha DESC, r.hora DESC
    ");
}

if (isset($stmt)) {
    $stmt->execute();
    $reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$titulo_pagina = 'Reuniones de Seguimiento';
ob_start();
?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
    body {
        background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                    url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
        min-height: 100vh;
        padding: 30px;
    }
    .contenedor {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .cabecera {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ffc107;
    }
    h1 { color: #003366; font-size: 24px; display: flex; align-items: center; gap: 10px; }
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-volver { background: #6c757d; color: white; margin-right: 8px; }
    .btn-nuevo { background: #28a745; color: white; }
    .btn-editar { background: #007bff; color: white; padding: 6px 12px; font-size: 13px; }
    .btn-eliminar { background: #dc3545; color: white; padding: 6px 12px; font-size: 13px; }
    .vacio {
        text-align: center;
        color: #666;
        padding: 40px;
        font-size: 16px;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    th { background: #f8f9fa; color: #003366; font-weight: bold; }
    .acciones { display: flex; gap: 6px; }
</style>

<div class="contenedor">
    <div class="cabecera">
        <h1>📅 Reuniones de Seguimiento</h1>
        <div>
            <a href="index.php" class="btn btn-volver">← Volver</a>
            <?php if (tieneRol(['administrador','tutor'])): ?>
                <a href="index.php?accion=reunion_crear" class="btn btn-nuevo">+ Nueva Reunión</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($reuniones)): ?>
        <p class="vacio">No hay reuniones registradas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Lugar</th>
                    <th>Asistentes</th>
                    <th>Registró</th>
                    <?php if (tieneRol(['administrador','tutor'])): ?><th>Acciones</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reuniones as $r): ?>
                <tr>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($r['fecha']))) ?></td>
                    <td><?= htmlspecialchars(date('H:i', strtotime($r['hora']))) ?></td>
                    <td><?= htmlspecialchars($r['lugar'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['asistentes'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['nombre_completo'] ?? '—') ?></td>
                    <?php if (tieneRol(['administrador','tutor'])): ?>
                    <td class="acciones">
                        <a href="index.php?accion=reunion_editar&id=<?= $r['id_reunion'] ?>" class="btn btn-editar">Editar</a>
                        <a href="index.php?accion=reunion_eliminar&id=<?= $r['id_reunion'] ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar esta reunión?')">Eliminar</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';