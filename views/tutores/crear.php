<?php
$titulo_pagina = 'Crear Tutor';
ob_start();
?>

<h1>Crear Nuevo Tutor</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=tutor_crear">
    <label>Usuario:</label>
    <select name="id_usuario" required>
        <option value="">Seleccione un usuario</option>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?> (<?= htmlspecialchars($u['usuario']) ?>)</option>
        <?php endforeach; ?>
    </select>

    <label>Especialidad:</label>
    <input type="text" name="especialidad" placeholder="Ej: Matemáticas, Programación...">

    <label>Biografía:</label>
    <textarea name="biografia" rows="4" placeholder="Breve descripción académica o profesional"></textarea>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=tutores_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';