 <?php
$titulo_pagina = 'Registrar Seguimiento';
ob_start();
?>

<h1>Registrar Seguimiento</h1>

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
                <option value="<?= $t['id_tutoria'] ?>">
                    ID <?= $t['id_tutoria'] ?> — <?= htmlspecialchars($t['fecha'] ?? '') ?> (<?= htmlspecialchars($t['estado'] ?? '') ?>)
                </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>No hay tutorías registradas</option>
            <?php endif; ?>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Asistió:</label>
        <select name="asistio" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="si">Sí</option>
            <option value="no">No</option>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Temas Tratados:</label>
        <textarea name="temas_tratados" rows="4" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;" placeholder="Escribe los temas que se revisaron..."></textarea>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Nivel de Avance:</label>
        <select name="avance" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="sin_avance">Sin Avance</option>
            <option value="parcial">Avance Parcial</option>
            <option value="logrado">Logrado</option>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Recomendaciones:</label>
        <textarea name="recomendaciones" rows="3" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;" placeholder="Observaciones o sugerencias..."></textarea>
    </div>

    <button type="submit" style="background:#003366; color:white; padding:10px 22px; border:none; border-radius:4px; font-size:16px; cursor:pointer;">Guardar</button>
    <a href="index.php?accion=seguimientos_listar" style="color:#666; margin-left:12px; text-decoration:none;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';