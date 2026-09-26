<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modalidades de Grado</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 900px; margin: 0 auto; }
        .tarjeta {
            background: white; border-radius: 12px; padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2); color: #003366;
        }
        h1 { text-align: center; margin-bottom: 25px; }
        .btn-atras {
            display: inline-block; margin-bottom: 20px;
            color: #0066cc; text-decoration: none; font-weight: bold;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #003366; color: white; }
        .etiqueta {
            display: inline-block; padding: 4px 10px; border-radius: 12px;
            font-size: 12px; font-weight: bold;
        }
        .si { background: #d4edda; color: #155724; }
        .no { background: #e2e3e5; color: #555; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="btn-atras">← Volver al Inicio</a>
        <div class="tarjeta">
            <h1>📋 Modalidades de Grado</h1>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Requiere Tutor</th>
                    <th>Flujo</th>
                    <th>Estado</th>
                </tr>
                <?php foreach ($modalidades as $m): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($m['codigo']) ?></strong></td>
                    <td><?= htmlspecialchars($m['nombre']) ?></td>
                    <td>
                        <span class="etiqueta <?= $m['requiere_tutor'] ? 'si' : 'no' ?>">
                            <?= $m['requiere_tutor'] ? 'Sí' : 'No' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($m['flujo']) ?></td>
                    <td>
                        <span class="etiqueta <?= $m['activa'] ? 'si' : 'no' ?>">
                            <?= $m['activa'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>