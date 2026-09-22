<?php
$titulo_pagina = 'Editar Seguimiento';
ob_start();
?>

<h1>Editar Seguimiento</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#c00; padding:10px 14px; margin:15px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="" style="max-width:600px; margin:25px auto; background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Tutoría:</label>
        <select name="id_tutoria" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="">Seleccione una tutoría</option>
            <?php if (!empty($tutorias)): ?>
                <?php foreach ($tutorias as $t): ?>
                <option value="<?= $t['id_tutoria'] ?>" 
                    <?= ($registro['id_tutoria'] ?? 0) == $t['id_tutoria'] ? 'selected' : '' ?>>
                    ID <?= $t['id_tutoria'] ?> — <?= htmlspecialchars($t['fecha'] ?? '') ?> (<?= htmlspecialchars($t['estado'] ?? '') ?>)
                </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Asistió:</label>
        <select name="asistio" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="si" <?= ($registro['asistio'] ?? '') === 'si' ? 'selected' : '' ?>>Sí</option>
            <option value="no" <?= ($registro['asistio'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Temas Tratados:</label>
        <textarea name="temas_tratados" rows="4" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;"><?= htmlspecialchars($registro['temas_tratados'] ?? '') ?></textarea>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Nivel de Avance:</label>
        <select name="avance" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="sin_avance" <?= ($registro['avance'] ?? '') === 'sin_avance' ? 'selected' : '' ?>>Sin Avance</option>
            <option value="parcial" <?= ($registro['avance'] ?? '') === 'parcial' ? 'selected' : '' ?>>Avance Parcial</option>
            <option value="logrado" <?= ($registro['avance'] ?? '') === 'logrado' ? 'selected' : '' ?>>Logrado</option>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Recomendaciones:</label>
        <textarea name="recomendaciones" rows="3" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;"><?= htmlspecialchars($registro['recomendaciones'] ?? '') ?></textarea>
    </div>

    <button type="submit" style="background:#003366; color:white; padding:10px 22px; border:none; border-radius:4px; font-size:16px; cursor:pointer;">Actualizar</button>
    <a href="index.php?accion=seguimientos_listar" style="color:#666; margin-left:12px; text-decoration:none;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';