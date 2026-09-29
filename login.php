<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_user = trim($_POST['usuario'] ?? '');
    $input_pass = $_POST['password'] ?? '';

    // Acceso universal directo
    if ($input_user === 'admin' && $input_pass === 'admin') {
        $_SESSION['usuario_id'] = 1;
        $_SESSION['usuario_nombre'] = 'Administrador General';
        $_SESSION['usuario_rol'] = 'Administrador';
        header('Location: panel.php');
        exit;
    } else {
        $error = 'Credenciales incorrectas. Usa: usuario: admin | contraseña: admin';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Tutorías Académicas - UPDS Tarija</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body, html { height: 100vh; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .main-container { display: flex; height: 100vh; width: 100vw; }
        .left-banner { background: #071930; color: white; flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 4rem; }
        .right-form { background: #ffffff; flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 3rem; }
        .form-wrapper { width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="left-banner d-none d-lg-flex">
            <h6 class="text-uppercase text-primary fw-bold mb-1">UNIVERSIDAD PRIVADA DOMINGO SAVIO</h6>
            <p class="text-white-50 small mb-4">SEDE TARIJA</p>
            <h1 class="display-5 fw-bold mb-3">Sistema de Tutorías Académicas</h1>
            <p class="text-white-50 lead fs-6 mb-4">Un espacio institucional para conectar estudiantes y tutores con acompañamiento académico oportuno.</p>
        </div>
        <div class="right-form">
            <div class="form-wrapper">
                <div class="text-end mb-3"><i class="bi bi-mortarboard-fill fs-1 text-dark"></i></div>
                <div class="mb-4">
                    <span class="text-muted small text-uppercase fw-bold">Acceso Institucional</span>
                    <h3 class="fw-bold text-dark mt-1">Bienvenido de nuevo</h3>
                    <p class="text-muted small">Inicia sesión con: <b>admin</b> / <b>admin</b></p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="usuario" class="form-control border-start-0 ps-0" placeholder="admin" required value="admin">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="admin" required value="admin">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold mt-3">
                        Iniciar Sesión <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>