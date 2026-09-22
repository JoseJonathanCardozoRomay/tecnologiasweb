<?php
$titulo_pagina = 'Agendar Tutoría';
ob_start();
?>

<h1>Agendar Nueva Tutoría</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#cc0000; padding:10px; margin:15px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=tutoria_crear" style="max-width:500px; margin:20px auto;">

    <div style="margin-bottom:15px;">
        <label>Estudiante:</label>
        <select name="id_estudiante" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">Seleccione</option>
            <?php foreach ($estudiantes as $e): ?>
            <option value="<?= $e['id_estudiante'] ?>">
                <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Tutor:</label>
        <select name="id_tutor" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">Seleccione</option>
            <?php foreach ($tutores as $t): ?>
            <option value="<?= $t['id_tutor'] ?>">
                <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Materia:</label>
        <select name="id_materia" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">Seleccione</option>
            <?php foreach ($materias as $m): ?>
            <option value="<?= $m['id_materia'] ?>">
                <?= htmlspecialchars($m['nombre_materia']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Fecha:</label>
        <input type="date" name="fecha" required
               value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Hora de Inicio:</label>
        <input type="time" name="hora_inicio" required
               value="<?= htmlspecialchars($_POST['hora_inicio'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Hora de Fin:</label>
        <input type="time" name="hora_fin" required
               value="<?= htmlspecialchars($_POST['hora_fin'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Modalidad:</label>
        <select name="modalidad" style="width:100%; padding:8px; margin-top:5px;">
            <option value="presencial" <?= (($_POST['modalidad'] ?? 'presencial') === 'presencial') ? 'selected' : '' ?>>Presencial</option>
            <option value="virtual" <?= (($_POST['modalidad'] ?? '') === 'virtual') ? 'selected' : '' ?>>Virtual</option>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Lugar / Enlace:</label>
        <input type="text" name="lugar_o_enlace" 
               value="<?= htmlspecialchars($_POST['lugar_o_enlace'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Estado:</label>
        <select name="estado" style="width:100%; padding:8px; margin-top:5px;">
            <option value="pendiente" <?= (($_POST['estado'] ?? 'pendiente') === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
            <option value="confirmada" <?= (($_POST['estado'] ?? '') === 'confirmada') ? 'selected' : '' ?>>Confirmada</option>
            <option value="realizada" <?= (($_POST['estado'] ?? '') === 'realizada') ? 'selected' : '' ?>>Realizada</option>
            <option value="cancelada" <?= (($_POST['estado'] ?? '') === 'cancelada') ? 'selected' : '' ?>>Cancelada</option>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Observaciones:</label>
        <textarea name="observaciones" rows="3" style="width:100%; padding:8px; margin-top:5px;"><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
    </div>

    <button type="submit" style="background:#003366; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:16px;">Guardar</button>
    <a href="index.php?accion=tutorias_listar" style="margin-left:10px; color:#666;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';