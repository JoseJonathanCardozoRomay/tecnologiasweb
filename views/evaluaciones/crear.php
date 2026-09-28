<?php
if (!isset($error)) $error = '';
if (!isset($tutorias)) $tutorias = [];
$titulo_pagina = 'Nueva Evaluación';
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Evaluación</title>
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
        select, input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
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
        button:hover { background: #218838; }
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
        <h1>⭐ Nueva Evaluación</h1>

        <?php if (!empty($error)): ?>
        <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($tutorias)): ?>
        <p style="text-align:center; color:#666; padding:20px;">
            No hay tutorías disponibles para evaluar.<br>
            Primero debes tener una tutoría registrada.
        </p>
        <a href="index.php?accion=evaluaciones_listar" class="btn-volver" style="display:block; text-align:center; margin-left:0;">Volver</a>
        <?php else: ?>
        <form method="POST" action="index.php?accion=evaluacion_crear">
            <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">

            <label>Tutoría *</label>
            <select name="id_tutoria" required>
                <option value="">Selecciona una tutoría</option>
                <?php foreach ($tutorias as $t): ?>
                <option value="<?= $t['id_tutoria'] ?>">
                    <?= htmlspecialchars(($t['estudiante_nombre'] ?? '') . ' — ' . ($t['materia_nombre'] ?? '')) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Calificación *</label>
            <input type="text" name="calificacion" placeholder="ej: 5 / 10 — Excelente" required>

            <label>Comentario</label>
            <textarea name="comentario" rows="4" placeholder="Escribe tu opinión sobre la tutoría..."></textarea>

            <button type="submit">Guardar Evaluación</button>
            <a href="index.php?accion=evaluaciones_listar" class="btn-volver">Volver</a>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';