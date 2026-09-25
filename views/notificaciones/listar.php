<?php
if (!isset($notificaciones)) $notificaciones = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
if (!isset($id_usuario_actual)) $id_usuario_actual = $_SESSION['id_usuario'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
        }
        .btn-nuevo {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .fila-nueva { background: #fff3cd !important; border-left: 4px solid #ffc107; }
        .etiqueta-nueva {
            display: inline-block;
            background: #ffc107;
            color: #000;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
        }
        .etiqueta-leida {
            display: inline-block;
            background: #d1e7dd;
            color: #0f5132;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #004080;
            color: white;
            padding: 14px 10px;
            text-align: left;
        }
        td {
            padding: 14px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even):not(.fila-nueva) { background: #f0f5ff; }
        tr:hover { background: #e6f0ff; }
        .eliminar {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .eliminar:hover { background: #c82333; }
        .vacio {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .tipo { font-weight: bold; color: #003366; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al inicio</a>
        
        <h1>🔔 Notificaciones</h1>

        <?php if ($rol_actual === 'administrador'): ?>
            <a href="index.php?accion=notificacion_crear" class="btn-nuevo">+ Nueva Notificación</a>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <?php if ($rol_actual === 'administrador'): ?><th>Destinatario</th><?php endif; ?>
                    <th>Tipo</th>
                    <th>Mensaje</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <?php if ($rol_actual === 'administrador'): ?><th>Acciones</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notificaciones)): ?>
                    <tr>
                        <td colspan="<?= ($rol_actual === 'administrador') ? 7 : 5 ?>" class="vacio">
                            No tienes notificaciones pendientes. ✅
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($notificaciones as $n): ?>
                    <tr class="<?= empty($n['leida']) ? 'fila-nueva' : '' ?>">
                        <td><?= $n['id_notificacion'] ?></td>
                        <?php if ($rol_actual === 'administrador'): ?>
                            <td><strong><?= htmlspecialchars(($n['nombre'] ?? '') . ' ' . ($n['apellido'] ?? '')) ?></strong></td>
                        <?php endif; ?>
                        <td class="tipo"><?= htmlspecialchars($n['tipo']) ?></td>
                        <td><?= htmlspecialchars($n['mensaje']) ?></td>
                        <td>
                            <?php if (empty($n['leida'])): ?>
                                <span class="etiqueta-nueva">🔔 Nueva</span>
                            <?php else: ?>
                                <span class="etiqueta-leida">✅ Leída</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($n['fecha_creacion'])) ?></td>
                        <?php if ($rol_actual === 'administrador'): ?>
                            <td>
                                <a href="index.php?accion=notificacion_eliminar&id=<?= $n['id_notificacion'] ?>" 
                                   class="eliminar"
                                   onclick="return confirm('¿Eliminar esta notificación?')">Eliminar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>