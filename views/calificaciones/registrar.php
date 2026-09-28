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
                        Modalidades de Grado
                    </p>

                    <h1>Registrar calificación</h1>

                    <p>
                        La calificación permanecerá oculta hasta que
                        Coordinación decida publicarla.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/defensa_ver.php?id=<?= (int) $idDefensa ?>"
                    class="secondary-link"
                >
                    Volver a la defensa
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="form-container form-container-wide mb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <p class="section-label mb-2">
                            Estudiante
                        </p>

                        <h2 class="h4 mb-1">
                            <?= htmlspecialchars(
                                $datosDefensa['nombre']
                                . ' '
                                . $datosDefensa['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                $datosDefensa['titulo_trabajo']
                                    ?: 'Trabajo sin título registrado',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Etapa
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                strtoupper(
                                    $datosDefensa['etapa']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Estado de publicación
                        </p>

                        <span
                            class="status-label <?= !empty(
                                $datosDefensa['publicada']
                            )
                                ? 'status-active'
                                : 'status-inactive' ?>"
                        >
                            <?= !empty($datosDefensa['publicada'])
                                ? 'Publicada'
                                : 'No publicada' ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-container form-container-wide">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="module-form">
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_defensa"
                        value="<?= (int) $idDefensa ?>"
                    >

                    <div class="form-group">
                        <label for="nota">
                            Calificación
                        </label>

                        <input
                            type="number"
                            id="nota"
                            name="nota"
                            value="<?= htmlspecialchars(
                                (string) $nota,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            min="<?= htmlspecialchars(
                                (string) $limites['minimo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            max="<?= htmlspecialchars(
                                (string) $limites['maximo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            step="0.01"
                            required
                            autofocus
                        >

                        <p class="field-help">
                            Escala configurable:
                            <?= htmlspecialchars(
                                (string) $limites['minimo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                            a
                            <?= htmlspecialchars(
                                (string) $limites['maximo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">
                            Observaciones
                        </label>

                        <textarea
                            id="observaciones"
                            name="observaciones"
                            rows="4"
                            maxlength="500"
                        ><?= htmlspecialchars(
                            $observaciones,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar calificación
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/defensa_ver.php?id=<?= (int) $idDefensa ?>"
                            class="cancel-action"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>

                <?php if (
                    !empty($datosDefensa['id_calificacion'])
                ): ?>
                    <hr class="my-4">

                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/calificaciones_publicar.php"
                    >
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_calificacion"
                            value="<?= (int) $datosDefensa['id_calificacion'] ?>"
                        >

                        <input
                            type="hidden"
                            name="id_defensa"
                            value="<?= (int) $idDefensa ?>"
                        >

                        <input
                            type="hidden"
                            name="publicada"
                            value="<?= !empty(
                                $datosDefensa['publicada']
                            ) ? '0' : '1' ?>"
                        >

                        <button
                            type="submit"
                            class="<?= !empty(
                                $datosDefensa['publicada']
                            )
                                ? 'secondary-action'
                                : 'primary-action' ?>"
                        >
                            <?= !empty($datosDefensa['publicada'])
                                ? 'Ocultar al estudiante'
                                : 'Publicar al estudiante' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>