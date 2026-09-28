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
                        <?= htmlspecialchars(
                            $informe['nombre_estudiante']
                            . ' '
                            . $informe['apellido_estudiante'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        ·
                        <?= htmlspecialchars(
                            $informe['titulo_trabajo']
                                ?: 'Trabajo sin título definido',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/informes_listar.php?expediente=<?= (int) $informe['id_expediente'] ?>"
                    class="secondary-link"
                >
                    Volver a informes
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-success"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

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
                        <h2>Información del informe</h2>

                        <p>
                            Registrado el
                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y',
                                    strtotime(
                                        $informe['fecha_informe']
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>.
                        </p>
                    </div>

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
                            ucfirst($informe['estado']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </div>

                <div class="form-grid">
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
                    $informe['estado'] === 'observado'
                ): ?>
                    <div class="alert alert-warning">
                        <strong>
                            Observación de coordinación:
                        </strong>

                        <p class="mb-0 mt-2">
                            <?= htmlspecialchars(
                                $informe['observacion_revision']
                                    ?: 'Sin detalle registrado.',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (
                    in_array(
                        $informe['estado'],
                        ['borrador', 'observado'],
                        true
                    )
                ): ?>
                    <div class="form-actions">
                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/informes_editar.php?id=<?= (int) $idInforme ?>"
                            class="secondary-action"
                        >
                            Editar informe
                        </a>

                        <form
                            method="POST"
                            onsubmit="return confirm('¿Confirmas que deseas presentar este informe a coordinación?');"
                        >
                            <?= campoCsrf() ?>

                            <input
                                type="hidden"
                                name="id_informe"
                                value="<?= (int) $idInforme ?>"
                            >

                            <input
                                type="hidden"
                                name="accion"
                                value="presentar"
                            >

                            <button
                                type="submit"
                                class="primary-action"
                            >
                                Presentar informe
                            </button>
                        </form>
                    </div>
                <?php elseif (
                    $informe['estado'] === 'presentado'
                ): ?>
                    <div class="alert alert-info">
                        El informe está esperando la revisión de
                        coordinación.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>