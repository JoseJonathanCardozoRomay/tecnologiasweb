<?php
$titulo_pagina = 'Nueva Notificación';
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Notificación</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 700px;
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
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .alerta {
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .error { background: #f8d7da; color: #721c24; }
        .exito { background: #d4edda; color: #155724; }
        form label {
            display: block;
            margin: 15px 0 6px;
            font-weight: bold;
            color: #003366;
        }
        form input, form select, form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }
        button {
            margin-top: 25px;
            background: #0066cc;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #0052a3; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=notificaciones_listar" class="volver">← Volver al inicio</a>
        
        <h1>🔔 Nueva Notificación</h1>

        <?php if ($error): ?>
            <div class="alerta error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($exito): ?>
            <div class="alerta exito"><?= $exito ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="id_usuario">Destinatario</label>
            <select name="id_usuario" id="id_usuario" required>
                <option value="">Selecciona un usuario...</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id_usuario'] ?>">
                        <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?> — <?= $u['nombre_rol'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="tipo">Tipo</label>
            <select name="tipo" id="tipo" required>
                <option value="">Selecciona el tipo...</option>
                <option value="aviso">📢 Aviso</option>
                <option value="recordatorio">⏰ Recordatorio</option>
                <option value="tutoria">📚 Tutoría</option>
                <option value="evaluacion">⭐ Evaluación</option>
                <option value="general">📋 General</option>
            </select>

            <label for="mensaje">Mensaje</label>
            <textarea name="mensaje" id="mensaje" rows="4" placeholder="Escribe el mensaje..." required></textarea>

            <button type="submit">✅ Enviar Notificación</button>
        </form>
    </div>
</body>
</html>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';