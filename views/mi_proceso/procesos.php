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

                    <h1>Mi proceso de grado</h1>

                    <p>
                        Consulta tu modalidad, etapa, tutor asignado y
                        avance académico.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/mi_calendario.php"
                    class="secondary-link"
                >
                    Ver mi calendario
                </a>
            </div>

            <div class="list-summary">
                <span>
                    <?= count($expedientes) ?>
                    <?= count($expedientes) === 1
                        ? 'proceso académico'
                        : 'procesos académicos' ?>
                </span>
            </div>

            <?php if (empty($expedientes)): ?>
                <div class="form-container">
                    <p class="section-label">
                        Sin expediente
                    </p>

                    <h2>
                        Todavía no tienes un proceso registrado
                    </h2>

                    <p>
                        Coordinación debe crear tu expediente y
                        relacionarlo con una cohorte antes de que puedas
                        consultar el seguimiento.
                    </p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach (
                        $expedientes as $expediente
                    ): ?>
                        <div class="col-lg-6">
                            <article class="form-container h-100">
                                <div class="form-section-heading">
                                    <div>
                                        <p class="section-label">
                                            <?= htmlspecialchars(
                                                $expediente['nombre_modalidad'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>

                                        <h2>
                                            <?= htmlspecialchars(
                                                $expediente['titulo_trabajo']
                                                    ?: 'Trabajo sin título definido',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </h2>
                                    </div>

                                    <span
                                        class="status-label <?= $expediente['estado'] === 'activo'
                                            ? 'status-active'
                                            : 'status-inactive' ?>"
                                    >
                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $expediente['estado']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Etapa actual</label>

                                        <p>
                                            <?= htmlspecialchars(
                                                strtoupper(
                                                    $expediente['etapa_actual']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label>Cohorte</label>

                                        <p>
                                            <?= htmlspecialchars(
                                                $expediente['nombre_cohorte'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label>Tutor asignado</label>

                                        <p>
                                            <?php if (
                                                $expediente['nombre_tutor']
                                            ): ?>
                                                <?= htmlspecialchars(
                                                    $expediente['nombre_tutor']
                                                    . ' '
                                                    . $expediente['apellido_tutor'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            <?php else: ?>
                                                Pendiente de asignación
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label>Designación</label>

                                        <p>
                                            <?= htmlspecialchars(
                                                $expediente['estado_carta']
                                                    ? ucfirst(
                                                        $expediente['estado_carta']
                                                    )
                                                    : 'No requerida o pendiente',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>
                                    </div>
                                </div>

                                <a
                                    href="<?= htmlspecialchars(
                                        $rutaBase,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>controllers/mi_proceso_ver.php?id=<?= (int) $expediente['id_expediente'] ?>"
                                    class="primary-action"
                                >
                                    Ver seguimiento
                                </a>
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