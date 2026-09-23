<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="mb-4">
    <a href="mis_tutorias.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver a mis tutorías</a>
    <span class="eyebrow d-block mt-3">Tutoría personal</span>
    <h1 class="page-title mb-1">Solicitar acompañamiento de proyecto</h1>
    <p class="text-muted mb-0">Selecciona un tutor y consulta sus días y horas libres antes de enviar la solicitud.</p>
</div>

<?php if($errores): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach($errores as $err): ?><li><?=e($err)?></li><?php endforeach;?></ul></div>
<?php endif; ?>

<div class="card card-custom p-4">
<form method="post" id="formPersonal" autocomplete="off">
    <?=csrfField()?>
    <input type="hidden" name="fecha" id="fechaSeleccionada" value="<?=e($datos['fecha'])?>">
    <input type="hidden" name="hora_inicio" id="horaInicioSeleccionada" value="<?=e($datos['hora_inicio'])?>">
    <input type="hidden" name="hora_fin" id="horaFinSeleccionada" value="<?=e($datos['hora_fin'])?>">
    <input type="hidden" name="id_estudiante" value="<?=e($datos['id_estudiante'])?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="idProyecto">Proyecto de grado</label>
            <select id="idProyecto" name="id_proyecto" class="form-select tom-select" required>
                <?php if(!$proyectos): ?>
                    <option value="">Primero registra un proyecto</option>
                <?php else: ?>
                    <option value="">Seleccionar proyecto</option>
                    <?php foreach($proyectos as $p): ?>
                        <option value="<?=e($p['id_proyecto'])?>" <?=((string)$datos['id_proyecto']===(string)$p['id_proyecto'])?'selected':''?>><?=e($p['titulo'])?> — <?=e($p['nombre_carrera'])?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="idTutor">Tutor disponible</label>
            <select name="id_tutor" id="idTutor" class="form-select tom-select" required>
                <option value="">Seleccionar tutor</option>
                <?php foreach($tutores as $t): ?>
                    <option value="<?=e($t['id_tutor'])?>" <?=((string)$datos['id_tutor']===(string)$t['id_tutor'])?'selected':''?>><?=e(trim($t['apellido'].' '.$t['nombre']))?><?=!empty($t['especialidad'])?' — '.e($t['especialidad']):''?></option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">La disponibilidad se actualiza automáticamente al elegir el tutor.</div>
        </div>
    </div>

    <div class="availability-panel mt-4" id="availabilityPanel">
        <div class="availability-panel-header">
            <div>
                <span class="eyebrow">Disponibilidad</span>
                <h2 class="h6 mb-1">Días y horas libres del tutor</h2>
                <p class="text-muted small mb-0" id="availabilitySubtitle">Selecciona un tutor para consultar sus espacios de 1 hora.</p>
            </div>
            <div class="availability-legend"><span class="availability-dot"></span> Hora disponible</div>
        </div>
        <div id="availabilityLoading" class="availability-status d-none">
            <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Cargando disponibilidad…
        </div>
        <div id="availabilityEmpty" class="availability-empty">
            <i class="bi bi-calendar-week"></i>
            <strong>Selecciona un tutor</strong>
            <span>Verás aquí los próximos días que tienen al menos una hora libre.</span>
        </div>
        <div id="availabilityDays" class="availability-days"></div>
    </div>

    <div class="selected-slot mt-3" id="selectedSlotBox" aria-live="polite">
        <div class="selected-slot-icon"><i class="bi bi-calendar-check"></i></div>
        <div>
            <div class="small text-muted">Horario seleccionado</div>
            <strong id="selectedSlotText">Aún no has seleccionado una hora.</strong>
        </div>
        <button type="button" class="btn btn-sm btn-light ms-auto d-none" id="clearSlot">Cambiar</button>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-4">
            <label class="form-label" for="modalidad">Modalidad solicitada</label>
            <select name="modalidad" id="modalidad" class="form-select">
                <option value="presencial" <?=($datos['modalidad']??'')==='presencial'?'selected':''?>>Presencial</option>
                <option value="virtual" <?=($datos['modalidad']??'')==='virtual'?'selected':''?>>Virtual</option>
            </select>
        </div>
        <div class="col-md-8 d-flex align-items-end">
            <div class="alert alert-info border-0 w-100 mb-0 py-2">
                <i class="bi bi-shield-check me-2"></i>El <strong>aula o enlace</strong> será asignado por administración al confirmar la tutoría.
            </div>
        </div>
        <div class="col-12">
            <label class="form-label" for="observaciones">Observaciones</label>
            <textarea class="form-control" name="observaciones" id="observaciones" maxlength="1000" rows="4" placeholder="Describe brevemente lo que necesitas trabajar."><?=e($datos['observaciones'])?></textarea>
        </div>
    </div>

    <div class="alert alert-warning border-0 mt-4">
        <i class="bi bi-clock me-2"></i>Las tutorías personales duran <strong>1 hora</strong>. Los bloques 07–10, 15–18 y 19–22 corresponden únicamente a tutorías grupales.
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="mis_tutorias.php" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary" id="btnSolicitar" <?=!$proyectos?'disabled':''?>>
            <i class="bi bi-send me-1"></i>Solicitar tutoría personal
        </button>
    </div>
</form>
</div>

