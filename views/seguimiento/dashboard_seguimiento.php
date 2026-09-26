<?php if (!isset($rol)) $rol = $_SESSION['rol_nombre'] ?? ''; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Seguimiento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body { background: linear-gradient(rgba(0,38,77,0.88),rgba(0,38,77,0.88)), url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed; min-height: 100vh; padding: 30px; }
        .contenedor { max-width: 1100px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 35px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        h1 { color: #003366; margin-bottom: 25px; border-bottom: 3px solid #ffc107; padding-bottom: 10px; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 5px; border: none; cursor: pointer; }
        .btn-volver { background: #6c757d; color: #fff; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin: 30px 0; }
        .tarjeta { background: linear-gradient(135deg, #003366, #00509e); color: #fff; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 4px 12px rgba(0,51,102,0.3); }
        .tarjeta.valor { font-size: 38px; font-weight: bold; margin: 10px 0; }
        .tarjeta.etiqueta { font-size: 14px; opacity: 0.9; }
        .tarjeta.verde { background: linear-gradient(135deg, #1e7e34, #28a745); }
        .tarjeta.amarillo { background: linear-gradient(135deg, #d39e00, #ffc107); color: #000; }
        .tarjeta.rojo { background: linear-gradient(135deg, #c82333, #dc3545); }
        .tarjeta.azul-claro { background: linear-gradient(135deg, #138496, #17a2b8); }
        .enlaces { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        .enlaces a { display: inline-block; margin: 8px 10px; padding: 12px 20px; background: #f0f4f8; color: #003366; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.2s; }
        .enlaces a:hover { background: #003366; color: #fff; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>📊 Panel de Seguimiento</h1>
        <a href="index.php" class="btn btn-volver">← Volver al Inicio</a>

        <div class="grid">
            <div class="tarjeta">
                <div class="valor"><?= $metricas['total_tutorias'] ?? 0 ?></div>
                <div class="etiqueta">Total Tutorías</div>
            </div>
            <div class="tarjeta verde">
                <div class="valor"><?= $metricas['tutorias_completadas'] ?? 0 ?></div>
                <div class="etiqueta">Completadas</div>
            </div>
            <div class="tarjeta amarillo">
                <div class="valor"><?= $metricas['informes_pendientes'] ?? 0 ?></div>
                <div class="etiqueta">Informes Pendientes</div>
            </div>
            <div class="tarjeta rojo">
                <div class="valor"><?= $metricas['alertas_activas'] ?? 0 ?></div>
                <div class="etiqueta">Alertas Activas</div>
            </div>
            <div class="tarjeta azul-claro">
                <div class="valor"><?= $metricas['reuniones_mes'] ?? 0 ?></div>
                <div class="etiqueta">Reuniones este Mes</div>
            </div>
        </div>

        <div class="enlaces">
            <h3 style="margin-bottom:15px; color:#003366;">Gestión Directa</h3>
            <a href="index.php?accion=reuniones_listar">📅 Reuniones de Seguimiento</a>
            <a href="index.php?accion=informes_listar">📄 Informes de Avance</a>
            <a href="index.php?accion=alertas_listar">⚠️ Alertas de Seguimiento</a>
        </div>
    </div>
</body>
</html>