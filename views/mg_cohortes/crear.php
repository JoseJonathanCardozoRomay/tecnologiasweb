 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cohorte</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                        url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
            min-height: 100vh; padding: 30px;
        }
        .contenedor { max-width: 600px; margin: 0 auto; }
        .tarjeta {
            background: white; border-radius: 12px; padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2); color: #003366;
        }
        h1 { text-align: center; margin-bottom: 25px; font-size: 22px; }
        .btn-atras {
            display: inline-block; margin-bottom: 20px;
            color: #0066cc; text-decoration: none; font-weight: bold;
        }
        .error {
            background: #f8d7da; color: #721c24; padding: 12px 20px;
            border-radius: 8px; margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
        }
        .grupo { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 6px; font-size: 14px;
        }
        input:focus {
            outline: none; border-color: #0066cc; box-shadow: 0 0 0 2px rgba(0,102,204,0.2);
        }
        .checkbox { display: flex; align-items: center; gap: 8px; }
        .checkbox input { width: auto; margin: 0; }
        .botones { display: flex; gap: 10px; margin-top: 25px; }
        .btn-guardar {
            flex: 1; background: #0066cc; color: white; border: none; padding: 12px;
            border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;
            transition: background 0.2s;
        }
        .btn-guardar:hover { background: #0052a3; }
        .btn-cancelar {
            flex: 1; background: #e2e3e5; color: #333; text-align: center;
            padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold;
            transition: background 0.2s;
        }
        .btn-cancelar:hover { background: #d0d2d4; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=mg_cohortes" class="btn-atras">← Volver</a>
        <div class="tarjeta">
            <h1>📅 Nueva Cohorte</h1>
            
            <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="grupo">
                    <label for="codigo">Código *</label>
                    <input type="text" id="codigo" name="codigo" placeholder="Ej: 2027-1" required>
                </div>
                <div class="grupo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: Ingreso 2027 Semestre I" required>
                </div>
                <div class="grupo">
                    <label for="fecha_inicio">Fecha de Inicio *</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div class="grupo">
                    <label for="fecha_fin">Fecha de Fin (opcional)</label>
                    <input type="date" id="fecha_fin" name="fecha_fin">
                </div>
                <div class="grupo checkbox">
                    <input type="checkbox" name="activa" id="activa" checked>
                    <label for="activa">Cohorte Activa</label>
                </div>
                <div class="botones">
                    <a href="index.php?accion=mg_cohortes" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>