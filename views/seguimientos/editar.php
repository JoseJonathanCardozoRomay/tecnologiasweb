<?php
$titulo_pagina = 'Editar Seguimiento';
ob_start();
?>

<h1>Editar Seguimiento</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=seguimiento_editar&id=<?= $seg['id_seguimiento'] ?>">
    <label>Tutoría:</label>
    <select name="id_tutoria" required>
        <?php foreach ($tutorias as $t): ?>
        <option value="<?= $t['id_tutoria'] ?>" <?= $t['id_tutoria'] == $seg['id_tutoria'] ? 'selected' : '' ?>>
            Tutoría #<?= $t['id_tutoria'] ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>¿Asistió?</label>
    <select name="asistio" required>
        <option value="si" <?= $seg['asistio'] === 'si' ? 'selected' : '' ?>>Sí ✅</option>
        <option value="no" <?= $seg['asistio'] === 'no' ? 'selected' : '' ?>>No ❌</option>
    </select>

    <label>Temas Tratados:</label>
    <textarea name="temas_tratados" rows="4"><?= htmlspecialchars($seg['temas_tratados'] ?? '') ?></textarea>

    <label>Nivel de Avance:</label>
    <select name="avance">
        <option value="sin_avance" <?= ($seg['avance'] ?? '') === 'sin_avance' ? 'selected' : '' ?>>Sin avance</option>
        <option value="parcial" <?= ($seg['avance'] ?? '') === 'parcial' ? 'selected' : '' ?>>Avance Parcial</option>
        <option value="logrado" <?= ($seg['avance'] ?? '') === 'logrado' ? 'selected' : '' ?>>Objetivo Logrado</option>
    </select>

    <label>Recomendaciones:</label>
    <textarea name="recomendaciones" rows="3"><?= htmlspecialchars($seg['recomendaciones'] ?? '') ?></textarea>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=seguimientos_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';