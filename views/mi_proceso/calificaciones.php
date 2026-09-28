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
                        Mi proceso académico
                    </p>

                    <h1>Mis calificaciones</h1>

                    <p>
                        Aquí aparecen únicamente las calificaciones
                        publicadas oficialmente por Coordinación.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/mi_proceso_listar.php"
                    class="secondary-link"
                >
                    Volver a mi proceso
                </a>
            </div>

            <div class="list-summary">
                <span>
                    <?= count($calificaciones) ?>

                    <?= count($calificaciones) === 1
                        ? 'calificación publicada'
                        : 'calificaciones publicadas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Modalidad</th>
                            <th>Etapa</th>
                            <th>Fecha de defensa</th>
                            <th>Calificación</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($calificaciones)): ?>
                            <tr>
                                <td
                                    colspan="5"
                                    class="empty-result"
                                >
                                    Todavía no existen calificaciones
                                    publicadas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $calificaciones as $calificacion
                            ): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $calificacion[
                                                        'modalidad'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $calificacion[
                                                        'titulo_trabajo'
                                                    ] ?: 'Trabajo sin título',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $calificacion['etapa']
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
                                                    $calificacion[
                                                        'fecha_defensa'
                                                    ]
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <strong class="fs-5">
                                            <?= htmlspecialchars(
                                                number_format(
                                                    (float) $calificacion[
                                                        'nota'
                                                    ],
                                                    2
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $calificacion[
                                                'observaciones'
                                            ] ?: 'Sin observaciones',
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