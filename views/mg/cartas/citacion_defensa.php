<?php
/** Vista de impresión A4: Citación formal a Defensa (HU-030). */
$universidad = $datosPagina['universidad'];
$eslogan = $datosPagina['eslogan'];
$telefono = $datosPagina['telefono'];
$correo = $datosPagina['correo'];
$tribunales = $defensa['tribunales'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Citación a Defensa - <?= htmlspecialchars($defensa['estudiante_apellido'] . ' ' . $defensa['estudiante_nombre']) ?></title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background: #e9ecef; color: #212529; }
    .no-print { text-align: center; padding: 14px; }
    .no-print button { font-size: 0.95rem; padding: 10px 22px; border: 0; border-radius: 6px;
                       background: #0d47a1; color: #fff; cursor: pointer; }
    .no-print a { margin-left: 10px; color: #555; font-size: 0.9rem; }
    .paper { width: 210mm; min-height: 297mm; margin: 12px auto; background: #fff;
             padding: 20mm 18mm; box-shadow: 0 3px 14px rgba(0,0,0,.18); }
    .letterhead { display: flex; justify-content: space-between; align-items: center;
                  border-bottom: 3px solid #0d47a1; padding-bottom: 10px; margin-bottom: 22px; }
    .brand { display: flex; align-items: center; gap: 10px; }
    .brand img { height: 54px; }
    .brand-name { font-size: 1.05rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
    .brand-slogan { font-size: .72rem; color: #6c757d; }
    .correlativo { text-align: right; font-size: .78rem; color: #495057; line-height: 1.5; }
    .correlativo .num { font-weight: 700; color: #0d47a1; font-size: .9rem; }
    .fecha { text-align: right; font-size: .9rem; margin-bottom: 18px; }
    .dir { margin-bottom: 16px; font-size: .95rem; }
    .asunto { margin: 8px 0 18px; font-size: .95rem; }
    .ptarrafo { text-align: justify; font-size: .95rem; line-height: 1.65; margin-bottom: 12px; }
    .datos { border-collapse: collapse; margin: 16px 0; width: 100%; font-size: .93rem; }
    .datos td { padding: 7px 10px; border: 1px solid #ced4da; vertical-align: top; }
    .datos td.lbl { width: 30%; font-weight: 700; background: #f8f9fa; }
    .atentamente { margin-top: 26px; }
    .firmas { display: flex; justify-content: space-between; margin-top: 46px; text-align: center; }
    .firma-bloque { width: 42%; }
    .firma-linea { border-top: 1px solid #212529; padding-top: 6px; margin-top: 52px; font-weight: 700; font-size: .92rem; }
    .firma-rol { font-size: .8rem; color: #495057; }
    .pie { margin-top: 34px; padding-top: 8px; border-top: 1px solid #dee2e6;
          font-size: .72rem; color: #6c757d; display: flex; justify-content: space-between; }
    @media print {
      body { background: #fff; }
      .paper { box-shadow: none; margin: 0; width: auto; min-height: auto; padding: 0; }
      .no-print { display: none; }
      @page { size: A4; margin: 18mm 16mm; }
    }
  </style>
</head>
<body>
  <div class="no-print">
    <button onclick="window.print()">Imprimir / Guardar PDF</button>
    <a href="/controllers/mg_defensas_listar.php?cohorte=<?= (int) $defensa['id_cohorte_mg'] ?>">← Volver a defensas</a>
  </div>

  <div class="paper">
    <div class="letterhead">
      <div class="brand">
        <img src="/assets/img/logo-upds.svg" alt="Logo">
        <div>
          <div class="brand-name"><?= htmlspecialchars($universidad) ?></div>
          <div class="brand-slogan"><?= htmlspecialchars($eslogan) ?> · Sede Tarija</div>
        </div>
      </div>
      <div class="correlativo">
        <div>Correlativo</div>
        <div class="num"><?= htmlspecialchars($defensa['correlativo']) ?></div>
        <div>Coordinación de Modalidad de Grado</div>
      </div>
    </div>

    <div class="fecha"><?= htmlspecialchars(mgFechaEspanol(date('Y-m-d'))) ?></div>

    <div class="dir">
      Señores(as) MIEMBROS DEL TRIBUNAL EVALUADOR y
      <strong><?= htmlspecialchars($defensa['estudiante_apellido'] . ' ' . $defensa['estudiante_nombre']) ?></strong>,<br>
      Presente. -
    </div>

    <div class="asunto">
      <strong>ASUNTO:</strong> CITACIÓN FORMAL A DEFENSA DE
      <?= htmlspecialchars(mb_strtoupper($defensa['modalidad_nombre'])) ?>
      — COHORTE <?= htmlspecialchars($defensa['nombre_periodo']) ?>
    </div>

    <p class="ptarrafo">
      Por medio de la presente se cita formalmente a los docentes del tribunal evaluador y al
      estudiante postulante, para la defensa pública de la Modalidad de Grado
      <strong><?= htmlspecialchars($defensa['modalidad_nombre']) ?></strong>, según el siguiente detalle:
    </p>

    <table class="datos">
      <tr>
        <td class="lbl">Estudiante</td>
        <td><?= htmlspecialchars($defensa['estudiante_apellido'] . ' ' . $defensa['estudiante_nombre']) ?>
            (RU: <?= htmlspecialchars($defensa['registro_universitario']) ?>)</td>
      </tr>
      <tr>
        <td class="lbl">Carrera</td>
        <td><?= htmlspecialchars($defensa['nombre_carrera']) ?></td>
      </tr>
      <tr>
        <td class="lbl">Tutor asignado</td>
        <td><?= $defensa['id_tutor_activo'] ? htmlspecialchars($defensa['tutor_apellido'] . ' ' . $defensa['tutor_nombre']) : '—' ?></td>
      </tr>
      <tr>
        <td class="lbl">Tribunal evaluador</td>
        <td>
          <?php foreach ($tribunales as $i => $t): ?>
            <div><?= $i + 1 ?>. <?= htmlspecialchars(ucfirst($t['rol'])) ?>: <?= htmlspecialchars($t['apellido'] . ' ' . $t['nombre']) ?></div>
          <?php endforeach; ?>
        </td>
      </tr>
      <tr>
        <td class="lbl">Lugar (ambiente)</td>
        <td><?= htmlspecialchars((string) $defensa['ambiente_nombre']) ?><?= $defensa['ambiente_ubicacion'] ? ' — ' . htmlspecialchars($defensa['ambiente_ubicacion']) : '' ?></td>
      </tr>
      <tr>
        <td class="lbl">Fecha y hora</td>
        <td><?= htmlspecialchars(mgFechaEspanol($defensa['fecha_defensa'])) ?>, de <?= htmlspecialchars($defensa['hora_inicio']) ?> a <?= htmlspecialchars($defensa['hora_fin']) ?></td>
      </tr>
    </table>

    <p class="ptarrafo">
      Se solicita puntual asistencia. La inasistencia de los miembros del tribunal será registrada
      para los fines académicos correspondientes.
    </p>

    <p class="ptarrafo atentamente">Se despide atentamente,</p>

    <div class="firmas">
      <div class="firma-bloque">
        <div class="firma-linea">Coordinación de Modalidad de Grado</div>
        <div class="firma-rol"><?= htmlspecialchars($universidad) ?></div>
      </div>
    </div>

    <div class="pie">
      <span><?= htmlspecialchars($universidad) ?> — <?= htmlspecialchars($eslogan) ?></span>
      <span><?= htmlspecialchars($telefono) ?> · <?= htmlspecialchars($correo) ?></span>
    </div>
  </div>
</body>
</html>