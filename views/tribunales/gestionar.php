<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$etapaActual = $expediente['etapa_actual'];

$tribunalesVigentes = array_filter(
    $tribunales,
    fn(array $tribunal): bool =>
        $tribunal['etapa'] === $etapaActual
        && $tribunal['estado'] === 'vigente'
);

$historialTribunales = array_filter(
    $tribunales,
    fn(array $tribunal): bool =>
        $tribunal['estado'] === 'reemplazado'
);

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Modalidades de Grado
                    </p>

                    <h1>Tribunales del expediente</h1>

                    <p>
                        Administra los docentes evaluadores y conserva
                        el historial de cambios.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/tribunales_listar.php"
                    class="secondary-link"
                >
                    Volver al listado
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-<?= htmlspecialchars(
                        $tipoMensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="form-container form-container-wide mb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <p class="section-label mb-2">
                            Estudiante
                        </p>

                        <h2 class="h4 mb-1">
                            <?= htmlspecialchars(
                                $expediente['nombre']
                                . ' '
                                . $expediente['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                $expediente['titulo_trabajo']
                                    ?: 'Trabajo sin título registrado',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Modalidad
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                $expediente['modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                $expediente['cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Etapa actual
                        </p>

                        <span class="status-label status-active">
                            <?= htmlspecialchars(
                                strtoupper($etapaActual),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($tutorPrincipal): ?>
                <div class="alert alert-info" role="alert">
                    <strong>Tutor principal:</strong>

                    <?= htmlspecialchars(
                        $tutorPrincipal['nombre']
                        . ' '
                        . $tutorPrincipal['apellido'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>.

                    Este dato debe revisarse al seleccionar los tribunales
                    para evitar posibles conflictos de funciones.
                </div>
            <?php endif; ?>

            <div class="module-heading mt-5">
                <div>
                    <p class="section-label">
                        Etapa <?= htmlspecialchars(
                            strtoupper($etapaActual),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <h2>Tribunales vigentes</h2>

                    <p>
                        Actualmente existen
                        <?= count($tribunalesVigentes) ?>

                        <?= count($tribunalesVigentes) === 1
                            ? 'docente asignado'
                            : 'docentes asignados' ?>.
                    </p>
                </div>

                <?php if ($puedeAsignar): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/tribunales_asignar.php?id=<?= (int) $idExpediente ?>"
                        class="primary-action"
                    >
                        Asignar tribunal
                    </a>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th>Docente</th>
                            <th>Especialidad</th>
                            <th>Fecha de asignación</th>

                            <?php if ($puedeCambiar): ?>
                                <th class="actions-column">
                                    Acciones
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($tribunalesVigentes)): ?>
                            <tr>
                                <td
                                    colspan="<?= $puedeCambiar ? 5 : 4 ?>"
                                    class="empty-result"
                                >
                                    Todavía no se asignaron tribunales
                                    para esta etapa.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $tribunalesVigentes as $tribunal
                            ): ?>
                                <tr>
                                    <td>
                                        Tribunal
                                        <?= (int) $tribunal['orden'] ?>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $tribunal['nombre']
                                                    . ' '
                                                    . $tribunal['apellido'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $tribunal['correo'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tribunal['especialidad']
                                                ?: 'Sin especialidad registrada',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $tribunal[
                                                        'fecha_asignacion'
                                                    ]
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <?php if ($puedeCambiar): ?>
                                        <td class="table-actions">
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/tribunales_reemplazar.php?id=<?= (int) $tribunal['id_tribunal'] ?>"
                                                class="table-link"
                                            >
                                                Reemplazar
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="module-heading mt-5">
                <div>
                    <p class="section-label">
                        Trazabilidad
                    </p>

                    <h2>Historial de tribunales</h2>

                    <p>
                        Los cambios anteriores se conservan para permitir
                        su revisión.
                    </p>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Posición</th>
                            <th>Docente anterior</th>
                            <th>Periodo</th>
                            <th>Motivo del cambio</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($historialTribunales)): ?>
                            <tr>
                                <td
                                    colspan="5"
                                    class="empty-result"
                                >
                                    No existen reemplazos registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $historialTribunales as $tribunal
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
                                            date(
                                                'd/m/Y',
                                                strtotime(
                                                    $tribunal[
                                                        'fecha_asignacion'
                                                    ]
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        <?php if (
                                            $tribunal['fecha_fin']
                                        ): ?>
                                            hasta

                                            <?= htmlspecialchars(
                                                date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $tribunal[
                                                            'fecha_fin'
                                                        ]
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tribunal['motivo_cambio']
                                                ?: 'Sin motivo registrado',
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
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>