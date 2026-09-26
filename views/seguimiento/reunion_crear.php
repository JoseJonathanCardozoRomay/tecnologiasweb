<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Reunión</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body { background: linear-gradient(rgba(0,38,77,0.88),rgba(0,38,77,0.88)), url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 650px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 35px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        h1 { color: #003366; margin-bottom: 25px; border-bottom: 3px solid #ffc107; padding-bottom: 10px; text-align: center; }
        .error { background: #ffebee; color: #c00; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; color: #333; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .btn { padding: 12px 25px; border-radius: 6px; border: none; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 15px; }
        .btn-guardar { background: #28a745; color: #fff; width: 100%; }
        .btn-volver { background: #6c757d; color: #fff; text-decoration: none; display: inline-block; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=reuniones_listar" class="btn btn-volver">← Volver</a>
        <h1>📅 Nueva Reunión</h1>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <label>Tutoría *</label>
            <select name="id_tutoria" required>
                <option value="">Seleccione</option>
                <?php foreach ($tutorias as $t): ?>
                    <option value="<?= $t['id_tutoria'] ?>">Tutoría #<?= $t['id_tutoria'] ?> — <?= $t['modalidad'] ?></option>
                <?php endforeach; ?>
            </select>
            <label>Título *</label>
            <input type="text" name="titulo" required>
            <label>Fecha y Hora *</label>
            <input type="datetime-local" name="fecha_reunion" required>
            <label>Ubicación / Aula</label>
            <input type="text" name="ubicacion" placeholder="Ej: Aula 305, Edificio A">
            <label>Enlace (si es virtual)</label>
            <input type="url" name="enlace" placeholder="https://...">
            <label>Observaciones</label>
            <textarea name="observaciones_inicio" rows="3" placeholder="Notas iniciales de la reunión..."></textarea>
            <button type="submit" class="btn btn-guardar">✅ Guardar Reunión</button>
        </form>
    </div>
</body>
</html>