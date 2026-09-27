<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Asignar Tribunales - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Tutorías', 'url' => '/controllers/tutorias_listar.php'],
    ['texto' => 'Asignar Tribunales', 'actual' => true],
];
$titulo = 'Asignar Tribunales';
$descripcion = 'Asigna o actualiza los tribunales de las tutorías de Modalidad de Grado. Solo se muestran docentes de la carrera del estudiante; el Tribunal 1 y 2 son obligatorios y puedes integrar hasta 5 por expediente.';
$icono = 'bi-people-fill';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo completar la acción:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-1 text-primary"></i>Tutorías elegibles</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Tutoría</th>
              <th>Tutor</th>
              <th>Estudiante</th>
              <th>Estado</th>
              <th class="text-end pe-4">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($tutorias)): ?>
              <tr>
                <td colspan="5" class="text-center py-5">
                  <?php $emptyTitulo='No hay tutorías elegibles'; $emptyTexto='Las tutorías activas (asignadas, aceptadas o en proceso) aparecerán aquí.'; include __DIR__ . '/../partials/empty_state.php'; ?>
                </td>
              </tr>
            <?php endif; ?>
            <?php foreach ($tutorias as $t): ?>
              <tr class="<?= ((int) ($id_tutoria ?? 0) === (int) $t['id_tutoria']) ? 'table-primary' : '' ?>">
                <td class="ps-4 fw-bold text-dark">#<?= $t['id_tutoria'] ?></td>
                <td>
                  <div>Prof. <?= htmlspecialchars($t['tutor_nombre'] . ' ' . $t['tutor_apellido']) ?></div>
                </td>
                <td class="text-muted"><?= htmlspecialchars($t['estudiante_nombre'] . ' ' . $t['estudiante_apellido']) ?></td>
                <td><span class="badge rounded-pill border px-3 py-1 badge-accent"><?= str_replace('_', ' ', ucfirst($t['estado'])) ?></span></td>
                <td class="text-end pe-4">
                  <a href="tribunales_asignar.php?id=<?= $t['id_tutoria'] ?>" class="btn btn-sm <?= ((int) ($id_tutoria ?? 0) === (int) $t['id_tutoria']) ? 'btn-primary' : 'btn-outline-primary' ?> rounded-3"><i class="bi bi-people me-1"></i>Tribunales</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <?php if ($id_tutoria > 0): ?>
      <?php
      $idsAsignados = array_map('intval', array_column($tribunalesAsignados, 'id_usuario'));
      $maxTribunales = $tribunalModel->maximoTribunales();
      $minBaseTribunales = $tribunalModel->cantidadPorTutoriaEsperada();
      $nombreCarrera = htmlspecialchars($infoTutoria['nombre_carrera'] ?? '');
      $estudianteTxt = htmlspecialchars(trim(($infoTutoria['estudiante_nombre'] ?? '') . ' ' . ($infoTutoria['estudiante_apellido'] ?? '')));
      ?>
      <?php
      $opcionDocente = function (int $excluir = 0) use ($docentesDisponibles, $idsAsignados): void {
          foreach ($docentesDisponibles as $d):
              $ocupado = in_array((int) $d['id_usuario'], $idsAsignados, true) && (int) $d['id_usuario'] !== $excluir;
              $texto = 'Prof. ' . $d['nombre'] . ' ' . $d['apellido'] . ' — ' . $d['especialidad'];
              if (!empty($d['materias_carrera'])) {
                  $texto .= ' · ' . $d['materias_carrera'];
              }
              ?>
              <option value="<?= (int) $d['id_usuario'] ?>" <?= $ocupado ? 'disabled' : '' ?>><?= htmlspecialchars($texto) ?></option>
          <?php endforeach;
      }; ?>
      <div class="card card-custom shadow-sm mb-3">
        <div class="card-header bg-white py-3">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill me-1 text-primary"></i>Asignar tribunales — Tutoría #<?= (int) $id_tutoria ?></h5>
          <small class="text-muted"><?= $estudianteTxt ?> · <?= $nombreCarrera ?></small>
        </div>
        <div class="card-body">
          <div class="alert alert-info rounded-3 py-2 px-3 small mb-3">
            <i class="bi bi-info-circle me-1"></i>Solo se muestran docentes de la carrera <strong><?= $nombreCarrera ?></strong>. El tutor de esta tutoría queda excluido automáticamente. Tribunal 1 y 2 son obligatorios; puedes agregar hasta <?= $maxTribunales ?>.
          </div>

          <form method="GET" action="tribunales_asignar.php" class="input-group input-group-sm mb-3" autocomplete="off">
            <input type="hidden" name="id" value="<?= (int) $id_tutoria ?>">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="search" name="q_tribunal" value="<?= htmlspecialchars($filtroTribunal) ?>" class="form-control border-start-0" placeholder="Filtrar por especialidad o materia (ej. Suelos, Estructuras, Programación)">
            <button class="btn btn-primary" type="submit">Buscar</button>
            <?php if ($filtroTribunal !== ''): ?>
              <a href="tribunales_asignar.php?id=<?= (int) $id_tutoria ?>" class="btn btn-outline-secondary" title="Limpiar filtro"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
          </form>

          <?php if (empty($docentesDisponibles)): ?>
            <div class="alert alert-warning rounded-3 py-2 px-3 small mb-3">
              <i class="bi bi-exclamation-triangle me-1"></i>
              <?php if ($filtroTribunal !== ''): ?>
                No hay docentes de la carrera <?= $nombreCarrera ?> que coincidan con «<?= htmlspecialchars($filtroTribunal) ?>».
              <?php else: ?>
                No hay docentes registrados en la carrera <?= $nombreCarrera ?> para asignar como tribunal.
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="tribunales_asignar.php" autocomplete="off" id="formTribunales">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="accion" value="asignar">
            <input type="hidden" name="id" value="<?= (int) $id_tutoria ?>">

            <div id="contenedorTribunales">
              <div class="mb-3 campa-tribunal" data-num="1">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Tribunal 1 *</label>
                <select name="tribunal_1" class="form-select rounded-3 py-2 tutoria-tribunal-select" required>
                  <option value="">Selecciona un docente</option>
                  <?php $opcionDocente(); ?>
                </select>
              </div>

              <div class="mb-3 campa-tribunal" data-num="2">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Tribunal 2 *</label>
                <select name="tribunal_2" class="form-select rounded-3 py-2 tutoria-tribunal-select" required>
                  <option value="">Selecciona un docente distinto</option>
                  <?php $opcionDocente(); ?>
                </select>
              </div>

              <?php for ($n = 3; $n <= $maxTribunales; $n++): ?>
                <div class="mb-3 d-none campa-tribunal-opcional" data-num="<?= $n ?>">
                  <div class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1">
                      <label class="form-label fw-semibold text-secondary small text-uppercase">Tribunal <?= $n ?> <span class="text-muted">(opcional)</span></label>
                      <select name="tribunal_<?= $n ?>" class="form-select rounded-3 py-2 tutoria-tribunal-select">
                        <option value="">— Sin tribunal <?= $n ?> —</option>
                        <?php $opcionDocente(); ?>
                      </select>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 quitar-tribunal mb-0" title="Eliminar este tribunal opcional"><i class="bi bi-x-lg"></i></button>
                  </div>
                </div>
              <?php endfor; ?>
            </div>

            <div class="d-flex flex-column gap-2 mb-3">
              <button type="button" id="btnAgregarTribunal" class="btn btn-sm btn-outline-primary rounded-3 d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-plus-lg"></i>Agregar tribunal
              </button>
              <button type="submit" class="btn btn-primary w-100 rounded-3 py-2"><i class="bi bi-check2-circle me-1"></i>Registrar tribunales</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card card-custom shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-check me-1 text-primary"></i>Tribunales asignados</h5>
          <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle"><?= count($tribunalesAsignados) ?>/<?= $maxTribunales ?></span>
        </div>
        <div class="card-body d-flex flex-column gap-2">
          <?php if (empty($tribunalesAsignados)): ?>
            <p class="text-muted small mb-0">Esta tutoría aún no tiene tribunales asignados. Completa el formulario superior para asignarlos.</p>
          <?php endif; ?>
          <?php foreach ($tribunalesAsignados as $i => $tr): ?>
            <?php $esBase = $i < $minBaseTribunales; ?>
            <div class="border rounded-3 p-2 d-flex align-items-center gap-2 bg-light">
              <span class="avatar avatar-tutor"><?= htmlspecialchars(strtoupper(substr($tr['nombre'], 0, 1) . substr($tr['apellido'], 0, 1))) ?></span>
              <div class="flex-grow-1">
                <div class="fw-medium">Prof. <?= htmlspecialchars($tr['nombre'] . ' ' . $tr['apellido']) ?></div>
                <small class="text-muted"><?= htmlspecialchars($tr['correo']) ?></small>
              </div>
              <span class="badge <?= $esBase ? 'bg-success bg-opacity-10 text-success border border-success-subtle' : 'bg-info bg-opacity-10 text-info border border-info-subtle' ?>">Tribunal <?= $i + 1 ?> (<?= $esBase ? 'obligatorio' : 'opcional' ?>)</span>
              <button class="btn btn-sm btn-outline-secondary rounded-3" type="button" data-bs-toggle="modal" data-bs-target="#editarTribunal<?= $tr['id_tribunal'] ?>" title="Cambiar o reemplazar este tribunal"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger rounded-3" type="button" <?= $esBase ? 'disabled' : '' ?> onclick="if(confirm('¿Eliminar este tribunal opcional?')){ this.closest('form').submit(); }" title="<?= $esBase ? 'Tribunales obligatorios: usa el lápiz para reemplazarlo' : 'Eliminar tribunal opcional' ?>"><i class="bi bi-trash"></i></button>
              <form method="POST" action="tribunales_asignar.php" class="d-none">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_tribunal" value="<?= $tr['id_tribunal'] ?>">
                <input type="hidden" name="id_usuario_miembro" value="<?= $tr['id_usuario'] ?>">
              </form>
            </div>

            <div class="modal fade" id="editarTribunal<?= $tr['id_tribunal'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form method="POST" class="modal-content">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="accion" value="editar">
                  <input type="hidden" name="id_tribunal" value="<?= $tr['id_tribunal'] ?>">
                  <div class="modal-header"><h5 class="modal-title">Modificar tribunal</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                  <div class="modal-body">
                    <p class="text-muted small">Reemplaza a <strong>Prof. <?= htmlspecialchars($tr['nombre'] . ' ' . $tr['apellido']) ?></strong> por otro docente de la carrera <?= $nombreCarrera ?>.</p>
                    <label class="form-label fw-semibold">Nuevo miembro *</label>
                    <select name="nuevo_miembro" class="form-select rounded-3 py-2 tutoria-tribunal-select" required>
                      <option value="">Selecciona un docente</option>
                      <?php $opcionDocente((int) $tr['id_usuario']); ?>
                    </select>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                  </div>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="card card-custom shadow-sm">
        <div class="card-body p-4">
          <?php $emptyTitulo='Selecciona una tutoría'; $emptyTexto='Elige una tutoría activa de la lista para asignar sus tribunales.'; $emptyIcono='bi-people'; include __DIR__ . '/../partials/empty_state.php'; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$idsTribunalesExistentes = $idsAsignados ?? [];
