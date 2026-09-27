<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../includes/auth.php';
requerirRol(['estudiante']);

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/EstudianteModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';
require_once __DIR__ . '/../../models/MgExpedienteModel.php';
require_once __DIR__ . '/../../models/MgCalendarioModel.php';
require_once __DIR__ . '/../../models/MgDefensaModel.php';
require_once __DIR__ . '/../../models/NotificationModel.php';
require_once __DIR__ . '/../../includes/mg_catalogos.php';

$idUsuario = (int) ($_SESSION['id_usuario'] ?? 0);
$estudianteModel = new EstudianteModel($pdo);
$estudiante = $estudianteModel->obtenerPorUsuario($idUsuario);

$expediente = null;
$expedientes = [];
$tutor = null;
$hitos = [];
$defensas = [];
$defensaProxima = null;
$avisos = [];
$avisosNoLeidos = 0;
$hitosCumplidos = 0;

if ($estudiante) {
    $idEstudiante = (int) $estudiante['id_estudiante'];
    $expedienteModel = new MgExpedienteModel($pdo);
    $calendarioModel = new MgCalendarioModel($pdo);
    $defensaModel = new MgDefensaModel($pdo);
    $notificationModel = new NotificationModel($pdo);

    $expedientes = $expedienteModel->obtenerPorEstudiante($idEstudiante);
    $expediente = $expedienteModel->obtenerActivoPorEstudiante($idEstudiante);
    $defensas = $defensaModel->obtenerPorEstudiante($idEstudiante);
    $avisos = $notificationModel->recientes($idUsuario, 6);
    $avisosNoLeidos = (int) $notificationModel->noLeidas($idUsuario);

    if ($expediente) {
        $tutor = $expedienteModel->asignacionActiva((int) $expediente['id_expediente_mg']);
        $hitos = $calendarioModel->listarPorCohorte((int) $expediente['id_cohorte_mg']);
    }

    foreach ($defensas as $defensa) {
        if ($defensa['estado'] === 'programada') {
            $defensaProxima = $defensa;
            break;
        }
    }
    foreach ($hitos as $hito) {
        if ($hito['fecha_hito'] < date('Y-m-d')) {
            $hitosCumplidos++;
        }
    }
}

$mgCompletada = $estudiante ? (new TutoriaModel($pdo))->mgCompletada((int) $estudiante['id_estudiante']) : false;

$tituloPagina = 'Modalidad de Grado - Estudiante - UPDS';
include __DIR__ . '/../layouts/header.php';

$hoy = date('Y-m-d');
$proximoHito = null;
foreach ($hitos as $hito) {
    if ($hito['fecha_hito'] >= $hoy) {
        $proximoHito = $hito;
        break;
    }
}
$diasParaDefensa = null;
if ($defensaProxima) {
    $diasParaDefensa = (int) floor((strtotime($defensaProxima['fecha_defensa']) - strtotime($hoy)) / 86400);
}
?>

<?php
$titulo = 'Modalidad de Grado';
$descripcion = 'Consulta el estado de tu expediente, tu tutor, el cronograma de tu cohorte y tus defensas.';
$icono = 'bi-mortarboard-fill';
$accion = '<a href="/controllers/notificaciones_listar.php" class="btn btn-outline-primary rounded-3 px-3"><i class="bi bi-bell me-1"></i>Mis avisos</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!$estudiante): ?>
  <div class="card card-custom shadow-sm">
    <div class="card-body">
      <?php
      $emptyIcono = 'bi-person-exclamation';
      $emptyTitulo = 'Tu perfil de estudiante está incompleto';
      $emptyTexto = 'No encontramos un registro de estudiante asociado a tu cuenta. Contacta al administrador del sistema.';
      $emptyAccion = '<a href="/views/estudiante/panel.php" class="btn btn-primary rounded-3">Volver a mi panel</a>';
      include __DIR__ . '/../partials/empty_state.php';
      ?>
    </div>
  </div>
<?php elseif (!$expediente): ?>
  <div class="card card-custom shadow-sm">
    <div class="card-body">
      <?php
      $emptyIcono = 'bi-folder2-open';
      $emptyTitulo = 'No tienes un expediente de Modalidad de Grado en curso';
      $emptyTexto = $expedientes
          ? 'Todos tus expedientes están cerrados o concluidos. Puedes revisar su historial en la sección de expedientes.'
          : 'Aún no solicitaste tu Modalidad de Grado. Valida tu comprobante de pago y solicita tu inscripción en una cohorte abierta.';
      $emptyAccion = $expedientes
          ? null
          : '<a href="/controllers/mg_comprobantes_registrar.php" class="btn btn-primary rounded-3"><i class="bi bi-file-earmark-check me-1"></i>Mis comprobantes</a>';
      include __DIR__ . '/../partials/empty_state.php';
      ?>
    </div>
  </div>
