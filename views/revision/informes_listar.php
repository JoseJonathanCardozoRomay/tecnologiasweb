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
                        Control académico
                    </p>

                    <h1>Revisión de informes</h1>

                    <p>
                        Revisa los informes presentados por los tutores
                        antes de incorporarlos al seguimiento oficial.
                    </p>
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
                        ? 'informe pendiente'
                        : 'informes pendientes' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Tutor</th>
                            <th>Trabajo</th>
                            <th>Informe</th>
                            <th>Avance</th>
                            <th>Presentación</th>
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
                                    No existen informes pendientes de revisión.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($informes as $informe): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $informe['nombre_estudiante']
                                                    . ' '
                                                    . $informe['apellido_estudiante'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $informe['nombre_cohorte'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $informe['nombre_tutor']
                                            . ' '
                                            . $informe['apellido_tutor'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <span>
                                                <?= htmlspecialchars(
                                                    $informe['titulo_trabajo']
                                                        ?: 'Sin título definido',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $informe['nombre_modalidad'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        Informe
                                        <?= (int) $informe['numero_informe'] ?>
                                        ·
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $informe['etapa']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= (int) $informe['porcentaje_avance'] ?>%
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $informe['fecha_actualizacion']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/informe_revision_ver.php?id=<?= (int) $informe['id_informe'] ?>"
                                            class="table-link"
                                        >
                                            Revisar
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