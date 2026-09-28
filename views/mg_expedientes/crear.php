<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Expediente</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 700px; margin: 0 auto; }
        .tarjeta {
            background: white; border-radius: 12px; padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2); color: #003366;
        }
        h1 { text-align: center; margin-bottom: 25px; font-size: 24px; }
        .btn-atras { display: inline-block; margin-bottom: 20px; color: #0066cc; text-decoration: none; font-weight: bold; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; color: #003366; }
        select, input, textarea {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 6px; font-size: 15px;
        }
        button {
            background: #0066cc; color: white; border: none; padding: 12px 25px;
            border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer;
            width: 100%; margin-top: 10px;
        }
        button:hover { background: #004c99; }
        .error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=mg_expedientes_listar" class="btn-atras">← Volver a Expedientes</a>
        <div class="tarjeta">
            <h1>📝 Nuevo Expediente</h1>
            <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Estudiante *</label>
                    <select name="id_estudiante" required>
                        <option value="">Seleccione un estudiante</option>
                        <?php if (!empty($estudiantes)): ?>
                            <?php foreach ($estudiantes as $e): ?>
                            <option value="<?= (int)($e['id_estudiante'] ?? 0) ?>">
                                <?= htmlspecialchars(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')) ?>
                                — <?= htmlspecialchars($e['codigo_estudiante'] ?? '') ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Modalidad *</label>
                    <select name="id_modalidad" required>
                        <option value="">Seleccione una modalidad</option>
                        <?php if (!empty($modalidades)): ?>
                            <?php foreach ($modalidades as $m): ?>
                            <option value="<?= (int)($m['id_modalidad'] ?? 0) ?>">
                                <?= htmlspecialchars($m['nombre'] ?? '') ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cohorte / Promoción *</label>
                    <select name="id_cohorte" required>
                        <option value="">Seleccione una cohorte</option>
                        <?php if (!empty($cohortes)): ?>
                            <?php foreach ($cohortes as $c): ?>
                            <option value="<?= (int)($c['id_cohorte'] ?? 0) ?>">
                                <?= htmlspecialchars(($c['codigo'] ?? '') . ' — ' . ($c['nombre'] ?? '')) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha de inicio *</label>
                    <input type="date" name="fecha_inicio" required>
                </div>

                <div class="form-group">
                    <label>Título del trabajo</label>
                    <input type="text" name="titulo_trabajo" placeholder="Título provisional">
                </div>

                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" rows="3" placeholder="Notas adicionales..."></textarea>
                </div>

                <button type="submit">✅ Crear Expediente</button>
            </form>
        </div>
    </div>
</body>
</html>