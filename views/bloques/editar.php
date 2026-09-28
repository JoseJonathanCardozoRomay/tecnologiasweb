<?php
if (!isset($bloque)) $bloque = [];
if (!isset($error)) $error = '';
$titulo_pagina = 'Editar Bloque Horario';
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Bloque Horario</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
        }
        .alerta-error {
            background: #ffdddd;
            color: #c00;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #003366;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn-exito {
            display: inline-block;
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            margin-top: 20px;
            cursor: pointer;
        }
        .btn-exito:hover { background: #218838; }
        .btn-volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>⏰ Editar Bloque Horario</h1>
        <?php if (!empty($error)): ?>
        <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="index.php?accion=bloque_editar&id=<?= (int)($bloque['id_bloque'] ?? 0) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
            
            <label>Nombre del Bloque:</label>
            <input type="text" name="nombre_bloque" value="<?= htmlspecialchars($bloque['nombre_bloque'] ?? '') ?>" required>
            
            <label>Hora de Inicio:</label>
            <input type="time" name="hora_inicio" value="<?= htmlspecialchars($bloque['hora_inicio'] ?? '') ?>" required>
            
            <label>Hora de Fin:</label>
            <input type="time" name="hora_fin" value="<?= htmlspecialchars($bloque['hora_fin'] ?? '') ?>" required>
            
            <label>Descripción:</label>
            <input type="text" name="descripcion" value="<?= htmlspecialchars($bloque['descripcion'] ?? '') ?>">
            
            <button type="submit" class="btn-exito">Guardar Cambios</button>
            <a href="index.php?accion=bloques_listar" class="btn-volver">Volver</a>
        </form>
    </div>
</body>
</html>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';