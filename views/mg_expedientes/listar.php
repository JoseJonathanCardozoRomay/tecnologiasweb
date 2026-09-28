<?php
if (!isset($expedientes)) $expedientes = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expedientes — Modalidades de Grado</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Roboto, sans-serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 1100px; margin: 0 auto; }
        .btn-volver {
            display: inline-block; background: rgba(255,255,255,0.2); color: white;
            padding: 10px 20px; border-radius: 8px; text-decoration: none;
            font-weight: bold; margin-bottom: 20px; transition: all 0.2s ease;
        }
        .btn-volver:hover { background: rgba(255,255,255,0.3); }
        
        .tarjeta {
            background: white; border-radius: 14px; padding: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        .encabezado {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 25px; flex-wrap: wrap; gap: 15px;
        }
        h1 { font-size: 22px; color: #003366; }
        
        .btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px;
            border-radius: 8px; text-decoration: none; font-weight: 600;
            font-size: 14px; border: none; cursor: pointer;
            transition: all 0.25s ease;
        }
        .btn-primario { background: #0066cc; color: white; }
        .btn-primario:hover { background: #0052a3; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,102,204,0.3); }
        
        /* Botones de Acción — Diseño Mejorado */
        .acciones { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-ver { background: #e3f2fd; color: #0d47a1; }
        .btn-ver:hover { background: #bbdefb; transform: scale(1.05); }
        .btn-editar { background: #fff3e0; color: #e65100; }
        .btn-editar:hover { background: #ffe0b2; transform: scale(1.05); }
        .btn-eliminar { background: #ffebee; color: #c62828; }
        .btn-eliminar:hover { background: #ffcdd2; transform: scale(1.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 16px; text-align: left; border-bottom: 1px solid #f0f2f5; }
        th { background: #003366; color: white; font-weight: 600; font-size: 14px; }
        tr:hover { background: #f8fafc; }
        
        .etiqueta {
            display: inline-block; padding: 6px 14px; border-radius: 20px;
            font-size: 13px; font-weight: 600;
        }
        .etapa { background: #e3f2fd; color: #01579b; }
        .estado { background: #e8f5e9; color: #2e7d32; }
        
        .sin-datos { color: #999; font-style: italic; }
        .vacio { text-align: center; padding: 50px; color: #666; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="btn-volver">← Volver al Menú</a>
        
        <div class="tarjeta">
            <div class="encabezado">
                <h1>📋 Expedientes — Modalidades de Grado</h1>
                <?php if (tieneRol(['administrador','coordinador_mg','auxiliar_mg'])): ?>
                <a href="index.php?accion=mg_expediente_crear" class="btn btn-primario">+ Nuevo Expediente</a>
                <?php endif; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Modalidad</th>
                        <th>Cohorte</th>
                        <th>Etapa</th>
                        <th>Estado</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expedientes)): ?>
                        <?php foreach ($expedientes as $exp): ?>
                        <tr>
                            <td>
                                <?php if (!empty($exp['nombre']) || !empty($exp['apellido'])): ?>
                                    <strong style="color: #003366; font-size: 15px;">
                                        <?= htmlspecialchars(($exp['nombre'] ?? '') . ' ' . ($exp['apellido'] ?? '')) ?>
                                    </strong>
                                    <br>
                                    <small style="color:#777; font-size:12px;">
                                        <?= htmlspecialchars($exp['codigo_estudiante'] ?? '') ?>
                                    </small>
                                <?php else: ?>
                                    <span class="sin-datos">Sin datos</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($exp['modalidad_nombre'] ?? '') ?></td>
                            <td><?= htmlspecialchars(($exp['cohorte_codigo'] ?? '') . ' — ' . ($exp['cohorte_nombre'] ?? '')) ?></td>
                            <td><span class="etiqueta etapa">Previa</span></td>
                            <td><span class="etiqueta estado">Activo</span></td>
                            <td>
                                <div class="acciones">
                                    <a href="index.php?accion=mg_expediente_ver&id=<?= (int)($exp['id_expediente'] ?? 0) ?>" class="btn btn-ver">👁 Ver</a>
                                    <?php if (tieneRol(['administrador','coordinador_mg','auxiliar_mg'])): ?>
                                    <a href="index.php?accion=mg_expediente_editar&id=<?= (int)($exp['id_expediente'] ?? 0) ?>" class="btn btn-editar">✏ Editar</a>
                                    <a href="index.php?accion=mg_expediente_eliminar&id=<?= (int)($exp['id_expediente'] ?? 0) ?>" 
                                       class="btn btn-eliminar" 
                                       onclick="return confirm('¿Eliminar este expediente? Esta acción no se puede deshacer.')">🗑 Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="vacio">No hay expedientes registrados aún.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>