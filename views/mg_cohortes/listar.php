<?php
if (!isset($cohortes)) $cohortes = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cohortes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 900px; margin: 0 auto; }
        .tarjeta {
            background: white; border-radius: 12px; padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2); color: #003366;
        }
        h1 { text-align: center; margin-bottom: 20px; }
        .btn-atras {
            display: inline-block; margin-bottom: 20px;
            color: #0066cc; text-decoration: none; font-weight: bold;
        }
        .btn-nuevo {
            display: inline-block; background: #28a745; color: white; padding: 10px 20px;
            border-radius: 8px; text-decoration: none; font-weight: bold; margin-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #003366; color: white; }
        .btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .editar { background: #ffc107; color: #000; margin-right: 5px; }
        .eliminar { background: #dc3545; color: white; }
        .etiqueta { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .activa { background: #d4edda; color: #155724; }
        .inactiva { background: #e2e3e5; color: #555; }
        .vacio { text-align: center; padding: 30px; color: #666; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="btn-atras">← Volver al Inicio</a>
        <div class="tarjeta">
            <h1>📅 Cohortes de Ingreso</h1>
            
            <?php if (tieneRol(['administrador','coordinador_mg'])): ?>
            <a href="index.php?accion=mg_cohorte_crear" class="btn-nuevo">+ Nueva Cohorte</a>
            <?php endif; ?>
            
            <?php if (empty($cohortes)): ?>
            <p class="vacio">No hay cohortes registradas aún.</p>
            <?php else: ?>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($cohortes as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['codigo']) ?></strong></td>
                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                    <td><?= htmlspecialchars($c['fecha_inicio']) ?></td>
                    <td><?= !empty($c['fecha_fin']) ? htmlspecialchars($c['fecha_fin']) : '—' ?></td>
                    <td>
                        <span class="etiqueta <?= $c['activo'] ? 'activa' : 'inactiva' ?>">
                            <?= $c['activo'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </td>
                    <td>
                        <?php if (tieneRol(['administrador','coordinador_mg'])): ?>
                        <a href="index.php?accion=mg_cohorte_editar&id=<?= $c['id_cohorte'] ?>" class="btn editar">Editar</a>
                        <a href="index.php?accion=mg_cohorte_eliminar&id=<?= $c['id_cohorte'] ?>" class="btn eliminar" onclick="return confirm('¿Eliminar esta cohorte?')">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>