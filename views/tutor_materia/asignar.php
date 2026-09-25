<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materia a Tutor</title>
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
        .alerta-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            color: #003366;
            margin-bottom: 8px;
        }
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            width: 100%;
            background: #0066cc;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #0052a3;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=tutor_materia_listar" class="volver">← Volver</a>
        
        <h1>📚 Asignar Materia a Tutor</h1>

        <?php if (!empty($error)): ?>
            <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="id_tutor">Seleccionar Tutor *</label>
                <select name="id_tutor" id="id_tutor" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($tutores as $t): ?>
                        <option value="<?= $t['id_tutor'] ?>">
                            <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_materia">Seleccionar Materia *</label>
                <select name="id_materia" id="id_materia" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id_materia'] ?>">
                            <?= htmlspecialchars($m['nombre_materia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">✅ Asignar Materia</button>
        </form>
    </div>
</body>
</html>