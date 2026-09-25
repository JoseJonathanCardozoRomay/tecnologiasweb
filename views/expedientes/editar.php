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
                        Modalidades de grado
                    </p>

                    <h1>Editar expediente</h1>

                    <p>
                        Actualiza el título del trabajo y las observaciones
                        generales del proceso académico.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/expedientes_ver.php?id=<?= (int) $idExpediente ?>"
                    class="secondary-link"
                >
                    Volver al expediente
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

                <div class="form-section-heading">
                    <div>
                        <h2>Información del proceso</h2>

                        <p>
                            Estos datos identifican al estudiante y no se
                            modifican desde este formulario.
                        </p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Estudiante</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['nombre']
                                . ' '
                                . $expediente['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Registro universitario</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['registro_universitario']
                                    ?: 'Sin registro',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Modalidad</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['nombre_modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Cohorte</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['nombre_cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Etapa actual</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                strtoupper(
                                    $expediente['etapa_actual']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Estado</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                ucfirst(
                                    $expediente['estado']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>
                </div>

                <div class="form-section-heading">
                    <div>
                        <h2>Datos editables</h2>

                        <p>
                            Registra la información principal del trabajo
                            desarrollado por el estudiante.
                        </p>
                    </div>
                </div>

                <form method="POST" class="module-form">
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_expediente"
                        value="<?= (int) $idExpediente ?>"
                    >

                    <div class="form-group">
                        <label for="titulo_trabajo">
                            Título del trabajo
                        </label>

                        <input
                            type="text"
                            id="titulo_trabajo"
                            name="titulo_trabajo"
                            value="<?= htmlspecialchars(
                                $tituloTrabajo,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="200"
                            placeholder="Título provisional o definitivo"
                            autofocus
                        >

                        <p class="field-help">
                            Puede quedar vacío mientras el estudiante
                            define su tema.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">
                            Observaciones
                        </label>

                        <textarea
                            id="observaciones"
                            name="observaciones"
                            rows="5"
                            maxlength="2000"
                            placeholder="Información adicional del expediente"
                        ><?= htmlspecialchars(
                            $observaciones,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                        <p class="field-help">
                            Máximo 2000 caracteres.
                        </p>
                    </div>

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
                            ) ?>controllers/expedientes_ver.php?id=<?= (int) $idExpediente ?>"
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