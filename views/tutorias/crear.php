<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Nueva Tutoría</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', 'Times New Roman', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.82), rgba(0, 38, 77, 0.82)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 520px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .boton-volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 25px;
            transition: background 0.3s ease;
        }
        .boton-volver:hover {
            background: #495057;
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            color: #003366;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 15px;
        }
        input, select, textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }
        .campo-lectura {
            background: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
        }
        .btn-guardar {
            background: linear-gradient(90deg, #0066cc, #004080);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 8px;
            transition: transform 0.2s ease;
        }
        .btn-guardar:hover {
            transform: scale(1.02);
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .oculto { display: none; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=tutorias_listar" class="boton-volver">← Volver al Listado</a>

        <h1>Agendar Nueva Tutoría</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?accion=tutoria_crear" method="POST" id="formTutoria">
            <!-- Estudiante - Solo lectura -->
            <div class="form-group">
                <label>Estudiante:</label>
                <input type="text" class="campo-lectura" value="<?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Estudiante') ?>" readonly>
                <input type="hidden" name="id_estudiante" value="<?= $_SESSION['id_usuario'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label for="id_tutor">Tutor:</label>
                <select name="id_tutor" id="id_tutor" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($tutores as $t): ?>
                        <option value="<?= $t['id_tutor'] ?>">
                            <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_materia">Materia:</label>
                <select name="id_materia" id="id_materia" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id_materia'] ?>">
                            <?= htmlspecialchars($m['nombre_materia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" id="fecha" required>
            </div>

            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio:</label>
                <input type="time" name="hora_inicio" id="hora_inicio" required>
            </div>

            <div class="form-group">
                <label for="hora_fin">Hora de Fin:</label>
                <input type="time" name="hora_fin" id="hora_fin" required>
            </div>

            <div class="form-group">
                <label for="modalidad">Modalidad:</label>
                <select name="modalidad" id="modalidad" onchange="cambiarLugar()">
                    <option value="presencial">Presencial</option>
                    <option value="virtual">Virtual</option>
                </select>
            </div>

            <!-- Lugar / Enlace - CAMBIA SEGÚN MODALIDAD -->
            <div class="form-group" id="grupoLugar">
                <label for="lugar_o_enlace">Lugar:</label>
                <select name="lugar_o_enlace" id="lugarPresencial">
                    <option value="">Seleccione el lugar</option>
                    <option value="Universidad Domingo Sabio - Aula 101">Universidad Domingo Sabio — Aula 101</option>
                    <option value="Universidad Domingo Sabio - Aula 102">Universidad Domingo Sabio — Aula 102</option>
                    <option value="Universidad Domingo Sabio - Aula 201">Universidad Domingo Sabio — Aula 201</option>
                    <option value="Universidad Domingo Sabio - Aula 202">Universidad Domingo Sabio — Aula 202</option>
                    <option value="Universidad Domingo Sabio - Aula 301">Universidad Domingo Sabio — Aula 301</option>
                    <option value="Universidad Domingo Sabio - Aula 302">Universidad Domingo Sabio — Aula 302</option>
                    <option value="Universidad Domingo Sabio - Laboratorio 1">Universidad Domingo Sabio — Laboratorio 1</option>
                    <option value="Universidad Domingo Sabio - Laboratorio 2">Universidad Domingo Sabio — Laboratorio 2</option>
                    <option value="Universidad Domingo Sabio - Biblioteca">Universidad Domingo Sabio — Biblioteca</option>
                    <option value="Otro">Otro lugar...</option>
                </select>
                <input type="text" name="enlace_virtual" id="enlaceVirtual" class="oculto" placeholder="Enlace de reunión (Zoom, Meet, etc.)">
            </div>

            <!-- ✅ ESTADO ELIMINADO — SE GUARDA COMO 'pendiente' AUTOMÁTICAMENTE -->
            <input type="hidden" name="estado" value="pendiente">

            <div class="form-group">
                <label for="observaciones">Observaciones:</label>
                <textarea name="observaciones" id="observaciones" rows="3"></textarea>
            </div>

            <button type="submit" class="btn-guardar">Guardar Tutoría</button>
        </form>
    </div>

    <script>
        function cambiarLugar() {
            const modalidad = document.getElementById('modalidad').value;
            const selectPresencial = document.getElementById('lugarPresencial');
            const inputVirtual = document.getElementById('enlaceVirtual');

            if (modalidad === 'presencial') {
                selectPresencial.classList.remove('oculto');
                inputVirtual.classList.add('oculto');
                selectPresencial.name = 'lugar_o_enlace';
                inputVirtual.name = '';
            } else {
                selectPresencial.classList.add('oculto');
                inputVirtual.classList.remove('oculto');
                inputVirtual.name = 'lugar_o_enlace';
                selectPresencial.name = '';
            }
        }
    </script>
</body>
</html>