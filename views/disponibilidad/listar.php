<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilidad Horaria</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.82), rgba(0, 38, 77, 0.82)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 25px;
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
        }
        .btn-nuevo {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 10px 20px;
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
            padding: 14px 12px;
            text-align: left;
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
        tr:hover { background: #f0f7ff; }
        .acciones a {
            margin-right: 8px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al Menú</a>

        <h1>📅 Disponibilidad Horaria</h1>

        <!-- BOTÓN NUEVA DISPONIBILIDAD → SOLO TUTOR Y ADMIN -->
        <?php if (!empty($rol_actual) && ($rol_actual === 'tutor' || $rol_actual === 'administrador')): ?>
            <a href="index.php?accion=disponibilidad_crear" class="btn-nuevo">➕ Nueva Disponibilidad</a>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tutor</th>
                    <th>Día</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($disponibilidades)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:25px; color:#666;">
                            No hay horarios registrados.
                            <?php if (!empty($rol_actual) && $rol_actual === 'tutor'): ?>
                                <br><strong>Agrega tus horarios disponibles con el botón de arriba.</strong>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($disponibilidades as $d): ?>
                    <tr>
                        <td><?= $d['id_disponibilidad'] ?></td>
                        <td><?= htmlspecialchars($d['tutor_nombre'] ?? 'Sin nombre') ?></td>
                        <td><?= $d['dia_semana'] ?></td>
                        <td><?= $d['hora_inicio'] ?></td>
                        <td><?= $d['hora_fin'] ?></td>
                        <td class="acciones">
                            <!-- EDITAR/ELIMINAR → SOLO ADMIN O EL PROPIO TUTOR -->
                            <?php 
                            $id_usuario_tutor = $d['id_usuario'] ?? 0;
                            $id_sesion = $_SESSION['id_usuario'] ?? 0;
                            if (!empty($rol_actual) && ($rol_actual === 'administrador' || 
                               ($rol_actual === 'tutor' && $id_usuario_tutor == $id_sesion))): 
                            ?>
                                <a href="index.php?accion=disponibilidad_editar&id=<?= $d['id_disponibilidad'] ?>">✏️ Editar</a>
                                <a href="index.php?accion=disponibilidad_eliminar&id=<?= $d['id_disponibilidad'] ?>" 
                                   onclick="return confirm('¿Eliminar este horario?')">🗑️ Eliminar</a>
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