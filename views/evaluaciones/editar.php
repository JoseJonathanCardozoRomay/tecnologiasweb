 <?php
$titulo_pagina = 'Editar Evaluación';
ob_start();
$eval = $evaluacion ?? [];
?>

<h1>Editar Evaluación</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#cc0000; padding:10px; margin:15px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="" style="max-width:500px; margin:20px auto;">

    <div style="margin-bottom:15px;">
        <label>Tutoría:</label>
        <select name="id_tutoria" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">Seleccione</option>
            <?php foreach ($tutorias as $t): ?>
            <option value="<?= $t['id_tutoria'] ?>"
                <?= ($eval['id_tutoria'] ?? 0) == $t['id_tutoria'] ? 'selected' : '' ?>>
                ID <?= $t['id_tutoria'] ?> — <?= htmlspecialchars($t['fecha'] ?? '') ?> (<?= htmlspecialchars($t['estado'] ?? '') ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Calificación (1 a 5):</label>
        <select name="calificacion" style="width:100%; padding:8px; margin-top:5px;">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>"
                <?= (($eval['calificacion'] ?? 1) == $i) ? 'selected' : '' ?>>
                <?= $i ?>
            </option>
            <?php endfor; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Comentario:</label>
        <textarea name="comentario" rows="4" style="width:100%; padding:8px; margin-top:5px;"><?= htmlspecialchars($eval['comentario'] ?? '') ?></textarea>
    </div>

    <button type="submit" style="background:#003366; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:16px;">Actualizar</button>
    <a href="index.php?accion=evaluaciones_listar" style="margin-left:10px; color:#666;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';