<?php else: ?>

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <?php
      $kpiClase = 'primary'; $kpiIcono = 'bi-folder2-open'; $kpiValor = $expediente['id_expediente_mg']; $kpiEtiqueta = 'N° de expediente';
      include __DIR__ . '/../partials/kpi_card.php';
      ?>
    </div>
    <div class="col-6 col-md-3">
      <?php
      $kpiClase = 'info'; $kpiIcono = 'bi-calendar-event'; $kpiValor = $hitosCumplidos . '/' . count($hitos); $kpiEtiqueta = 'Hitos cumplidos';
      include __DIR__ . '/../partials/kpi_card.php';
      ?>
    </div>
    <div class="col-6 col-md-3">
      <?php
      $kpiClase = $defensaProxima ? 'warning' : 'secondary'; $kpiIcono = 'bi-calendar2-check';
      $kpiValor = $diasParaDefensa === null ? '—' : $diasParaDefensa; $kpiEtiqueta = $diasParaDefensa === null ? 'Sin defensa programada' : 'Días para tu defensa';
      include __DIR__ . '/../partials/kpi_card.php';
      ?>
    </div>
    <div class="col-6 col-md-3">
      <?php
      $kpiClase = $avisosNoLeidos > 0 ? 'danger' : 'success'; $kpiIcono = 'bi-bell'; $kpiValor = $avisosNoLeidos; $kpiEtiqueta = 'Avisos sin leer';
      include __DIR__ . '/../partials/kpi_card.php';
      ?>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
      <div class="card card-custom shadow-sm h-100">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-folder2-open me-1 text-primary"></i>Mi expediente</h5>
          <?= mgEstadoExpedienteBadge($expediente['estado']) ?>
        </div>
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-sm-5 text-muted fw-semibold">Modalidad de grado</dt>
            <dd class="col-sm-7"><?= htmlspecialchars($expediente['modalidad']) ?></dd>

            <dt class="col-sm-5 text-muted fw-semibold">Flujo</dt>
            <dd class="col-sm-7"><?= htmlspecialchars(mgFlujoLabel((string) $expediente['modalidad_flujo'])) ?></dd>

            <dt class="col-sm-5 text-muted fw-semibold">Cohorte</dt>
            <dd class="col-sm-7">
              <?= htmlspecialchars($expediente['cohorte']) ?>
              <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle ms-1"><?= htmlspecialchars(ucfirst($expediente['cohorte_estado'])) ?></span>
            </dd>

            <dt class="col-sm-5 text-muted fw-semibold">Período</dt>
            <dd class="col-sm-7 mb-0">
              <?= htmlspecialchars(mgFechaEspanol($expediente['cohorte_inicio'])) ?>
              <?= $expediente['cohorte_fin'] ? ' — ' . htmlspecialchars(mgFechaEspanol($expediente['cohorte_fin'])) : '' ?>
            </dd>
          </dl>
          <hr>
          <dl class="row mb-0 small">
            <dt class="col-sm-5 text-muted fw-semibold">Registro universitario</dt>
            <dd class="col-sm-7"><?= htmlspecialchars((string) $expediente['registro_universitario']) ?></dd>

            <dt class="col-sm-5 text-muted fw-semibold">Carrera</dt>
            <dd class="col-sm-7"><?= htmlspecialchars($expediente['nombre_carrera']) ?></dd>

            <dt class="col-sm-5 text-muted fw-semibold">Fecha de solicitud</dt>
            <dd class="col-sm-7"><?= htmlspecialchars(mgFechaEspanol($expediente['fecha_solicitud'])) ?></dd>

            <?php if ($expediente['fecha_validacion']): ?>
              <dt class="col-sm-5 text-muted fw-semibold">Fecha de validación</dt>
              <dd class="col-sm-7"><?= htmlspecialchars(mgFechaEspanol($expediente['fecha_validacion'])) ?></dd>
            <?php endif; ?>

            <?php if (!empty($expediente['resolucion_admin'])): ?>
              <dt class="col-sm-5 text-muted fw-semibold">Resolución</dt>
              <dd class="col-sm-7 mb-0"><i class="bi bi-patch-check-fill text-success me-1"></i><?= htmlspecialchars($expediente['resolucion_admin']) ?></dd>
            <?php endif; ?>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="card card-custom shadow-sm h-100">
        <div class="card-header bg-white py-3">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-video3 me-1 text-primary"></i>Mi tutor</h5>
        </div>
        <div class="card-body">
          <?php if (!$tutor): ?>
            <div class="text-center py-4">
              <span class="avatar avatar-default mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.5rem;" aria-hidden="true"><i class="bi bi-hourglass-split"></i></span>
              <p class="fw-semibold text-dark mb-1">Aún no tienes un tutor asignado</p>
              <p class="text-muted small mb-0">La Dirección Académica asignará tu docente cuando tu expediente sea validado. Recibirás un aviso en cuanto ocurra.</p>
            </div>
          <?php else: ?>
            <div class="d-flex align-items-center gap-3 mb-3">
              <?= avatar($tutor['tutor_nombre'], '', 'tutor') ?>
              <div>
                <div class="fw-bold text-dark"><?= htmlspecialchars($tutor['tutor_nombre']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars((string) $tutor['tutor_especialidad']) ?></div>
              </div>
            </div>
            <dl class="row mb-0 small">
              <dt class="col-sm-5 text-muted fw-semibold">Correo</dt>
              <dd class="col-sm-7 mb-0 text-break"><?= htmlspecialchars((string) $tutor['tutor_correo']) ?></dd>
            </dl>
            <div class="alert alert-light border small mt-3 mb-0">
              <i class="bi bi-info-circle me-1 text-primary"></i>La asignación es activa desde el <?= htmlspecialchars(mgFechaEspanol($tutor['fecha_asignacion'])) ?>.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-custom shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
      <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-event me-1 text-primary"></i>Cronograma de mi cohorte</h5>
      <?php if ($proximoHito): ?>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
          Próximo: <?= htmlspecialchars($proximoHito['titulo']) ?>
        </span>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <?php if (!$hitos): ?>
        <?php
        $emptyIcono = 'bi-calendar-x';
        $emptyTitulo = 'La cohorte aún no tiene cronograma publicado';
        $emptyTexto = 'La Dirección Académica publicará los hitos de inscripción, perfil, defensa y cierre.';
        include __DIR__ . '/../partials/empty_state.php';
        ?>
      <?php else: ?>
        <div class="list-group list-group-flush">
          <?php foreach ($hitos as $hito):
              $cumplido = $hito['fecha_hito'] < $hoy;
              $esProximo = $proximoHito && (int) $proximoHito['id_hito'] === (int) $hito['id_hito'];
          ?>
            <div class="list-group-item px-0 d-flex flex-column flex-md-row align-items-md-center gap-3 <?= $esProximo ? 'bg-light bg-opacity-50' : '' ?>">
              <div class="d-flex align-items-center gap-3 flex-grow-1">
                <span class="avatar <?= $cumplido ? 'avatar-admin' : 'avatar-default' ?>" aria-hidden="true">
                  <i class="bi <?= $cumplido ? 'bi-check-lg' : 'bi-hourglass' ?>"></i>
                </span>
                <div>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars($hito['titulo']) ?></div>
                  <?php if (!empty($hito['descripcion'])): ?>
                    <div class="text-muted small"><?= htmlspecialchars($hito['descripcion']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <?= mgTipoHitoBadge($hito['tipo_hito']) ?>
                <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars(mgFechaEspanol($hito['fecha_hito'])) ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="card card-custom shadow-sm h-100">
        <div class="card-header bg-white py-3">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar2-check me-1 text-primary"></i>Mis defensas y citaciones</h5>
        </div>
        <div class="card-body">
          <?php if (!$defensas): ?>
            <?php
            $emptyIcono = 'bi-calendar2-x';
            $emptyTitulo = 'No tienes defensas programadas';
            $emptyTexto = 'Cuando la Dirección Académica programe tu defensa recibirás un aviso con la fecha, la hora, el ambiente y el tribunal.';
            include __DIR__ . '/../partials/empty_state.php';
            ?>
          <?php else: ?>
            <div class="accordion" id="acordeonDefensas">
              <?php foreach ($defensas as $idx => $defensa): ?>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button <?= $idx === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse"
                            data-bs-target="#defensa<?= (int) $defensa['id_defensa_mg'] ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>"
                            aria-controls="defensa<?= (int) $defensa['id_defensa_mg'] ?>">
                      <span class="d-flex flex-wrap align-items-center gap-2 me-2">
                        <span class="fw-semibold text-dark"><?= htmlspecialchars(mgFechaEspanol($defensa['fecha_defensa'])) ?></span>
                        <?= mgDefensaEstadoBadge($defensa['estado']) ?>
                        <?= mgResultadoBadge($defensa['resultado']) ?>
                      </span>
                    </button>
                  </h2>
                  <div id="defensa<?= (int) $defensa['id_defensa_mg'] ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>"
                       data-bs-parent="#acordeonDefensas">
                    <div class="accordion-body">
                      <dl class="row mb-0 small">
                        <dt class="col-sm-4 text-muted fw-semibold">Horario</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars(substr((string) $defensa['hora_inicio'], 0, 5)) ?> — <?= htmlspecialchars(substr((string) $defensa['hora_fin'], 0, 5)) ?></dd>

                        <dt class="col-sm-4 text-muted fw-semibold">Ambiente</dt>
                        <dd class="col-sm-8">
                          <?php if ($defensa['ambiente_nombre']): ?>
                            <?= htmlspecialchars($defensa['ambiente_nombre']) ?>
                            <small class="text-muted d-block"><?= htmlspecialchars((string) $defensa['ambiente_ubicacion']) ?></small>
                          <?php else: ?>
                            <span class="text-muted">Por definir</span>
                          <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4 text-muted fw-semibold">Citación</dt>
                        <dd class="col-sm-8">
                          <?php if ($defensa['correlativo']): ?>
                            <span class="fw-semibold text-primary"><?= htmlspecialchars($defensa['correlativo']) ?></span>
                          <?php else: ?>
                            <span class="text-muted">Aún no emitida</span>
                          <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4 text-muted fw-semibold">Tribunal</dt>
                        <dd class="col-sm-8">
                          <?php if (!empty($defensa['tribunal_nombres'])): ?>
                            <?= htmlspecialchars($defensa['tribunal_nombres']) ?>
                            <small class="text-muted">(<?= (int) $defensa['num_tribunales'] ?> docente(s))</small>
                          <?php else: ?>
                            <span class="text-muted">Por designar</span>
                          <?php endif; ?>
                        </dd>

                        <?php if ($defensa['nota_final'] !== null): ?>
                          <dt class="col-sm-4 text-muted fw-semibold">Nota final</dt>
                          <dd class="col-sm-8 mb-0 fw-bold"><?= htmlspecialchars(number_format((float) $defensa['nota_final'], 2)) ?> / 100</dd>
                        <?php endif; ?>
                      </dl>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="card card-custom shadow-sm h-100">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-bell me-1 text-primary"></i>Avisos recientes</h5>
          <a href="/controllers/notificaciones_listar.php" class="small text-decoration-none">Ver todos</a>
        </div>
        <div class="card-body p-0">
          <?php if (!$avisos): ?>
            <div class="p-4">
              <?php
              $emptyIcono = 'bi-bell-slash';
              $emptyTitulo = 'Sin avisos por ahora';
              $emptyTexto = 'Te avisaremos cuando cambie el estado de tu expediente, se te asigne un tutor o se programe tu defensa.';
              include __DIR__ . '/../partials/empty_state.php';
              ?>
            </div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($avisos as $aviso): ?>
                <li class="list-group-item px-3 d-flex gap-3 <?= (int) $aviso['leida'] === 0 ? 'bg-primary bg-opacity-5' : '' ?>">
                  <i class="bi <?= htmlspecialchars(mgAvisoIcono($aviso['tipo']), ENT_QUOTES, 'UTF-8') ?> fs-5 <?= htmlspecialchars(mgAvisoClase($aviso['tipo']), ENT_QUOTES, 'UTF-8') ?>"></i>
                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                      <span class="fw-semibold small text-dark"><?= htmlspecialchars(mgAvisoLabel($aviso['tipo'])) ?></span>
                      <?php if ((int) $aviso['leida'] === 0): ?>
                        <span class="badge rounded-pill bg-danger text-white">Nuevo</span>
                      <?php endif; ?>
                    </div>
                    <div class="small text-muted"><?= htmlspecialchars($aviso['mensaje']) ?></div>
                    <div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i><?= htmlspecialchars(mgFechaEspanol($aviso['fecha'])) ?></div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

<?php endif; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
