<?php

require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$escapar = static fn($valor): string => htmlspecialchars(
    (string) $valor,
    ENT_QUOTES,
    'UTF-8'
);

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">Tutorías por materia</p>
                    <h1>Solicitudes recibidas</h1>
                    <p>
                        Revisa las solicitudes y, si aceptas atender al
                        estudiante, propone fecha, horario y modalidad.
                        La propuesta debe ser aprobada por coordinación.
                    </p>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-<?= $escapar($tipoMensaje) ?>"
                    role="alert"
                >
                    <?= $escapar($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="list-summary">
                <span>
                    <?= count($solicitudes) ?>
                    <?= count($solicitudes) === 1
                        ? 'solicitud por atender'
                        : 'solicitudes por atender' ?>
                </span>
            </div>

            <?php if (empty($solicitudes)): ?>
                <div class="form-container">
                    <h2>No tienes solicitudes pendientes</h2>
                    <p>
                        Cuando un estudiante solicite una tutoría contigo,
                        aparecerá aquí.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <?php
                    $esObservada =
                        $solicitud['estado'] === 'observada';
                    ?>

                    <article class="form-container mb-4">
                        <div class="module-heading">
                            <div>
                                <p class="section-label">
                                    <?= $esObservada
                                        ? 'Solicitud devuelta para corrección'
                                        : 'Nueva solicitud' ?>
                                </p>

                                <h2>
                                    <?= $escapar(
                                        $solicitud['nombre_materia']
                                    ) ?>
                                </h2>

                                <p>
                                    Estudiante:
                                    <strong>
                                        <?= $escapar(
                                            $solicitud['nombre_estudiante']
                                            . ' '
                                            . $solicitud['apellido_estudiante']
                                        ) ?>
                                    </strong>
                                </p>
                            </div>
                        </div>

                        <?php if (
                            $esObservada
                            && !empty($solicitud['observacion_revision'])
                        ): ?>
                            <div class="alert alert-warning" role="alert">
                                <strong>Observaciones de coordinación:</strong>
                                <p class="mb-0">
                                    <?= nl2br(
                                        $escapar(
                                            $solicitud[
                                                'observacion_revision'
                                            ]
                                        )
                                    ) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <form
                            method="POST"
                            action="<?= $escapar($rutaBase) ?>controllers/solicitudes_tutor.php"
                            class="formulario-respuesta-tutor"
                        >
                            <?= campoCsrf() ?>

                            <input
                                type="hidden"
                                name="id_tutoria"
                                value="<?= (int) $solicitud['id_tutoria'] ?>"
                            >

                            <input
                                type="hidden"
                                name="accion"
                                value="aceptar"
                            >

                            <h3>
                                <?= $esObservada
                                    ? 'Corrige la propuesta'
                                    : 'Proponer horario y modalidad' ?>
                            </h3>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label
                                        for="fecha_<?= (int) $solicitud['id_tutoria'] ?>"
                                    >
                                        Fecha
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_<?= (int) $solicitud['id_tutoria'] ?>"
                                        name="fecha"
                                        min="<?= date('Y-m-d') ?>"
                                        value="<?= $escapar(
                                            $solicitud['fecha'] ?? ''
                                        ) ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label
                                        for="inicio_<?= (int) $solicitud['id_tutoria'] ?>"
                                    >
                                        Hora de inicio
                                    </label>
                                    <input
                                        type="time"
                                        id="inicio_<?= (int) $solicitud['id_tutoria'] ?>"
                                        name="hora_inicio"
                                        value="<?= $escapar(
                                            isset($solicitud['hora_inicio'])
                                                ? substr(
                                                    $solicitud['hora_inicio'],
                                                    0,
                                                    5
                                                )
                                                : ''
                                        ) ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label
                                        for="fin_<?= (int) $solicitud['id_tutoria'] ?>"
                                    >
                                        Hora de finalización
                                    </label>
                                    <input
                                        type="time"
                                        id="fin_<?= (int) $solicitud['id_tutoria'] ?>"
                                        name="hora_fin"
                                        value="<?= $escapar(
                                            isset($solicitud['hora_fin'])
                                                ? substr(
                                                    $solicitud['hora_fin'],
                                                    0,
                                                    5
                                                )
                                                : ''
                                        ) ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label
                                        for="modalidad_<?= (int) $solicitud['id_tutoria'] ?>"
                                    >
                                        Modalidad
                                    </label>
                                    <select
                                        id="modalidad_<?= (int) $solicitud['id_tutoria'] ?>"
                                        name="modalidad"
                                        class="selector-modalidad"
                                        required
                                    >
                                        <option value="">
                                            Selecciona una modalidad
                                        </option>
                                        <option
                                            value="presencial"
                                            <?= ($solicitud['modalidad'] ?? '')
                                                === 'presencial'
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            Presencial
                                        </option>
                                        <option
                                            value="virtual"
                                            <?= ($solicitud['modalidad'] ?? '')
                                                === 'virtual'
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            Virtual
                                        </option>
                                    </select>
                                </div>

                                <div
                                    class="form-group campo-aula"
                                    <?= ($solicitud['modalidad'] ?? '')
                                        === 'virtual'
                                        ? 'hidden'
                                        : '' ?>
                                >
                                    <label
                                        for="lugar_<?= (int) $solicitud['id_tutoria'] ?>"
                                    >
                                        Aula
                                    </label>
                                    <input
                                        type="text"
                                        id="lugar_<?= (int) $solicitud['id_tutoria'] ?>"
                                        name="lugar_o_enlace"
                                        maxlength="200"
                                        value="<?= $escapar(
                                            $solicitud['lugar_o_enlace'] ?? ''
                                        ) ?>"
                                        placeholder="Indica el aula"
                                        <?= ($solicitud['modalidad'] ?? '')
                                            !== 'virtual'
                                            ? 'required'
                                            : '' ?>
                                    >
                                </div>
                            </div>

                            <p class="field-help">
                                La modalidad virtual no requiere ingresar
                                enlace en esta etapa. Después coordinarás las
                                reuniones directamente con el estudiante.
                            </p>

                            <div class="form-actions">
                                <button
                                    type="submit"
                                    class="primary-action"
                                >
                                    Enviar a aprobación
                                </button>
                            </div>
                        </form>

                        <form
                            method="POST"
                            action="<?= $escapar($rutaBase) ?>controllers/solicitudes_tutor.php"
                            class="mt-3"
                            onsubmit="return confirm('¿Rechazar esta solicitud?');"
                        >
                            <?= campoCsrf() ?>

                            <input
                                type="hidden"
                                name="id_tutoria"
                                value="<?= (int) $solicitud['id_tutoria'] ?>"
                            >

                            <input
                                type="hidden"
                                name="accion"
                                value="rechazar"
                            >

                            <button
                                type="submit"
                                class="table-link delete-link"
                            >
                                Rechazar solicitud
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    document
        .querySelectorAll('.formulario-respuesta-tutor')
        .forEach((formulario) => {
            const selector = formulario.querySelector(
                '.selector-modalidad'
            );
            const campoAula = formulario.querySelector('.campo-aula');
            const entradaAula = campoAula.querySelector(
                '[name="lugar_o_enlace"]'
            );

            const actualizarAula = () => {
                const esPresencial = selector.value === 'presencial';

                campoAula.hidden = !esPresencial;
                entradaAula.required = esPresencial;

                if (!esPresencial) {
                    entradaAula.value = '';
                }
            };

            selector.addEventListener('change', actualizarAula);
            actualizarAula();
        });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
