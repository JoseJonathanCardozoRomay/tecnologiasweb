<?php
$titulo_pagina = 'Editar Materia';
ob_start();
?>

<h1>Editar Materia</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=materia_editar&id=<?= $materia['id_materia'] ?>">
    <label>Nombre de la Materia:</label>
    <input type="text" name="nombre_materia" value="<?= htmlspecialchars($materia['nombre_materia']) ?>" required>

    <label>Carrera:</label>
    <select name="id_carrera">
        <option value="">General / Sin carrera</option>
        <?php foreach ($carreras as $c): ?>
        <option value="<?= $c['id_carrera'] ?>" <?= $c['id_carrera'] == $materia['id_carrera'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre_carrera']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=materias_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';