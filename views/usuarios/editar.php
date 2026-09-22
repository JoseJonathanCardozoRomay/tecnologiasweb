<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body { background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 600px; margin: 0 auto; }
        .encabezado { background: linear-gradient(90deg,#00264d,#003366); color: white; padding: 22px 30px; border-radius: 10px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #cc9900; }
        .encabezado h1 { font-size: 22px; font-weight: bold; }
        .volver-btn { background: rgba(255,255,255,0.18); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); }
        .tarjeta { background: rgba(255,255,255,0.97); padding: 35px; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.25); border-top: 4px solid #cc9900; }
        .mensaje-error { background: #ffe6e6; border-left: 4px solid #b30000; color: #800000; padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        label { display: block; margin: 18px 0 6px; font-weight: bold; color: #00264d; }
        input, select { width: 100%; padding: 12px 15px; border: 1px solid #99b3cc; border-radius: 6px; font-size: 15px; background: #fafcff; }
        input:focus, select:focus { outline: none; border-color: #cc9900; box-shadow: 0 0 0 3px rgba(204,153,0,0.2); }
        button { background: linear-gradient(90deg,#cc9900,#e6ac00,#cc9900); color: #00264d; border: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold; width: 100%; margin-top: 25px; cursor: pointer; }
        button:hover { background: linear-gradient(90deg,#b38600,#cc9900); }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>✏️ Editar Usuario</h1>
            <a href="index.php?accion=usuarios_listar" class="volver-btn">← Volver al Listado</a>
        </div>
        <div class="tarjeta">
            <?php if ($error): ?><div class="mensaje-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="POST" action="index.php?accion=usuario_editar&id=<?= $usuario['id_usuario'] ?>">
                <label for="id_rol">Rol:</label>
                <select name="id_rol" id="id_rol" required>
                    <?php while ($r = $roles->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?= $r['id_rol'] ?>" <?= $usuario['id_rol'] == $r['id_rol'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre_rol']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado">
                    <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
                <button type="submit">💾 Actualizar Usuario</button>
            </form>
        </div>
    </div>
</body>
</html>