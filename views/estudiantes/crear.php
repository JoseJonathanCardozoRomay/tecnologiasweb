<?php
$titulo_pagina = 'Crear Estudiante';
ob_start();
?>

<h1>Crear Nuevo Estudiante</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=estudiante_crear">
    <label>Usuario:</label>
    <select name="id_usuario" required>
        <option value="">Seleccione</option>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Carrera:</label>
    <select name="id_carrera" required>
        <option value="">Seleccione</option>
        <?php foreach ($carreras as $c): ?>
        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre_carrera']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Semestre:</label>
    <input type="number" name="semestre" min="1" max="12" required>

    <label>Número de Registro Universitario:</label>
    <input type="text" name="registro_universitario" placeholder="Ej: 1234567">

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=estudiantes_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';