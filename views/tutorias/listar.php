<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .contenedor {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 25px;
        }
        .botones-superior {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn-volver:hover { background: #5a6268; }
        .btn-nuevo {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn-nuevo:hover { background: #0052a3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #003366;
            color: white;
        }
        tr:hover { background: #f0f5ff; }
        .pendiente { color: #e67700; font-weight: bold; }
        .confirmada { color: #008000; font-weight: bold; }
        .realizada { color: #0066cc; font-weight: bold; }
        .cancelada { color: #cc0000; font-weight: bold; }
        a.editar { color: #0066cc; text-decoration: none; margin-right: 10px; }
        a.eliminar { color: #cc0000; text-decoration: none; }
        em { color: #999; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>📋 Listado de Tutorías</h1>

        <div class="botones-superior">
            <a href="index.php" class="btn-volver">← Volver al Menú</a>
            <a href="index.php?accion=tutoria_crear" class="btn-nuevo">+ Nueva Tutoría</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Tutor</th>
                    <th>Materia</th>
                    <th>Fecha / Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tutorias)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:20px; color:#666;">No hay tutorías registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($tutorias as $fila): ?>
                <tr>
                    <td><?= $fila['id_tutoria'] ?></td>
                    <td>
                        <?php if (!empty($fila['nombre_estudiante'])): ?>
                            <?= htmlspecialchars($fila['nombre_estudiante'] . ' ' . $fila['apellido_estudiante']) ?>
                        <?php else: ?>
                            <em>Sin datos</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($fila['nombre_tutor'])): ?>
                            <?= htmlspecialchars($fila['nombre_tutor'] . ' ' . $fila['apellido_tutor']) ?>
                        <?php else: ?>
                            <em>Sin datos</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($fila['nombre_materia'])): ?>
                            <?= htmlspecialchars($fila['nombre_materia']) ?>
                        <?php else: ?>
                            <em>-</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= date('Y-m-d', strtotime($fila['fecha'])) ?><br>
                        <?= date('H:i', strtotime($fila['hora_inicio'])) ?> - <?= date('H:i', strtotime($fila['hora_fin'])) ?>
                    </td>
                    <td class="<?= $fila['estado'] ?>">
                        <?= ucfirst($fila['estado']) ?>
                    </td>
                    <td>
                        <a href="index.php?accion=tutoria_editar&id=<?= $fila['id_tutoria'] ?>" class="editar">Editar</a>
                        <a href="index.php?accion=tutoria_eliminar&id=<?= $fila['id_tutoria'] ?>" class="eliminar" onclick="return confirm('¿Eliminar esta tutoría?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>