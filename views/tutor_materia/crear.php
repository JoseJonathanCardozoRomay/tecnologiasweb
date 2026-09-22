<?php
$titulo_pagina = 'Asignar Materia a Tutor';
ob_start();
?>

<h1>Asignar Materia a Tutor</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=tutor_materia_crear">
    <label>Tutor:</label>
    <select name="id_tutor" required>
        <option value="">Seleccione</option>
        <?php foreach ($tutores as $t): ?>
        <option value="<?= $t['id_tutor'] ?>"><?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Materia:</label>
    <select name="id_materia" required>
        <option value="">Seleccione</option>
        <?php foreach ($materias as $m): ?>
        <option value="<?= $m['id_materia'] ?>"><?= htmlspecialchars($m['nombre_materia']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primario">Asignar</button>
    <a href="index.php?accion=tutor_materia_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';