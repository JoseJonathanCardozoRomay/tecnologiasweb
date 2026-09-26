<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisar Informe</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body { background: linear-gradient(rgba(0,38,77,0.88),rgba(0,38,77,0.88)), url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 650px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 35px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        h1 { color: #003366; margin-bottom: 25px; border-bottom: 3px solid #ffc107; padding-bottom: 10px; text-align: center; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; color: #333; }
        select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .btn { padding: 12px 25px; border-radius: 6px; border: none; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 15px; width: 100%; }
        .btn-aprobar { background: #28a745; color: #fff; }
        .btn-observar { background: #fd7e14; color: #fff; }
        .btn-volver { background: #6c757d; color: #fff; text-decoration: none; display: inline-block; margin-bottom: 15px; text-align: center; width: auto; }
        .info { background: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=informes_listar" class="btn btn-volver">← Volver</a>
        <h1>🔍 Revisar Informe</h1>
        <div class="info">
            <strong>Título:</strong> <?= htmlspecialchars($informe['titulo']) ?><br>
            <strong>Avance:</strong> <?= $informe['progreso_porcentaje'] ?>%<br>
            <strong>Enviado por:</strong> <?= htmlspecialchars(($informe['nombre_crea'] ?? '') . ' ' . ($informe['apellido_crea'] ?? '')) ?>
        </div>
        <form method="POST">
            <label>Decisión *</label>
            <select name="estado" id="estadoSel" required>
                <option value="aprobado">✅ Aprobar</option>
                <option value="observado">⚠️ Devolver con Observaciones</option>
            </select>
            <label>Observaciones / Comentarios</label>
            <textarea name="observaciones_revision" rows="4" placeholder="Escribe aquí tu retroalimentación..."></textarea>
            <button type="submit" class="btn btn-aprobar">💾 Guardar Revisión</button>
        </form>
    </div>
</body>
</html>