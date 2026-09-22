<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Seguimiento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        
        body {
            background: 
                linear-gradient(rgba(0, 38, 77, 0.92), rgba(0, 38, 77, 0.92)),
                url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }

        .contenedor {
            max-width: 700px;
            margin: 0 auto;
        }

        .encabezado {
            background: linear-gradient(90deg, #00264d 0%, #003366 100%);
            color: white;
            padding: 22px 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #cc9900;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .encabezado h1 {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .volver-btn {
            background: rgba(255, 255, 255, 0.18);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
        }

        .volver-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-3px);
        }

        .tarjeta {
            background: rgba(255, 255, 255, 0.97);
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            border-top: 4px solid #cc9900;
            backdrop-filter: blur(8px);
        }

        .mensaje {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .mensaje-error {
            background: #fff2f2;
            border-left: 4px solid #b30000;
            color: #800000;
        }

        label {
            display: block;
            margin: 20px 0 7px;
            font-weight: bold;
            color: #00264d;
            font-size: 15px;
        }

        select, textarea {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #99b3cc;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
            background: #fafcff;
        }

        select:focus, textarea:focus {
            outline: none;
            border-color: #cc9900;
            box-shadow: 0 0 0 3px rgba(204, 153, 0, 0.2);
            background: #fff;
        }

        button {
            background: linear-gradient(90deg, #cc9900 0%, #e6ac00 50%, #cc9900 100%);
            color: #00264d;
            border: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s;
            width: 100%;
            letter-spacing: 0.5px;
        }

        button:hover {
            background: linear-gradient(90deg, #b38600 0%, #cc9900 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(204, 153, 0, 0.35);
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>📝 Registrar Seguimiento de Sesión</h1>
            <a href="index.php?accion=seguimientos_listar" class="volver-btn">← Volver al Listado</a>
        </div>

        <div class="tarjeta">
            <?php if (empty($tutorias_pendientes)): ?>
                <div class="mensaje mensaje-error">
                    No hay tutorías pendientes de seguimiento.
                </div>
            <?php else: ?>
                <form method="POST" action="index.php?accion=seguimiento_crear">
                    <label for="id_tutoria">Tutoría:</label>
                    <select name="id_tutoria" id="id_tutoria" required>
                        <option value="">-- Seleccionar tutoría --</option>
                        <?php foreach ($tutorias_pendientes as $t): ?>
                            <option value="<?= $t['id_tutoria'] ?>">
                                Fecha: <?= htmlspecialchars($t['fecha']) ?> — Hora: <?= htmlspecialchars($t['hora_inicio']) ?> — Estado: <?= htmlspecialchars($t['estado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="asistio">¿Asistió?:</label>
                    <select name="asistio" id="asistio" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="si">✅ Sí</option>
                        <option value="no">❌ No</option>
                    </select>

                    <label for="temas_tratados">Temas Tratados:</label>
                    <textarea name="temas_tratados" id="temas_tratados" rows="4" placeholder="Describa los temas revisados en la sesión..."></textarea>

                    <button type="submit">✅ Guardar Seguimiento</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>