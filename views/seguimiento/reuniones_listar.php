<?php if (!isset($rol)) $rol = $_SESSION['rol_nombre'] ?? ''; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reuniones de Seguimiento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body { background: linear-gradient(rgba(0,38,77,0.88),rgba(0,38,77,0.88)), url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 1100px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        h1 { color: #003366; margin-bottom: 25px; border-bottom: 3px solid #ffc107; padding-bottom: 10px; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 5px; border: none; cursor: pointer; font-size: 14px; }
        .btn-nuevo { background: #28a745; color: #fff; }
        .btn-editar { background: #007bff; color: #fff; }
        .btn-eliminar { background: #dc3545; color: #fff; }
        .btn-volver { background: #6c757d; color: #fff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #003366; color: #fff; }
        tr:hover { background: #f8f9fa; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .pendiente { background: #ffc107; color: #000; }
        .completa { background: #28a745; color: #fff; }
        .parcial { background: #17a2b8; color: #fff; }
        .ninguna { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>📅 Reuniones de Seguimiento</h1>
        <a href="index.php" class="btn btn-volver">← Volver</a>
        <?php if (tieneRol(['administrador','tutor'])): ?>
            <a href="index.php?accion=reunion_crear" class="btn btn-nuevo">+ Nueva Reunión</a>
        <?php endif; ?>

        <?php if (empty($todas_reuniones)): ?>
            <p style="text-align:center; padding:30px; color:#666;">No hay reuniones registradas.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Ubicación</th>
                    <th>Asistencia</th>
                    <?php if (tieneRol(['administrador','tutor'])): ?><th>Acciones</th><?php endif; ?>
                </tr>
                <?php foreach ($todas_reuniones as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['titulo']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($r['fecha_reunion'])) ?></td>
                    <td><?= htmlspecialchars($r['ubicacion'] ?? '—') ?></td>
                    <td><span class="badge <?= $r['asistencia'] ?>"><?= ucfirst($r['asistencia']) ?></span></td>
                    <?php if (tieneRol(['administrador','tutor'])): ?>
                    <td>
                        <a href="index.php?accion=reunion_editar&id=<?= $r['id_reunion'] ?>" class="btn btn-editar">Editar</a>
                        <a href="index.php?accion=reunion_eliminar&id=<?= $r['id_reunion'] ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>