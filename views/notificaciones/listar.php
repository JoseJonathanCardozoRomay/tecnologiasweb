<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .contenedor {
            max-width: 1000px;
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
        .aviso-nuevas {
            background: #fff3cd;
            border-left: 4px solid #ff9900;
            color: #856404;
            padding: 12px 20px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-marcar {
            background: #0066cc;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-marcar:hover { background: #0052a3; }
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
        tr.no-leida { background: #fff3cd; font-weight: bold; border-left: 3px solid #ff9900; }
        .estado-leida { color: #008000; font-weight: bold; }
        .estado-noleida { color: #cc6600; font-weight: bold; }
        em { color: #999; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>🔔 Notificaciones</h1>

        <?php
        $sin_leer = 0;
        if (!empty($notificaciones)) {
            foreach ($notificaciones as $n) {
                if (($n['leida'] ?? 0) == 0) $sin_leer++;
            }
        }
        if ($sin_leer > 0):
        ?>
        <div class="aviso-nuevas">
            📢 Tienes <strong><?= $sin_leer ?></strong> notificación(es) nueva(s)
            <form method="post" style="margin:0;">
                <button type="submit" name="marcar_leidas" class="btn-marcar">Marcar como leídas</button>
            </form>
        </div>
        <?php endif; ?>

        <a href="index.php" class="btn-volver">← Volver al Menú</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Mensaje</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notificaciones)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px; color:#666;">No hay notificaciones</td>
                </tr>
                <?php else: ?>
                <?php foreach ($notificaciones as $fila): ?>
                <tr class="<?= ($fila['leida'] ?? 0) == 0 ? 'no-leida' : '' ?>">
                    <td><?= $fila['id_notificacion'] ?></td>
                    <td><?= htmlspecialchars($fila['tipo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fila['mensaje'] ?? '') ?></td>
                    <td class="<?= ($fila['leida'] ?? 0) == 1 ? 'estado-leida' : 'estado-noleida' ?>">
                        <?= ($fila['leida'] ?? 0) == 1 ? '✅ Leída' : '🔴 No leída' ?>
                    </td>
                    <td>
                        <?= isset($fila['fecha_creacion']) ? date('d/m/Y H:i', strtotime($fila['fecha_creacion'])) : '' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>