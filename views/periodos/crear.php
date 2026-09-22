<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Periodo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Georgia, serif; }
        body { background: linear-gradient(rgba(0,38,77,0.92),rgba(0,38,77,0.92)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 600px; margin: 0 auto; }
        .encabezado { background: linear-gradient(90deg,#00264d,#003366); color: white; padding: 22px 30px; border-radius: 10px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #cc9900; }
        .volver-btn { background: rgba(255,255,255,0.18); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); }
        .tarjeta { background: rgba(255,255,255,0.97); padding: 35px; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.25); border-top: 4px solid #cc9900; }
        .mensaje-error { background: #ffe6e6; border-left: 4px solid #b30000; color: #800000; padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        label { display: block; margin: 18px 0 6px; font-weight: bold; color: #00264d; }
        input { width: 100%; padding: 12px 15px; border: 1px solid #99b3cc; border-radius: 6px; font-size: 15px; background: #fafcff; }
        input:focus { outline: none; border-color: #cc9900; box-shadow: 0 0 0 3px rgba(204,153,0,0.2); }
        button { background: linear-gradient(90deg,#cc9900,#e6ac00,#cc9900); color: #00264d; border: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold; width: 100%; margin-top: 25px; cursor: pointer; }
        button:hover { background: linear-gradient(90deg,#b38600,#cc9900); }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>📅 Nuevo Periodo</h1>
            <a href="index.php?accion=periodos_listar" class="volver-btn">← Volver</a>
        </div>
        <div class="tarjeta">
            <?php if ($error): ?><div class="mensaje-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="POST" action="index.php?accion=periodo_crear">
                <label for="codigo">Código del Periodo:</label>
                <input type="text" id="codigo" name="codigo" placeholder="Ej: 2026-2" required>
                <label for="nombre">Nombre del Periodo:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej: Semestre II - 2026" required>
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                <label for="fecha_fin">Fecha de Finalización:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
                <label for="activo">Estado:</label>
                <select name="activo" id="activo">
                    <option value="1" selected>Activo</option>
                    <option value="0">Inactivo</option>
                </select>
                <button type="submit">✅ Guardar Periodo</button>
            </form>
        </div>
    </div>
</body>
</html>