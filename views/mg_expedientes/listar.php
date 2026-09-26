<?php
if (!isset($expedientes)) $expedientes = [
    ['estudiante_nombre' => 'Maria', 'estudiante_apellido' => 'Estudiante', 'modalidad_nombre' => 'Graduación por Excelencia', 'cohorte_codigo' => 'ana', 'etapa_actual' => 'previa', 'estado' => 'activo', 'id_expediente' => 1],
    ['estudiante_nombre' => 'Maria', 'estudiante_apellido' => 'Estudiante', 'modalidad_nombre' => 'Trabajo Dirigido', 'cohorte_codigo' => 'ana', 'etapa_actual' => 'previa', 'estado' => 'activo', 'id_expediente' => 2],
    ['estudiante_nombre' => 'Maria', 'estudiante_apellido' => 'Estudiante', 'modalidad_nombre' => 'Examen de Grado', 'cohorte_codigo' => 'ana', 'etapa_actual' => 'previa', 'estado' => 'activo', 'id_expediente' => 3],
    ['estudiante_nombre' => 'Maria', 'estudiante_apellido' => 'Estudiante', 'modalidad_nombre' => 'Graduación por Excelencia', 'cohorte_codigo' => 'anai', 'etapa_actual' => 'previa', 'estado' => 'activo', 'id_expediente' => 4],
    ['estudiante_nombre' => 'Maria', 'estudiante_apellido' => 'Estudiante', 'modalidad_nombre' => 'Trabajo Dirigido', 'cohorte_codigo' => 'anai', 'etapa_actual' => 'previa', 'estado' => 'activo', 'id_expediente' => 5],
    ['estudiante_nombre' => 'Maria', 'estudiante_apellido' => 'Estudiante', 'modalidad_nombre' => 'Examen de Grado', 'cohorte_codigo' => 'anai', 'etapa_actual' => 'previa', 'estado' => 'activo', 'id_expediente' => 6],
    ['estudiante_nombre' => 'Maria', 'estudiante_apellido' => 'Estudiante', 'modalidad_nombre' => 'Tesis', 'cohorte_codigo' => 'ana', 'etapa_actual' => 'previa', 'estado' => 'activo', 'id_expediente' => 7],
];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expedientes — Modalidades de Grado</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .volver {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 20px;
        }
        .volver:hover { text-decoration: underline; }
        .contenedor {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 25px;
            font-size: 22px;
        }
        .boton-nuevo {
            background: #0066cc;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 25px;
        }
        .boton-nuevo:hover { background: #0052b3; }
        .filtros {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            align-items: center;
            background: #f5f7fa;
            padding: 15px;
            border-radius: 8px;
        }
        .filtros select,
        .filtros input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background: white;
        }
        .boton-filtrar {
            background: #0066cc;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #003366;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        tr:hover { background: #f9fbfc; }
        .etapa-tag {
            background: #e6f0fa;
            color: #004080;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-block;
        }
        .estado-tag {
            background: #e6f9e6;
            color: #006600;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-block;
        }
        .boton-ver {
            color: #0066cc;
            text-decoration: none;
            font-weight: 500;
            margin-right: 5px;
        }
        .boton-editar {
            background: #ff9900;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            margin: 0 3px;
        }
        .boton-editar:hover { background: #e68a00; }
        .boton-eliminar {
            background: #cc0000;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
        }
        .boton-eliminar:hover { background: #b30000; }
    </style>
</head>
<body>
    <a href="index.php" class="volver">← Volver al Menú</a>

    <div class="contenedor">
        <h1>📚 Expedientes — Modalidades de Grado</h1>

        <?php if (tieneRol(['administrador'])): ?>
        <a href="index.php?accion=mg_expediente_crear" class="boton-nuevo">+ Nuevo Expediente</a>
        <?php endif; ?>

        <form method="get" action="index.php" class="filtros">
            <input type="hidden" name="accion" value="mg_expedientes_listar">
            <select name="id_cohorte">
                <option value="">2026 — anai</option>
            </select>
            <select name="id_modalidad">
                <option value="">Proyecto de Grado</option>
            </select>
            <select name="etapa_actual">
                <option value="">MG2</option>
            </select>
            <input type="text" name="buscar" placeholder="Buscar estudiante...">
            <button type="submit" class="boton-filtrar">Filtrar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Modalidad</th>
                    <th>Cohorte</th>
                    <th>Etapa</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expedientes as $exp): ?>
                <tr>
                    <td><?= htmlspecialchars(($exp['estudiante_nombre'] ?? '') . ' ' . ($exp['estudiante_apellido'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($exp['modalidad_nombre'] ?? '') ?></td>
                    <td><?= htmlspecialchars($exp['cohorte_codigo'] ?? '') ?></td>
                    <td><span class="etapa-tag"><?= htmlspecialchars(ucfirst($exp['etapa_actual'] ?? 'Previa')) ?></span></td>
                    <td><span class="estado-tag"><?= htmlspecialchars(ucfirst($exp['estado'] ?? 'Activo')) ?></span></td>
                    <td>
                        <a href="index.php?accion=mg_expediente_ver&id=<?= $exp['id_expediente'] ?>" class="boton-ver">Ver →</a>
                        <?php if (tieneRol(['administrador'])): ?>
                        <a href="index.php?accion=mg_expediente_editar&id=<?= $exp['id_expediente'] ?>" class="boton-editar">Editar</a>
                        <a href="index.php?accion=mg_expediente_eliminar&id=<?= $exp['id_expediente'] ?>" class="boton-eliminar" onclick="return confirm('¿Eliminar este expediente?')">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>