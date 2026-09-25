<?php
if (!isset($tutoria)) $tutoria = [];
if (!isset($estudiantes)) $estudiantes = [];
if (!isset($tutores)) $tutores = [];
if (!isset($materias)) $materias = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tutoría</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.82), rgba(0, 38, 77, 0.82)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .contenedor {
            max-width: 580px;
            width: 100%;
            background: rgba(209, 231, 221, 0.96);
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
            font-size: 22px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            color: #003366;
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 14px;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .solo-lectura {
            background: #e9ecef;
            color: #495057;
            cursor: not-allowed;
        }
        .aviso {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        .botones {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }
        .btn-actualizar {
            flex: 1;
            background: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-volver {
            flex: 1;
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>📝 Editar Tutoría</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?accion=tutoria_editar&id=<?= $tutoria['id_tutoria'] ?>" method="POST">
            
            <!-- Estudiante -->
            <div class="form-group">
                <label>Estudiante:</label>
                <?php if ($rol_actual === 'tutor'): ?>
                    <input type="text" class="solo-lectura" 
                           value="<?= htmlspecialchars($tutoria['estudiante_nombre'] ?? '') ?>" readonly>
                    <input type="hidden" name="id_estudiante" value="<?= $tutoria['id_estudiante'] ?>">
                    <p class="aviso">🔒 No modificable</p>
                <?php else: ?>
                    <select name="id_estudiante" required>
                        <?php foreach ($estudiantes as $e): ?>
                            <option value="<?= $e['id_estudiante'] ?>" 
                                <?= ($tutoria['id_estudiante'] == $e['id_estudiante']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Tutor -->
            <div class="form-group">
                <label>Tutor:</label>
                <?php if ($rol_actual === 'tutor'): ?>
                    <input type="text" class="solo-lectura" 
                           value="<?= htmlspecialchars($tutoria['tutor_nombre'] ?? '') ?>" readonly>
                    <input type="hidden" name="id_tutor" value="<?= $tutoria['id_tutor'] ?>">
                    <p class="aviso">🔒 No modificable</p>
                <?php else: ?>
                    <select name="id_tutor" required>
                        <?php foreach ($tutores as $t): ?>
                            <option value="<?= $t['id_tutor'] ?>" 
                                <?= ($tutoria['id_tutor'] == $t['id_tutor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Materia -->
            <div class="form-group">
                <label>Materia:</label>
                <?php if ($rol_actual === 'tutor'): ?>
                    <input type="text" class="solo-lectura" 
                           value="<?= htmlspecialchars($tutoria['nombre_materia'] ?? '') ?>" readonly>
                    <input type="hidden" name="id_materia" value="<?= $tutoria['id_materia'] ?>">
                    <p class="aviso">🔒 No modificable</p>
                <?php else: ?>
                    <select name="id_materia" required>
                        <?php foreach ($materias as $m): ?>
                            <option value="<?= $m['id_materia'] ?>" 
                                <?= ($tutoria['id_materia'] == $m['id_materia']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nombre_materia'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha" value="<?= $tutoria['fecha'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Hora de Inicio:</label>
                <input type="time" name="hora_inicio" value="<?= $tutoria['hora_inicio'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Hora de Fin:</label>
                <input type="time" name="hora_fin" value="<?= $tutoria['hora_fin'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Modalidad:</label>
                <select name="modalidad" id="modalidad">
                    <option value="presencial" <?= ($tutoria['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                    <option value="virtual" <?= ($tutoria['modalidad'] ?? '') === 'virtual' ? 'selected' : '' ?>>Virtual</option>
                </select>
            </div>

            <!-- Lugar o Enlace → Lista de aulas para Tutor + campo libre -->
            <div class="form-group">
                <label>Lugar o Enlace:</label>
                
                <?php if ($rol_actual === 'tutor'): ?>
                    <!-- Para Tutor: lista desplegable con aulas + opción libre -->
                    <select name="lugar_o_enlace" id="lugar_select">
                        <option value="">-- Seleccionar aula o escribir enlace --</option>
                        <option value="Aula 101 - Edificio Principal" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Aula 101 - Edificio Principal' ? 'selected' : '' ?>>Aula 101 - Edificio Principal</option>
                        <option value="Aula 102 - Edificio Principal" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Aula 102 - Edificio Principal' ? 'selected' : '' ?>>Aula 102 - Edificio Principal</option>
                        <option value="Aula 201 - Edificio Ciencias" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Aula 201 - Edificio Ciencias' ? 'selected' : '' ?>>Aula 201 - Edificio Ciencias</option>
                        <option value="Aula 204 - Edificio Ciencias" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Aula 204 - Edificio Ciencias' ? 'selected' : '' ?>>Aula 204 - Edificio Ciencias</option>
                        <option value="Aula 305 - Laboratorio" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Aula 305 - Laboratorio' ? 'selected' : '' ?>>Aula 305 - Laboratorio</option>
                        <option value="Sala de Tutorías 1" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Sala de Tutorías 1' ? 'selected' : '' ?>>Sala de Tutorías 1</option>
                        <option value="Sala de Tutorías 2" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Sala de Tutorías 2' ? 'selected' : '' ?>>Sala de Tutorías 2</option>
                        <option value="Biblioteca - Sala B" <?= ($tutoria['lugar_o_enlace'] ?? '') === 'Biblioteca - Sala B' ? 'selected' : '' ?>>Biblioteca - Sala B</option>
                        <option value="otro" <?= !in_array(($tutoria['lugar_o_enlace'] ?? ''), ['Aula 101 - Edificio Principal','Aula 102 - Edificio Principal','Aula 201 - Edificio Ciencias','Aula 204 - Edificio Ciencias','Aula 305 - Laboratorio','Sala de Tutorías 1','Sala de Tutorías 2','Biblioteca - Sala B']) ? 'selected' : '' ?>>Escribir otro...</option>
                    </select>
                    <input type="text" name="lugar_personalizado" id="lugar_otro" 
                           placeholder="Escribe el lugar o enlace" style="display:none; margin-top:8px;"
                           value="<?= htmlspecialchars($tutoria['lugar_o_enlace'] ?? '') ?>">
                <?php else: ?>
                    <!-- Para Estudiante y Admin: campo normal -->
                    <input type="text" name="lugar_o_enlace" 
                           value="<?= htmlspecialchars($tutoria['lugar_o_enlace'] ?? '') ?>"
                           placeholder="Ej: Aula 204 o enlace de reunión">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Estado:</label>
                <select name="estado">
                    <option value="pendiente" <?= ($tutoria['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="confirmada" <?= ($tutoria['estado'] ?? '') === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                    <option value="realizada" <?= ($tutoria['estado'] ?? '') === 'realizada' ? 'selected' : '' ?>>Realizada</option>
                    <option value="cancelada" <?= ($tutoria['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>

            <div class="form-group">
                <label>Observaciones:</label>
                <textarea name="observaciones" rows="3"><?= htmlspecialchars($tutoria['observaciones'] ?? '') ?></textarea>
            </div>

            <div class="botones">
                <button type="submit" class="btn-actualizar">Actualizar</button>
                <a href="index.php?accion=tutorias_listar" class="btn-volver">Volver</a>
            </div>
        </form>
    </div>

    <?php if ($rol_actual === 'tutor'): ?>
    <script>
        // Mostrar campo personalizado cuando se elige "otro"
        const select = document.getElementById('lugar_select');
        const otro = document.getElementById('lugar_otro');
        
        function verificarSeleccion() {
            if (select.value === 'otro') {
                otro.style.display = 'block';
                otro.name = 'lugar_o_enlace';
            } else {
                otro.style.display = 'none';
                otro.name = '';
            }
        }
        select.addEventListener('change', verificarSeleccion);
        verificarSeleccion();
    </script>
    <?php endif; ?>
</body>
</html>