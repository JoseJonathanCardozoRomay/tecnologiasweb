<?php
if (!isset($error)) $error = '';
if (!isset($tribunal)) $tribunal = [
    'nombre_completo' => '',
    'especialidad' => '',
    'correo' => '',
    'telefono' => '',
    'activo' => 1
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tribunal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .volver { color: white; text-decoration: none; font-weight: 500; display: inline-block; margin-bottom: 20px; }
        .contenedor { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        h1 { text-align: center; color: #003366; margin-bottom: 25px; font-size: 22px; }
        .error { background: #ffdddd; color: #c00; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; }
        .grupo { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
        input { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .checkbox-linea { display: flex; align-items: center; gap: 8px; }
        .checkbox-linea input { width: auto; margin: 0; }
        .botones { display: flex; gap: 12px; margin-top: 25px; }
        .btn-guardar { flex: 1; background: #0066cc; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; }
        .btn-cancelar { flex: 1; background: #e6e6e6; color: #333; border: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; text-decoration: none; text-align: center; }
    </style>
</head>
<body>
    <a href="index.php?accion=mg_tribunales_listar" class="volver">← Volver al listado</a>
    <div class="contenedor">
        <h1>✏️ Editar Tribunal</h1>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="">
            <div class="grupo">
                <label>Nombre Completo *</label>
                <input type="text" name="nombre_completo" required 
                       value="<?= htmlspecialchars($tribunal['nombre_completo']) ?>">
            </div>
            <div class="grupo">
                <label>Especialidad</label>
                <input type="text" name="especialidad" 
                       value="<?= htmlspecialchars($tribunal['especialidad'] ?? '') ?>">
            </div>
            <div class="grupo">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" 
                       value="<?= htmlspecialchars($tribunal['correo'] ?? '') ?>">
            </div>
            <div class="grupo">
                <label>Teléfono</label>
                <input type="text" name="telefono" 
                       value="<?= htmlspecialchars($tribunal['telefono'] ?? '') ?>">
            </div>
            <div class="grupo">
                <label class="checkbox-linea">
                    <input type="checkbox" name="activo" 
                           <?= !empty($tribunal['activo']) ? 'checked' : '' ?>>
                    Tribunal Activo
                </label>
            </div>
            <div class="botones">
                <a href="index.php?accion=mg_tribunales_listar" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>