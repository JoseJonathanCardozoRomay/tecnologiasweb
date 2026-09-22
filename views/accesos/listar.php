<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Accesos — Auditoría</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body { 
            background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)), 
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920') center/cover no-repeat fixed; 
            min-height: 100vh; padding: 30px; 
        }
        .contenedor { max-width: 1100px; margin: 0 auto; }
        .encabezado { 
            background: linear-gradient(90deg,#00264d,#003366); color: white; 
            padding: 22px 30px; border-radius: 10px; margin-bottom: 25px; 
            display: flex; justify-content: space-between; align-items: center; 
            border-bottom: 3px solid #cc9900; 
        }
        .volver-btn { 
            background: rgba(255,255,255,0.18); color: white; 
            padding: 10px 20px; border-radius: 6px; text-decoration: none; 
            border: 1px solid rgba(255,255,255,0.3); 
        }
        .tarjeta { 
            background: rgba(255,255,255,0.97); padding: 35px; border-radius: 10px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.25); border-top: 4px solid #cc9900; 
        }
        .mensaje { padding: 15px 20px; border-radius: 6px; margin-bottom: 20px; }
        .mensaje-exito { background: #e6f9e6; border-left: 4px solid #00802b; color: #006622; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #003366; color: white; padding: 14px 12px; text-align: left; font-weight: bold; }
        tr:nth-child(even) { background: rgba(240,244,248,0.6); }
        tr:hover { background: rgba(204,153,0,0.08); }
        td { padding: 13px 12px; border-bottom: 1px solid #d9e2eb; }
        .btn { display: inline-block; padding: 9px 16px; border: none; border-radius: 6px; 
               font-size: 14px; font-weight: bold; text-decoration: none; margin: 3px; cursor: pointer; }
        .btn-peligro { background: linear-gradient(90deg,#b30000,#cc0000); color: white; }
        .exito { color: #00802b; font-weight: bold; }
        .fallido { color: #b30000; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>📋 Registro de Accesos — Auditoría</h1>
            <a href="index.php" class="volver-btn">← Volver</a>
        </div>
        <div class="tarjeta">
            <?php if ($mensaje === 'eliminado'): ?>
                <div class="mensaje mensaje-exito">✅ Registro eliminado.</div>
            <?php endif; ?>
            
            <table>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Fecha y Hora</th>
                    <th>Dirección IP</th>
                    <th>Resultado</th>
                    <th>Acción</th>
                </tr>
                <?php while ($reg = $registros->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $reg['id_acceso'] ?></td>
                    <td><?= htmlspecialchars($reg['usuario_nombre'] ?? 'Anónimo') ?></td>
                    <td><?= date('d/m/Y H:i:s', strtotime($reg['fecha_hora'])) ?></td>
                    <td><?= htmlspecialchars($reg['ip_origen'] ?? '-') ?></td>
                    <td class="<?= $reg['resultado'] === 'exitoso' ? 'exito' : 'fallido' ?>">
                        <?= ucfirst($reg['resultado']) ?>
                    </td>
                    <td>
                        <a href="index.php?accion=registro_acceso_eliminar&id=<?= $reg['id_acceso'] ?>" 
                           class="btn btn-peligro" 
                           onclick="return confirm('¿Eliminar este registro?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>