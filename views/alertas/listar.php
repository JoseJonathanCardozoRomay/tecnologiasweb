<?php
if (!isset($alertas)) $alertas = [];
if (!isset($total_sin_leer)) $total_sin_leer = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas de Seguimiento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .contenedor {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #ffd700;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffc107;
        }
        .volver-btn {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .volver-btn:hover { background: #5a6268; }
        .alerta-tarjeta {
            background: white;
            color: #003366;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            border-left: 5px solid #0066cc;
        }
        .alerta-tarjeta.sin-leer {
            background: #e6f0ff;
            border-left-color: #ff9900;
        }
        .alerta-mensaje {
            font-size: 15px;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        .alerta-fecha {
            font-size: 12px;
            color: #666;
            margin-bottom: 12px;
        }
        .marcar-leida-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .marcar-leida-btn:hover { background: #218838; }
        .sin-alertas {
            background: rgba(255,255,255,0.9);
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            color: #003366;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver-btn">← Volver</a>
        
        <h1>⚠️ Alertas de Seguimiento</h1>

        <?php if (empty($alertas)): ?>
            <div class="sin-alertas">
                ✅ No tienes alertas pendientes.
            </div>
        <?php else: ?>
            <?php foreach ($alertas as $alerta): ?>
                <div class="alerta-tarjeta <?= empty($alerta['leida']) ? 'sin-leer' : '' ?>">
                    <div class="alerta-mensaje">
                        <?= htmlspecialchars($alerta['mensaje']) ?>
                    </div>
                    <div class="alerta-fecha">
                        📅 <?= date('d/m/Y H:i', strtotime($alerta['fecha_creacion'])) ?>
                    </div>
                    <?php if (empty($alerta['leida'])): ?>
                        <form method="POST" action="index.php?accion=alerta_marcar_leida&id=<?= $alerta['id_alerta'] ?>">
                            <button type="submit" class="marcar-leida-btn">✓ Marcar como leída</button>
                        </form>
                    <?php else: ?>
                        <span style="color:#28a745; font-weight:bold;">✓ Leída</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>