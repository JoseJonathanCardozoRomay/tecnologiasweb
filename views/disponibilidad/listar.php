<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Disponibilidad de Tutores</title>
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
        .disponible { color: #00802b; font-weight: bold; }
        .ocupado { color: #b30000; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>📅 Disponibilidad de Tutores</h1>
            <a href="index.php" class="volver-btn">← Volver</a>
        </div>
        <div class="tarjeta">
            <a href="index.php?accion=disponibilidad_crear" class="btn btn-primario nuevo">+ Nueva Disponibilidad</a>
            <?php if (($mensaje ?? '') === 'creado'): ?><div class="mensaje mensaje-exito">✅ Disponibilidad registrada.</div><?php endif; ?>
            <?php if (($mensaje ?? '') === 'actualizado'): ?><div class="mensaje mensaje-exito">✅ Disponibilidad actualizada.</div><?php endif; ?>
            <?php if (($mensaje ?? '') === 'eliminado'): ?><div class="mensaje mensaje-exito">✅ Disponibilidad eliminada.</div><?php endif; ?>
            <table>
                <tr><th>ID</th><th>Tutor</th><th>Día</th><th>Hora Inicio</th><th>Hora Fin</th><th>Estado</th><th>Acciones</th></tr>
                <?php while ($d = $disponibilidad->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $d['id_disponibilidad'] ?></td>
                    <td><?= htmlspecialchars($d['nombre_tutor']) ?></td>
                    <td><?= htmlspecialchars($d['dia_semana']) ?></td>
                    <td><?= substr($d['hora_inicio'],0,5) ?></td>
                    <td><?= substr($d['hora_fin'],0,5) ?></td>
                    <td class="<?= $d['estado']==='disponible'?'disponible':'ocupado' ?>">
                        <?= ucfirst($d['estado']) ?>
                    </td>
                    <td>
                        <a href="index.php?accion=disponibilidad_editar&id=<?= $d['id_disponibilidad'] ?>" class="btn btn-aviso">Editar</a>
                        <a href="index.php?accion=disponibilidad_eliminar&id=<?= $d['id_disponibilidad'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>