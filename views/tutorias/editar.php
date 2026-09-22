<?php
$titulo_pagina = 'Editar Tutoría';
ob_start();
?>

<h1>Editar Tutoría</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=tutoria_editar&id=<?= $tutoria['id_tutoria'] ?>">
    <label>Estudiante:</label>
    <select name="id_estudiante" required>
        <?php foreach ($estudiantes as $e): ?>
        <option value="<?= $e['id_estudiante'] ?>" <?= $e['id_estudiante'] == $tutoria['id_estudiante'] ? 'selected' : '' ?>>
            <?= htmlspecialchars(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Tutor:</label>
    <select name="id_tutor" required>
        <?php foreach ($tutores as $t): ?>
        <option value="<?= $t['id_tutor'] ?>" <?= $t['id_tutor'] == $tutoria['id_tutor'] ? 'selected' : '' ?>>
            <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Materia:</label>
    <select name="id_materia" required>
        <?php foreach ($materias as $m): ?>
        <option value="<?= $m['id_materia'] ?>" <?= $m['id_materia'] == $tutoria['id_materia'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($m['nombre_materia']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Fecha:</label>
    <input type="date" name="fecha" value="<?= $tutoria['fecha'] ?>" required>

    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" value="<?= $tutoria['hora_inicio'] ?>" required>

    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" value="<?= $tutoria['hora_fin'] ?>" required>

    <label>Modalidad:</label>
    <select name="modalidad">
        <option value="presencial" <?= ($tutoria['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
        <option value="virtual" <?= ($tutoria['modalidad'] ?? '') === 'virtual' ? 'selected' : '' ?>>Virtual</option>
    </select>

    <label>Lugar o Enlace:</label>
    <input type="text" name="lugar_o_enlace" value="<?= htmlspecialchars($tutoria['lugar_o_enlace'] ?? '') ?>">

    <label>Estado:</label>
    <select name="estado">
        <option value="pendiente" <?= ($tutoria['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="confirmada" <?= ($tutoria['estado'] ?? '') === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
        <option value="realizada" <?= ($tutoria['estado'] ?? '') === 'realizada' ? 'selected' : '' ?>>Realizada</option>
        <option value="cancelada" <?= ($tutoria['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
    </select>

    <label>Observaciones:</label>
    <textarea name="observaciones" rows="3"><?= htmlspecialchars($tutoria['observaciones'] ?? '') ?></textarea>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=tutorias_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';