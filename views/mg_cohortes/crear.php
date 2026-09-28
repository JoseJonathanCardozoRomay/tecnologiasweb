 <?php
if (!isset($error)) $error = '';
$titulo_pagina = 'Nueva Cohorte';
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Cohorte</title>
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
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        h1 { text-align: center; color: #003366; margin-bottom: 25px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; color: #003366; }
        input {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 6px; font-size: 15px;
        }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input { width: auto; margin: 0; }
        button {
            width: 100%; background: #0066cc; color: white; border: none;
            padding: 12px; border-radius: 6px; font-size: 16px; font-weight: bold;
            cursor: pointer; margin-top: 10px;
        }
        button:hover { background: #004c99; }
        .btn-atras {
            display: inline-block; margin-bottom: 20px;
            color: #0066cc; text-decoration: none; font-weight: bold;
        }
        .alerta-error { 
            background: #fff8e1; border-left: 4px solid #f59e0b; color: #78350f; 
            padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; 
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=mg_cohortes" class="btn-atras">← Volver a Cohortes</a>
        <div class="tarjeta">
            <h1>➕ Nueva Cohorte</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alerta-error">
                    <?= nl2br(htmlspecialchars($error)) ?>
                </div>
            <?php endif; ?>
            
            <!-- ✅ Acción del formulario corregida -->
            <form method="POST" action="index.php?accion=mg_cohorte_crear">
                <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
                
                <div class="form-group">
                    <label>Código *</label>
                    <input type="text" name="codigo" placeholder="ej: 2026" required>
                </div>
                
                <div class="form-group">
                    <label>Nombre de la Cohorte *</label>
                    <input type="text" name="nombre" placeholder="ej: Sistemas 2026" required>
                </div>
                
                <div class="form-group">
                    <label>Fecha de Inicio *</label>
                    <input type="date" name="fecha_inicio" required>
                </div>
                
                <div class="form-group">
                    <label>Fecha de Fin</label>
                    <input type="date" name="fecha_fin">
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="activo" id="activo" checked>
                    <label for="activo">Cohorte Activa</label>
                </div>
                
                <button type="submit">💾 Guardar Cohorte</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
$contenido = ob_get_clean();
// ✅ Ruta de plantilla corregida
require_once __DIR__ . '/../config/plantilla.php';