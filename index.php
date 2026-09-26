<?php
/**
 * DASHBOARD — Sistema de Gestión de Tutorías
 * Versión: Completa + Sprint 4 + Sprint 5 — Seguimiento
 * ✅ Corregido: Sesión primero + consultas optimizadas
 */

// ✅ PRIMERO: Cargar sesión ANTES de todo — sin espacios ni líneas vacías antes
require_once __DIR__ . '/config/sesion.php';
require_once __DIR__ . '/config/conexion.php';

// ✅ Contar notificaciones sin leer
$total_sin_leer = 0;
$alertas_seguimiento_sin_leer = 0;

if (isset($_SESSION['id_usuario'])) {
    global $conexion;
    
    // Notificaciones
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM notificaciones WHERE id_usuario = :id_usuario AND leida = 0");
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
    $stmt->execute();
    $total_sin_leer = (int)$stmt->fetchColumn();
    
    // Alertas de seguimiento
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM alertas_seguimiento WHERE id_usuario_destino = :id_usuario AND leida = 0");
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
    $stmt->execute();
    $alertas_seguimiento_sin_leer = (int)$stmt->fetchColumn();
}

$accion = $_GET['accion'] ?? '';

// === RUTAS PÚBLICAS ===
$rutas_publicas = [
    'login' => 'controllers/login.php',
    'cerrar_sesion' => 'controllers/cerrar_sesion.php'
];

