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
                        Centro de avisos
                    </p>

                    <h1>Notificaciones</h1>

                    <p>
                        Consulta asignaciones, defensas, observaciones
                        y resultados relacionados con tu cuenta.
                    </p>
                </div>

                <?php if ($totalNoLeidas > 0): ?>
                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/notificaciones_leer.php"
                    >
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="marcar_todas"
                            value="1"
                        >

                        <button
                            type="submit"
                            class="secondary-action"
                        >
                            Marcar todas como leídas
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="list-summary">
                <span>
                    <?= (int) $totalNoLeidas ?>

                    <?= $totalNoLeidas === 1
                        ? 'notificación pendiente'
                        : 'notificaciones pendientes' ?>
                </span>
            </div>

            <?php if (empty($notificaciones)): ?>
                <div class="form-container text-center py-5">
                    <h2 class="h4">
                        No tienes notificaciones
                    </h2>

                    <p class="mb-0 text-secondary">
                        Los nuevos avisos relacionados con tu proceso
                        aparecerán en esta sección.
                    </p>
                </div>
            <?php else: ?>
                <div class="d-grid gap-3">
                    <?php foreach (
                        $notificaciones as $notificacion
                    ): ?>
                        <article
                            class="form-container <?= (int) $notificacion['leida'] === 0
                                ? 'border-primary'
                                : '' ?>"
                        >
                            <div
                                class="d-flex justify-content-between align-items-start gap-4"
                            >
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <?php if (
                                            (int) $notificacion['leida']
                                            === 0
                                        ): ?>
                                            <span class="badge text-bg-primary">
                                                Nueva
                                            </span>
                                        <?php endif; ?>

                                        <span class="text-secondary small">
                                            <?= htmlspecialchars(
                                                date(
                                                    'd/m/Y H:i',
                                                    strtotime(
                                                        $notificacion[
                                                            'fecha_registro'
                                                        ]
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </div>

                                    <h2 class="h5">
                                        <?= htmlspecialchars(
                                            $notificacion['titulo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </h2>

                                    <p class="mb-0">
                                        <?= htmlspecialchars(
                                            $notificacion['mensaje'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </p>
                                </div>

                                <form
                                    method="POST"
                                    action="<?= htmlspecialchars(
                                        $rutaBase,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>controllers/notificaciones_leer.php"
                                >
                                    <?= campoCsrf() ?>

                                    <input
                                        type="hidden"
                                        name="id_notificacion"
                                        value="<?= (int) $notificacion[
                                            'id_notificacion'
                                        ] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="table-link"
                                    >
                                        <?= (int) $notificacion['leida'] === 0
                                            ? 'Abrir'
                                            : 'Ver detalle' ?>
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>