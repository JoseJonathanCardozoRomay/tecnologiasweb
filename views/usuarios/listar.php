<?php
if (!isset($usuarios)) $usuarios = [];
if (!isset($pagina)) $pagina = 1;
if (!isset($total_paginas)) $total_paginas = 1;
if (!isset($total_registros)) $total_registros = 0;

$titulo_pagina = 'Listado de Usuarios';
ob_start();
?>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
body {
    background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
    min-height: 100vh;
    padding: 30px 20px;
}
.contenedor {
    max-width: 1100px;
    margin: 0 auto;
    background: #fff;
    padding: 35px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}
.volver {
    display: inline-block;
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    margin-bottom: 20px;
}
h1 {
    color: #003366;
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ffc107;
}
.btn-nuevo {
    display: inline-block;
    background: #0066cc;
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th {
    background: #004080;
    color: white;
    padding: 14px 12px;
    text-align: left;
}
td {
    padding: 14px 12px;
    border-bottom: 1px solid #ddd;
}
tr:nth-child(even) { background: #f0f5ff; }
.btn-editar {
    display: inline-block;
    background: #28a745;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    margin-right: 5px;
}
.btn-eliminar {
    display: inline-block;
    background: #dc3545;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
}
.estado-activo { color: green; font-weight: bold; }
.estado-inactivo { color: red; font-weight: bold; }

/* PAGINACIÓN */
.paginacion {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.paginacion a, .paginacion span {
    padding: 10px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
}
.paginacion a {
    background: #e6f0ff;
    color: #004080;
}
.paginacion a:hover {
    background: #004080;
    color: white;
}
.paginacion .actual {
    background: #004080;
    color: white;
}
.info-total {
    text-align: right;
    margin-top: 10px;
    color: #555;
    font-size: 0.9em;
}
</style>

<div class="contenedor">
    <a href="index.php" class="volver">← Volver al inicio</a>
    
    <h1>👥 Listado de Usuarios</h1>
    
    <a href="index.php?accion=usuario_crear" class="btn-nuevo">+ Nuevo Usuario</a>
    
    <div class="info-total">
        Total: <strong><?= $total_registros ?></strong> usuarios — Página <?= $pagina ?> de <?= $total_paginas ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:30px; color:#666;">
                        No hay usuarios registrados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id_usuario'] ?? '') ?></td>
                    <td><?= htmlspecialchars(($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($u['usuario'] ?? '') ?></td>
                    <td><?= htmlspecialchars($u['nombre_rol'] ?? '') ?></td>
                    <td class="<?= ($u['estado'] ?? '') === 'activo' ? 'estado-activo' : 'estado-inactivo' ?>">
                        <?= ($u['estado'] ?? '') === 'activo' ? '✅ Activo' : '❌ Inactivo' ?>
                    </td>
                    <td>
                        <a href="index.php?accion=usuario_editar&id=<?= $u['id_usuario'] ?>" class="btn-editar">Editar</a>
                        <a href="index.php?accion=usuario_eliminar&id=<?= $u['id_usuario'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- PAGINACIÓN -->
    <?php if ($total_paginas > 1): ?>
    <div class="paginacion">
        <?php if ($pagina > 1): ?>
            <a href="index.php?accion=usuarios_listar&pagina=1">Primera</a>
            <a href="index.php?accion=usuarios_listar&pagina=<?= $pagina - 1 ?>">← Anterior</a>
        <?php endif; ?>
        
        <span class="actual"><?= $pagina ?> / <?= $total_paginas ?></span>
        
        <?php if ($pagina < $total_paginas): ?>
            <a href="index.php?accion=usuarios_listar&pagina=<?= $pagina + 1 ?>">Siguiente →</a>
            <a href="index.php?accion=usuarios_listar&pagina=<?= $total_paginas ?>">Última</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';