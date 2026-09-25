<?php
if (!isset($bloques)) $bloques = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Bloques Horarios</title>
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
        tr:nth-child(even) { background: #f0f5ff; }
        tr:hover { background: #e6f0ff; }
        .editar {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 8px;
        }
        .editar:hover { background: #218838; }
        .eliminar {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .eliminar:hover { background: #c82333; }
        .vacio {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al inicio</a>
        
        <h1>🕐 Listado de Bloques Horarios</h1>

        <?php if ($rol_actual === 'administrador'): ?>
            <a href="index.php?accion=bloque_crear" class="btn-nuevo">+ Nuevo Bloque</a>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Bloque</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bloques)): ?>
                    <tr>
                        <td colspan="6" class="vacio">
                            No hay bloques horarios registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bloques as $b): ?>
                    <tr>
                        <td><?= $b['id_bloque'] ?></td>
                        <td><strong><?= htmlspecialchars($b['nombre_bloque']) ?></strong></td>
                        <td><?= date('H:i', strtotime($b['hora_inicio'])) ?></td>
                        <td><?= date('H:i', strtotime($b['hora_fin'])) ?></td>
                        <td><?= htmlspecialchars($b['descripcion'] ?? '—') ?></td>
                        <td>
                            <?php if ($rol_actual === 'administrador'): ?>
                                <a href="index.php?accion=bloque_editar&id=<?= $b['id_bloque'] ?>" class="editar">Editar</a>
                                <a href="index.php?accion=bloque_eliminar&id=<?= $b['id_bloque'] ?>" 
                                   class="eliminar"
                                   onclick="return confirm('¿Seguro que quieres eliminar este bloque?')">Eliminar</a>
                            <?php else: ?>
                                <em>Solo lectura</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>