?>
<script>
  (function () {
    const contenedor = document.getElementById('contenedorTribunales');
    if (!contenedor) return;
    const btnAgregar = document.getElementById('btnAgregarTribunal');
    const excluidos = <?= json_encode($idsTribunalesExistentes) ?>;
    const opcionales = Array.from(contenedor.querySelectorAll('.campa-tribunal-opcional'));

    function sincronizarDuplicados(origen) {
      const elegido = origen ? parseInt(origen.value, 10) : 0;
      Array.from(document.querySelectorAll('.tutoria-tribunal-select')).forEach((destino) => {
        if (destino === origen) return;
        const valorDestino = parseInt(destino.value, 10);
        Array.from(destino.options).forEach((op) => {
          const val = parseInt(op.value, 10);
          const prohibido = excluidos.includes(val) || (elegido !== 0 && elegido === val);
          op.disabled = prohibido && !(valorDestino === val);
        });
      });
    }

    function actualizarBotones() {
      if (!btnAgregar) return;
      const ocultos = opcionales.filter((el) => el.classList.contains('d-none'));
      btnAgregar.classList.toggle('d-none', ocultos.length === 0);
    }

    btnAgregar.addEventListener('click', () => {
      const oculto = opcionales.find((el) => el.classList.contains('d-none'));
      if (!oculto) return;
      oculto.classList.remove('d-none');
      actualizarBotones();
      sincronizarDuplicados(null);
    });

    contenedor.addEventListener('click', (e) => {
      const btnQuitar = e.target.closest('.quitar-tribunal');
      if (!btnQuitar) return;
      const bloque = btnQuitar.closest('.campa-tribunal-opcional');
      bloque.classList.add('d-none');
      bloque.querySelector('select').value = '';
      sincronizarDuplicados(null);
      actualizarBotones();
    });

    document.addEventListener('change', (e) => {
      if (e.target.classList && e.target.classList.contains('tutoria-tribunal-select')) {
        sincronizarDuplicados(e.target);
      }
    });

    actualizarBotones();
    sincronizarDuplicados(null);
  })();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>