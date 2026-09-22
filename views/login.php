<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Sistema de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body { background: linear-gradient(135deg, #003366 0%, #0059b3 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .caja-login { background: white; padding: 45px 35px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); width: 100%; max-width: 420px; }
        h1 { color: #003366; text-align: center; margin-bottom: 8px; font-size: 26px; }
        .subtitulo { text-align: center; color: #666; margin-bottom: 30px; }
        .error { background: #ffe6e6; border-left: 4px solid #cc0000; padding: 12px; margin-bottom: 20px; color: #990000; border-radius: 4px; }
        label { display: block; margin: 18px 0 6px; font-weight: bold; color: #333; font-size: 15px; }
        input { width: 100%; padding: 13px; border: 1px solid #ccc; border-radius: 6px; font-size: 16px; }
        button { width: 100%; padding: 14px; background: #003366; color: white; border: none; border-radius: 6px; font-size: 17px; margin-top: 25px; cursor: pointer; }
        button:hover { background: #004080; }
    </style>
</head>
<body>
    <div class="caja-login">
        <h1>🔐 Sistema de Tutorías</h1>
        <p class="subtitulo">Plataforma de Gestión Académica</p>
        
        <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?accion=login">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required placeholder="admin / tutor1 / estudiante1">
            
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required placeholder="12345">
            
            <button type="submit">Ingresar al Sistema</button>
        </form>
    </div>
</body>
</html>