// === RUTAS PRIVADAS con permisos por rol ===
$rutas_privadas = [
    // === ROLES ===
    'listar'                          => ['controllers/roles_listar.php', ['administrador']],
    'rol_crear'                       => ['controllers/rol_crear.php', ['administrador']],
    'rol_editar'                      => ['controllers/rol_editar.php', ['administrador']],
    'rol_eliminar'                    => ['controllers/rol_eliminar.php', ['administrador']],
    
    // === USUARIOS ===
    'usuarios_listar'                 => ['controllers/usuarios_listar.php', ['administrador']],
    'usuario_crear'                    => ['controllers/usuario_crear.php', ['administrador']],
    'usuario_editar'                   => ['controllers/usuario_editar.php', ['administrador']],
    'usuario_eliminar'                 => ['controllers/usuario_eliminar.php', ['administrador']],
    
    // === CARRERAS ===
    'carreras_listar'                  => ['controllers/carreras_listar.php', ['administrador','tutor','estudiante']],
    'carrera_crear'                    => ['controllers/carrera_crear.php', ['administrador']],
    'carrera_editar'                   => ['controllers/carrera_editar.php', ['administrador']],
    'carrera_eliminar'                 => ['controllers/carrera_eliminar.php', ['administrador']],
    
    // === MATERIAS ===
    'materias_listar'                  => ['controllers/materias_listar.php', ['administrador','tutor','estudiante']],
    'materia_crear'                    => ['controllers/materia_crear.php', ['administrador']],
    'materia_editar'                   => ['controllers/materia_editar.php', ['administrador']],
    'materia_eliminar'                 => ['controllers/materia_eliminar.php', ['administrador']],
    
    // === TUTORES ===
    'tutores_listar'                   => ['controllers/tutores_listar.php', ['administrador','tutor','estudiante']],
    'tutor_crear'                      => ['controllers/tutor_crear.php', ['administrador']],
    'tutor_editar'                     => ['controllers/tutor_editar.php', ['administrador','tutor']],
    'tutor_eliminar'                   => ['controllers/tutor_eliminar.php', ['administrador']],
    
    // === TUTOR-MATERIA ===
    'tutor_materia_listar'             => ['controllers/tutor_materia_listar.php', ['administrador']],
    'tutor_materia_asignar'            => ['controllers/tutor_materia_asignar.php', ['administrador']],
    'tutor_materia_quitar'             => ['controllers/tutor_materia_quitar.php', ['administrador']],
    
    // === ESTUDIANTES ===
    'estudiantes_listar'                => ['controllers/estudiantes_listar.php', ['administrador','tutor']],
    'estudiante_crear'                  => ['controllers/estudiante_crear.php', ['administrador']],
    'estudiante_editar'                 => ['controllers/estudiante_editar.php', ['administrador','estudiante']],
    'estudiante_eliminar'               => ['controllers/estudiante_eliminar.php', ['administrador']],
    
    // === DISPONIBILIDAD ===
    'disponibilidad_listar'             => ['controllers/disponibilidad_listar.php', ['administrador','tutor','estudiante']],
    'disponibilidad_crear'              => ['controllers/disponibilidad_crear.php', ['administrador','tutor']],
    'disponibilidad_editar'             => ['controllers/disponibilidad_editar.php', ['administrador','tutor']],
    'disponibilidad_eliminar'           => ['controllers/disponibilidad_eliminar.php', ['administrador','tutor']],
    
    // === TUTORÍAS ===
    'tutorias_listar'                   => ['controllers/tutorias_listar.php', ['administrador','tutor','estudiante']],
    'tutoria_crear'                      => ['controllers/tutoria_crear.php', ['administrador','estudiante']],
    'tutoria_editar'                     => ['controllers/tutoria_editar.php', ['administrador','tutor']],
    'tutoria_eliminar'                   => ['controllers/tutoria_eliminar.php', ['administrador']],
    
    // === EVALUACIONES ===
    'evaluaciones_listar'               => ['controllers/evaluaciones_listar.php', ['administrador','tutor','estudiante']],
    'evaluacion_crear'                   => ['controllers/evaluacion_crear.php', ['administrador','estudiante']],
    'evaluacion_editar'                  => ['controllers/evaluacion_editar.php', ['administrador']],
    'evaluacion_eliminar'                => ['controllers/evaluacion_eliminar.php', ['administrador']],
    
    // === ACCESOS ===
    'accesos_listar'                     => ['controllers/accesos_listar.php', ['administrador']],
    
    // === NOTIFICACIONES ===
    'notificaciones_listar'              => ['controllers/notificaciones_listar.php', ['administrador','tutor','estudiante']],
    'notificacion_crear'                  => ['controllers/notificacion_crear.php', ['administrador']],
    'notificacion_eliminar'              => ['controllers/notificacion_eliminar.php', ['administrador']],
    
    // === PERIODOS ===
    'periodos_listar'                    => ['controllers/periodos_listar.php', ['administrador','tutor','estudiante']],
    'periodo_crear'                       => ['controllers/periodo_crear.php', ['administrador']],
    'periodo_editar'                      => ['controllers/periodo_editar.php', ['administrador']],
    'periodo_eliminar'                    => ['controllers/periodo_eliminar.php', ['administrador']],
    
    // === BLOQUES HORARIOS ===
    'bloques_listar'                     => ['controllers/bloques_listar.php', ['administrador','tutor','estudiante']],
    'bloque_crear'                        => ['controllers/bloque_crear.php', ['administrador']],
    'bloque_editar'                       => ['controllers/bloque_editar.php', ['administrador']],
    'bloque_eliminar'                     => ['controllers/bloque_eliminar.php', ['administrador']],
    
    // === SEGUIMIENTO DE SESIÓN ===
    'seguimientos_listar'                 => ['controllers/seguimientos_listar.php', ['administrador','tutor','estudiante']],
    'seguimiento_crear'                   => ['controllers/seguimiento_crear.php', ['administrador','tutor']],
    'seguimiento_editar'                  => ['controllers/seguimiento_editar.php', ['administrador','tutor']],
    'seguimiento_eliminar'                => ['controllers/seguimiento_eliminar.php', ['administrador','tutor']],
    
    // === EXPEDIENTES DE MODALIDADES DE GRADO ===
    'mg_expedientes_listar'              => ['controllers/mg_expedientes_listar.php', ['administrador','coordinador_mg','auxiliar_mg','tutor','estudiante']],
    'mg_expediente_crear'                 => ['controllers/mg_expediente_crear.php', ['administrador','coordinador_mg','auxiliar_mg']],
    'mg_expediente_ver'                   => ['controllers/mg_expediente_ver.php', ['administrador','coordinador_mg','auxiliar_mg','tutor','estudiante']],
    'mg_expediente_editar'                => ['controllers/mg_expediente_editar.php', ['administrador','coordinador_mg','auxiliar_mg']],
    'mg_expediente_eliminar'              => ['controllers/mg_expediente_eliminar.php', ['administrador','coordinador_mg']],
    
    // === MODALIDADES DE GRADO ===
    'mg_parametros'                       => ['controllers/mg_parametros_listar.php', ['administrador','coordinador_mg']],
    'mg_modalidades'                      => ['controllers/mg_modalidades_listar.php', ['administrador','coordinador_mg','auxiliar_mg']],
    'mg_cohortes'                         => ['controllers/mg_cohortes_listar.php', ['administrador','coordinador_mg','auxiliar_mg']],
    'mg_cohorte_crear'                    => ['controllers/mg_cohorte_crear.php', ['administrador','coordinador_mg']],
    'mg_cohorte_editar'                   => ['controllers/mg_cohorte_editar.php', ['administrador','coordinador_mg']],
    
    // === DEFENSAS, TRIBUNALES, REPORTES ===
    'mg_tribunales_listar'                => ['controllers/mg_tribunales_listar.php', ['administrador','coordinador_mg','auxiliar_mg','tutor','estudiante']],
    'mg_tribunal_crear'                   => ['controllers/mg_tribunal_crear.php', ['administrador','coordinador_mg']],
    'mg_tribunal_editar'                  => ['controllers/mg_tribunal_editar.php', ['administrador','coordinador_mg']],
    'mg_tribunal_eliminar'                => ['controllers/mg_tribunal_eliminar.php', ['administrador','coordinador_mg']],
    
    'mg_defensas_listar'                  => ['controllers/mg_defensas_listar.php', ['administrador','coordinador_mg','auxiliar_mg','tutor','estudiante']],
    'mg_defensa_crear'                    => ['controllers/mg_defensa_crear.php', ['administrador','coordinador_mg','auxiliar_mg']],
    'mg_defensa_editar'                   => ['controllers/mg_defensa_editar.php', ['administrador','coordinador_mg','auxiliar_mg']],
    'mg_defensa_eliminar'                 => ['controllers/mg_defensa_eliminar.php', ['administrador','coordinador_mg']],
    
    'mg_reporte_cohorte_listar'           => ['controllers/mg_reporte_cohorte_listar.php', ['administrador','coordinador_mg','auxiliar_mg']],
    
    // === REUNIONES DE SEGUIMIENTO ===
    'reuniones_listar'                    => ['controllers/reuniones_listar.php', ['administrador','tutor','estudiante']],
    'reunion_crear'                       => ['controllers/reunion_crear.php', ['administrador','tutor']],
    'reunion_editar'                      => ['controllers/reunion_editar.php', ['administrador','tutor']],
    'reunion_eliminar'                    => ['controllers/reunion_eliminar.php', ['administrador','tutor']],
    
    // === INFORMES DE AVANCE ===
    'informes_listar'                     => ['controllers/informes_listar.php', ['administrador','tutor','estudiante']],
    'informe_crear'                       => ['controllers/informe_crear.php', ['administrador','estudiante']],
    'informe_editar'                      => ['controllers/informe_editar.php', ['administrador','estudiante']],
    'informe_enviar'                      => ['controllers/informe_enviar.php', ['administrador','estudiante']],
    'informe_revisar'                     => ['controllers/informe_revisar.php', ['administrador','tutor']],
    'informe_eliminar'                    => ['controllers/informe_eliminar.php', ['administrador','estudiante']],
    
    // === ALERTAS DE SEGUIMIENTO ===
    'alertas_listar'                      => ['controllers/alertas_listar.php', ['administrador','tutor','estudiante']],
    'alerta_marcar_leida'                 => ['controllers/alerta_marcar_leida.php', ['administrador','tutor','estudiante']],
    
    // === DASHBOARD SEGUIMIENTO ===
    'dashboard_seguimiento'               => ['controllers/dashboard_seguimiento.php', ['administrador','coordinador_mg','tutor']]
];

