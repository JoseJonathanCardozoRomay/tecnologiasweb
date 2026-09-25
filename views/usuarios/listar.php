<?php
if (!isset($usuarios)) $usuarios = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>
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
        .estado-activo {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #28a745;
            font-weight: bold;
        }
        .estado-inactivo {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #dc3545;
            font-weight: bold;
        }
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
        
        <h1>👥 Listado de Usuarios</h1>

        <?php if ($rol_actual === 'administrador'): ?>
            <a href="index.php?accion=usuario_crear" class="btn-nuevo">+ Nuevo Usuario</a>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" class="vacio">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id_usuario'] ?></td>
                        <td><strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></strong></td>
                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                        <td><?= htmlspecialchars($u['nombre_rol']) ?></td>
                        <td>
                            <?php if (($u['estado'] ?? '') === 'activo'): ?>
                                <span class="estado-activo">✅ Activo</span>
                            <?php else: ?>
                                <span class="estado-inactivo">❌ Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($rol_actual === 'administrador'): ?>
                                <a href="index.php?accion=usuario_editar&id=<?= $u['id_usuario'] ?>" class="editar">Editar</a>
                                <a href="index.php?accion=usuario_eliminar&id=<?= $u['id_usuario'] ?>" 
                                   class="eliminar"
                                   onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</a>
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