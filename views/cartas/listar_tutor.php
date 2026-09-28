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
                        Modalidades de grado
                    </p>

                    <h1>Mis cartas de designación</h1>

                    <p>
                        Revisa las asignaciones de tutoría académica
                        que fueron registradas a tu nombre.
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
                    <?= count($cartas) ?>
                    <?= count($cartas) === 1
                        ? 'carta encontrada'
                        : 'cartas encontradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Trabajo</th>
                            <th>Modalidad</th>
                            <th>Etapa</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($cartas)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    No tienes cartas de designación.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cartas as $carta): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $carta['nombre_estudiante']
                                                    . ' '
                                                    . $carta['apellido_estudiante'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $carta['nombre_cohorte'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $carta['titulo_trabajo']
                                                ?: 'Sin título definido',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $carta['nombre_modalidad'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $carta['etapa_actual']
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
                                                    $carta['fecha_generacion']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?php
                                        $claseEstado = match (
                                            $carta['estado']
                                        ) {
                                            'aceptada' => 'status-active',
                                            'rechazada',
                                            'anulada' => 'status-inactive',
                                            default => 'status-pending'
                                        };
                                        ?>

                                        <span
                                            class="status-label <?= $claseEstado ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $carta['estado']
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
                                            ) ?>controllers/carta_tutor_ver.php?id=<?= (int) $carta['id_carta'] ?>"
                                            class="table-link"
                                        >
                                            <?= $carta['estado'] === 'pendiente'
                                                ? 'Revisar'
                                                : 'Ver detalle' ?>
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