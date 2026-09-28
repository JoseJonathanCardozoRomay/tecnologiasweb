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
                        Seguimiento de avance
                    </p>

                    <h1>Informes académicos</h1>

                    <p>
                        <?= htmlspecialchars(
                            $tutorado['nombre_estudiante']
                            . ' '
                            . $tutorado['apellido_estudiante'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        ·
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
                        ) ?>controllers/informes_crear.php?expediente=<?= (int) $idExpediente ?>"
                        class="primary-action"
                    >
                        Nuevo informe
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
                    <?= count($informes) ?>
                    <?= count($informes) === 1
                        ? 'informe registrado'
                        : 'informes registrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Informe</th>
                            <th>Etapa</th>
                            <th>Fecha</th>
                            <th>Hito</th>
                            <th>Avance</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($informes)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    Todavía no se registraron informes.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($informes as $informe): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            Informe
                                            <?= (int) $informe['numero_informe'] ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $informe['etapa']
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
                                                    $informe['fecha_informe']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <span>
                                                <?= htmlspecialchars(
                                                    $informe['nombre_hito']
                                                        ?: 'Sin hito relacionado',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>

                                            <?php if (
                                                $informe['fecha_limite']
                                            ): ?>
                                                <span>
                                                    Límite:
                                                    <?= htmlspecialchars(
                                                        date(
                                                            'd/m/Y',
                                                            strtotime(
                                                                $informe['fecha_limite']
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= (int) $informe['porcentaje_avance'] ?>%
                                        </strong>
                                    </td>

                                    <td>
                                        <?php
                                        $claseEstado = match (
                                            $informe['estado']
                                        ) {
                                            'aprobado' => 'status-active',
                                            'observado' => 'status-inactive',
                                            default => 'status-pending'
                                        };
                                        ?>

                                        <span
                                            class="status-label <?= $claseEstado ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $informe['estado']
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
                                            ) ?>controllers/informe_ver.php?id=<?= (int) $informe['id_informe'] ?>"
                                            class="table-link"
                                        >
                                            Ver informe
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