// === PROCESAR RUTAS ===
if (isset($rutas_publicas[$accion])) {
    require_once $rutas_publicas[$accion];
    exit;
}

if (!estaAutenticado()) {
    header('Location: index.php?accion=login');
    exit;
}

if (isset($rutas_privadas[$accion])) {
    list($archivo, $roles_permitidos) = $rutas_privadas[$accion];
    
    if (!tieneRol($roles_permitidos)) {
        echo "<script>alert('No tienes permiso para acceder a esta sección');history.back();</script>";
        exit;
    }
    
    if (file_exists($archivo)) {
        require_once $archivo;
        exit;
    }
    
    http_response_code(404);
    die("<h2>Archivo no encontrado: $archivo</h2><a href='index.php'>Volver al inicio</a>");
}

// === PÁGINA PRINCIPAL ===
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
            background: linear-gradient(rgba(0, 38, 77, 0.85), rgba(0, 38, 77, 0.85)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            color: #fff;
        }
        .menu-lateral {
            width: 260px;
            background: rgba(0, 10, 26, 0.9);
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
            display: flex;
            align-items: center;
            justify-content: space-between;
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
        .menu-separador {
            color: #88b8e6;
            font-size: 12px;
            padding: 15px 15px 5px;
            margin-top: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .notif-item {
            background: <?= $total_sin_leer > 0 ? 'rgba(255, 153, 0, 0.2)' : 'transparent'; ?>;
            border-left: 3px solid <?= $total_sin_leer > 0 ? '#ff9900' : 'transparent'; ?>;
        }
        .alerta-item {
            background: <?= $alertas_seguimiento_sin_leer > 0 ? 'rgba(204, 0, 0, 0.15)' : 'transparent'; ?>;
            border-left: 3px solid <?= $alertas_seguimiento_sin_leer > 0 ? '#cc0000' : 'transparent'; ?>;
        }
        .notif-badge {
            background: #cc0000;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 12px;
            min-width: 22px;
            text-align: center;
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
        .cerrar-btn:hover { transform: scale(1.03); }
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
        .bienvenida h1 { font-size: 30px; margin-bottom: 10px; }
        .bienvenida p { font-size: 17px; color: #555; }
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
        .tarjeta.mg { border-top-color: #9933ff; }
        .tarjeta.mg:hover { border-top-color: #ffc107; }
        .tarjeta.sp5 { border-top-color: #28a745; }
        .tarjeta.sp5:hover { border-top-color: #ffc107; }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .menu-lateral { width: 100%; position: relative; height: auto; }
            .contenido { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    <aside class="menu-lateral">
        <div class="menu-header">
            <h2>🎓 Sistema de Tutorías</h2>
            <p>Gestión Académica</p>
        </div>
        <div class="usuario-info">
            <div class="usuario-nombre"><?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') ?></div>
            <span class="rol-etiqueta">
                <?php
                $etiquetas = [
                    'administrador' => '👑 Administrador',
                    'coordinador_mg' => '🎓 Coord. MG',
                    'auxiliar_mg' => '📋 Aux. MG',
                    'tutor' => '👨‍🏫 Tutor',
                    'estudiante' => '🎓 Estudiante'
                ];
                echo $etiquetas[$rol_actual] ?? htmlspecialchars($rol_actual);
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
                <a href="index.php?accion=seguimientos_listar">📝 Seguimiento Sesión</a>
                
                <div class="menu-separador">—— SEGUIMIENTO SPRINT 5 ——</div>
                <a href="index.php?accion=dashboard_seguimiento">📊 Dashboard Seguimiento</a>
                <a href="index.php?accion=reuniones_listar">📅 Reuniones</a>
                <a href="index.php?accion=informes_listar">📄 Informes de Avance</a>
                <a href="index.php?accion=alertas_listar" class="alerta-item">
                    ⚠️ Alertas
                    <?php if ($alertas_seguimiento_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $alertas_seguimiento_sin_leer ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="menu-separador">—— MODALIDADES DE GRADO ——</div>
                <a href="index.php?accion=mg_parametros">⚙️ Parámetros MG</a>
                <a href="index.php?accion=mg_modalidades">📋 Modalidades</a>
                <a href="index.php?accion=mg_cohortes">📅 Cohortes</a>
                <a href="index.php?accion=mg_expedientes_listar">👥 Expedientes</a>
                
                <div class="menu-separador">—— DEFENSAS ——</div>
                <a href="index.php?accion=mg_tribunales_listar">⚖️ Tribunales / Jurados</a>
                <a href="index.php?accion=mg_defensas_listar">📅 Programación Defensas</a>
                <a href="index.php?accion=mg_reporte_cohorte_listar">📊 Reporte por Cohorte</a>
                
                <a href="index.php?accion=notificaciones_listar" class="notif-item">
                    🔔 Notificaciones
                    <?php if ($total_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $total_sin_leer ?></span>
                    <?php endif; ?>
                </a>
                <a href="index.php?accion=accesos_listar">📋 Accesos</a>
            <?php endif; ?>
            
            <?php if ($rol_actual === 'coordinador_mg'): ?>
                <a href="index.php?accion=mg_parametros">⚙️ Parámetros</a>
                <a href="index.php?accion=mg_modalidades">📋 Modalidades</a>
                <a href="index.php?accion=mg_cohortes">📅 Cohortes</a>
                <a href="index.php?accion=mg_expedientes_listar">👥 Expedientes</a>
                
                <div class="menu-separador">—— SEGUIMIENTO ——</div>
                <a href="index.php?accion=dashboard_seguimiento">📊 Dashboard Seguimiento</a>
                <a href="index.php?accion=reuniones_listar">📅 Reuniones</a>
                <a href="index.php?accion=informes_listar">📄 Informes</a>
                <a href="index.php?accion=alertas_listar" class="alerta-item">
                    ⚠️ Alertas
                    <?php if ($alertas_seguimiento_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $alertas_seguimiento_sin_leer ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="menu-separador">—— DEFENSAS ——</div>
                <a href="index.php?accion=mg_tribunales_listar">⚖️ Tribunales</a>
                <a href="index.php?accion=mg_defensas_listar">📅 Defensas Programadas</a>
                <a href="index.php?accion=mg_reporte_cohorte_listar">📊 Reporte Cohorte</a>
                
                <a href="index.php?accion=notificaciones_listar" class="notif-item">
                    🔔 Notificaciones
                    <?php if ($total_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $total_sin_leer ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            
            <?php if ($rol_actual === 'auxiliar_mg'): ?>
                <a href="index.php?accion=mg_modalidades">📋 Modalidades</a>
                <a href="index.php?accion=mg_cohortes">📅 Cohortes</a>
                <a href="index.php?accion=mg_expedientes_listar">👥 Expedientes</a>
                
                <div class="menu-separador">—— DEFENSAS ——</div>
                <a href="index.php?accion=mg_defensas_listar">📅 Defensas</a>
                <a href="index.php?accion=mg_reporte_cohorte_listar">📊 Reporte</a>
                
                <a href="index.php?accion=notificaciones_listar" class="notif-item">
                    🔔 Notificaciones
                    <?php if ($total_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $total_sin_leer ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            
            <?php if ($rol_actual === 'tutor'): ?>
                <a href="index.php?accion=carreras_listar">🎓 Carreras</a>
                <a href="index.php?accion=materias_listar">📚 Materias</a>
                <a href="index.php?accion=tutores_listar">👤 Mi Perfil</a>
                <a href="index.php?accion=disponibilidad_listar">📅 Mi Disponibilidad</a>
                <a href="index.php?accion=tutorias_listar">📝 Mis Tutorías</a>
                <a href="index.php?accion=seguimientos_listar">📝 Seguimiento Sesión</a>
                <a href="index.php?accion=estudiantes_listar">🎓 Estudiantes</a>
                
                <div class="menu-separador">—— SEGUIMIENTO ——</div>
                <a href="index.php?accion=dashboard_seguimiento">📊 Mi Avance</a>
                <a href="index.php?accion=reuniones_listar">📅 Reuniones</a>
                <a href="index.php?accion=informes_listar">📄 Informes</a>
                <a href="index.php?accion=alertas_listar" class="alerta-item">
                    ⚠️ Alertas
                    <?php if ($alertas_seguimiento_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $alertas_seguimiento_sin_leer ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="menu-separador">—— MODALIDADES DE GRADO ——</div>
                <a href="index.php?accion=mg_expedientes_listar">🎓 Expedientes MG</a>
                <a href="index.php?accion=mg_defensas_listar">📅 Defensas Programadas</a>
                
                <a href="index.php?accion=notificaciones_listar" class="notif-item">
                    🔔 Notificaciones
                    <?php if ($total_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $total_sin_leer ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            
            <?php if ($rol_actual === 'estudiante'): ?>
                <a href="index.php?accion=carreras_listar">🎓 Carreras</a>
                <a href="index.php?accion=tutores_listar">👨‍🏫 Tutores</a>
                <a href="index.php?accion=tutorias_listar">📝 Mis Tutorías</a>
                <a href="index.php?accion=evaluaciones_listar">⭐ Evaluaciones</a>
                <a href="index.php?accion=seguimientos_listar">📝 Mi Seguimiento</a>
                
                <div class="menu-separador">—— SEGUIMIENTO ——</div>
                <a href="index.php?accion=reuniones_listar">📅 Mis Reuniones</a>
                <a href="index.php?accion=informes_listar">📄 Mis Informes</a>
                <a href="index.php?accion=alertas_listar" class="alerta-item">
                    ⚠️ Alertas
                    <?php if ($alertas_seguimiento_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $alertas_seguimiento_sin_leer ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="menu-separador">—— MODALIDADES DE GRADO ——</div>
                <a href="index.php?accion=mg_expedientes_listar">🎓 Mi Expediente</a>
                <a href="index.php?accion=mg_defensas_listar">📅 Mi Defensa</a>
                
                <a href="index.php?accion=notificaciones_listar" class="notif-item">
                    🔔 Notificaciones
                    <?php if ($total_sin_leer > 0): ?>
                        <span class="notif-badge"><?= $total_sin_leer ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </nav>
        <div class="cerrar-sesion">
            <a href="index.php?accion=cerrar_sesion" class="cerrar-btn">🚪 Cerrar Sesión</a>
        </div>
    </aside>
    <main class="contenido">
        <div class="bienvenida">
            <h1>¡Bienvenido(a), <?= htmlspecialchars(explode(' ', $_SESSION['nombre_completo'] ?? 'Usuario')[0]) ?>!</h1>
            <p>Seleccione una opción del menú lateral para comenzar.</p>
        </div>
        <div class="tarjetas-contenedor">
            <?php if ($rol_actual === 'administrador'): ?>
                <a href="index.php?accion=listar" class="tarjeta">Roles</a>
                <a href="index.php?accion=usuarios_listar" class="tarjeta">Usuarios</a>
                <a href="index.php?accion=carreras_listar" class="tarjeta">Carreras</a>
                <a href="index.php?accion=materias_listar" class="tarjeta">Materias</a>
                <a href="index.php?accion=tutores_listar" class="tarjeta">Tutores</a>
                <a href="index.php?accion=estudiantes_listar" class="tarjeta">Estudiantes</a>
                <a href="index.php?accion=tutorias_listar" class="tarjeta">Tutorías</a>
                <a href="index.php?accion=dashboard_seguimiento" class="tarjeta sp5">📊 Dashboard Seguimiento</a>
                <a href="index.php?accion=mg_expedientes_listar" class="tarjeta mg">Expedientes MG</a>
                <a href="index.php?accion=mg_tribunales_listar" class="tarjeta mg">Tribunales</a>
                <a href="index.php?accion=mg_defensas_listar" class="tarjeta mg">Defensas</a>
                <a href="index.php?accion=mg_reporte_cohorte_listar" class="tarjeta mg">Reportes Cohorte</a>
            <?php endif; ?>
            
            <?php if ($rol_actual === 'estudiante'): ?>
                <a href="index.php?accion=tutorias_listar" class="tarjeta">Mis Tutorías</a>
                <a href="index.php?accion=reuniones_listar" class="tarjeta sp5">📅 Mis Reuniones</a>
                <a href="index.php?accion=informes_listar" class="tarjeta sp5">📄 Mis Informes</a>
                <a href="index.php?accion=evaluaciones_listar" class="tarjeta">Evaluaciones</a>
                <a href="index.php?accion=mg_expedientes_listar" class="tarjeta mg">Mi Expediente</a>
                <a href="index.php?accion=mg_defensas_listar" class="tarjeta mg">Mi Defensa</a>
            <?php endif; ?>
            
            <?php if ($rol_actual === 'tutor'): ?>
                <a href="index.php?accion=tutorias_listar" class="tarjeta">Mis Tutorías</a>
                <a href="index.php?accion=dashboard_seguimiento" class="tarjeta sp5">📊 Mi Avance</a>
                <a href="index.php?accion=reuniones_listar" class="tarjeta sp5">📅 Reuniones</a>
                <a href="index.php?accion=mg_expedientes_listar" class="tarjeta mg">Expedientes MG</a>
                <a href="index.php?accion=mg_defensas_listar" class="tarjeta mg">Defensas</a>
            <?php endif; ?>
            
            <?php if ($rol_actual === 'coordinador_mg' || $rol_actual === 'auxiliar_mg'): ?>
                <a href="index.php?accion=dashboard_seguimiento" class="tarjeta sp5">📊 Dashboard Seguimiento</a>
                <a href="index.php?accion=mg_expedientes_listar" class="tarjeta mg">Expedientes</a>
                <a href="index.php?accion=mg_defensas_listar" class="tarjeta mg">Defensas</a>
                <a href="index.php?accion=mg_reporte_cohorte_listar" class="tarjeta mg">Reportes</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>