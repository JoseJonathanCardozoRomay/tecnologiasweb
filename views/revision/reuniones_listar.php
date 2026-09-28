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

                    <h1>Validación de reuniones</h1>

                    <p>
                        Revisa la asistencia, los acuerdos y las
                        evidencias registradas por los tutores.
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
                    <?= count($reuniones) ?>
                    <?= count($reuniones) === 1
                        ? 'reunión pendiente'
                        : 'reuniones pendientes' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Tutor</th>
                            <th>Reunión</th>
                            <th>Fecha</th>
                            <th>Asistencia</th>
                            <th>Evidencias</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($reuniones)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    No existen reuniones pendientes de validación.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reuniones as $reunion): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            $reunion['nombre_estudiante']
                                            . ' '
                                            . $reunion['apellido_estudiante'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $reunion['nombre_tutor']
                                            . ' '
                                            . $reunion['apellido_tutor'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $reunion['tema'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    ucfirst(
                                                        $reunion['modalidad']
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <span>
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
                                            </span>

                                            <span>
                                                <?= htmlspecialchars(
                                                    substr(
                                                        $reunion['hora_inicio'],
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
                                    </td>

                                    <td>
                                        <?= (int) $reunion['total_evidencias'] ?>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/reunion_revision_ver.php?id=<?= (int) $reunion['id_reunion'] ?>"
                                            class="table-link"
                                        >
                                            Validar
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