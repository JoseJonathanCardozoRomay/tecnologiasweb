<?php
if (!isset($defensas)) $defensas = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programación de Defensas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .volver { color: white; text-decoration: none; font-weight: 500; display: inline-block; margin-bottom: 20px; }
        .contenedor { max-width: 1100px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        h1 { text-align: center; color: #003366; margin-bottom: 25px; font-size: 22px; }
        .boton-nuevo { background: #0066cc; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; margin-bottom: 25px; }
        .mensaje-ok { background: #e6f9e6; color: #006600; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background: #003366; color: white; padding: 12px 10px; text-align: left; font-weight: 600; }
        td { padding: 12px 10px; border-bottom: 1px solid #eee; color: #333; }
        tr:hover { background: #f9fbfc; }
        .estado-prog { background: #e6f3ff; color: #004499; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .estado-real { background: #e6f9e6; color: #006600; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .estado-canc { background: #f3e6e6; color: #990000; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .boton-editar { background: #ff9900; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-right: 4px; }
        .boton-eliminar { background: #cc0000; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; }
        .vacio { text-align: center; padding: 40px; color: #888; }
    </style>
</head>
<body>
    <a href="index.php?accion=listar" class="volver">← Volver al Menú</a>
    <div class="contenedor">
        <h1>📅 Programación de Defensas</h1>

        <?php if (isset($_GET['guardado'])): ?>
        <div class="mensaje-ok">✅ Defensa programada correctamente</div>
        <?php endif; ?>
        <?php if (isset($_GET['actualizado'])): ?>
        <div class="mensaje-ok">✅ Defensa actualizada correctamente</div>
        <?php endif; ?>

        <?php if (tieneRol(['administrador','coordinador_mg'])): ?>
        <a href="index.php?accion=mg_defensa_crear" class="boton-nuevo">+ Nueva Defensa</a>
        <?php endif; ?>

        <?php if (empty($defensas)): ?>
        <div class="vacio">No hay defensas programadas.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Título del Trabajo</th>
                    <th>Estudiante</th>
                    <th>Tribunal</th>
                    <th>Fecha / Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($defensas as $d): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($d['titulo_trabajo']) ?></strong></td>
                    <td><?= htmlspecialchars($d['estudiante_nombre']) ?></td>
                    <td><?= htmlspecialchars($d['nombre_tribunal'] ?? '—') ?></td>
                    <td>
                        <?= date('d/m/Y', strtotime($d['fecha_defensa'])) ?><br>
                        <small><?= date('H:i', strtotime($d['hora_inicio'])) ?> - <?= date('H:i', strtotime($d['hora_fin'])) ?></small>
                    </td>
                    <td>
                        <?php 
                        $clase = 'estado-'.$d['estado'];
                        $texto = ucfirst($d['estado']);
                        echo "<span class='$clase'>$texto</span>";
                        ?>
                    </td>
                    <td>
                        <?php if (tieneRol(['administrador','coordinador_mg'])): ?>
                        <a href="index.php?accion=mg_defensa_editar&id=<?= $d['id_defensa'] ?>" class="boton-editar">Editar</a>
                        <a href="index.php?accion=mg_defensa_eliminar&id=<?= $d['id_defensa'] ?>" class="boton-eliminar" onclick="return confirm('¿Eliminar esta defensa?')">X</a>
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