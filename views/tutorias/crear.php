 <?php
if (!isset($estudiantes)) $estudiantes = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
if (!isset($datos_estudiante)) $datos_estudiante = [];
if (!isset($id_estudiante_actual)) $id_estudiante_actual = 0;
if (!isset($error)) $error = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Nueva Tutoría</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Roboto, sans-serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 700px; margin: 0 auto; }
        .tarjeta {
            background: #c8f0d8; /* Tu verde original */
            border-radius: 14px; padding: 35px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        h1 { 
            text-align: center; color: #006633; 
            margin-bottom: 30px; font-size: 24px;
        }
        .alerta-error {
            background: #ffebee; color: #c62828; padding: 12px;
            border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #c62828;
        }
        label {
            display: block; margin: 18px 0 8px;
            font-weight: 600; color: #004d26;
        }
        input, select, textarea {
            width: 100%; padding: 12px; border: 2px solid #99d1a9;
            border-radius: 8px; font-size: 15px; background: white;
        }
        input:focus, select:focus {
            outline: none; border-color: #00994d;
        }
        input[readonly] {
            background: #e8f5e9; cursor: not-allowed;
        }
        .btn {
            display: inline-block; padding: 12px 25px; border-radius: 8px;
            text-decoration: none; font-weight: bold; border: none;
            cursor: pointer; font-size: 15px; margin-top: 25px; transition: all 0.2s;
        }
        .btn-primario { background: #006633; color: white; }
        .btn-primario:hover { background: #004d26; transform: translateY(-2px); }
        .btn-volver { background: #e9ecef; color: #333; margin-left: 10px; }
        .btn-volver:hover { background: #dee2e6; }
        .botones { text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="tarjeta">
            <h1>Agendar Nueva Tutoría</h1>

            <?php if (!empty($error)): ?>
            <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?accion=tutoria_crear">
                <!-- ✅ ESTUDIANTE — Admin ve TODOS / Estudiante ve solo el suyo -->
                <?php if ($rol_actual === 'administrador'): ?>
                <label>Estudiante *</label>
                <select name="id_estudiante" required>
                    <option value="">Seleccione estudiante</option>
                    <?php 
                    if (!empty($estudiantes)) {
                        foreach ($estudiantes as $e) {
                            $nombre_completo = trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''));
                            if (!empty($nombre_completo)) {
                                echo '<option value="' . $e['id_estudiante'] . '">' . htmlspecialchars($nombre_completo) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
                <?php else: ?>
                <label>Estudiante</label>
                <input type="text" 
                       value="<?= htmlspecialchars(($datos_estudiante['nombre'] ?? '') . ' ' . ($datos_estudiante['apellido'] ?? '')) ?>" 
                       readonly>
                <input type="hidden" name="id_estudiante" value="<?= $id_estudiante_actual ?>">
                <?php endif; ?>

                <!-- ✅ Tutor -->
                <label>Tutor *</label>
                <select name="id_tutor" required>
                    <option value="">Seleccione tutor</option>
                    <?php if (!empty($tutores)): ?>
                        <?php foreach ($tutores as $t): ?>
                            <option value="<?= $t['id_tutor'] ?>">
                                <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <!-- ✅ Materia -->
                <label>Materia *</label>
                <select name="id_materia" required>
                    <option value="">Seleccione materia</option>
                    <?php if (!empty($materias)): ?>
                        <?php foreach ($materias as $m): ?>
                            <option value="<?= $m['id_materia'] ?>">
                                <?= htmlspecialchars($m['nombre_materia'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <!-- ✅ Fecha -->
                <label>Fecha *</label>
                <input type="date" name="fecha" required>

                <!-- ✅ Hora -->
                <label>Hora de Inicio</label>
                <input type="time" name="hora_inicio">

                <label>Hora de Fin</label>
                <input type="time" name="hora_fin">

                <!-- ✅ Modalidad -->
                <label>Modalidad</label>
                <select name="modalidad">
                    <option value="presencial">Presencial</option>
                    <option value="virtual">Virtual</option>
                </select>

                <!-- ✅ Lugar / Enlace -->
                <label>Lugar o Enlace</label>
                <input type="text" name="lugar_o_enlace" placeholder="Aula o enlace de reunión">

                <!-- ✅ Observaciones -->
                <label>Observaciones</label>
                <textarea name="observaciones" rows="3" placeholder="Notas adicionales..."></textarea>

                <!-- ✅ Botones -->
                <div class="botones">
                    <button type="submit" class="btn btn-primario">Guardar Tutoría</button>
                    <a href="index.php?accion=tutorias_listar" class="btn btn-volver">Volver</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>