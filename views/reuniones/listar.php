<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Seguimiento de reuniones
                    </p>

                    <h1>
                        <?= htmlspecialchars(
                            $tutorado['nombre_estudiante']
                            . ' '
                            . $tutorado['apellido_estudiante'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        <?= htmlspecialchars(
                            $tutorado['titulo_trabajo']
                                ?: 'Trabajo sin título definido',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>
                </div>

                <div class="form-actions">
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/reuniones_crear.php?expediente=<?= (int) $idExpediente ?>"
                        class="primary-action"
                    >
                        Programar reunión
                    </a>

                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/tutorado_ver.php?id=<?= (int) $idExpediente ?>"
                        class="secondary-link"
                    >
                        Volver al seguimiento
                    </a>
                </div>
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

            <div class="list-summary">
                <span>
                    <?= count($reuniones) ?>
                    <?= count($reuniones) === 1
                        ? 'reunión registrada'
                        : 'reuniones registradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha y horario</th>
                            <th>Tema</th>
                            <th>Modalidad</th>
                            <th>Asistencia</th>
                            <th>Evidencias</th>
                            <th>Estado</th>
                            <th>Validación</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($reuniones)): ?>
                            <tr>
                                <td
                                    colspan="8"
                                    class="empty-result"
                                >
                                    Todavía no se registraron reuniones.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reuniones as $reunion): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    date(
                                                        'd/m/Y',
                                                        strtotime(
                                                            $reunion['fecha_reunion']
                                                        )
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    substr(
                                                        $reunion['hora_inicio'],
                                                        0,
                                                        5
                                                    )
                                                    . ' - '
                                                    . substr(
                                                        $reunion['hora_fin'],
                                                        0,
                                                        5
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $reunion['tema'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $reunion['modalidad']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            $reunion['estado'] === 'realizada'
                                        ): ?>
                                            <div class="user-cell">
                                                <span>
                                                    Tutor:
                                                    <?= htmlspecialchars(
                                                        ucfirst(
                                                            $reunion['asistencia_tutor']
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>

                                                <span>
                                                    Estudiante:
                                                    <?= htmlspecialchars(
                                                        ucfirst(
                                                            $reunion['asistencia_estudiante']
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            Pendiente
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= (int) $reunion['total_evidencias'] ?>
                                    </td>

                                    <td>
                                        <?php
                                        $claseEstado = match (
                                            $reunion['estado']
                                        ) {
                                            'realizada' => 'status-active',
                                            'cancelada' => 'status-inactive',
                                            default => 'status-pending'
                                        };
                                        ?>

                                        <span
                                            class="status-label <?= $claseEstado ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $reunion['estado']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php
                                        $claseValidacion = match (
                                            $reunion['estado_validacion']
                                        ) {
                                            'validada' => 'status-active',
                                            'observada' => 'status-inactive',
                                            default => 'status-pending'
                                        };
                                        ?>

                                        <span
                                            class="status-label <?= $claseValidacion ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $reunion['estado_validacion']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/reunion_ver.php?id=<?= (int) $reunion['id_reunion'] ?>"
                                            class="table-link"
                                        >
                                            Ver detalle
                                        </a>
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