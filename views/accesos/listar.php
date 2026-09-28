<?php
if (!isset($accesos)) $accesos = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Accesos</title>
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
        .exito { color: #2f855a; font-weight: bold; }
        .fallido { color: #c53030; font-weight: bold; }
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
        
        <h1>📊 Registro de Accesos</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Fecha y Hora</th>
                    <th>IP de Origen</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accesos)): ?>
                    <tr>
                        <td colspan="5" class="vacio">
                            No hay registros de acceso.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accesos as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['id_acceso'] ?? '') ?></td>
                        <td><strong><?= htmlspecialchars(($a['nombre'] ?? '') . ' ' . ($a['apellido'] ?? '')) ?></strong></td>
                        <td><?= htmlspecialchars($a['fecha_hora'] ?? '') ?></td>
                        <td><?= htmlspecialchars($a['ip_origen'] ?? '—') ?></td>
                        <td class="<?= ($a['resultado'] ?? '') === 'exitoso' ? 'exito' : 'fallido' ?>">
                            <?= htmlspecialchars(ucfirst($a['resultado'] ?? 'desconocido')) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>