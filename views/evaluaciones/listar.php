<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluaciones de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body { background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 1100px; margin: 0 auto; }
        .encabezado { background: linear-gradient(90deg,#00264d,#003366); color: white; padding: 22px 30px; border-radius: 10px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #cc9900; }
        .volver-btn { background: rgba(255,255,255,0.18); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); }
        .tarjeta { background: rgba(255,255,255,0.97); padding: 35px; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.25); border-top: 4px solid #cc9900; }
        .mensaje { padding: 15px 20px; border-radius: 6px; margin-bottom: 20px; }
        .mensaje-exito { background: #e6f9e6; border-left: 4px solid #00802b; color: #006622; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #003366; color: white; padding: 14px 12px; text-align: left; font-weight: bold; }
        tr:nth-child(even) { background: rgba(240,244,248,0.6); }
        tr:hover { background: rgba(204,153,0,0.08); }
        td { padding: 13px 12px; border-bottom: 1px solid #d9e2eb; }
        .btn { display: inline-block; padding: 9px 16px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; text-decoration: none; margin: 3px; cursor: pointer; }
        .btn-primario { background: linear-gradient(90deg,#003366,#004080); color: white; }
        .btn-aviso { background: linear-gradient(90deg,#cc9900,#e6ac00); color: #00264d; }
        .btn-peligro { background: linear-gradient(90deg,#b30000,#cc0000); color: white; }
        .nuevo { margin-bottom: 20px; display: inline-block; }
        .estrellas { color: #cc9900; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>⭐ Evaluaciones de Tutorías</h1>
            <a href="index.php" class="volver-btn">← Volver</a>
        </div>
        <div class="tarjeta">
            <a href="index.php?accion=evaluacion_crear" class="btn btn-primario nuevo">+ Nueva Evaluación</a>
            <?php if ($mensaje === 'creado'): ?><div class="mensaje mensaje-exito">✅ Evaluación registrada.</div><?php endif; ?>
            <?php if ($mensaje === 'actualizado'): ?><div class="mensaje mensaje-exito">✅ Evaluación actualizada.</div><?php endif; ?>
            <?php if ($mensaje === 'eliminado'): ?><div class="mensaje mensaje-exito">✅ Evaluación eliminada.</div><?php endif; ?>
            <table>
                <tr><th>ID</th><th>Estudiante</th><th>Materia</th><th>Calificación</th><th>Fecha</th><th>Acciones</th></tr>
                <?php while ($e = $evaluaciones->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $e['id_evaluacion'] ?></td>
                    <td><?= htmlspecialchars($e['estudiante_nombre'] ?? '') ?></td>
                    <td><?= htmlspecialchars($e['nombre_materia'] ?? '') ?></td>
                    <td class="estrellas"><?= str_repeat('★', $e['calificacion']) ?><?= str_repeat('☆', 5 - $e['calificacion']) ?> (<?= $e['calificacion'] ?>/5)</td>
                    <td><?= isset($e['fecha_evaluacion']) ? date('d/m/Y', strtotime($e['fecha_evaluacion'])) : '-' ?></td>
                    <td>
                        <a href="index.php?accion=evaluacion_editar&id=<?= $e['id_evaluacion'] ?>" class="btn btn-aviso">Editar</a>
                        <a href="index.php?accion=evaluacion_eliminar&id=<?= $e['id_evaluacion'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>