<script>
(() => {
    const tutorSelect = document.getElementById('idTutor');
    const panel = document.getElementById('availabilityPanel');
    const daysContainer = document.getElementById('availabilityDays');
    const emptyState = document.getElementById('availabilityEmpty');
    const loading = document.getElementById('availabilityLoading');
    const subtitle = document.getElementById('availabilitySubtitle');
    const dateInput = document.getElementById('fechaSeleccionada');
    const startInput = document.getElementById('horaInicioSeleccionada');
    const endInput = document.getElementById('horaFinSeleccionada');
    const selectedText = document.getElementById('selectedSlotText');
    const clearButton = document.getElementById('clearSlot');
    const submitButton = document.getElementById('btnSolicitar');
    let datosDisponibilidad = null;

    function limpiarSeleccion() {
        dateInput.value = '';
        startInput.value = '';
        endInput.value = '';
        selectedText.textContent = 'Aún no has seleccionado una hora.';
        clearButton.classList.add('d-none');
        document.querySelectorAll('.availability-slot.selected').forEach(el => el.classList.remove('selected'));
        submitButton.disabled = true;
    }

    function seleccionarSlot(fecha, textoDia, slot, button) {
        document.querySelectorAll('.availability-slot.selected').forEach(el => el.classList.remove('selected'));
        button.classList.add('selected');
        dateInput.value = fecha;
        startInput.value = slot.hora_inicio;
        endInput.value = slot.hora_fin;
        selectedText.textContent = `${textoDia} · ${slot.etiqueta}`;
        clearButton.classList.remove('d-none');
        submitButton.disabled = false;
    }

    function renderDays(data) {
        daysContainer.innerHTML = '';
        emptyState.classList.add('d-none');
        if (!data.dias || !data.dias.length) {
            emptyState.classList.remove('d-none');
            emptyState.innerHTML = '<i class="bi bi-calendar-x"></i><strong>No hay horas libres próximamente</strong><span>Prueba con otro tutor o revisa más adelante.</span>';
            submitButton.disabled = true;
            return;
        }

        subtitle.textContent = `${data.tutor} · próximos 28 días`;
        data.dias.forEach(day => {
            const card = document.createElement('div');
            card.className = 'availability-day';
            const disponibilidad = day.disponibilidad.map(d => `${d.hora_inicio}–${d.hora_fin}`).join(' · ');

            const slotsHtml = day.slots.map(slot => `
                <button type="button" class="availability-slot"
                    data-fecha="${day.fecha}"
                    data-dia="${day.dia_nombre} ${day.fecha_texto}"
                    data-inicio="${slot.hora_inicio}"
                    data-fin="${slot.hora_fin}"
                    data-etiqueta="${slot.etiqueta}">
                    <i class="bi bi-clock me-1"></i>${slot.etiqueta}
                </button>`).join('');

            card.innerHTML = `
                <div class="availability-day-heading">
                    <div>
                        <span class="availability-day-name">${day.dia_nombre}</span>
                        <strong>${day.fecha_texto}</strong>
                    </div>
                    <span class="badge text-bg-light border">${day.slots.length} hora${day.slots.length === 1 ? '' : 's'} libre${day.slots.length === 1 ? '' : 's'}</span>
                </div>
                <div class="small text-muted mb-2"><i class="bi bi-calendar2-range me-1"></i>Disponibilidad: ${disponibilidad || '—'}</div>
                <div class="availability-slots">${slotsHtml}</div>`;

            daysContainer.appendChild(card);
        });

        daysContainer.querySelectorAll('.availability-slot').forEach(button => {
            button.addEventListener('click', () => seleccionarSlot(
                button.dataset.fecha,
                button.dataset.dia,
                { hora_inicio: button.dataset.inicio, hora_fin: button.dataset.fin, etiqueta: button.dataset.etiqueta },
                button
            ));
        });
    }

    async function cargarDisponibilidad(idTutor) {
        limpiarSeleccion();
        daysContainer.innerHTML = '';
        if (!idTutor) {
            loading.classList.add('d-none');
            emptyState.classList.remove('d-none');
            emptyState.innerHTML = '<i class="bi bi-calendar-week"></i><strong>Selecciona un tutor</strong><span>Verás aquí los próximos días que tienen al menos una hora libre.</span>';
            subtitle.textContent = 'Selecciona un tutor para consultar sus espacios de 1 hora.';
            return;
        }

        loading.classList.remove('d-none');
        emptyState.classList.add('d-none');
        try {
            const response = await fetch(`/controllers/api_disponibilidad_personal.php?id_tutor=${encodeURIComponent(idTutor)}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            if (!data.ok) throw new Error(data.message || 'No se pudo consultar la disponibilidad.');
            datosDisponibilidad = data;
            renderDays(data);
        } catch (error) {
            daysContainer.innerHTML = '';
            emptyState.classList.remove('d-none');
            emptyState.innerHTML = `<i class="bi bi-exclamation-triangle"></i><strong>No se pudo cargar la disponibilidad</strong><span>${error.message}</span>`;
            subtitle.textContent = 'Intenta nuevamente con otro tutor.';
        } finally {
            loading.classList.add('d-none');
        }
    }

    tutorSelect.addEventListener('change', () => cargarDisponibilidad(tutorSelect.value));
    clearButton.addEventListener('click', limpiarSeleccion);

    document.getElementById('formPersonal').addEventListener('submit', event => {
        if (!dateInput.value || !startInput.value || !endInput.value) {
            event.preventDefault();
            alert('Selecciona un día y una hora disponible antes de enviar la solicitud.');
            return;
        }
        if (startInput.value === endInput.value) {
            event.preventDefault();
            alert('El horario seleccionado no es válido.');
        }
    });

    if (tutorSelect.value) cargarDisponibilidad(tutorSelect.value);
})();
</script>
<?php include __DIR__.'/../layouts/footer.php'; ?>
