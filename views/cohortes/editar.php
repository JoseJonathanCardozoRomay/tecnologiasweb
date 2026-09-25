<?php

require_once __DIR__ . '/../../includes/csrf.php';
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

                    <h1>Editar cohorte</h1>

                    <p>
                        Actualiza el periodo académico utilizado para
                        organizar los procesos de graduación.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/cohortes_listar.php"
                    class="secondary-link"
                >
                    Volver al listado
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
                        name="id_cohorte"
                        value="<?= (int) $idCohorte ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="codigo">
                                Código
                            </label>

                            <input
                                type="text"
                                id="codigo"
                                name="codigo"
                                value="<?= htmlspecialchars(
                                    $codigo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                minlength="3"
                                maxlength="30"
                                required
                                autofocus
                            >

                            <p class="field-help">
                                Utiliza letras, números, guiones o guiones
                                bajos.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="nombre">
                                Nombre de la cohorte
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="<?= htmlspecialchars(
                                    $nombre,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                minlength="3"
                                maxlength="120"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">
                                Fecha de inicio
                            </label>

                            <input
                                type="date"
                                id="fecha_inicio"
                                name="fecha_inicio"
                                value="<?= htmlspecialchars(
                                    $fechaInicio,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="fecha_fin">
                                Fecha de finalización
                            </label>

                            <input
                                type="date"
                                id="fecha_fin"
                                name="fecha_fin"
                                value="<?= htmlspecialchars(
                                    $fechaFin,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <p class="field-help">
                                Puede quedar vacía mientras la cohorte no
                                tenga una fecha oficial de cierre.
                            </p>
                        </div>
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
                            ) ?>controllers/cohortes_listar.php"
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