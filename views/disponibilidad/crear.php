<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Disponibilidad Horaria</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.82), rgba(0, 38, 77, 0.82)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .contenedor {
            max-width: 540px;
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
            margin-bottom: 18px;
        }
        label {
            display: block;
            color: #003366;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 15px;
        }
        select, input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }
        .dias-opciones {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 5px;
        }
        .dia-opcion {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        .dia-opcion:hover {
            border-color: #0066cc;
            background: #e6f0ff;
        }
        .dia-opcion input {
            width: auto;
            margin: 0;
        }
        .botones {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }
        .btn-guardar {
            flex: 1;
            background: #0066cc;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-volver {
            flex: 1;
            background: #6c757d;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 8px;
            font-size: 16px;
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
        .exito {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
        }
        .campo-solo-lectura {
            background: #e9ecef;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>📅 Registrar Disponibilidad Horaria</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($exito)): ?>
            <div class="exito"><?= htmlspecialchars($exito) ?></div>
        <?php endif; ?>

        <form action="index.php?accion=disponibilidad_crear" method="POST">
            <!-- TUTOR: Si entra como Tutor → su nombre aparece automáticamente ✅ -->
            <div class="form-group">
                <label for="id_tutor">Tutor:</label>
                <?php if (!empty($rol_actual) && $rol_actual === 'tutor'): ?>
                    <input type="text" class="campo-solo-lectura" 
                           value="<?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?>" readonly>
                    <input type="hidden" name="id_tutor" value="<?= $id_tutor_actual ?? 0 ?>">
                <?php else: ?>
                    <select name="id_tutor" id="id_tutor" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($tutores as $t): ?>
                            <option value="<?= $t['id_tutor'] ?>">
                                <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- DÍAS: SELECCIONAR VARIOS A LA VEZ ✅ -->
            <div class="form-group">
                <label>Día(s) de la Semana: <small style="font-weight:normal; color:#666;">(puedes elegir varios)</small></label>
                <div class="dias-opciones">
                    <label class="dia-opcion">
                        <input type="checkbox" name="dias[]" value="Lunes"> Lunes
                    </label>
                    <label class="dia-opcion">
                        <input type="checkbox" name="dias[]" value="Martes"> Martes
                    </label>
                    <label class="dia-opcion">
                        <input type="checkbox" name="dias[]" value="Miercoles"> Miércoles
                    </label>
                    <label class="dia-opcion">
                        <input type="checkbox" name="dias[]" value="Jueves"> Jueves
                    </label>
                    <label class="dia-opcion">
                        <input type="checkbox" name="dias[]" value="Viernes"> Viernes
                    </label>
                    <label class="dia-opcion">
                        <input type="checkbox" name="dias[]" value="Sabado"> Sábado
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio:</label>
                <input type="time" name="hora_inicio" id="hora_inicio" required>
            </div>

            <div class="form-group">
                <label for="hora_fin">Hora de Fin:</label>
                <input type="time" name="hora_fin" id="hora_fin" required>
            </div>

            <div class="botones">
                <button type="submit" class="btn-guardar">Guardar</button>
                <a href="index.php?accion=disponibilidad_listar" class="btn-volver">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>