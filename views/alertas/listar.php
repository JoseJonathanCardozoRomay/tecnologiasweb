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
                        Seguimiento académico
                    </p>

                    <h1>Alertas pendientes</h1>

                    <p>
                        Situaciones que requieren revisión de
                        Coordinación. Ninguna alerta modifica
                        automáticamente el estado del estudiante.
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
                >
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-5">
                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Total pendientes
                        </p>

                        <h2 class="h3 mb-0">
                            <?= count($alertas) ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Prioridad alta
                        </p>

                        <h2 class="h3 mb-0 text-danger">
                            <?= (int) $resumen['alta'] ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Prioridad media
                        </p>

                        <h2 class="h3 mb-0 text-warning">
                            <?= (int) $resumen['media'] ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Prioridad baja
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) $resumen['baja'] ?>
                        </h2>
                    </div>
                </div>
            </div>

            <?php if (empty($alertas)): ?>
                <div class="form-container text-center py-5">
                    <h2 class="h4">
                        No existen alertas pendientes
                    </h2>

                    <p class="mb-0 text-secondary">
                        Los procesos académicos no presentan situaciones
                        que requieran atención en este momento.
                    </p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($alertas as $alerta): ?>
                        <?php
                        $claseAlerta = match (
                            $alerta['severidad']
                        ) {
                            'alta' => 'danger',
                            'media' => 'warning',
                            default => 'info'
                        };
                        ?>

                        <div class="col-lg-6">
                            <article class="form-container h-100">
                                <div
                                    class="d-flex justify-content-between align-items-start gap-3 mb-3"
                                >
                                    <div>
                                        <span
                                            class="badge text-bg-<?= htmlspecialchars(
                                                $claseAlerta,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?> mb-2"
                                        >
                                            Prioridad
                                            <?= htmlspecialchars(
                                                $alerta['severidad'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                        <h2 class="h5 mb-0">
                                            <?= htmlspecialchars(
                                                $alerta['titulo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </h2>
                                    </div>
                                </div>

                                <p>
                                    <?= htmlspecialchars(
                                        $alerta['descripcion'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <a
                                    href="<?= htmlspecialchars(
                                        $rutaBase
                                        . $alerta['url'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    class="table-link d-inline-block mb-4"
                                >
                                    Revisar registro relacionado
                                </a>

                                <?php if ($puedeAtender): ?>
                                    <form
                                        method="POST"
                                        action="<?= htmlspecialchars(
                                            $rutaBase,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>controllers/alertas_atender.php"
                                        class="module-form"
                                    >
                                        <?= campoCsrf() ?>

                                        <input
                                            type="hidden"
                                            name="tipo_alerta"
                                            value="<?= htmlspecialchars(
                                                $alerta['tipo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id_referencia"
                                            value="<?= (int) $alerta[
                                                'id_referencia'
                                            ] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="clave_alerta"
                                            value="<?= htmlspecialchars(
                                                $alerta['clave'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <div class="form-group">
                                            <label
                                                for="nota_<?= htmlspecialchars(
                                                    md5(
                                                        $alerta['clave']
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >
                                                Acción realizada
                                            </label>

                                            <textarea
                                                id="nota_<?= htmlspecialchars(
                                                    md5(
                                                        $alerta['clave']
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                name="nota"
                                                rows="2"
                                                minlength="5"
                                                maxlength="500"
                                                placeholder="Describe cómo fue atendida"
                                                required
                                            ></textarea>
                                        </div>

                                        <button
                                            type="submit"
                                            class="secondary-action"
                                        >
                                            Marcar atendida
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>