<?php
if (!isset($titulo_pagina)) $titulo_pagina = 'Sistema de Tutorías';
if (!isset($contenido)) $contenido = '';
$rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo_pagina) ?> — Sistema de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', 'Times New Roman', serif;
            background: linear-gradient(rgba(0, 26, 66, 0.85), rgba(0, 45, 90, 0.85)),
                        url('https://campushome.es/wp-content/uploads/2020/07/students.jpeg') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            color: #a7a1a1;
        }
        .menu-lateral {
            width: 260px;
            background: rgba(0, 15, 40, 0.9);
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
            background: linear-gradient(180deg, #003366 0%, #001a33 100%);
            border-bottom: 2px solid #ffd700;
        }
        .menu-header h2 { font-size: 18px; text-align: center; color: #ffd700; }
        .menu-header p { font-size: 12px; text-align: center; color: #99ccff; margin-top: 5px; }
        .usuario-info { padding: 18px 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .usuario-nombre { font-weight: bold; font-size: 14px; margin-bottom: 8px; }
        .rol-etiqueta {
            display: inline-block;
            background: linear-gradient(90deg, #ffd700, #ffaa00);
            color: #000;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .menu-navegacion { padding: 20px 12px; flex: 1; }
        .menu-navegacion a {
            display: block;
            color: #e9ecf3;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 6px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        .menu-navegacion a:hover {
            background: rgba(255, 215, 0, 0.15);
            border-left-color: #ffd700;
            padding-left: 20px;
        }
        .cerrar-sesion { padding: 20px 15px 30px; border-top: 1px solid rgba(255,255,255,0.1); }
        .cerrar-btn {
            display: block;
            background: linear-gradient(90deg, #c0392b, #e74c3c);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .cerrar-btn:hover { transform: scale(1.03); }
        .contenido { flex: 1; margin-left: 260px; padding: 40px; }
        .tarjeta-contenido {
            background: rgba(192, 243, 217, 0.96);
            color: #003366;
            padding: 35px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-left: 6px solid #ffd700;
        }
        .tarjeta-contenido h1, .tarjeta-contenido h2 {
            color: #003366;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e6e6e6;
        }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #003366; color: white; font-weight: 600; }
        table tr:hover { background: #f0f5fa; }
        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primario { background: #0066cc; color: white; }
        .btn-primario:hover { background: #0052a3; }
        .btn-exito { background: #27ae60; color: white; }
        .btn-exito:hover { background: #219653; }
        .btn-peligro { background: #c0392b; color: white; }
        .btn-peligro:hover { background: #a93226; }
        .btn-volver { background: #7f8c8d; color: white; }
        .btn-volver:hover { background: #6c7a7b; }
        form label { display: block; margin: 15px 0 5px; font-weight: 600; color: #003366; }
        form input, form select, form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        form input:focus, form select:focus, form textarea:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0,102,204,0.15);
        }
        .alerta { padding: 15px 20px; border-radius: 8px; margin: 20px 0; }
        .alerta-exito { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alerta-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
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
                <a href="index.php?accion=bloques_listar">🕐 Bloques</a>
                <a href="index.php?accion=periodos_listar">📅 Periodos</a>
                <a href="index.php?accion=tutorias_listar">📝 Tutorías</a>
                <a href="index.php?accion=evaluaciones_listar">⭐ Evaluaciones</a>
                <a href="index.php?accion=seguimientos_listar">📝 Seguimiento</a>
                <a href="index.php?accion=notificaciones_listar">🔔 Notificaciones</a>
                <a href="index.php?accion=accesos_listar">🔒 Accesos</a>
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
    <main class="contenido">
        <div class="tarjeta-contenido"><?= $contenido ?></div>
    </main>
</body>
</html>