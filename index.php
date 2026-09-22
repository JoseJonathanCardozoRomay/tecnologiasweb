<?php
/**
 * DASHBOARD — Sistema de Gestión de Tutorías
 * Menú lateral izquierdo · Fondo con imagen de universidad
 */
require_once __DIR__ . '/config/sesion.php';
$accion = $_GET['accion'] ?? '';

$rutas_publicas = [
    'login' => 'controllers/login.php',
    'cerrar_sesion' => 'controllers/cerrar_sesion.php'
];

$rutas_privadas = [
    'listar'                => ['controllers/roles_listar.php', ['administrador']],
    'rol_crear'             => ['controllers/rol_crear.php', ['administrador']],
    'rol_editar'            => ['controllers/rol_editar.php', ['administrador']],
    'rol_eliminar'          => ['controllers/rol_eliminar.php', ['administrador']],
    
     // === USUARIOS ===
    'usuarios_listar'       => ['controllers/usuarios_listar.php', ['administrador']],
    'usuario_crear'         => ['controllers/usuario_crear.php', ['administrador']],
    'usuario_editar'        => ['controllers/usuario_editar.php', ['administrador']],
    'usuario_eliminar'      => ['controllers/usuario_eliminar.php', ['administrador']],
    
    'carreras_listar' => ['controllers/carreras_listar.php', ['administrador','tutor','estudiante']],
    'carrera_crear' => ['controllers/carrera_crear.php', ['administrador']],
    'carrera_editar' => ['controllers/carrera_editar.php', ['administrador']],
    'carrera_eliminar' => ['controllers/carrera_eliminar.php', ['administrador']],
    
    'materias_listar' => ['controllers/materias_listar.php', ['administrador','tutor','estudiante']],
    'materia_crear' => ['controllers/materia_crear.php', ['administrador']],
    'materia_editar' => ['controllers/materia_editar.php', ['administrador']],
    'materia_eliminar' => ['controllers/materia_eliminar.php', ['administrador']],
    
    'tutores_listar' => ['controllers/tutores_listar.php', ['administrador','estudiante']],
    'tutor_crear' => ['controllers/tutor_crear.php', ['administrador']],
    'tutor_editar' => ['controllers/tutor_editar.php', ['administrador','tutor']],
    'tutor_eliminar' => ['controllers/tutor_eliminar.php', ['administrador']],
    
    'tutor_materia_listar' => ['controllers/tutor_materia_listar.php', ['administrador','tutor','estudiante']],
    'tutor_materia_crear' => ['controllers/tutor_materia_crear.php', ['administrador','tutor']],
    'tutor_materia_eliminar' => ['controllers/tutor_materia_eliminar.php', ['administrador','tutor']],
    
    'estudiantes_listar' => ['controllers/estudiantes_listar.php', ['administrador','tutor']],
    'estudiante_crear' => ['controllers/estudiante_crear.php', ['administrador']],
    'estudiante_editar' => ['controllers/estudiante_editar.php', ['administrador','estudiante']],
    'estudiante_eliminar' => ['controllers/estudiante_eliminar.php', ['administrador']],
    
    'disponibilidad_listar' => ['controllers/disponibilidad_listar.php', ['administrador','tutor','estudiante']],
    'disponibilidad_crear' => ['controllers/disponibilidad_crear.php', ['administrador','tutor']],
    'disponibilidad_editar' => ['controllers/disponibilidad_editar.php', ['administrador','tutor']],
    'disponibilidad_eliminar' => ['controllers/disponibilidad_eliminar.php', ['administrador','tutor']],
    
    'tutorias_listar' => ['controllers/tutorias_listar.php', ['administrador','tutor','estudiante']],
    'tutoria_crear' => ['controllers/tutoria_crear.php', ['administrador','estudiante']],
    'tutoria_editar' => ['controllers/tutoria_editar.php', ['administrador','tutor','estudiante']],
    'tutoria_eliminar' => ['controllers/tutoria_eliminar.php', ['administrador']],
    
    'evaluaciones_listar' => ['controllers/evaluaciones_listar.php', ['administrador','tutor','estudiante']],
    'evaluacion_crear' => ['controllers/evaluacion_crear.php', ['administrador','estudiante']],
    'evaluacion_editar' => ['controllers/evaluacion_editar.php', ['administrador']],
    'evaluacion_eliminar' => ['controllers/evaluacion_eliminar.php', ['administrador']],
    
    'accesos_listar' => ['controllers/accesos_listar.php', ['administrador']],
    'accesos_crear' => ['controllers/accesos_crear.php', ['administrador']],
    'accesos_editar' => ['controllers/accesos_editar.php', ['administrador']],
    'accesos_eliminar' => ['controllers/accesos_eliminar.php', ['administrador']],
    
    'notificaciones_listar' => ['controllers/notificaciones_listar.php', ['administrador','tutor','estudiante']],
    'notificacion_crear' => ['controllers/notificacion_crear.php', ['administrador']],
    'notificacion_editar' => ['controllers/notificacion_editar.php', ['administrador']],
    'notificacion_eliminar' => ['controllers/notificacion_eliminar.php', ['administrador']],
    
    'periodos_listar' => ['controllers/periodos_listar.php', ['administrador','tutor','estudiante']],
    'periodo_crear' => ['controllers/periodo_crear.php', ['administrador']],
    'periodo_editar' => ['controllers/periodo_editar.php', ['administrador']],
    'periodo_eliminar' => ['controllers/periodo_eliminar.php', ['administrador']],
    
    'bloques_listar' => ['controllers/bloques_listar.php', ['administrador','tutor','estudiante']],
    'bloque_crear' => ['controllers/bloque_crear.php', ['administrador']],
    'bloque_editar' => ['controllers/bloque_editar.php', ['administrador']],
    'bloque_eliminar' => ['controllers/bloque_eliminar.php', ['administrador']],
    
    'seguimientos_listar' => ['controllers/seguimientos_listar.php', ['administrador','tutor']],
    'seguimiento_crear' => ['controllers/seguimiento_crear.php', ['administrador','tutor']],
    'seguimiento_editar' => ['controllers/seguimiento_editar.php', ['administrador','tutor']],
    'seguimiento_eliminar' => ['controllers/seguimiento_eliminar.php', ['administrador']]
];

