<?php
$titulo_pagina = 'Crear Tutor';
ob_start();
?>
<h1>Registrar Nuevo Tutor</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <!-- ✅ Protegido -->
    <?= htmlspecialchars($error ?? '') ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=tutor_crear">
    <!-- ✅ Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Usuario:</label>
    <select name="id_usuario" required>
        <option value="">Seleccione usuario</option>
        <?php foreach ($usuarios as $u): ?>
        <!-- ✅ ID protegido como número -->
        <option value="<?= (int)$u['id_usuario'] ?>">
            <?= htmlspecialchars(($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? '')) ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <label>Especialidad:</label>
    <input type="text" name="especialidad" required>
    
    <label>Biografía:</label>
    <textarea name="biografia" rows="4"></textarea>
    
    <button type="submit" class="btn btn-primario">Guardar Tutor</button>
    <a href="index.php?accion=tutores_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';