<?php
if (!isset($tribunales)) $tribunales = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['nombre_rol'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tribunales / Jurados</title>
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
            max-width: 1000px;
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
        .mensaje-ok {
            background: #e6f9e6;
            color: #006600;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
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
        .etiqueta-activo {
            background: #e6f9e6;
            color: #006600;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .etiqueta-inactivo {
            background: #f3e6e6;
            color: #990000;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .boton-editar {
            background: #ff9900;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
        }
        .boton-editar:hover { background: #e68a00; }
        .boton-eliminar {
            background: #cc0000;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
        }
        .boton-eliminar:hover { background: #b30000; }
        .vacio {
            text-align: center;
            padding: 40px;
            color: #888;
        }
    </style>
</head>
<body>
    <a href="index.php?accion=listar" class="volver">← Volver al Menú</a>

    <div class="contenedor">
        <h1>⚖️ Tribunales / Jurados de Defensa</h1>

        <?php if (isset($_GET['guardado'])): ?>
        <div class="mensaje-ok">✅ Tribunal registrado correctamente</div>
        <?php endif; ?>
        <?php if (isset($_GET['actualizado'])): ?>
        <div class="mensaje-ok">✅ Tribunal actualizado correctamente</div>
        <?php endif; ?>

        <?php if (tieneRol(['administrador','coordinador_mg'])): ?>
        <a href="index.php?accion=mg_tribunal_crear" class="boton-nuevo">+ Nuevo Tribunal</a>
        <?php endif; ?>

        <?php if (empty($tribunales)): ?>
        <div class="vacio">No hay tribunales registrados.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Especialidad</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tribunales as $t): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($t['nombre_completo']) ?></strong></td>
                    <td><?= htmlspecialchars($t['especialidad'] ?? '—') ?></td>
                    <td>
                        <?= !empty($t['correo']) ? htmlspecialchars($t['correo']) . '<br>' : '' ?>
                        <?= !empty($t['telefono']) ? htmlspecialchars($t['telefono']) : '—' ?>
                    </td>
                    <td>
                        <?php if (!empty($t['activo'])): ?>
                            <span class="etiqueta-activo">Activo</span>
                        <?php else: ?>
                            <span class="etiqueta-inactivo">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (tieneRol(['administrador','coordinador_mg'])): ?>
                        <a href="index.php?accion=mg_tribunal_editar&id=<?= $t['id_tribunal'] ?>" class="boton-editar">Editar</a>
                        <a href="index.php?accion=mg_tribunal_eliminar&id=<?= $t['id_tribunal'] ?>" class="boton-eliminar" onclick="return confirm('¿Eliminar este tribunal?')">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>