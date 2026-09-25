<?php
if (!isset($seguimientos)) $seguimientos = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
if (!isset($id_usuario_actual)) $id_usuario_actual = $_SESSION['id_usuario'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Sesiones</title>
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
            background: #ffffff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
        }
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-nuevo {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #004080;
            color: white;
            padding: 14px 10px;
            text-align: left;
        }
        td {
            padding: 14px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover { background: #f0f7ff; }
        .avance {
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        .sin_avance { background: #e9ecef; color: #495057; }
        .parcial { background: #ffc107; color: #000; }
        .logrado { background: #28a745; color: white; }
        .si { color: #28a745; font-weight: bold; }
        .no { color: #dc3545; font-weight: bold; }
        .acciones a {
            margin-right: 10px;
            text-decoration: none;
            font-weight: bold;
        }
        .editar { color: #0066cc; }
        .eliminar { color: #dc3545; }
        .vacio {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al inicio</a>

        <h1>📋 Seguimiento de Sesiones</h1>

        <?php if ($rol_actual === 'tutor' || $rol_actual === 'administrador'): ?>
            <a href="index.php?accion=seguimiento_crear" class="btn-nuevo">+ Nuevo Seguimiento</a>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tutoría</th>
                    <th>Asistió</th>
                    <th>Avance</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($seguimientos)): ?>
                    <tr>
                        <td colspan="6" class="vacio">
                            No hay registros de seguimiento.
                            <?php if ($rol_actual === 'tutor'): ?>
                                <br><strong>Registra el seguimiento de tus sesiones con el botón "Nuevo Seguimiento".</strong>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($seguimientos as $fila): 
                        $puede_editar = false;
                        if ($rol_actual === 'administrador') {
                            $puede_editar = true;
                        } elseif ($rol_actual === 'tutor') {
                            $puede_editar = ($fila['id_usuario_tutor'] ?? 0) == $id_usuario_actual;
                        }

                        $avance_clase = $fila['avance'] ?? 'sin_avance';
                        $asistio_texto = ($fila['asistio'] ?? '') === 'si' ? 'Sí' : 'No';
                        $asistio_clase = ($fila['asistio'] ?? '') === 'si' ? 'si' : 'no';
                    ?>
                    <tr>
                        <td><?= $fila['id_seguimiento'] ?></td>
                        <td>
                            ID <?= $fila['id_tutoria'] ?> — <?= date('Y-m-d', strtotime($fila['fecha'])) ?>
                        </td>
                        <td class="<?= $asistio_clase ?>"><?= $asistio_texto ?></td>
                        <td>
                            <span class="avance <?= $avance_clase ?>">
                                <?= ucfirst(str_replace('_', ' ', $fila['avance'] ?? 'Sin avance')) ?>
                            </span>
                        </td>
                        <td><?= date('Y-m-d H:i', strtotime($fila['fecha_registro'])) ?></td>
                        <td class="acciones">
                            <?php if ($puede_editar): ?>
                                <a href="index.php?accion=seguimiento_editar&id=<?= $fila['id_seguimiento'] ?>" class="editar">Editar</a>
                                <a href="index.php?accion=seguimiento_eliminar&id=<?= $fila['id_seguimiento'] ?>" 
                                   class="eliminar"
                                   onclick="return confirm('¿Eliminar este registro?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>