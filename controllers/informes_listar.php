<?php
/**
 * Listado de Informes de Avance
 * Tablas confirmadas: informes_avance + usuarios
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor','estudiante']);
require_once __DIR__ . '/../config/conexion.php';

$rol_actual = $_SESSION['rol_nombre'] ?? '';
$informes = [];

global $conexion;

if ($rol_actual === 'administrador') {
    $stmt = $conexion->query("
        SELECT i.*, 
               CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
        FROM informes_avance i
        LEFT JOIN usuarios u ON i.id_usuario_crea = u.id_usuario
        ORDER BY i.fecha_registro DESC
    ");
} elseif ($rol_actual === 'estudiante') {
    $id_usuario = $_SESSION['id_usuario'];
    $stmt = $conexion->prepare("
        SELECT i.*
        FROM informes_avance i
        WHERE i.id_usuario_crea = :id_usuario
        ORDER BY i.fecha_registro DESC
    ");
    $stmt->bindParam(':id_usuario', $id_usuario);
} else {
    $stmt = $conexion->query("
        SELECT i.*, 
               CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
        FROM informes_avance i
        LEFT JOIN usuarios u ON i.id_usuario_crea = u.id_usuario
        ORDER BY i.fecha_registro DESC
    ");
}

if (isset($stmt) && $stmt) {
    $stmt->execute();
    $informes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$titulo_pagina = 'Informes de Avance';
ob_start();
?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
    body {
        background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                    url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
        min-height: 100vh;
        padding: 30px;
    }
    .contenedor { max-width: 900px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .cabecera { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #ffc107; }
    h1 { color: #003366; font-size: 24px; }
    .btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; font-size: 14px; }
    .btn-nuevo { background: #28a745; color: white; }
    .btn-editar { background: #007bff; color: white; padding: 6px 12px; font-size: 13px; }
    .btn-eliminar { background: #dc3545; color: white; padding: 6px 12px; font-size: 13px; }
    .vacio { text-align: center; color: #666; padding: 40px; font-size: 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    th { background: #f8f9fa; color: #003366; font-weight: bold; }
    .porcentaje { font-weight: bold; color: #28a745; }
    .acciones { display: flex; gap: 6px; }
</style>

<div class="contenedor">
    <div class="cabecera">
        <h1>📄 Informes de Avance</h1>
        <?php if ($rol_actual === 'administrador' || $rol_actual === 'estudiante'): ?>
            <a href="index.php?accion=informe_crear" class="btn btn-nuevo">+ Nuevo Informe</a>
        <?php endif; ?>
    </div>

    <?php if (empty($informes)): ?>
        <p class="vacio">No hay informes registrados aún.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tutoría</th>
                    <th>Título</th>
                    <th>Avance</th>
                    <?php if ($rol_actual !== 'estudiante'): ?><th>Autor</th><?php endif; ?>
                    <th>Fecha</th>
                    <?php if ($rol_actual === 'administrador' || $rol_actual === 'estudiante'): ?><th>Acciones</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($informes as $inf): ?>
                <tr>
                    <td><?= $inf['id_informe'] ?></td>
                    <td>Tutoría #<?= $inf['id_tutoria'] ?></td>
                    <td><?= htmlspecialchars($inf['titulo']) ?></td>
                    <td class="porcentaje"><?= $inf['progreso_porcentaje'] ?>%</td>
                    <?php if ($rol_actual !== 'estudiante'): ?>
                        <td><?= htmlspecialchars($inf['usuario_nombre'] ?? '—') ?></td>
                    <?php endif; ?>
                    <td><?= !empty($inf['fecha_registro']) ? date('d/m/Y H:i', strtotime($inf['fecha_registro'])) : '—' ?></td>
                    <?php if ($rol_actual === 'administrador' || $rol_actual === 'estudiante'): ?>
                    <td class="acciones">
                        <a href="index.php?accion=informe_editar&id=<?= $inf['id_informe'] ?>" class="btn btn-editar">Editar</a>
                        <a href="index.php?accion=informe_eliminar&id=<?= $inf['id_informe'] ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar este informe?')">Eliminar</a>
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