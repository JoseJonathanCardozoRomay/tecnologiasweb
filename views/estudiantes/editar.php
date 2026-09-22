<?php
$titulo_pagina = 'Editar Estudiante';
ob_start();
?>

<h1>Editar Estudiante</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=estudiante_editar&id=<?= $estudiante['id_estudiante'] ?>">
    <label>Usuario:</label>
    <select name="id_usuario" required>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>" <?= $u['id_usuario'] == $estudiante['id_usuario'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Carrera:</label>
    <select name="id_carrera" required>
        <?php foreach ($carreras as $c): ?>
        <option value="<?= $c['id_carrera'] ?>" <?= $c['id_carrera'] == $estudiante['id_carrera'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre_carrera']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Semestre:</label>
    <input type="number" name="semestre" value="<?= $estudiante['semestre'] ?>" min="1" max="12" required>

    <label>Registro Universitario:</label>
    <input type="text" name="registro_universitario" value="<?= htmlspecialchars($estudiante['registro_universitario'] ?? '') ?>">

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=estudiantes_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';