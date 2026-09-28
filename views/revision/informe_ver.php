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

                    <h1>
                        Informe
                        <?= (int) $informe['numero_informe'] ?>
                        ·
                        <?= htmlspecialchars(
                            strtoupper($informe['etapa']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        Presentado por
                        <?= htmlspecialchars(
                            $informe['nombre_tutor']
                            . ' '
                            . $informe['apellido_tutor'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/informes_revision_listar.php"
                    class="secondary-link"
                >
                    Volver a pendientes
                </a>
            </div>

            <?php if ($error !== ''): ?>
                <div
                    class="alert alert-danger"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="form-container form-container-wide">
                <div class="form-section-heading">
                    <div>
                        <h2>
                            <?= htmlspecialchars(
                                $informe['titulo_trabajo']
                                    ?: 'Trabajo sin título definido',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars(
                                $informe['nombre_estudiante']
                                . ' '
                                . $informe['apellido_estudiante'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                            ·
                            <?= htmlspecialchars(
                                $informe['nombre_modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <span class="status-label status-pending">
                        <?= htmlspecialchars(
                            ucfirst($informe['estado']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Fecha del informe</label>

                        <p>
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
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Porcentaje de avance</label>

                        <p>
                            <strong>
                                <?= (int) $informe['porcentaje_avance'] ?>%
                            </strong>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Hito relacionado</label>

                        <p>
                            <?= htmlspecialchars(
                                $informe['nombre_hito']
                                    ?: 'Sin hito relacionado',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Cohorte</label>

                        <p>
                            <?= htmlspecialchars(
                                $informe['nombre_cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Resumen del avance</label>

                    <p>
                        <?= nl2br(
                            htmlspecialchars(
                                $informe['resumen_avance'],
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ) ?>
                    </p>
                </div>

                <?php
                $secciones = [
                    'logros' => 'Logros alcanzados',
                    'dificultades' => 'Dificultades encontradas',
                    'recomendaciones' => 'Recomendaciones',
                    'proximas_actividades' => 'Próximas actividades'
                ];
                ?>

                <?php foreach (
                    $secciones as $campo => $titulo
                ): ?>
                    <?php if (
                        !empty($informe[$campo])
                    ): ?>
                        <div class="form-group">
                            <label>
                                <?= htmlspecialchars(
                                    $titulo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </label>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $informe[$campo],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if (
                    $informe['estado'] === 'presentado'
                ): ?>
                    <div class="form-section-heading">
                        <div>
                            <h2>Decisión de coordinación</h2>

                            <p>
                                Aprueba el informe o devuélvelo al tutor
                                indicando qué debe corregir.
                            </p>
                        </div>
                    </div>

                    <form method="POST" class="module-form">
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_informe"
                            value="<?= (int) $idInforme ?>"
                        >

                        <div class="form-group">
                            <label for="observacion">
                                Observación
                            </label>

                            <textarea
                                id="observacion"
                                name="observacion"
                                rows="4"
                                maxlength="500"
                                placeholder="Obligatoria cuando el informe será observado"
                            ><?= htmlspecialchars(
                                $observacion,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button
                                type="submit"
                                name="decision"
                                value="aprobado"
                                class="primary-action"
                                onclick="return confirm('¿Confirmas la aprobación del informe?');"
                            >
                                Aprobar informe
                            </button>

                            <button
                                type="submit"
                                name="decision"
                                value="observado"
                                class="secondary-action"
                                onclick="return confirm('¿Deseas devolver el informe con observaciones?');"
                            >
                                Observar informe
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        Este informe ya fue revisado y actualmente se
                        encuentra en estado
                        <strong>
                            <?= htmlspecialchars(
                                $informe['estado'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>