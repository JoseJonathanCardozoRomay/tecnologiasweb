<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Tutorías Académicas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0b192c; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #333; }
        .container { background: white; padding: 40px; border-radius: 12px; width: 380px; box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
        h2 { margin-top: 0; color: #0b192c; }
        p { color: #666; font-size: 14px; }
        label { display: block; margin-top: 15px; font-weight: 600; font-size: 13px; }
        input { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; margin-top: 20px; background: #1e3e62; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 15px; }
        button:hover { background: #000; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Acceso al Sistema</h2>
        <p>Ingresa con cualquier credencial para la presentación</p>
        <form action="../../controllers/login_procesar.php" method="POST">
            <label>Usuario o Correo</label>
            <input type="text" name="usuario" value="admin" required>
            
            <label>Contraseña</label>
            <input type="password" name="password" value="12345" required>
            
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>