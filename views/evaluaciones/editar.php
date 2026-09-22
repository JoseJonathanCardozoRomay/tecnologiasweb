<?php
$titulo_pagina = 'Editar Evaluación';
ob_start();
?>

<h1>Editar Evaluación</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=evaluacion_editar&id=<?= $eval['id_evaluacion'] ?>">
    <label>Tutoría:</label>
    <select name="id_tutoria" required>
        <?php foreach ($tutorias as $t): ?>
        <option value="<?= $t['id_tutoria'] ?>" <?= $t['id_tutoria'] == $eval['id_tutoria'] ? 'selected' : '' ?>>
            Tutoría #<?= $t['id_tutoria'] ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Calificación (1 a 5):</label>
    <select name="calificacion" required>
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>" <?= $i == $eval['calificacion'] ? 'selected' : '' ?>>
            <?= $i ?> — <?= str_repeat('⭐', $i) ?>
        </option>
        <?php endfor; ?>
    </select>

    <label>Comentario:</label>
    <textarea name="comentario" rows="4"><?= htmlspecialchars($eval['comentario'] ?? '') ?></textarea>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=evaluaciones_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';