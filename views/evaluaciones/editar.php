<?php
$titulo_pagina = 'Evaluar Tutoría';
ob_start();
?>
<h1>Evaluación de Tutoría</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <?= htmlspecialchars($error ?? '') ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=evaluacion_editar_guardar&id=<?= (int)($evaluacion['id_evaluacion'] ?? 0) ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <div style="background:#f0f5ff; padding:15px; border-radius:6px; margin-bottom:20px;">
        <p><strong>Tutoría:</strong> <?= htmlspecialchars($tutoria_info['materia'] ?? '') ?> — <?= htmlspecialchars($tutoria_info['fecha'] ?? '') ?></p>
        <p><strong>Tutor:</strong> <?= htmlspecialchars(($tutoria_info['nombre_tutor'] ?? '') . ' ' . ($tutoria_info['apellido_tutor'] ?? '')) ?></p>
    </div>
    
    <label>Calificación (1 a 5):</label>
    <select name="calificacion" required>
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>" <?= ($evaluacion['calificacion'] ?? 0) == $i ? 'selected' : '' ?>>
            <?= $i ?> ⭐
        </option>
        <?php endfor; ?>
    </select>
    
    <label>Comentario:</label>
    <textarea name="comentario" rows="4" placeholder="Escribe tu opinión sobre la tutoría..."><?= htmlspecialchars($evaluacion['comentario'] ?? '') ?></textarea>
    
    <button type="submit" class="btn btn-primario">Guardar Evaluación</button>
    <a href="index.php?accion=evaluaciones_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';