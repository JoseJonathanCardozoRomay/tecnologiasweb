<?php
if (!isset($periodo)) $periodo = [];
if (!isset($error)) $error = '';
$titulo_pagina = 'Editar Periodo';
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Periodo</title>
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
        .checkbox-linea {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 15px 0;
        }
        .checkbox-linea input {
            width: auto;
            margin: 0;
        }
        .btn-guardar {
            background: #0066cc;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            margin-top: 20px;
            cursor: pointer;
        }
        .btn-guardar:hover { background: #0052a3; }
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
        <h1>✏️ Editar Periodo</h1>

        <?php if (!empty($error)): ?>
        <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?accion=periodo_editar&id=<?= (int)($periodo['id_periodo'] ?? 0) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">

            <label>Código:</label>
            <input type="text" name="codigo" value="<?= htmlspecialchars($periodo['codigo'] ?? '') ?>" required>

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($periodo['nombre'] ?? '') ?>" required>

            <label>Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($periodo['fecha_inicio'] ?? '') ?>" required>

            <label>Fecha de Fin:</label>
            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($periodo['fecha_fin'] ?? '') ?>" required>

            <label class="checkbox-linea">
                <input type="checkbox" name="activo" <?= !empty($periodo['activo']) ? 'checked' : '' ?>>
                Periodo Activo
            </label>

            <button type="submit" class="btn-guardar">Guardar Cambios</button>
            <a href="index.php?accion=periodos_listar" class="btn-volver">Volver</a>
        </form>
    </div>
</body>
</html>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';