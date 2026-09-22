<?php
$titulo_pagina = 'Editar Disponibilidad';
ob_start();
?>

<h1>Editar Disponibilidad Horaria</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=disponibilidad_editar&id=<?= $disp['id_disponibilidad'] ?>">
    <label>Tutor:</label>
    <select name="id_tutor" required>
        <?php foreach ($tutores as $t): ?>
        <option value="<?= $t['id_tutor'] ?>" <?= $t['id_tutor'] == $disp['id_tutor'] ? 'selected' : '' ?>>
            <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Día de la Semana:</label>
    <select name="dia_semana" required>
        <?php $dias = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado']; ?>
        <?php foreach ($dias as $d): ?>
        <option value="<?= $d ?>" <?= $d == $disp['dia_semana'] ? 'selected' : '' ?>><?= $d ?></option>
        <?php endforeach; ?>
    </select>

    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" value="<?= $disp['hora_inicio'] ?>" required>

    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" value="<?= $disp['hora_fin'] ?>" required>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=disponibilidad_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';