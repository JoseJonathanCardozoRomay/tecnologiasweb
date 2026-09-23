<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Tutores</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .contenedor {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-volver:hover { background: #5a6268; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #003366;
            color: white;
        }
        tr:hover { background: #f0f5ff; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>📋 Listado de Tutores</h1>

        <a href="index.php" class="btn-volver">← Volver al Menú</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Especialidad</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tutores)): ?>
                <tr>
                    <td colspan="3" style="text-align:center; padding:20px; color:#666;">No hay tutores registrados</td>
                </tr>
                <?php else: ?>
                <?php foreach ($tutores as $fila): ?>
                <tr>
                    <td><?= $fila['id_tutor'] ?></td>
                    <td>
                        <?= htmlspecialchars(($fila['nombre'] ?? '') . ' ' . ($fila['apellido'] ?? '')) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($fila['especialidad'] ?? '') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>