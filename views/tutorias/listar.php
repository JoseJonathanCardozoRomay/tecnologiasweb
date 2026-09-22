<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body { background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 1200px; margin: 0 auto; }
        .encabezado { background: linear-gradient(90deg,#00264d,#003366); color: white; padding: 22px 30px; border-radius: 10px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #cc9900; }
        .volver-btn { background: rgba(255,255,255,0.18); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); }
        .tarjeta { background: rgba(255,255,255,0.97); padding: 35px; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.25); border-top: 4px solid #cc9900; }
        .mensaje { padding: 15px 20px; border-radius: 6px; margin-bottom: 20px; }
        .mensaje-exito { background: #e6f9e6; border-left: 4px solid #00802b; color: #006622; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        th { background: #003366; color: white; padding: 12px 10px; text-align: left; font-weight: bold; }
        tr:nth-child(even) { background: rgba(240,244,248,0.6); }
        tr:hover { background: rgba(204,153,0,0.08); }
        td { padding: 11px 10px; border-bottom: 1px solid #d9e2eb; }
        .btn { display: inline-block; padding: 8px 14px; border: none; border-radius: 6px; font-size: 13px; font-weight: bold; text-decoration: none; margin: 2px; cursor: pointer; }
        .btn-primario { background: linear-gradient(90deg,#003366,#004080); color: white; }
        .btn-aviso { background: linear-gradient(90deg,#cc9900,#e6ac00); color: #00264d; }
        .btn-peligro { background: linear-gradient(90deg,#b30000,#cc0000); color: white; }
        .nuevo { margin-bottom: 20px; display: inline-block; }
        .pendiente { color: #b38600; font-weight: bold; }
        .confirmada { color: #00802b; font-weight: bold; }
        .cancelada { color: #b30000; font-weight: bold; }
        .realizada { color: #0066cc; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>📋 Tutorías Programadas</h1>
            <a href="index.php" class="volver-btn">← Volver</a>
        </div>
        <div class="tarjeta">
            <a href="index.php?accion=tutoria_crear" class="btn btn-primario nuevo">+ Nueva Tutoría</a>
            <?php if (($mensaje ?? '') === 'creado'): ?><div class="mensaje mensaje-exito">✅ Tutoría programada.</div><?php endif; ?>
            <?php if (($mensaje ?? '') === 'actualizado'): ?><div class="mensaje mensaje-exito">✅ Tutoría actualizada.</div><?php endif; ?>
            <?php if (($mensaje ?? '') === 'eliminado'): ?><div class="mensaje mensaje-exito">✅ Tutoría eliminada.</div><?php endif; ?>
            <table>
                <tr><th>ID</th><th>Tutor</th><th>Estudiante</th><th>Materia</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Acciones</th></tr>
                <?php while ($t = $tutorias->fetch(PDO::FETCH_ASSOC)):
                $clase = match($t['estado']??'pendiente') {
                    'confirmada'=>'confirmada','cancelada'=>'cancelada','realizada'=>'realizada','pendiente'=> 'pendiente'
                };
                ?>
                <tr>
                    <td><?= $t['id_tutoria'] ?></td>
                    <td><?= htmlspecialchars($t['tutor_nombre']) ?></td>
                    <td><?= htmlspecialchars($t['estudiante_nombre']) ?></td>
                    <td><?= htmlspecialchars($t['materia_nombre']) ?></td>
                    <td><?= date('d/m/Y',strtotime($t['fecha'])) ?></td>
                    <td><?= substr($t['hora_inicio'],0,5) ?> - <?= substr($t['hora_fin'],0,5) ?></td>
                    <td class="<?= $clase ?>"><?= ucfirst($t['estado']??'pendiente') ?></td>
                    <td>
                        <a href="index.php?accion=tutoria_editar&id=<?= $t['id_tutoria'] ?>" class="btn btn-aviso">Editar</a>
                        <a href="index.php?accion=tutoria_eliminar&id=<?= $t['id_tutoria'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>