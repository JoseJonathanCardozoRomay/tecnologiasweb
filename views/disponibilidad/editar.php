<?php
if (!isset($disponibilidad)) $disponibilidad = [];
if (!isset($tutores)) $tutores = [];
if (!isset($error)) $error = '';
$rol_actual = $_SESSION['rol_nombre'] ?? '';

$titulo_pagina = 'Editar Disponibilidad';
ob_start();
?>
<h1>Editar Disponibilidad Horaria</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=disponibilidad_editar&id=<?= (int)($disponibilidad['id_disponibilidad'] ?? 0) ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">

    <?php if ($rol_actual === 'administrador'): ?>
    <label>Tutor:</label>
    <select name="id_tutor" required>
        <option value="">Seleccione tutor</option>
        <?php foreach ($tutores as $t): ?>
        <option value="<?= (int)$t['id_tutor'] ?>" 
            <?= ($t['id_tutor'] == ($disponibilidad['id_tutor'] ?? 0)) ? 'selected' : '' ?>>
            <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php else: ?>
    <input type="hidden" name="id_tutor" value="<?= (int)($disponibilidad['id_tutor'] ?? 0) ?>">
    <?php endif; ?>

    <label>Día de la Semana:</label>
    <select name="dia_semana" required>
        <option value="">Seleccione día</option>
        <?php 
        $dias = ['lunes','martes','miércoles','jueves','viernes','sábado'];
        foreach ($dias as $d): 
        ?>
        <option value="<?= htmlspecialchars($d) ?>" 
            <?= (($disponibilidad['dia_semana'] ?? '') === $d) ? 'selected' : '' ?>>
            <?= htmlspecialchars(ucfirst($d)) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" 
        value="<?= htmlspecialchars($disponibilidad['hora_inicio'] ?? '') ?>" required>

    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" 
        value="<?= htmlspecialchars($disponibilidad['hora_fin'] ?? '') ?>" required>

    <button type="submit" class="btn btn-exito">Guardar Cambios</button>
    <a href="index.php?accion=disponibilidad_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';