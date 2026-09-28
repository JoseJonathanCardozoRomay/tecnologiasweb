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
                        Calendario académico
                    </p>

                    <h1>Mi calendario</h1>

                    <p>
                        Consulta únicamente las fechas correspondientes
                        a las cohortes de tus procesos de grado.
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
                    <?= count($hitos) ?>
                    <?= count($hitos) === 1
                        ? 'hito académico'
                        : 'hitos académicos' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha límite</th>
                            <th>Actividad</th>
                            <th>Cohorte</th>
                            <th>Etapa</th>
                            <th>Tipo</th>
                            <th>Avance esperado</th>
                            <th>Situación</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($hitos)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    No existen hitos para tus cohortes.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($hitos as $hito): ?>
                                <?php
                                $fechaLimite = new DateTimeImmutable(
                                    $hito['fecha_limite']
                                );

                                $vencido = $fechaLimite < $fechaActual;
                                ?>

                                <tr>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $fechaLimite->format(
                                                    'd/m/Y'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $hito['nombre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $hito['nombre_cohorte'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $hito['etapa']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $hito['tipo']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $hito['avance_esperado_pct'] !== null
                                            ? (int) $hito['avance_esperado_pct']
                                                . '%'
                                            : 'No definido' ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $vencido
                                                ? 'status-inactive'
                                                : 'status-active' ?>"
                                        >
                                            <?= $vencido
                                                ? 'Fecha vencida'
                                                : 'Próximo' ?>
                                        </span>
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