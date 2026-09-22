<?php
$titulo_pagina = 'Registrar Disponibilidad';
ob_start();
?>

<h1>Registrar Disponibilidad Horaria</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=disponibilidad_crear">
    <label>Tutor:</label>
    <select name="id_tutor" required>
        <option value="">Seleccione</option>
        <?php foreach ($tutores as $t): ?>
        <option value="<?= $t['id_tutor'] ?>"><?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Día de la Semana:</label>
    <select name="dia_semana" required>
        <option value="">Seleccione</option>
        <option value="Lunes">Lunes</option>
        <option value="Martes">Martes</option>
        <option value="Miercoles">Miércoles</option>
        <option value="Jueves">Jueves</option>
        <option value="Viernes">Viernes</option>
        <option value="Sabado">Sábado</option>
    </select>

    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" required>

    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" required>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=disponibilidad_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';