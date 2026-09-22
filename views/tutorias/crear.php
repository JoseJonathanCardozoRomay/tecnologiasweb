<?php
$titulo_pagina = 'Agendar Tutoría';
ob_start();
?>

<h1>Agendar Nueva Tutoría</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=tutoria_crear">
    <label>Estudiante:</label>
    <select name="id_estudiante" required>
        <option value="">Seleccione</option>
        <?php foreach ($estudiantes as $e): ?>
        <option value="<?= $e['id_estudiante'] ?>"><?= htmlspecialchars(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')) ?></option>
        <?php endforeach; ?>
    </select>

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

    <label>Fecha:</label>
    <input type="date" name="fecha" required>

    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" required>

    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" required>

    <label>Modalidad:</label>
    <select name="modalidad">
        <option value="presencial">Presencial</option>
        <option value="virtual">Virtual</option>
    </select>

    <label>Lugar o Enlace:</label>
    <input type="text" name="lugar_o_enlace" placeholder="Aula o enlace de reunión">

    <label>Observaciones:</label>
    <textarea name="observaciones" rows="3" placeholder="Notas adicionales..."></textarea>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=tutorias_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';