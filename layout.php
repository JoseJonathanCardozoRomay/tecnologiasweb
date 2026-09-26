<?php
if (!isset($titulo_pagina)) $titulo_pagina = 'Sistema de Gestión de Tutorías';
if (!isset($contenido)) $contenido = '';
if (!isset($total_sin_leer)) $total_sin_leer = 0;
$rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo_pagina) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', 'Times New Roman', Georgia, serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            color: #333;
        }
        .contenedor-principal {
            display: flex;
            min-height: 100vh;
        }
        .menu-lateral {
            width: 260px;
            background: rgba(0, 26, 51, 0.95);
            padding: 0;
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .menu-header {
            padding: 20px;
            background: linear-gradient(180deg, #004080 0%, #00264d 100%);
            border-bottom: 2px solid #ffc107;
        }
        .menu-header h2 {
            font-size: 18px;
            text-align: center;
            color: #ffd700;
            margin-bottom: 5px;
        }
        .menu-lateral a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: #e6f0ff;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .menu-lateral a:hover {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #ffc107;
            padding-left: 25px;
        }
        .contenido-pagina {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }
        .tarjeta {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            color: #003366;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        .badge {
            display: inline-block;
            background: #d32f2f;
            color: #fff;
            font-size: 0.75em;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }
        .menu-separador {
            color: #88b8e6;
            font-size: 12px;
            padding: 15px 20px 5px;
            margin-top: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .notif-item {
            background: <?= $total_sin_leer > 0 ? 'rgba(255, 153, 0, 0.2)' : 'transparent'; ?>;
            border-left: 3px solid <?= $total_sin_leer > 0 ? '#ff9900' : 'transparent'; ?>;
        }
        .rol-etiqueta {
            display: inline-block;
            background: linear-gradient(90deg, #ffc107, #ff9800);
            color: #000;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="contenedor-principal">
        <!-- Menú Lateral -->
        <nav class="menu-lateral">
            <div class="menu-header">
                <h2>📘 Sistema de Tutorías</h2>
            </div>
            
            <?php if (isset($_SESSION['rol_nombre'])): ?>
                <p style="padding: 15px 20px; font-size: 0.9em; opacity: 0.8;">
                    Bienvenido,<br>
                    <strong><?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') ?></strong><br>
                    <span class="rol-etiqueta">
                        <?php
                        $etiquetas = [
                            'administrador' => '👑 Administrador',
                            'coordinador_mg' => '🎓 Coord. MG',
                            'auxiliar_mg' => '📋 Aux. MG',
                            'tutor' => '👨‍🏫 Tutor',
                            'estudiante' => '🎓 Estudiante'
                        ];
                        echo $etiquetas[$rol_actual] ?? $rol_actual;
                        ?>
                    </span>
                </p>
                <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin:10px 0;">
                
                <?php if ($rol_actual === 'administrador'): ?>
                    <a href="index.php?accion=listar">📋 Roles</a>
                    <a href="index.php?accion=usuarios_listar">👤 Usuarios</a>
                    <a href="index.php?accion=carreras_listar">🎓 Carreras</a>
                    <a href="index.php?accion=materias_listar">📚 Materias</a>
                    <a href="index.php?accion=mg_tribunales_listar">⚖️ Tribunales</a>
                    <a href="index.php?accion=mg_defensas_listar">📅 Defensas</a>
                    <a href="index.php?accion=mg_reporte_cohorte_listar">📊 Reportes Cohorte</a>
                    <a href="index.php?accion=accesos_listar">📋 Accesos</a>
                <?php endif; ?>
                
                <?php if ($rol_actual === 'coordinador_mg' || $rol_actual === 'auxiliar_mg'): ?>
                    <a href="index.php?accion=mg_tribunales_listar">⚖️ Tribunales</a>
                    <a href="index.php?accion=mg_defensas_listar">📅 Defensas</a>
                    <a href="index.php?accion=mg_reporte_cohorte_listar">📊 Reportes</a>
                <?php endif; ?>
                
                <?php if ($rol_actual === 'tutor'): ?>
                    <a href="index.php?accion=mg_defensas_listar">📅 Mis Defensas</a>
                <?php endif; ?>
                
                <?php if ($rol_actual === 'estudiante'): ?>
                    <a href="index.php?accion=mg_defensas_listar">📅 Mi Defensa</a>
                <?php endif; ?>
                
                <?php if ($total_sin_leer > 0): ?>
                    <a href="index.php?accion=notificaciones_listar" class="notif-item">
                        🔔 Notificaciones
                        <span class="badge"><?= $total_sin_leer ?></span>
                    </a>
                <?php else: ?>
                    <a href="index.php?accion=notificaciones_listar">🔔 Notificaciones</a>
                <?php endif; ?>
                
                <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin:10px 0;">
                <a href="index.php?accion=cerrar_sesion" style="color:#ffcccb;">🚪 Cerrar Sesión</a>
            <?php else: ?>
                <a href="index.php?accion=login">🔐 Iniciar Sesión</a>
            <?php endif; ?>
        </nav>
        
        <!-- Contenido de la página -->
        <main class="contenido-pagina">
            <div class="tarjeta">
                <?= $contenido ?>
            </div>
        </main>
    </div>
</body>
</html>