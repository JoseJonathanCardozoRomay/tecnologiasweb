 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parámetros del Sistema — Modalidades de Grado</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 1000px; margin: 0 auto; }
        .tarjeta {
            background: white; border-radius: 12px; padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2); color: #003366;
        }
        h1 { text-align: center; margin-bottom: 25px; color: #003366; }
        .btn-atras {
            display: inline-block; margin-bottom: 20px;
            color: #0066cc; text-decoration: none; font-weight: bold;
        }
        .btn-atras:hover { text-decoration: underline; }
        .mensaje {
            background: #d4edda; color: #155724; padding: 12px 20px;
            border-radius: 8px; margin-bottom: 20px; font-weight: bold;
            text-align: center;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 14px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #003366; color: white; font-weight: bold; }
        tr:hover { background: #f0f5ff; }
        input[type="text"] {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 6px; font-size: 14px;
            transition: border 0.2s;
        }
        input[type="text"]:focus {
            outline: none; border-color: #0066cc; box-shadow: 0 0 0 2px rgba(0,102,204,0.2);
        }
        button {
            background: #0066cc; color: white; border: none; padding: 9px 16px;
            border-radius: 6px; cursor: pointer; font-weight: bold;
            font-size: 14px; transition: all 0.2s;
        }
        button:hover { background: #0052a3; transform: scale(1.05); }
        .estado {
            display: inline-block; padding: 5px 12px; border-radius: 15px;
            font-size: 12px; font-weight: bold;
        }
        .confirmado { background: #d4edda; color: #155724; }
        .propuesta { background: #fff3cd; color: #856404; }
        .pendiente { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="btn-atras">← Volver al Inicio</a>
        <div class="tarjeta">
            <h1>⚙️ Parámetros del Sistema</h1>
            
            <?php if (!empty($mensaje)): ?>
            <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Parámetro</th>
                        <th>Descripción</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Guardar</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($parametros as $p): ?>
                    <tr>
                        <form method="POST" action="">
                            <td><strong><?= htmlspecialchars($p['clave']) ?></strong></td>
                            <td><?= htmlspecialchars($p['descripcion']) ?></td>
                            <td>
                                <input type="hidden" name="clave" value="<?= htmlspecialchars($p['clave']) ?>">
                                <input type="text" name="valor" 
                                       value="<?= htmlspecialchars($p['valor']) ?>" 
                                       placeholder="Escribe el valor">
                            </td>
                            <td>
                                <?php 
                                $clase = 'propuesta';
                                $texto = 'propuesta';
                                if (!empty($p['estado_evidencia'])) {
                                    if ($p['estado_evidencia'] === 'confirmado') {
                                        $clase = 'confirmado';
                                        $texto = 'confirmado';
                                    } elseif ($p['estado_evidencia'] === 'pendiente') {
                                        $clase = 'pendiente';
                                        $texto = 'pendiente';
                                    }
                                }
                                ?>
                                <span class="estado <?= $clase ?>"><?= $texto ?></span>
                            </td>
                            <td>
                                <button type="submit" title="Guardar cambios">✓</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>