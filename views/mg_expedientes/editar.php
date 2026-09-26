<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Expediente</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', 'Times New Roman', serif;
            background: linear-gradient(rgba(0, 38, 77, 0.85), rgba(0, 38, 77, 0.85)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .contenedor { max-width: 700px; margin: 0 auto; }
        .tarjeta {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            color: #003366;
        }
        h1 { text-align: center; margin-bottom: 25px; font-size: 24px; }
        .btn-atras {
            display: inline-block;
            margin-bottom: 20px;
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }
        .grupo { margin-bottom: 18px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            background: #0066cc;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        .error { background: #ffe6e6; color: #cc0000; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=mg_expedientes_listar" class="btn-atras">← Volver a Expedientes</a>
        
        <div class="tarjeta">
            <h1>✏️ Editar Expediente</h1>
            
            <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="grupo">
                    <label>Estudiante</label>
                    <select name="id_estudiante" required>
                        <option value="">Seleccione estudiante...</option>
                        <?php foreach ($estudiantes as $e): ?>
                        <option value="<?= $e['id_estudiante'] ?>" 
                            <?= ($expediente['id_estudiante'] ?? 0) == $e['id_estudiante'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '') . ' — ' . ($e['codigo_registro'] ?? '')) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grupo">
                    <label>Modalidad</label>
                    <select name="id_modalidad" required>
                        <option value="">Seleccione modalidad...</option>
                        <?php foreach ($modalidades as $m): ?>
                        <option value="<?= $m['id_modalidad'] ?>"
                            <?= ($expediente['id_modalidad'] ?? 0) == $m['id_modalidad'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nombre'] ?? '') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grupo">
                    <label>Cohorte</label>
                    <select name="id_cohorte" required>
                        <option value="">Seleccione cohorte...</option>
                        <?php foreach ($cohortes as $c): ?>
                        <option value="<?= $c['id_cohorte'] ?>"
                            <?= ($expediente['id_cohorte'] ?? 0) == $c['id_cohorte'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($c['codigo'] ?? '') . ' — ' . ($c['nombre'] ?? '')) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grupo">
                    <label>Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($expediente['fecha_inicio'] ?? '') ?>" required>
                </div>
                
                <div class="grupo">
                    <label>Título del Trabajo</label>
                    <input type="text" name="titulo_trabajo" value="<?= htmlspecialchars($expediente['titulo_trabajo'] ?? '') ?>">
                </div>
                
                <div class="grupo">
                    <label>Etapa Actual</label>
                    <select name="etapa_actual">
                        <option value="previa" <?= ($expediente['etapa_actual'] ?? '') == 'previa' ? 'selected' : '' ?>>Previa</option>
                        <option value="mg1" <?= ($expediente['etapa_actual'] ?? '') == 'mg1' ? 'selected' : '' ?>>MG1</option>
                        <option value="mg2" <?= ($expediente['etapa_actual'] ?? '') == 'mg2' ? 'selected' : '' ?>>MG2</option>
                        <option value="finalizado" <?= ($expediente['etapa_actual'] ?? '') == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                    </select>
                </div>
                
                <div class="grupo">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="activo" <?= ($expediente['estado'] ?? '') == 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($expediente['estado'] ?? '') == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                
                <div class="grupo">
                    <label>Observaciones</label>
                    <textarea name="observaciones" rows="4"><?= htmlspecialchars($expediente['observaciones'] ?? '') ?></textarea>
                </div>
                
                <button type="submit">💾 Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>