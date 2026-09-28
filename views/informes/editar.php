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
                        Editar informe
                        <?= (int) $informe['numero_informe'] ?>
                    </h1>

                    <p>
                        Actualiza el borrador antes de presentarlo a
                        coordinación.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/informe_ver.php?id=<?= (int) $idInforme ?>"
                    class="secondary-link"
                >
                    Volver al informe
                </a>
            </div>

            <div class="form-container form-container-wide">
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

                <form method="POST" class="module-form">
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_informe"
                        value="<?= (int) $idInforme ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Número de informe</label>

                            <input
                                type="text"
                                value="<?= (int) $informe['numero_informe'] ?>"
                                readonly
                            >
                        </div>

                        <div class="form-group">
                            <label>Etapa</label>

                            <input
                                type="text"
                                value="<?= htmlspecialchars(
                                    strtoupper($etapaInforme),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                readonly
                            >
                        </div>

                        <div class="form-group">
                            <label for="fecha_informe">
                                Fecha del informe
                            </label>

                            <input
                                type="date"
                                id="fecha_informe"
                                name="fecha_informe"
                                value="<?= htmlspecialchars(
                                    $fechaInforme,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                max="<?= date('Y-m-d') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="porcentaje_avance">
                                Porcentaje de avance
                            </label>

                            <input
                                type="number"
                                id="porcentaje_avance"
                                name="porcentaje_avance"
                                value="<?= (int) $porcentajeAvance ?>"
                                min="0"
                                max="100"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_hito">
                            Hito relacionado
                        </label>

                        <select
                            id="id_hito"
                            name="id_hito"
                        >
                            <option value="">
                                Sin hito relacionado
                            </option>

                            <?php foreach ($hitos as $hito): ?>
                                <option
                                    value="<?= (int) $hito['id_hito'] ?>"
                                    <?= $idHito === (int) $hito['id_hito']
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $hito['nombre']
                                        . ' - '
                                        . date(
                                            'd/m/Y',
                                            strtotime(
                                                $hito['fecha_limite']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="resumen_avance">
                            Resumen del avance
                        </label>

                        <textarea
                            id="resumen_avance"
                            name="resumen_avance"
                            rows="5"
                            minlength="10"
                            maxlength="5000"
                            required
                            autofocus
                        ><?= htmlspecialchars(
                            $resumenAvance,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>
                    </div>

                    <?php
                    $campos = [
                        'logros' => [
                            'Logros alcanzados',
                            $logros
                        ],
                        'dificultades' => [
                            'Dificultades encontradas',
                            $dificultades
                        ],
                        'recomendaciones' => [
                            'Recomendaciones',
                            $recomendaciones
                        ],
                        'proximas_actividades' => [
                            'Próximas actividades',
                            $proximasActividades
                        ]
                    ];
                    ?>

                    <?php foreach (
                        $campos as $nombre => [$etiqueta, $valor]
                    ): ?>
                        <div class="form-group">
                            <label for="<?= $nombre ?>">
                                <?= htmlspecialchars(
                                    $etiqueta,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </label>

                            <textarea
                                id="<?= $nombre ?>"
                                name="<?= $nombre ?>"
                                rows="4"
                                maxlength="5000"
                            ><?= htmlspecialchars(
                                $valor,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>
                    <?php endforeach; ?>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar cambios
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/informe_ver.php?id=<?= (int) $idInforme ?>"
                            class="cancel-action"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>