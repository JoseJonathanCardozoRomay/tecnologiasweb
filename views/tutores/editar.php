<?php
$titulo_pagina = 'Editar Tutor';
ob_start();
?>

<h1>Editar Tutor</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=tutor_editar&id=<?= $tutor['id_tutor'] ?>">
    <label>Usuario:</label>
    <select name="id_usuario" required>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>" <?= $u['id_usuario'] == $tutor['id_usuario'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Especialidad:</label>
    <input type="text" name="especialidad" value="<?= htmlspecialchars($tutor['especialidad'] ?? '') ?>">

    <label>Biografía:</label>
    <textarea name="biografia" rows="4"><?= htmlspecialchars($tutor['biografia'] ?? '') ?></textarea>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=tutores_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';