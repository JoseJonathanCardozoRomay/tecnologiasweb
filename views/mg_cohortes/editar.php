<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cohorte</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 600px; margin: 0 auto; }
        .tarjeta {
            background: white; border-radius: 12px; padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2); color: #003366;
        }
        h1 { text-align: center; margin-bottom: 25px; font-size: 22px; }
        .btn-atras {
            display: inline-block; margin-bottom: 20px;
            color: #0066cc; text-decoration: none; font-weight: bold;
        }
        .error {
            background: #fce4e4; color: #a94444; padding: 12px;
            border-radius: 6px; margin-bottom: 20px;
        }
        .grupo { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 6px; font-size: 14px;
        }
        .checkbox { display: flex; align-items: center; gap: 8px; }
        .checkbox input { width: auto; margin: 0; }
        .botones { display: flex; gap: 10px; margin-top: 25px; }
        .btn-guardar {
            flex: 1; background: #0066cc; color: white; border: none; padding: 12px;
            border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;
        }
        .btn-cancelar {
            flex: 1; background: #e2e3e5; color: #333; text-align: center;
            padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=mg_cohortes" class="btn-atras">← Volver</a>
        <div class="tarjeta">
            <h1>📅 Editar Cohorte</h1>
            <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="grupo">
                    <label>Código *</label>
                    <input type="text" name="codigo" value="<?= htmlspecialchars($cohorte['codigo']) ?>" required>
                </div>
                <div class="grupo">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($cohorte['nombre']) ?>" required>
                </div>
                <div class="grupo">
                    <label>Fecha de Inicio *</label>
                    <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($cohorte['fecha_inicio']) ?>" required>
                </div>
                <div class="grupo">
                    <label>Fecha de Fin</label>
                    <input type="date" name="fecha_fin" value="<?= htmlspecialchars($cohorte['fecha_fin'] ?? '') ?>">
                </div>
                <div class="grupo checkbox">
                    <input type="checkbox" name="activa" id="activa" <?= $cohorte['activa'] ? 'checked' : '' ?>>
                    <label for="activa">Cohorte Activa</label>
                </div>
                <div class="botones">
                    <a href="index.php?accion=mg_cohortes" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>