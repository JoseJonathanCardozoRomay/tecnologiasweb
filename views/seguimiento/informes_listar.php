<?php if (!isset($rol)) $rol = $_SESSION['rol_nombre'] ?? ''; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informes de Avance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body { background: linear-gradient(rgba(0,38,77,0.88),rgba(0,38,77,0.88)), url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 1100px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        h1 { color: #003366; margin-bottom: 25px; border-bottom: 3px solid #ffc107; padding-bottom: 10px; }
        .btn { display: inline-block; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 3px; border: none; cursor: pointer; font-size: 13px; }
        .btn-nuevo { background: #28a745; color: #fff; }
        .btn-editar { background: #007bff; color: #fff; }
        .btn-enviar { background: #ffc107; color: #000; }
        .btn-revisar { background: #6f42c1; color: #fff; }
        .btn-eliminar { background: #dc3545; color: #fff; }
        .btn-volver { background: #6c757d; color: #fff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #003366; color: #fff; }
        .badge { padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .borrador { background: #e9ecef; color: #333; }
        .enviado { background: #007bff; color: #fff; }
        .aprobado { background: #28a745; color: #fff; }
        .observado { background: #fd7e14; color: #fff; }
        .progreso { height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden; width: 100px; display: inline-block; }
        .progreso-barra { height: 100%; background: #28a745; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>📄 Informes de Avance</h1>
        <a href="index.php" class="btn btn-volver">← Volver</a>
        <?php if (tieneRol(['administrador','estudiante'])): ?>
            <a href="index.php?accion=informe_crear" class="btn btn-nuevo">+ Nuevo Informe</a>
        <?php endif; ?>

        <?php if (empty($informes)): ?>
            <p style="text-align:center; padding:30px; color:#666;">No hay informes registrados.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Título</th>
                    <th>Progreso</th>
                    <th>Estado</th>
                    <th>Creador</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($informes as $inf): ?>
                <tr>
                    <td><?= htmlspecialchars($inf['titulo']) ?></td>
                    <td>
                        <div class="progreso">
                            <div class="progreso-barra" style="width: <?= $inf['progreso_porcentaje'] ?>%"></div>
                        </div>
                        <?= $inf['progreso_porcentaje'] ?>%
                    </td>
                    <td><span class="badge <?= $inf['estado'] ?>"><?= ucfirst($inf['estado']) ?></span></td>
                    <td><?= htmlspecialchars(($inf['nombre_crea'] ?? '') . ' ' . ($inf['apellido_crea'] ?? '')) ?></td>
                    <td><?= date('d/m/Y', strtotime($inf['fecha_registro'])) ?></td>
                    <td>
                        <?php if (tieneRol(['administrador','estudiante']) && $inf['estado'] === 'borrador'): ?>
                            <a href="index.php?accion=informe_editar&id=<?= $inf['id_informe'] ?>" class="btn btn-editar">Editar</a>
                            <a href="index.php?accion=informe_enviar&id=<?= $inf['id_informe'] ?>" class="btn btn-enviar" onclick="return confirm('¿Enviar informe?')">Enviar</a>
                            <a href="index.php?accion=informe_eliminar&id=<?= $inf['id_informe'] ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        <?php endif; ?>
                        <?php if (tieneRol(['administrador','tutor']) && $inf['estado'] === 'enviado'): ?>
                            <a href="index.php?accion=informe_revisar&id=<?= $inf['id_informe'] ?>" class="btn btn-revisar">Revisar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>