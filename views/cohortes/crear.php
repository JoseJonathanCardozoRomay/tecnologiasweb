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

                    <h1>Nueva cohorte</h1>

                    <p>
                        Registra el periodo académico en el que ingresarán
                        los estudiantes al proceso de graduación.
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
                                placeholder="Ejemplo: MG-2026-1"
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
                                placeholder="Ejemplo: Gestión 2026 - Primer semestre"
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

                            <p class="field-help">
                                Indica cuándo comienza el proceso de esta
                                cohorte.
                            </p>
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
                                Puede quedar vacía si todavía no existe una
                                fecha oficial de cierre.
                            </p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar cohorte
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