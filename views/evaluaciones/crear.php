<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluar Tutoría</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffc107;
            font-size: 22px;
        }
        .error { background: #fcebea; color: #c53030; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .exito { background: #e8f5e9; color: #2f855a; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 15px;
        }
        button {
            background: #0066cc;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        button:hover { background: #0052a3; }
        .estrellas { font-size: 30px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=tutorias_listar" class="volver">← Volver</a>
        
        <h1>⭐ Evaluar Tutoría</h1>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito"><?= htmlspecialchars($exito) ?></div>
        <?php endif; ?>

        <?php if (!$exito): ?>
        <form method="POST">
            <label>Calificación (1 a 5 estrellas):</label>
            <div class="estrellas">
                <select name="calificacion" required>
                    <option value="">-- Selecciona --</option>
                    <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                    <option value="4">⭐⭐⭐⭐ Muy Buena</option>
                    <option value="3">⭐⭐⭐ Buena</option>
                    <option value="2">⭐⭐ Regular</option>
                    <option value="1">⭐ Necesita Mejorar</option>
                </select>
            </div>

            <label>Comentario u Observaciones:</label>
            <textarea name="comentario" rows="5" placeholder="Escribe tu opinión sobre la tutoría..."></textarea>

            <button type="submit">✅ Guardar Evaluación</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>