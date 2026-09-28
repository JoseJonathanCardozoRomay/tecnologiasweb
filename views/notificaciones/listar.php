<?php
if (!isset($notificaciones)) $notificaciones = [];
$titulo_pagina = 'Notificaciones';
ob_start();
?>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
body {
    background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
    min-height: 100vh;
    padding: 40px 20px;
}
.contenedor {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    padding: 35px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
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
h1 {
    color: #003366;
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ffc107;
}
.notificacion {
    padding: 18px;
    margin-bottom: 12px;
    border-radius: 8px;
    border-left: 4px solid;
}
.no-leida {
    background: #e6f2ff;
    border-left-color: #0066cc;
    font-weight: bold;
}
.leida {
    background: #f9f9f9;
    border-left-color: #ccc;
    color: #666;
}
.tipo {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.85em;
    margin-right: 10px;
    background: #0066cc;
    color: white;
}
.fecha {
    font-size: 0.85em;
    color: #888;
    margin-top: 5px;
}
.enlace {
    margin-top: 8px;
    display: inline-block;
    color: #0066cc;
    text-decoration: none;
}
.enlace:hover { text-decoration: underline; }
.vacio {
    text-align: center;
    padding: 50px;
    color: #666;
}
</style>

<div class="contenedor">
    <a href="index.php" class="volver">← Volver al inicio</a>
    
    <h1>🔔 Notificaciones</h1>
    
    <?php if (empty($notificaciones)): ?>
        <div class="vacio">
            <p>No tienes notificaciones nuevas.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notificaciones as $n): ?>
        <div class="notificacion <?= ($n['leida'] ?? 0) ? 'leida' : 'no-leida' ?>">
            <span class="tipo"><?= htmlspecialchars(ucfirst($n['tipo'] ?? 'general')) ?></span>
            <p><?= htmlspecialchars($n['mensaje'] ?? '') ?></p>
            <div class="fecha"><?= htmlspecialchars($n['fecha_creacion'] ?? '') ?></div>
            <?php if (!empty($n['url'])): ?>
                <a href="<?= htmlspecialchars($n['url']) ?>" class="enlace">Ver detalles →</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';