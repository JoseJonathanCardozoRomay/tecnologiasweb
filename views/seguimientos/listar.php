<?php
if (!isset($seguimientos)) $seguimientos = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
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
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
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
        tr:nth-child(even) { background: #f0f5ff; }
        tr:hover { background: #e6f0ff; }
        .editar {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 8px;
        }
        .editar:hover { background: #218838; }
        .vacio {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .avance-sin { color: #c53030; font-weight: bold; }
        .avance-parcial { color: #d69e2e; font-weight: bold; }
        .avance-logrado { color: #2f855a; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al inicio</a>
        
        <h1>📋 Seguimiento de Sesiones</h1>
        <?php if ($rol_actual === 'administrador' || $rol_actual === 'tutor'): ?>
            <a href="index.php?accion=seguimiento_crear" class="btn-nuevo">+ Nuevo Seguimiento</a>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Tutoría</th>
                    <th>Estudiante</th>
                    <th>¿Asistió?</th>
                    <th>Avance</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($seguimientos)): ?>
                    <tr>
                        <td colspan="6" class="vacio">
                            No hay seguimientos registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($seguimientos as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['id_tutoria'] ?? '') ?></td>
                        <!-- ✅ CORREGIDO: El campo se llama estudiante_nombre -->
                        <td><strong><?= htmlspecialchars($s['estudiante_nombre'] ?? 'Sin nombre') ?></strong></td>
                        <td><?= htmlspecialchars(($s['asistio'] ?? '') === 'si' ? '✅ Sí' : '❌ No') ?></td>
                        <td class="avance-<?= htmlspecialchars($s['avance'] ?? 'sin_avance') ?>">
                            <?php 
                            $avances = [
                                'sin_avance' => 'Sin avance',
                                'parcial' => 'Avance parcial',
                                'logrado' => 'Objetivo logrado'
                            ];
                            echo htmlspecialchars($avances[$s['avance'] ?? 'sin_avance'] ?? 'Sin avance');
                            ?>
                        </td>
                        <td><?= htmlspecialchars($s['fecha_registro'] ?? ($s['fecha'] ?? '')) ?></td>
                        <td>
                            <?php if ($rol_actual === 'administrador' || $rol_actual === 'tutor'): ?>
                                <a href="index.php?accion=seguimiento_editar&id=<?= (int)($s['id_seguimiento'] ?? 0) ?>" class="editar">Editar</a>
                            <?php else: ?>
                                <em>Solo lectura</em>
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