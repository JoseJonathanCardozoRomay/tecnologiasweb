<?php

require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$escapar = static fn($valor): string => htmlspecialchars(
    (string) $valor,
    ENT_QUOTES,
    'UTF-8'
);

$materiasOferta = [];

foreach ($oferta as $opcion) {
    $materiasOferta[(int) $opcion['id_materia']] =
        $opcion['nombre_materia'];
}

$estadosTutoria = [
    'pendiente_tutor' => [
        'Esperando respuesta del tutor',
        'status-inactive'
    ],
    'pendiente_aprobacion' => [
        'Propuesta en revisión',
        'status-inactive'
    ],
    'observada' => [
        'El tutor debe corregir la propuesta',
        'status-inactive'
    ],
    'confirmada' => [
        'Aprobada',
        'status-active'
    ],
    'rechazada' => [
        'Solicitud rechazada',
        'status-inactive'
    ],
    'realizada' => [
        'Realizada',
        'status-active'
    ],
    'cancelada' => [
        'Cancelada',
        'status-inactive'
    ],
    'pendiente' => [
        'Pendiente',
        'status-inactive'
    ]
];

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">Tutorías por materia</p>
                    <h1>Solicitar tutoría</h1>
                    <p>
                        Selecciona la materia y el tutor al que deseas enviar
                        la solicitud. El tutor propondrá los datos de la
                        tutoría y coordinación los revisará.
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

            <?php if (!$periodo): ?>
                <div class="form-container">
                    <h2>No hay inscripciones abiertas</h2>
                    <p>
                        Por el momento no puedes enviar una solicitud.
                        Vuelve a intentarlo cuando se abra un periodo.
                    </p>
                </div>
            <?php else: ?>
                <div class="form-container mb-5">
                    <p class="section-label">
                        Periodo <?= $escapar($periodo['codigo']) ?>
                    </p>

                    <h2><?= $escapar($periodo['nombre']) ?></h2>

                    <?php if (empty($oferta)): ?>
                        <p>
                            Todavía no hay tutores habilitados para este
                            periodo.
                        </p>
                    <?php else: ?>
                        <form
                            method="POST"
                            action="<?= $escapar($rutaBase) ?>controllers/solicitudes_crear.php"
                        >
                            <?= campoCsrf() ?>

                            <input type="hidden" name="accion" value="solicitar">

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="id_materia">Materia</label>
                                    <select
                                        id="id_materia"
                                        name="id_materia"
                                        required
                                    >
                                        <option value="">
                                            Selecciona una materia
                                        </option>

                                        <?php foreach (
                                            $materiasOferta
                                            as $idMateria => $nombreMateria
                                        ): ?>
                                            <option
                                                value="<?= (int) $idMateria ?>"
                                            >
                                                <?= $escapar($nombreMateria) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_tutor">
                                        Tutor sugerido
                                    </label>
                                    <select
                                        id="id_tutor"
                                        name="id_tutor"
                                        required
                                        disabled
                                    >
                                        <option value="">
                                            Primero selecciona una materia
                                        </option>

                                        <?php foreach ($oferta as $opcion): ?>
                                            <?php
                                            $ocupados = (int) $opcion[
                                                'cupos_ocupados'
                                            ];
                                            $maximo = (int) $opcion[
                                                'cupo_maximo'
                                            ];
                                            $sinCupo = $ocupados >= $maximo;

                                            $nombreTutor = trim(
                                                $opcion['nombre']
                                                . ' '
                                                . $opcion['apellido']
                                            );
                                            ?>

                                            <option
                                                value="<?= (int) $opcion['id_tutor'] ?>"
                                                data-materia="<?= (int) $opcion['id_materia'] ?>"
                                                <?= $sinCupo ? 'disabled' : '' ?>
                                            >
                                                <?= $escapar($nombreTutor) ?>

                                                <?php if (
                                                    !empty(
                                                        $opcion['especialidad']
                                                    )
                                                ): ?>
                                                    — <?= $escapar(
                                                        $opcion['especialidad']
                                                    ) ?>
                                                <?php endif; ?>

                                                (<?= $ocupados ?>/<?= $maximo ?>
                                                estudiantes<?= $sinCupo
                                                    ? ', sin cupos'
                                                    : '' ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <p class="field-help">
                                        El tutor revisará tu solicitud y
                                        propondrá fecha, horario y modalidad.
                                    </p>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button
                                    type="submit"
                                    class="primary-action"
                                >
                                    Enviar solicitud
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="module-heading">
                <div>
                    <p class="section-label">Tu actividad</p>
                    <h2>Mis solicitudes</h2>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Tutor</th>
                            <th>Estado</th>
                            <th>Programación aprobada</th>
                            <th>Evaluación</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($solicitudes)): ?>
                            <tr>
                                <td colspan="5" class="empty-result">
                                    Todavía no tienes solicitudes de tutoría.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <?php
                                $estadoTutoria = $estadosTutoria[
                                    $solicitud['estado']
                                ] ?? ['Desconocido', 'status-inactive'];

                                $aprobada = in_array(
                                    $solicitud['estado'],
                                    ['confirmada', 'realizada'],
                                    true
                                );

                                $horarioDisponible =
                                    $aprobada
                                    && !empty($solicitud['fecha'])
                                    && !empty($solicitud['hora_inicio'])
                                    && !empty($solicitud['hora_fin']);
                                ?>

                                <tr>
                                    <td>
                                        <?= $escapar(
                                            $solicitud['nombre_materia']
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escapar(
                                            $solicitud['nombre_tutor']
                                            . ' '
                                            . $solicitud['apellido_tutor']
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $escapar(
                                                $estadoTutoria[1]
                                            ) ?>"
                                        >
                                            <?= $escapar(
                                                $estadoTutoria[0]
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if ($horarioDisponible): ?>
                                            <?= date(
                                                'd/m/Y',
                                                strtotime(
                                                    $solicitud['fecha']
                                                )
                                            ) ?>
                                            <br>
                                            <?= $escapar(
                                                substr(
                                                    $solicitud['hora_inicio'],
                                                    0,
                                                    5
                                                )
                                            ) ?>
                                            –
                                            <?= $escapar(
                                                substr(
                                                    $solicitud['hora_fin'],
                                                    0,
                                                    5
                                                )
                                            ) ?>
                                            <br>
                                            <?= $escapar(
                                                ucfirst(
                                                    $solicitud['modalidad']
                                                )
                                            ) ?>

                                            <?php if (
                                                !empty(
                                                    $solicitud[
                                                        'lugar_o_enlace'
                                                    ]
                                                )
                                            ): ?>
                                                ·
                                                <?= $escapar(
                                                    $solicitud[
                                                        'lugar_o_enlace'
                                                    ]
                                                ) ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?= $aprobada
                                                ? 'Coordina las reuniones con tu tutor.'
                                                : 'Se mostrará cuando sea aprobada.' ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($solicitud['estado'] === 'realizada'): ?>
                                            <?php if ($solicitud['calificacion'] !== null): ?>
                                                <?= (int) $solicitud['calificacion'] ?>/5
                                            <?php else: ?>
                                                <form method="POST" action="<?= $escapar($rutaBase) ?>controllers/solicitudes_crear.php" class="d-grid gap-2">
                                                    <?= campoCsrf() ?>
                                                    <input type="hidden" name="accion" value="evaluar">
                                                    <input type="hidden" name="id_tutoria" value="<?= (int) $solicitud['id_tutoria'] ?>">
                                                    <label class="visually-hidden" for="nota_<?= (int) $solicitud['id_tutoria'] ?>">Calificación</label>
                                                    <select id="nota_<?= (int) $solicitud['id_tutoria'] ?>" name="calificacion" class="form-select form-select-sm" required>
                                                        <option value="">Califica del 1 al 5</option>
                                                        <?php for ($nota = 1; $nota <= 5; $nota++): ?>
                                                            <option value="<?= $nota ?>"><?= $nota ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <label class="visually-hidden" for="comentario_<?= (int) $solicitud['id_tutoria'] ?>">Comentario opcional</label>
                                                    <textarea id="comentario_<?= (int) $solicitud['id_tutoria'] ?>" name="comentario" class="form-control form-control-sm" maxlength="1000" rows="2" placeholder="Comentario opcional"></textarea>
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">Enviar evaluación</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<script>
    const selectorMateria = document.getElementById('id_materia');
    const selectorTutor = document.getElementById('id_tutor');

    if (selectorMateria && selectorTutor) {
        selectorMateria.addEventListener('change', () => {
            const idMateria = selectorMateria.value;
            let hayTutores = false;

            for (const opcion of selectorTutor.options) {
                if (!opcion.value) {
                    continue;
                }

                const coincide =
                    opcion.dataset.materia === idMateria;

                opcion.hidden = !coincide;

                if (coincide && !opcion.disabled) {
                    hayTutores = true;
                }
            }

            selectorTutor.value = '';
            selectorTutor.disabled = !idMateria || !hayTutores;

            selectorTutor.options[0].textContent = !idMateria
                ? 'Primero selecciona una materia'
                : hayTutores
                    ? 'Selecciona un tutor'
                    : 'No hay tutores con cupo para esta materia';
        });
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
