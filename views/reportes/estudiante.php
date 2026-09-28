<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading d-print-none">
                <div>
                    <p class="section-label">
                        Reporte académico
                    </p>

                    <h1>Seguimiento del estudiante</h1>
                </div>

                <div class="d-flex gap-2">
                    <button
                        type="button"
                        class="primary-action"
                        onclick="window.print()"
                    >
                        Imprimir reporte
                    </button>

                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/expedientes_ver.php?id=<?= (int) $idExpediente ?>"
                        class="secondary-link"
                    >
                        Volver
                    </a>
                </div>
            </div>

            <div class="form-container form-container-wide mb-4">
                <p class="section-label">
                    Universidad Privada Domingo Savio
                </p>

                <h1 class="h3">
                    Reporte individual de Modalidades de Grado
                </h1>

                <hr>

                <div class="row g-4">
                    <div class="col-md-6">
                        <strong>Estudiante</strong>

                        <p>
                            <?= htmlspecialchars(
                                $expediente['nombre']
                                . ' '
                                . $expediente['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <strong>Registro universitario</strong>

                        <p>
                            <?= htmlspecialchars(
                                $expediente[
                                    'registro_universitario'
                                ] ?: 'Sin registro',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <strong>Carrera</strong>

                        <p>
                            <?= htmlspecialchars(
                                $expediente['nombre_carrera'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-6">
                        <strong>Modalidad</strong>

                        <p>
                            <?= htmlspecialchars(
                                $expediente['modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <strong>Cohorte</strong>

                        <p>
                            <?= htmlspecialchars(
                                $expediente['cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <strong>Etapa y estado</strong>

                        <p>
                            <?= htmlspecialchars(
                                strtoupper(
                                    $expediente['etapa_actual']
                                )
                                . ' — '
                                . ucfirst($expediente['estado']),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>

                <strong>Título del trabajo</strong>

                <p>
                    <?= htmlspecialchars(
                        $expediente['titulo_trabajo']
                            ?: 'Sin título registrado',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <strong>Tutor actual</strong>

                <p class="mb-0">
                    <?= htmlspecialchars(
                        $expediente['tutor_actual']
                            ?: 'Sin tutor asignado',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
            </div>

            <div class="row g-3 mb-5">
                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Reuniones
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) (
                                $seguimiento['total_reuniones']
                                ?? 0
                            ) ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Validadas
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) (
                                $seguimiento[
                                    'reuniones_validadas'
                                ] ?? 0
                            ) ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Informes
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) (
                                $seguimiento['total_informes']
                                ?? 0
                            ) ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Último avance
                        </p>

                        <h2 class="h3 mb-0">
                            <?= $seguimiento['ultimo_avance']
                                !== null
                                    ? (int) $seguimiento[
                                        'ultimo_avance'
                                    ] . '%'
                                    : 'Sin datos' ?>
                        </h2>
                    </div>
                </div>
            </div>

            <h2 class="h4 mb-3">Historial de tutores</h2>

            <div class="table-container mb-5">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tutor</th>
                            <th>Inicio</th>
                            <th>Finalización</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($tutores)): ?>
                            <tr>
                                <td colspan="4" class="empty-result">
                                    Sin tutores registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tutores as $tutor): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            $tutor['nombre']
                                            . ' '
                                            . $tutor['apellido'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y',
                                                strtotime(
                                                    $tutor[
                                                        'fecha_asignacion'
                                                    ]
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $tutor['fecha_fin']
                                            ? htmlspecialchars(
                                                date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $tutor['fecha_fin']
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Actual' ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            ucfirst($tutor['estado']),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h2 class="h4 mb-3">Tribunales</h2>

            <div class="table-container mb-5">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Posición</th>
                            <th>Docente</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($tribunales)): ?>
                            <tr>
                                <td colspan="4" class="empty-result">
                                    Sin tribunales registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $tribunales as $tribunal
                            ): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $tribunal['etapa']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        Tribunal
                                        <?= (int) $tribunal['orden'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tribunal['nombre']
                                            . ' '
                                            . $tribunal['apellido'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $tribunal['estado']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h2 class="h4 mb-3">
                Defensas y calificaciones
            </h2>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Fecha</th>
                            <th>Ambiente</th>
                            <th>Estado</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($defensas)): ?>
                            <tr>
                                <td colspan="5" class="empty-result">
                                    Sin defensas registradas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($defensas as $defensa): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $defensa['etapa']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y',
                                                strtotime(
                                                    $defensa['fecha']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $defensa['ambiente'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $defensa['estado']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $defensa['nota'] !== null
                                            ? htmlspecialchars(
                                                number_format(
                                                    (float) $defensa[
                                                        'nota'
                                                    ],
                                                    2
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Sin registrar' ?>
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

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>