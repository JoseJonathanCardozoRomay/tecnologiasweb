<?php
if (!isset($periodos)) $periodos = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
$titulo_pagina = 'Periodos de Tutoría';
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periodos de Tutoría</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px 20px;
        }
        .contenedor {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .cabecera {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
        }
        h1 {
            color: #003366;
            font-size: 22px;
        }
        .btn-volver {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-nuevo {
            background: #0066cc;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        .btn-nuevo:hover { background: #0052a3; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #003366;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .estado-activo {
            background: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        .estado-inactivo {
            background: #f8d7da;
            color: #721c24;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        .btn-editar {
            background: #ffc107;
            color: #000;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
        }
        .btn-eliminar {
            background: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
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
        <div class="cabecera">
            <a href="index.php" class="btn-volver">← Volver al inicio</a>
            <h1>📅 Periodos de Tutoría</h1>
            <div></div>
        </div>

        <?php if ($rol_actual === 'administrador'): ?>
        <a href="index.php?accion=periodo_crear" class="btn-nuevo">+ Nuevo Periodo</a>
        <?php endif; ?>

        <?php if (empty($periodos)): ?>
            <p class="vacio">No hay periodos registrados.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <?php if ($rol_actual === 'administrador'): ?><th>Acciones</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($periodos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['codigo']) ?></td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></td>
                    <td>
                        <?php if ($p['activo']): ?>
                            <span class="estado-activo">Activo</span>
                        <?php else: ?>
                            <span class="estado-inactivo">Cerrado</span>
                        <?php endif; ?>
                    </td>
                    <?php if ($rol_actual === 'administrador'): ?>
                    <td>
                        <a href="index.php?accion=periodo_editar&id=<?= $p['id_periodo'] ?>" class="btn-editar">Editar</a>
                        <a href="index.php?accion=periodo_eliminar&id=<?= $p['id_periodo'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este periodo?')">Eliminar</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';