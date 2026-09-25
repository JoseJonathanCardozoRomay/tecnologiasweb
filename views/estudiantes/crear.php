<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Estudiante</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 550px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 15px;
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffc107;
            font-size: 22px;
        }
        .alerta-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1e7dd;
            color: #0f5132;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            color: #003366;
            margin-bottom: 5px;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            width: 100%;
            background: #0066cc;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
        }
        button:hover { background: #0052a3; }
        .sep {
            margin: 18px 0 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
            color: #003366;
            font-weight: bold;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=estudiantes_listar" class="volver">← Volver</a>
        
        <h1>🎓 Nuevo Estudiante</h1>

        <div class="info">
            ✅ Todo listo. Completa y guarda.
        </div>

        <?php if (!empty($error)): ?>
            <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sep">Datos Personales</div>
            
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required placeholder="Ej: Juan">
            </div>

            <div class="form-group">
                <label>Apellido *</label>
                <input type="text" name="apellido" required placeholder="Ej: Mamani">
            </div>

            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" placeholder="Opcional">
            </div>

            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" name="contrasena" required placeholder="Para ingresar">
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" placeholder="Opcional">
            </div>

            <div class="sep">Datos Académicos</div>

            <div class="form-group">
                <label>Carrera *</label>
                <select name="id_carrera" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre_carrera']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Semestre *</label>
                <input type="number" name="semestre" min="1" max="12" required placeholder="Ej: 2">
            </div>

            <div class="form-group">
                <label>Nº Registro Universitario *</label>
                <input type="text" name="registro_universitario" required placeholder="Ej: 2026-001">
            </div>

            <button type="submit">✅ Guardar Estudiante</button>
        </form>
    </div>
</body>
</html>