if (isset($rutas_publicas[$accion])) {
    require_once $rutas_publicas[$accion];
    exit;
}

if (!estaAutenticado()) {
    header('Location: index.php?accion=login');
    exit;
}

if (isset($rutas_privadas[$accion])) {
    list($archivo, $permitidos) = $rutas_privadas[$accion];
    if (!tieneRol($permitidos)) {
        echo "<script>alert('No tienes permiso para acceder a esta sección');history.back();</script>";
        exit;
    }
    if (file_exists($archivo)) {
        require_once $archivo;
        exit;
    }
}

$rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', 'Times New Roman', serif;
            background: linear-gradient(rgba(0, 38, 77, 0.82), rgba(0, 38, 77, 0.82)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            color: #fff;
        }

        /* === MENÚ LATERAL IZQUIERDO === */
        .menu-lateral {
            width: 260px;
            background: rgba(0, 10, 26, 0.85);
            backdrop-filter: blur(10px);
            padding: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0,0,0,0.3);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .menu-header {
            padding: 25px 20px;
            background: linear-gradient(180deg, #004080 0%, #00264d 100%);
            border-bottom: 2px solid #ffc107;
        }

        .menu-header h2 {
            font-size: 18px;
            text-align: center;
            color: #ffd700;
            margin-bottom: 5px;
        }

        .menu-header p {
            font-size: 12px;
            text-align: center;
            color: #99ccff;
        }

        .usuario-info {
            padding: 18px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .usuario-nombre {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .rol-etiqueta {
            display: inline-block;
            background: linear-gradient(90deg, #ffc107, #ff9800);
            color: #000;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .menu-navegacion {
            padding: 20px 12px;
            flex: 1;
        }

        .menu-navegacion a {
            display: block;
            color: #e6f0ff;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 6px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .menu-navegacion a:hover {
            background: rgba(255, 193, 7, 0.2);
            border-left-color: #ffc107;
            padding-left: 20px;
        }

        .cerrar-sesion {
            padding: 20px 15px 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .cerrar-btn {
            display: block;
            background: linear-gradient(90deg, #c0392b, #e74c3c);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s ease;
        }

        .cerrar-btn:hover {
            transform: scale(1.03);
        }

        /* === CONTENIDO PRINCIPAL === */
        .contenido {
            flex: 1;
            margin-left: 260px;
            padding: 40px;
        }

        .bienvenida {
            background: rgba(255, 255, 255, 0.95);
            color: #003366;
            padding: 35px 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-left: 6px solid #ffc107;
        }

        .bienvenida h1 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .bienvenida p {
            font-size: 17px;
            color: #555;
        }

        .tarjetas-contenedor {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .tarjeta {
            background: rgba(255, 255, 255, 0.95);
            color: #003366;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            border-top: 4px solid #0066cc;
        }

        .tarjeta:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
            border-top-color: #ffc107;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .menu-lateral { width: 100%; position: relative; height: auto; }
            .contenido { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    <!-- MENÚ LATERAL IZQUIERDO -->
    <aside class="menu-lateral">
        <div class="menu-header">
            <h2>🎓 Sistema de Tutorías</h2>
            <p>Gestión Académica</p>
        </div>

        <div class="usuario-info">
            <div class="usuario-nombre"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></div>
            <span class="rol-etiqueta">
                <?php
                $et = ['administrador'=>'👑 Administrador', 'tutor'=>'👨‍🏫 Tutor', 'estudiante'=>'🎓 Estudiante'];
                echo $et[$rol_actual] ?? $rol_actual;
                ?>
            </span>
        </div>

        <nav class="menu-navegacion">
            <?php if ($rol_actual === 'administrador'): ?>
                <a href="index.php?accion=listar">📋 Roles</a>
                <a href="index.php?accion=usuarios_listar">👤 Usuarios</a>
                <a href="index.php?accion=carreras_listar">🎓 Carreras</a>
                <a href="index.php?accion=materias_listar">📚 Materias</a>
                <a href="index.php?accion=tutores_listar">👨‍🏫 Tutores</a>
                <a href="index.php?accion=tutor_materia_listar">🔗 Tutor-Materia</a>
                <a href="index.php?accion=estudiantes_listar">🎓 Estudiantes</a>
                <a href="index.php?accion=disponibilidad_listar">📅 Disponibilidad</a>
                <a href="index.php?accion=bloques_listar">🕐 Bloques Horarios</a>
                <a href="index.php?accion=periodos_listar">📅 Periodos</a>
                <a href="index.php?accion=tutorias_listar">📝 Tutorías</a>
                <a href="index.php?accion=evaluaciones_listar">⭐ Evaluaciones</a>
                <a href="index.php?accion=seguimientos_listar">📝 Seguimiento</a>
                <a href="index.php?accion=notificaciones_listar">🔔 Notificaciones</a>
                <a href="index.php?accion=accesos_listar">🔒 Registro de Accesos</a>
            <?php endif; ?>

            <?php if ($rol_actual === 'tutor'): ?>
                <a href="index.php?accion=disponibilidad_listar">📅 Disponibilidad</a>
                <a href="index.php?accion=tutorias_listar">📝 Mis Tutorías</a>
                <a href="index.php?accion=seguimientos_listar">📝 Seguimiento</a>
                <a href="index.php?accion=materias_listar">📚 Materias</a>
                <a href="index.php?accion=estudiantes_listar">🎓 Estudiantes</a>
                <a href="index.php?accion=notificaciones_listar">🔔 Notificaciones</a>
            <?php endif; ?>

            <?php if ($rol_actual === 'estudiante'): ?>
                <a href="index.php?accion=tutorias_listar">📝 Mis Tutorías</a>
                <a href="index.php?accion=tutores_listar">👨‍🏫 Tutores</a>
                <a href="index.php?accion=disponibilidad_listar">📅 Horarios</a>
                <a href="index.php?accion=evaluaciones_listar">⭐ Evaluaciones</a>
                <a href="index.php?accion=notificaciones_listar">🔔 Notificaciones</a>
            <?php endif; ?>
        </nav>

        <div class="cerrar-sesion">
            <a href="index.php?accion=cerrar_sesion" class="cerrar-btn">🚪 Cerrar Sesión</a>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido">
        <div class="bienvenida">
            <h1>¡Bienvenido(a), <?= explode(' ', $_SESSION['usuario_nombre'] ?? 'Usuario')[0] ?>!</h1>
            <p>Seleccione un módulo desde el menú lateral para comenzar.</p>
        </div>

        <div class="tarjetas-contenedor">
            <?php if ($rol_actual === 'administrador'): ?>
                <a href="index.php?accion=listar" class="tarjeta">Roles</a>
                <a href="index.php?accion=usuarios_listar" class="tarjeta">Usuarios</a>
                <a href="index.php?accion=carreras_listar" class="tarjeta">Carreras</a>
                <a href="index.php?accion=materias_listar" class="tarjeta">Materias</a>
                <a href="index.php?accion=tutores_listar" class="tarjeta">Tutores</a>
                <a href="index.php?accion=tutor_materia_listar" class="tarjeta">Tutor-Materia</a>
                <a href="index.php?accion=estudiantes_listar" class="tarjeta">Estudiantes</a>
                <a href="index.php?accion=disponibilidad_listar" class="tarjeta">Disponibilidad</a>
                <a href="index.php?accion=bloques_listar" class="tarjeta">Bloques</a>
                <a href="index.php?accion=periodos_listar" class="tarjeta">Periodos</a>
                <a href="index.php?accion=tutorias_listar" class="tarjeta">Tutorías</a>
                <a href="index.php?accion=evaluaciones_listar" class="tarjeta">Evaluaciones</a>
                <a href="index.php?accion=seguimientos_listar" class="tarjeta">Seguimiento</a>
                <a href="index.php?accion=notificaciones_listar" class="tarjeta">Notificaciones</a>
                <a href="index.php?accion=accesos_listar" class="tarjeta">Accesos</a>
            <?php endif; ?>

            <?php if ($rol_actual === 'tutor'): ?>
                <a href="index.php?accion=disponibilidad_listar" class="tarjeta">Disponibilidad</a>
                <a href="index.php?accion=tutorias_listar" class="tarjeta">Mis Tutorías</a>
                <a href="index.php?accion=seguimientos_listar" class="tarjeta">Seguimiento</a>
                <a href="index.php?accion=materias_listar" class="tarjeta">Materias</a>
                <a href="index.php?accion=estudiantes_listar" class="tarjeta">Estudiantes</a>
                <a href="index.php?accion=notificaciones_listar" class="tarjeta">Notificaciones</a>
            <?php endif; ?>

            <?php if ($rol_actual === 'estudiante'): ?>
                <a href="index.php?accion=tutorias_listar" class="tarjeta">Mis Tutorías</a>
                <a href="index.php?accion=tutores_listar" class="tarjeta">Tutores</a>
                <a href="index.php?accion=disponibilidad_listar" class="tarjeta">Horarios</a>
                <a href="index.php?accion=evaluaciones_listar" class="tarjeta">Evaluaciones</a>
                <a href="index.php?accion=notificaciones_listar" class="tarjeta">Notificaciones</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>