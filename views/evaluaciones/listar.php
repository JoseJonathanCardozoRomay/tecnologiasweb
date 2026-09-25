<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluaciones de Tutorías</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 900px;
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
        .mensaje {
            background: #d1e7dd;
            color: #0f5132;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .info-rol {
            background: #e7f3ff;
            color: #004085;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 13px;
        }
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
        .nota {
            font-weight: bold;
            font-size: 18px;
        }
        .nota-5 { color: #28a745; }
        .nota-4 { color: #28a745; }
        .nota-3 { color: #ffc107; }
        .nota-2 { color: #fd7e14; }
        .nota-1 { color: #dc3545; }
        .btn-eliminar {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .vacio {
            text-align: center;
            color: #666;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver</a>
        
        <h1>📋 Evaluaciones de Tutorías</h1>

        <div class="info-rol">
            👤 Tú ves: 
            <strong>
                <?php 
                $r = $_SESSION['usuario']['nombre_rol'] ?? '';
                echo match($r) {
                    'administrador' => 'TODAS las evaluaciones del sistema',
                    'tutor' => 'Las evaluaciones que te dejaron a ti',
                    'estudiante' => 'Tus propias evaluaciones enviadas',
                    default => ''
                };
                ?>
            </strong>
        </div>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'gracias'): ?>
            <div class="mensaje">✅ ¡Gracias por tu evaluación!</div>
        <?php endif; ?>

        <?php if (empty($evaluaciones)): ?>
            <p class="vacio">No hay evaluaciones registradas.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Materia / Tutoría</th>
                    <th>Calificación</th>
                    <th>Comentario</th>
                    <?php if (($usuario_actual['nombre_rol'] ?? '') === 'administrador'): ?>
                        <th>Acción</th>
                    <?php endif; ?>
                </tr>
                <?php foreach ($evaluaciones as $e): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($e['fecha_evaluacion'])) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($e['nombre_materia'] ?? '') ?></strong><br>
                            <small>
                                <?php 
                                $r = $_SESSION['usuario']['nombre_rol'] ?? '';
                                if ($r === 'estudiante') {
                                    echo 'Tutor: ' . htmlspecialchars(($e['tut_nombre'] ?? '') . ' ' . ($e['tut_apellido'] ?? ''));
                                } else {
                                    echo 'Estudiante: ' . htmlspecialchars(($e['est_nombre'] ?? '') . ' ' . ($e['est_apellido'] ?? ''));
                                }
                                ?>
                            </small>
                        </td>
                        <td class="nota nota-<?= $e['calificacion'] ?>">
                            <?= $e['calificacion'] ?> ⭐
                        </td>
                        <td style="max-width: 250px;"><?= htmlspecialchars($e['comentario'] ?? '-') ?></td>
                        <?php if (($usuario_actual['nombre_rol'] ?? '') === 'administrador'): ?>
                            <td>
                                <a href="index.php?accion=evaluacion_eliminar&id=<?= $e['id_evaluacion'] ?>" 
                                   class="btn-eliminar"
                                   onclick="return confirm('¿Eliminar esta evaluación?')">Eliminar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>