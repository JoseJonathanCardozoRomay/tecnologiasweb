<?php
if (!isset($error)) $error = '';
if (!isset($defensa)) $defensa = [
    'id_tribunal' => 0,
    'titulo_trabajo' => '',
    'estudiante_nombre' => '',
    'fecha_defensa' => '',
    'hora_inicio' => '',
    'hora_fin' => '',
    'modalidad' => 'presencial',
    'lugar_enlace' => '',
    'estado' => 'programada',
    'observaciones' => ''
];
if (!isset($tribunales)) $tribunales = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Defensa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .volver { color: white; text-decoration: none; font-weight: 500; display: inline-block; margin-bottom: 20px; }
        .contenedor { max-width: 650px; margin: 0 auto; background: white; border-radius: 12px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        h1 { text-align: center; color: #003366; margin-bottom: 25px; font-size: 22px; }
        .error { background: #ffdddd; color: #c00; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; }
        .grupo { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .fila-doble { display: flex; gap: 15px; }
        .fila-doble .grupo { flex: 1; }
        .botones { display: flex; gap: 12px; margin-top: 22px; }
        .btn-guardar { flex: 1; background: #0066cc; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; }
        .btn-cancelar { flex: 1; background: #e6e6e6; color: #333; border: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; text-decoration: none; text-align: center; }
        .req { color: red; }
    </style>
</head>
<body>
    <a href="index.php?accion=mg_defensas_listar" class="volver">← Volver al listado</a>
    <div class="contenedor">
        <h1>✏️ Editar Defensa</h1>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="">
            <div class="grupo">
                <label>Tribunal / Jurado <span class="req">*</span></label>
                <select name="id_tribunal" required>
                    <option value="">Seleccione un tribunal...</option>
                    <?php foreach ($tribunales as $t): ?>
                    <option value="<?= $t['id_tribunal'] ?>" <?= $defensa['id_tribunal'] == $t['id_tribunal'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre_completo']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grupo">
                <label>Título del Trabajo <span class="req">*</span></label>
                <input type="text" name="titulo_trabajo" required value="<?= htmlspecialchars($defensa['titulo_trabajo']) ?>">
            </div>
            <div class="grupo">
                <label>Nombre del Estudiante <span class="req">*</span></label>
                <input type="text" name="estudiante_nombre" required value="<?= htmlspecialchars($defensa['estudiante_nombre']) ?>">
            </div>
            <div class="fila-doble">
                <div class="grupo">
                    <label>Fecha de Defensa <span class="req">*</span></label>
                    <input type="date" name="fecha_defensa" required value="<?= $defensa['fecha_defensa'] ?>">
                </div>
                <div class="grupo">
                    <label>Modalidad</label>
                    <select name="modalidad">
                        <option value="presencial" <?= $defensa['modalidad'] == 'presencial' ? 'selected' : '' ?>>Presencial</option>
                        <option value="virtual" <?= $defensa['modalidad'] == 'virtual' ? 'selected' : '' ?>>Virtual</option>
                    </select>
                </div>
            </div>
            <div class="fila-doble">
                <div class="grupo">
                    <label>Hora Inicio</label>
                    <input type="time" name="hora_inicio" value="<?= $defensa['hora_inicio'] ?>">
                </div>
                <div class="grupo">
                    <label>Hora Fin</label>
                    <input type="time" name="hora_fin" value="<?= $defensa['hora_fin'] ?>">
                </div>
            </div>
            <div class="grupo">
                <label>Lugar o Enlace</label>
                <input type="text" name="lugar_enlace" value="<?= htmlspecialchars($defensa['lugar_enlace']) ?>">
            </div>
            <div class="grupo">
                <label>Estado</label>
                <select name="estado">
                    <option value="programada" <?= $defensa['estado'] == 'programada' ? 'selected' : '' ?>>Programada</option>
                    <option value="realizada" <?= $defensa['estado'] == 'realizada' ? 'selected' : '' ?>>Realizada</option>
                    <option value="cancelada" <?= $defensa['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            <div class="grupo">
                <label>Observaciones</label>
                <textarea name="observaciones" rows="3"><?= htmlspecialchars($defensa['observaciones']) ?></textarea>
            </div>
            <div class="botones">
                <a href="index.php?accion=mg_defensas_listar" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>