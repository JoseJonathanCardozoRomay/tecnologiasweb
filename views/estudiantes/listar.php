<?php
if (!isset($estudiantes)) $estudiantes = [];
$rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes</title>
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
            background: #fff;
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
        .btn-accion {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 5px;
            font-size: 13px;
        }
        .editar { background: #ffc107; color: #000; }
        .eliminar { background: #dc3545; color: white; }
        .solo-lectura { color: #666; font-style: italic; }
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

        <h1>🎓 Estudiantes</h1>

        <?php if ($rol_actual === 'administrador'): ?>
            <a href="index.php?accion=estudiante_crear" class="btn-nuevo">+ Nuevo Estudiante</a>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Carrera</th>
                    <th>Semestre</th>
                    <th>Registro Universitario</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estudiantes)): ?>
                    <tr>
                        <td colspan="6" class="vacio">
                            <?php if ($rol_actual === 'estudiante'): ?>
                                No tienes datos registrados.
                            <?php else: ?>
                                No hay estudiantes registrados.
                                <?php if ($rol_actual === 'administrador'): ?>
                                    <br><strong>Usa "Nuevo Estudiante" para agregar.</strong>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estudiantes as $e): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?></strong></td>
                        <td><?= htmlspecialchars($e['nombre_carrera']) ?></td>
                        <td><?= $e['semestre'] ?></td>
                        <td><?= htmlspecialchars($e['registro_universitario'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($e['telefono'] ?? '-') ?></td>
                        <td>
                            <?php if ($rol_actual === 'administrador'): ?>
                                <a href="index.php?accion=estudiante_editar&id=<?= $e['id_estudiante'] ?>" class="btn-accion editar">Editar</a>
                                <a href="index.php?accion=estudiante_eliminar&id=<?= $e['id_estudiante'] ?>" 
                                   class="btn-accion eliminar"
                                   onclick="return confirm('¿Eliminar este estudiante?')">Eliminar</a>
                            <?php else: ?>
                                <span class="solo-lectura">Solo lectura</span>
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