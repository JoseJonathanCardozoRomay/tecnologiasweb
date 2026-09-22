<?php
$titulo_pagina = 'Calificar Tutoría';
ob_start();
?>

<h1>Registrar Evaluación</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=evaluacion_crear">
    <label>Tutoría:</label>
    <select name="id_tutoria" required>
        <option value="">Seleccione la tutoría</option>
        <?php foreach ($tutorias as $t): ?>
        <option value="<?= $t['id_tutoria'] ?>">
            Tutoría #<?= $t['id_tutoria'] ?> — <?= htmlspecialchars($t['nombre_materia'] ?? '') ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Calificación (1 a 5):</label>
    <select name="calificacion" required>
        <option value="">Seleccione</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?> — <?= str_repeat('⭐', $i) ?></option>
        <?php endfor; ?>
    </select>

    <label>Comentario:</label>
    <textarea name="comentario" rows="4" placeholder="Escribe tu opinión sobre la tutoría..."></textarea>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=evaluaciones_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';