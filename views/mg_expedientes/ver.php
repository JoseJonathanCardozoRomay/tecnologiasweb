<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Expediente</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', 'Times New Roman', serif;
            background: linear-gradient(rgba(0, 38, 77, 0.85), rgba(0, 38, 77, 0.85)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 30px;
        }
        .contenedor { max-width: 900px; margin: 0 auto; }
        .tarjeta {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            color: #003366;
        }
        h1 { text-align: center; margin-bottom: 30px; font-size: 24px; }
        .btn-atras {
            display: inline-block;
            margin-bottom: 20px;
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-atras:hover { text-decoration: underline; }
        .fila {
            padding: 14px 0;
            border-bottom: 1px solid #e0e6ed;
        }
        .etiqueta {
            font-weight: bold;
            display: inline-block;
            width: 200px;
            color: #004080;
        }
        .valor { color: #333; }
        .etapa {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .etapa-previa { background: #e9ecef; color: #495057; }
        .etapa-mg1 { background: #cce5ff; color: #004085; }
        .etapa-mg2 { background: #d4edda; color: #155724; }
        .etapa-finalizado { background: #d1e7dd; color: #0f5132; }
        .estado-activo { color: #155724; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php?accion=mg_expedientes_listar" class="btn-atras">← Volver a Expedientes</a>
        
        <div class="tarjeta">
            <h1>📋 Detalle del Expediente</h1>
            
            <div class="fila">
                <span class="etiqueta">Estudiante:</span>
                <span class="valor">
                    <strong><?= htmlspecialchars(($expediente['estudiante_nombre'] ?? '') . ' ' . ($expediente['estudiante_apellido'] ?? '')) ?></strong>
                </span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Código Estudiante:</span>
                <span class="valor"><?= htmlspecialchars($expediente['codigo_estudiante'] ?? '—') ?></span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Modalidad:</span>
                <span class="valor"><?= htmlspecialchars($expediente['modalidad_nombre'] ?? '—') ?></span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Cohorte:</span>
                <span class="valor"><?= htmlspecialchars($expediente['cohorte_codigo'] ?? '—') ?></span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Fecha de Inicio:</span>
                <span class="valor"><?= htmlspecialchars($expediente['fecha_inicio'] ?? '—') ?></span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Título del Trabajo:</span>
                <span class="valor"><?= htmlspecialchars($expediente['titulo_trabajo'] ?? '—') ?></span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Etapa Actual:</span>
                <?php $etapa = $expediente['etapa_actual'] ?? 'previa'; ?>
                <span class="etapa etapa-<?= $etapa ?>"><?= ucfirst($etapa) ?></span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Estado:</span>
                <span class="valor estado-activo"><?= ucfirst($expediente['estado'] ?? 'activo') ?></span>
            </div>
            
            <div class="fila">
                <span class="etiqueta">Observaciones:</span>
                <span class="valor"><?= nl2br(htmlspecialchars($expediente['observaciones'] ?? '—')) ?></span>
            </div>
        </div>
    </div>
</body>
</html>