<?php
if (session_status() === PHP_SESSION_NONE) { @session_start(); }
$rol = $_SESSION["rol"] ?? $_SESSION["usuario_rol"] ?? "estudiante";
$nombre = $_SESSION["nombre"] ?? $_SESSION["usuario_nombre"] ?? "Usuario";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - Sistema de Tutorías Académicas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; margin: 0; color: #333; }
        header { background: #0b192c; color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        header h1 { margin: 0; font-size: 22px; }
        .user-info { font-size: 14px; background: #1e3e62; padding: 6px 12px; border-radius: 20px; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px; }
        h2 { color: #0b192c; margin-top: 0; }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 15px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .badge.administrador { background: #e74c3c; color: white; }
        .badge.tutor { background: #f39c12; color: white; }
        .badge.estudiante { background: #27ae60; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #1e3e62; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
        .btn:hover { background: #0b192c; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
        .box { background: #fff; padding: 20px; border-radius: 8px; border-left: 4px solid #1e3e62; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <header>
        <h1>Sistema de Tutorías Académicas</h1>
        <div class="user-info">
            👤 <b><?php echo htmlspecialchars($nombre); ?></b> (<span style="text-transform: capitalize;"><?php echo htmlspecialchars($rol); ?></span>)
            | <a href="views/login/login.php" style="color: #ff9f43; text-decoration: none; margin-left: 10px;">Salir</a>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>Bienvenido al Panel de Control</h2>
            <p>Has iniciado sesión correctamente con el rol de: <span class="badge <?php echo $rol; ?>"><?php echo $rol; ?></span></p>
            <p>Este espacio está configurado para la demostración y defensa del sistema web de tutorías académicas.</p>
        </div>

        <?php if ($rol === "administrador"): ?>
            <div class="grid">
                <div class="box">
                    <h3>Gestión de Usuarios</h3>
                    <p>Administrar cuentas de estudiantes, tutores y permisos del sistema.</p>
                    <a href="#" class="btn" onclick="alert('Sección de gestión de usuarios activa.'); return false;">Ver Usuarios</a>
                </div>
                <div class="box">
                    <h3>Reportes Globales</h3>
                    <p>Estadísticas de tutorías realizadas, horas acumuladas y rendimiento.</p>
                    <a href="#" class="btn" onclick="alert('Generando reporte global...'); return false;">Ver Reportes</a>
                </div>
            </div>
        <?php elseif ($rol === "tutor"): ?>
            <div class="grid">
                <div class="box">
                    <h3>Mis Tutorías Programadas</h3>
                    <p>Consulta las sesiones asignadas con estudiantes y horarios pendientes.</p>
                    <a href="#" class="btn" onclick="alert('Cargando agenda del tutor...'); return false;">Ver Agenda</a>
                </div>
                <div class="box">
                    <h3>Disponibilidad</h3>
                    <p>Configura tus horarios libres para que los estudiantes puedan reservar.</p>
                    <a href="#" class="btn" onclick="alert('Abriendo configuración de horarios...'); return false;">Configurar Horarios</a>
                </div>
            </div>
        <?php else: ?>
            <div class="grid">
                <div class="box">
                    <h3>Buscar Tutores</h3>
                    <p>Encuentra tutores disponibles por materia y solicita una sesión de apoyo.</p>
                    <a href="#" class="btn" onclick="alert('Buscando tutores disponibles...'); return false;">Buscar Tutor</a>
                </div>
                <div class="box">
                    <h3>Mis Reservas</h3>
                    <p>Revisa el estado de tus solicitudes de tutoría y próximas citas.</p>
                    <a href="#" class="btn" onclick="alert('Cargando historial de reservas...'); return false;">Ver Reservas</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>