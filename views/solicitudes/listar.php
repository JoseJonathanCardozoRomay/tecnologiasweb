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
                    <h1>Aprobación de tutorías</h1>
                    <p>
                        Revisa el horario y la modalidad propuestos por el
                        tutor. Puedes aprobar la tutoría o devolverla con
                        observaciones para que el tutor la corrija.
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
                        ? 'propuesta por revisar'
                        : 'propuestas por revisar' ?>
                </span>
            </div>

            <?php if (empty($solicitudes)): ?>
                <div class="form-container">
                    <h2>No hay propuestas pendientes</h2>
                    <p>
                        Las propuestas enviadas por los tutores aparecerán
                        aquí para su revisión.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <?php
                    $fechaProgramada = !empty($solicitud['fecha'])
                        ? date(
                            'd/m/Y',
                            strtotime($solicitud['fecha'])
                        )
                        : 'Sin fecha';

                    $horaInicio = !empty($solicitud['hora_inicio'])
                        ? substr($solicitud['hora_inicio'], 0, 5)
                        : '';

                    $horaFin = !empty($solicitud['hora_fin'])
                        ? substr($solicitud['hora_fin'], 0, 5)
                        : '';

                    $modalidad = $solicitud['modalidad'] ?? '';
                    $lugar = trim(
                        (string) ($solicitud['lugar_o_enlace'] ?? '')
                    );
                    ?>

                    <article class="form-container mb-4">
                        <div class="module-heading">
                            <div>
                                <p class="section-label">
                                    Propuesta del tutor
                                </p>
                                <h2>
                                    <?= $escapar(
                                        $solicitud['nombre_materia']
                                    ) ?>
                                </h2>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="data-table">
                                <tbody>
                                    <tr>
                                        <th>Estudiante</th>
                                        <td>
                                            <?= $escapar(
                                                $solicitud[
                                                    'nombre_estudiante'
                                                ]
                                                . ' '
                                                . $solicitud[
                                                    'apellido_estudiante'
                                                ]
                                            ) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tutor</th>
                                        <td>
                                            <?= $escapar(
                                                $solicitud['nombre_tutor']
                                                . ' '
                                                . $solicitud['apellido_tutor']
                                            ) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Fecha y horario</th>
                                        <td>
                                            <?= $escapar($fechaProgramada) ?>

                                            <?php if (
                                                $horaInicio !== ''
                                                && $horaFin !== ''
                                            ): ?>
                                                ·
                                                <?= $escapar($horaInicio) ?>
                                                –
                                                <?= $escapar($horaFin) ?>
                                            <?php else: ?>
                                                · Horario incompleto
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Modalidad</th>
                                        <td>
                                            <?= $modalidad !== ''
                                                ? $escapar(
                                                    ucfirst($modalidad)
                                                )
                                                : 'Sin especificar' ?>
                                        </td>
                                    </tr>

                                    <?php if ($modalidad === 'presencial'): ?>
                                        <tr>
                                            <th>Aula</th>
                                            <td>
                                                <?= $lugar !== ''
                                                    ? $escapar($lugar)
                                                    : 'Sin aula especificada' ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <th>Solicitud recibida</th>
                                        <td>
                                            <?= !empty(
                                                $solicitud['fecha_solicitud']
                                            )
                                                ? $escapar(
                                                    date(
                                                        'd/m/Y H:i',
                                                        strtotime(
                                                            $solicitud[
                                                                'fecha_solicitud'
                                                            ]
                                                        )
                                                    )
                                                )
                                                : 'Sin fecha de registro' ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-grid mt-3">
                            <form
                                method="POST"
                                action="<?= $escapar($rutaBase) ?>controllers/solicitudes_listar.php"
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
                                    value="aprobar"
                                >

                                <div class="form-actions">
                                    <button
                                        type="submit"
                                        class="primary-action"
                                    >
                                        Aprobar tutoría
                                    </button>
                                </div>
                            </form>

                            <form
                                method="POST"
                                action="<?= $escapar($rutaBase) ?>controllers/solicitudes_listar.php"
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
                                    value="observar"
                                >

                                <div class="form-group">
                                    <label
                                        for="observacion_<?= (int) $solicitud['id_tutoria'] ?>"
                                    >
                                        Observaciones para el tutor
                                    </label>
                                    <textarea
                                        id="observacion_<?= (int) $solicitud['id_tutoria'] ?>"
                                        name="observacion"
                                        maxlength="500"
                                        rows="3"
                                        required
                                        placeholder="Indica qué debe corregir el tutor"
                                    ></textarea>
                                </div>

                                <div class="form-actions">
                                    <button
                                        type="submit"
                                        class="table-link"
                                    >
                                        Devolver al tutor
                                    </button>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
