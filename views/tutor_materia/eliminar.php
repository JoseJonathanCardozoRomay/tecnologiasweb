<?php
$titulo_pagina = 'Quitar Materia';
ob_start();
?>
<h1>Quitar Materia al Tutor</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <!-- ✅ Protegido -->
    <?= htmlspecialchars($error ?? '') ?>
</div>
<?php endif; ?>
<div style="text-align:center; padding:20px;">
    <p>¿Seguro que quieres quitar la materia <strong><?= htmlspecialchars($nombre_materia ?? '') ?></strong> al tutor <strong><?= htmlspecialchars($nombre_tutor ?? '') ?></strong>?</p>
    <form method="POST" action="index.php?accion=tutor_materia_eliminar_confirmar">
        <!-- ✅ Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
        <input type="hidden" name="id_tutor" value="<?= (int)($id_tutor ?? 0) ?>">
        <input type="hidden" name="id_materia" value="<?= (int)($id_materia ?? 0) ?>">
        <button type="submit" class="btn btn-eliminar">Sí, Quitar</button>
        <a href="index.php?accion=tutor_materia_listar" class="btn btn-volver">Cancelar</a>
    </form>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';