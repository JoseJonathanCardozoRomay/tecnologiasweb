<?php
if (!isset($evaluaciones)) $evaluaciones = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
$titulo_pagina = 'Evaluaciones de Tutorías';
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluaciones de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 1000px;
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
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
        }
        .btn-nuevo {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-nuevo:hover { background: #218838; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #003366;
            color: white;
            padding: 12px 10px;
            text-align: left;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
        }
        .vacio {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .btn-editar {
            background: #0066cc;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
        }
        .btn-eliminar {
            background: #dc3545;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al inicio</a>
        <h1>⭐ Evaluaciones de Tutorías</h1>

        <?php if ($rol_actual === 'administrador' || $rol_actual === 'estudiante'): ?>
        <a href="index.php?accion=evaluacion_crear" class="btn-nuevo">+ Nueva Evaluación</a>
        <?php endif; ?>

        <?php if (empty($evaluaciones)): ?>
        <div class="vacio">
            <p>No hay evaluaciones registradas.</p>
            <p style="margin-top:10px; font-size:14px; color:#888;">
                Haz clic en "+ Nueva Evaluación" para agregar la primera.
            </p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Tutoría</th>
                    <th>Calificación</th>
                    <th>Comentario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evaluaciones as $e): ?>
                <tr>
                    <td>#<?= $e['id_evaluacion'] ?? '-' ?></td>
                    <td>#<?= $e['id_tutoria'] ?? '-' ?></td>
                    <td><strong><?= htmlspecialchars($e['calificacion'] ?? '-') ?></strong></td>
                    <td><?= htmlspecialchars(substr($e['comentario'] ?? '-', 0, 30)) ?>...</td>
                    <td>
                        <?php if ($rol_actual === 'administrador' || $rol_actual === 'estudiante'): ?>
                        <a href="index.php?accion=evaluacion_editar&id=<?= $e['id_evaluacion'] ?>" class="btn-editar">Editar</a>
                        <?php endif; ?>
                        <?php if ($rol_actual === 'administrador'): ?>
                        <a href="index.php?accion=evaluacion_eliminar&id=<?= $e['id_evaluacion'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';