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

                    <h1>Nuevo informe</h1>

                    <p>
                        Informe <?= (int) $numeroInforme ?>
                        de
                        <?= htmlspecialchars(
                            strtoupper($etapaActual),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        para
                        <?= htmlspecialchars(
                            $tutorado['nombre_estudiante']
                            . ' '
                            . $tutorado['apellido_estudiante'],
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
                    ) ?>controllers/informes_listar.php?expediente=<?= (int) $idExpediente ?>"
                    class="secondary-link"
                >
                    Volver a informes
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

                <form method="POST" class="module-form">
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_expediente"
                        value="<?= (int) $idExpediente ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Número de informe</label>

                            <input
                                type="text"
                                value="<?= (int) $numeroInforme ?>"
                                readonly
                            >
                        </div>

                        <div class="form-group">
                            <label>Etapa</label>

                            <input
                                type="text"
                                value="<?= htmlspecialchars(
                                    strtoupper($etapaActual),
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

                        <p class="field-help">
                            El hito es opcional y debe corresponder a la
                            etapa actual.
                        </p>
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

                    <div class="form-group">
                        <label for="logros">
                            Logros alcanzados
                        </label>

                        <textarea
                            id="logros"
                            name="logros"
                            rows="4"
                            maxlength="5000"
                        ><?= htmlspecialchars(
                            $logros,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="dificultades">
                            Dificultades encontradas
                        </label>

                        <textarea
                            id="dificultades"
                            name="dificultades"
                            rows="4"
                            maxlength="5000"
                        ><?= htmlspecialchars(
                            $dificultades,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="recomendaciones">
                            Recomendaciones
                        </label>

                        <textarea
                            id="recomendaciones"
                            name="recomendaciones"
                            rows="4"
                            maxlength="5000"
                        ><?= htmlspecialchars(
                            $recomendaciones,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="proximas_actividades">
                            Próximas actividades
                        </label>

                        <textarea
                            id="proximas_actividades"
                            name="proximas_actividades"
                            rows="4"
                            maxlength="5000"
                        ><?= htmlspecialchars(
                            $proximasActividades,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar borrador
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/informes_listar.php?expediente=<?= (int) $idExpediente ?>"
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