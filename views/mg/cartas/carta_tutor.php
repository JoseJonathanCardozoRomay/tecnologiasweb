<?php
/** Vista de impresión A4: Carta de Asignación de Tutor (HU-027). */
$universidad = $datosPagina['universidad'];
$eslogan = $datosPagina['eslogan'];
$telefono = $datosPagina['telefono'];
$correo = $datosPagina['correo'];
$plazoCarta = $datosPagina['plazo_carta'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carta de Asignación de Tutor - <?= htmlspecialchars($expediente['estudiante']) ?></title>
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
    .asunto { margin: 6px 0 18px; font-size: .95rem; }
    .tema { margin-bottom: 4px; font-size: .95rem; }
    .ptarrafo { text-align: justify; font-size: .95rem; line-height: 1.65; margin-bottom: 12px; }
    .lista { margin: 4px 0 14px 22px; font-size: .95rem; line-height: 1.8; }
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
    <a href="/controllers/mg_expediente.php?id=<?= (int) $expediente['id_expediente_mg'] ?>">← Volver al expediente</a>
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
        <div class="num"><?= htmlspecialchars($asignacion['correlativo_carta']) ?></div>
        <div>Coordinación de Modalidad de Grado</div>
      </div>
    </div>

    <div class="fecha"><?= htmlspecialchars(mgFechaEspanol(date('Y-m-d'))) ?></div>

    <div class="dir">
      Señor(a): <strong><?= htmlspecialchars($expediente['estudiante']) ?></strong><br>
      Registro Universitario: <?= htmlspecialchars($expediente['estudiante_ru']) ?><br>
      Carrera: <?= htmlspecialchars($expediente['carrera']) ?> · Semestre: <?= (int) $expediente['estudiante_semestre'] ?>
    </div>

    <div class="asunto">
      <strong>ASUNTO:</strong> ASIGNACIÓN DE TUTOR DE MODALIDAD DE GRADO
      — <?= htmlspecialchars(mb_strtoupper($expediente['modalidad'])) ?>
    </div>

    <p class="ptarrafo">
      En el marco del plan académico de la Modalidad de Grado correspondiente a la cohorte
      <strong><?= htmlspecialchars($expediente['cohorte']) ?></strong>, la Coordinación de Modalidad de Grado
      tiene a bien comunicarle que, mediante expediente <strong>Nº
      <?= (int) $expediente['id_expediente_mg'] ?></strong>, se le ha asignado como tutor
      de su proyecto al docente:
    </p>

    <p class="tema">
      <strong><?= htmlspecialchars($asignacion['tutor_nombre']) ?></strong>
      <?php if ($asignacion['tutor_especialidad']): ?>
        — <span title="Especialidad"><?= htmlspecialchars($asignacion['tutor_especialidad']) ?></span>
      <?php endif; ?>
      (correo: <?= htmlspecialchars($asignacion['tutor_correo']) ?>)
    </p>

    <p class="ptarrafo">
      La asignación fue registrada el día <?= htmlspecialchars(mgFechaEspanol($asignacion['fecha_asignacion'])) ?>.
      Se solicita al estudiante presentarse ante el tutor asignado dentro de un plazo máximo de
      <strong><?= (int) $plazoCarta ?> días hábiles</strong> para acordar el plan de trabajo, según el perfil
      de su tema: <em><?= htmlspecialchars((string) ($expediente['solicitud_detalle'] ?: 'Sin descripción registrada.')) ?></em>.
    </p>

    <p class="ptarrafo atentamente">
      Sin otro particular, reciba un cordial saludo.
    </p>

    <div class="firmas">
      <div class="firma-bloque">
        <div class="firma-linea"><?= htmlspecialchars($asignacion['tutor_nombre']) ?></div>
        <div class="firma-rol">TUTOR ASIGNADO</div>
      </div>
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