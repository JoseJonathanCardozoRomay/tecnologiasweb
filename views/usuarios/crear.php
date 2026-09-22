<?php
$titulo_pagina = 'Crear Usuario';
ob_start();
?>

<h1>Crear Nuevo Usuario</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=usuario_crear">
    <label>Rol:</label>
    <select name="id_rol" required>
        <option value="">Seleccione</option>
        <?php foreach ($roles as $r): ?>
        <option value="<?= $r['id_rol'] ?>"><?= htmlspecialchars($r['nombre_rol']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Nombre:</label>
    <input type="text" name="nombre" required>

    <label>Apellido:</label>
    <input type="text" name="apellido" required>

    <label>Correo:</label>
    <input type="email" name="correo" required>

    <label>Usuario:</label>
    <input type="text" name="usuario" required>

    <label>Contraseña:</label>
    <input type="password" name="contrasena" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono">

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=usuarios_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';