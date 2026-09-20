<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>

<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div>
        <span class="eyebrow">Gestión académica</span>
        <h1 class="page-title mb-1">
            <?= esTutor() ? 'Solicitudes y sesiones' : (esEstudiante() ? 'Mis tutorías' : 'Tutorías') ?>
        </h1>
        <p class="text-muted mb-0">
            Seguimiento de solicitudes, confirmaciones, sesiones y cancelaciones.
        </p>
    </div>

    <!-- Solo los estudiantes pueden solicitar tutorías desde este módulo.
         El administrador supervisa y el tutor gestiona las solicitudes. -->
    <?php if (esEstudiante()): ?>
        <a href="tutorias_crear.php" class="btn btn-primary">
            <i class="bi bi-calendar-plus me-1"></i>Solicitar tutoría
        </a>
    <?php endif; ?>
</div>

<?php if (esAdministrador()): ?>
    <div class="alert alert-info border-0 shadow-sm">
        <i class="bi bi-shield-check me-2"></i>
        Como administrador puedes supervisar las tutorías y cancelarlas si es necesario.
        Las confirmaciones, rechazos y sesiones realizadas son gestionadas por el tutor asignado.
    </div>
<?php endif; ?>

<div class="card card-custom p-3 mb-3">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
            <label class="small text-muted">Estado</label>
        </div>
        <div class="col-md-3">
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <?php foreach (['pendiente','confirmada','rechazada','realizada','cancelada'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filtro === $s ? 'selected' : '' ?>>
                        <?= e(estadoEtiqueta($s)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary">Filtrar</button>
        </div>
        <div class="col-auto">
            <a href="tutorias_listar.php" class="btn btn-light">Limpiar</a>
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha / hora</th>
                    <th>Estudiante</th>
                    <th>Tutor</th>
                    <th>Materia</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($registros as $r): ?>
                <tr>
                    <td>
                        <strong><?= date('d/m/Y', strtotime($r['fecha'])) ?></strong><br>
                        <small><?= e(substr($r['hora_inicio'], 0, 5)) ?> - <?= e(substr($r['hora_fin'], 0, 5)) ?></small>
                    </td>

                    <td><?= e($r['estudiante'] ?? '—') ?></td>
                    <td><?= e($r['tutor'] ?? '—') ?></td>
                    <td><?= e($r['nombre_materia']) ?></td>
                    <td><?= ucfirst(e($r['modalidad'])) ?></td>

                    <td>
                        <span class="badge text-bg-<?= estadoBadge($r['estado']) ?>">
                            <?= e(estadoEtiqueta($r['estado'])) ?>
                        </span>

                        <?php if ($r['estado'] === 'cancelada' && !empty($r['motivo_cancelacion'])): ?>
                            <div class="small text-muted mt-1" style="max-width:260px;">
                                <strong>Motivo:</strong>
                                <?= e($r['motivo_cancelacion']) ?>
                            </div>
                            <?php if (!empty($r['fecha_cancelacion'])): ?>
                                <div class="small text-muted">
                                    Cancelada: <?= e(date('d/m/Y H:i', strtotime($r['fecha_cancelacion']))) ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>

                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1 flex-wrap">

                            <!-- El administrador NO edita tutorías. -->
                            <?php if (esTutor() && $r['estado'] === 'pendiente'): ?>
                                <form method="POST" action="tutorias_accion.php">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_tutoria" value="<?= $r['id_tutoria'] ?>">
                                    <button name="accion" value="confirmar" class="btn btn-sm btn-outline-success" title="Confirmar">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>

                                <form method="POST" action="tutorias_accion.php">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_tutoria" value="<?= $r['id_tutoria'] ?>">
                                    <button name="accion" value="rechazar" class="btn btn-sm btn-outline-danger" title="Rechazar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if (esTutor() && $r['estado'] === 'confirmada'): ?>
                                <form method="POST" action="tutorias_accion.php">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_tutoria" value="<?= $r['id_tutoria'] ?>">
                                    <button name="accion" value="realizar" class="btn btn-sm btn-outline-success" title="Marcar realizada">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if (
                                (esTutor() || esEstudiante() || esAdministrador())
                                && in_array($r['estado'], ['pendiente', 'confirmada'], true)
                            ): ?>
                                <!-- La cancelación exige un motivo. -->
                                <form
                                    method="POST"
                                    action="tutorias_accion.php"
                                    class="d-flex align-items-center gap-1"
                                    onsubmit="return validarMotivoCancelacion(this);"
                                >
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_tutoria" value="<?= $r['id_tutoria'] ?>">
                                    <input
                                        type="text"
                                        name="motivo_cancelacion"
                                        class="form-control form-control-sm"
                                        style="max-width:210px;"
                                        minlength="5"
                                        maxlength="500"
                                        placeholder="Motivo de cancelación"
                                        required
                                    >
                                    <button name="accion" value="cancelar" class="btn btn-sm btn-outline-secondary" title="Cancelar tutoría">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$registros): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar2-x fs-1 d-block mb-2"></i>
                        No hay tutorías para mostrar.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function validarMotivoCancelacion(formulario) {
    const campo = formulario.querySelector('[name="motivo_cancelacion"]');
    const motivo = campo ? campo.value.trim() : '';

    if (motivo.length < 5) {
        alert('Indica un motivo de cancelación de al menos 5 caracteres.');
        campo?.focus();
        return false;
    }

    return confirm(
        'La tutoría quedará registrada como cancelada. ¿Deseas continuar?'
    );
}
</script>

<?php include __DIR__.'/../layouts/footer.php'; ?>
