<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 15px;
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffc107;
            font-size: 22px;
        }
        .btn-nuevo {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            float: right;
        }
        .btn-nuevo:hover { background: #0052a3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #003366;
            color: white;
        }
        .estado {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
        }
        .pendiente { background: #fff3cd; color: #856404; }
        .confirmada { background: #d1e7dd; color: #0f5132; }
        .realizada { background: #cfe2ff; color: #084298; }
        .cancelada { background: #f8d7da; color: #842029; }
        .btn-editar {
            color: #0066cc;
            text-decoration: none;
            margin-right: 8px;
            font-weight: bold;
        }
        .btn-eliminar {
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-evaluar {
            display: inline-block;
            background: #ffc107;
            color: #000 !important;
            padding: 6px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            margin-left: 5px;
        }
        .vacio {
            text-align: center;
            color: #666;
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al Menú</a>
        
        <h1>📋 Listado de Tutorías</h1>

        <?php 
        // ✅ Leer rol compatible con tu sesión
        $rol_actual = $_SESSION['rol_nombre'] ?? $_SESSION['usuario']['nombre_rol'] ?? '';
        
        // Botón Nueva Tutoría — Solo Administrador y Estudiante
        if ($rol_actual === 'administrador' || $rol_actual === 'estudiante'): 
        ?>
            <a href="index.php?accion=tutoria_crear" class="btn-nuevo">+ Nueva Tutoría</a>
        <?php endif; ?>

        <div style="clear: both;"></div>

        <?php if (empty($tutorias)): ?>
            <p class="vacio">No hay tutorías registradas.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Tutor</th>
                    <th>Materia</th>
                    <th>Fecha / Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($tutorias as $t): 
                    $id_tutoria = $t['id_tutoria'];
                    $estado = $t['estado'] ?? '';
                    
                    // Verificar si ya fue evaluada
                    $ya_evaluada = false;
                    if ($estado === 'realizada') {
                        global $conexion;
                        $stmt = $conexion->prepare("SELECT 1 FROM evaluaciones_tutoria WHERE id_tutoria = ? LIMIT 1");
                        $stmt->execute([$id_tutoria]);
                        $ya_evaluada = $stmt->rowCount() > 0;
                    }
                ?>
                    <tr>
                        <td><?= $id_tutoria ?></td>
                        <!-- ✅ NOMBRES CORRECTOS según el modelo actualizado -->
                        <td><?= htmlspecialchars(($t['nombre_estudiante'] ?? '') . ' ' . ($t['apellido_estudiante'] ?? '')) ?></td>
                        <td><?= htmlspecialchars(($t['nombre_tutor'] ?? '') . ' ' . ($t['apellido_tutor'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($t['nombre_materia'] ?? '') ?></td>
                        <td>
                            <?= !empty($t['fecha']) ? date('d/m/Y', strtotime($t['fecha'])) : '' ?><br>
                            <small>
                                <?= isset($t['hora_inicio']) ? substr($t['hora_inicio'], 0, 5) : '' ?> - 
                                <?= isset($t['hora_fin']) ? substr($t['hora_fin'], 0, 5) : '' ?>
                            </small>
                        </td>
                        <td>
                            <span class="estado <?= $estado ?>">
                                <?= ucfirst($estado) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            // ✅ ESTUDIANTE: Solo Evaluar
                            if ($rol_actual === 'estudiante') {
                                if ($estado === 'realizada' && !$ya_evaluada) {
                                    echo '<a href="index.php?accion=evaluacion_crear&id_tutoria='.$id_tutoria.'" class="btn-evaluar">⭐ Evaluar</a>';
                                } elseif ($estado === 'realizada' && $ya_evaluada) {
                                    echo '<span style="color:#28a745; font-weight:bold;">✅ Evaluada</span>';
                                }
                            }

                            // ✅ TUTOR: Cambiar Estado
                            if ($rol_actual === 'tutor') {
                                if ($estado === 'pendiente' || $estado === 'confirmada') {
                                    echo '<a href="index.php?accion=tutoria_editar&id='.$id_tutoria.'" class="btn-editar">Cambiar Estado</a>';
                                }
                            }

                            // ✅ ADMINISTRADOR: Editar + Eliminar
                            if ($rol_actual === 'administrador') {
                                echo '<a href="index.php?accion=tutoria_editar&id='.$id_tutoria.'" class="btn-editar">Editar</a>';
                                echo '<a href="index.php?accion=tutoria_eliminar&id='.$id_tutoria.'" class="btn-eliminar" onclick="return confirm(\'¿Eliminar esta tutoría?\')">Eliminar</a>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>