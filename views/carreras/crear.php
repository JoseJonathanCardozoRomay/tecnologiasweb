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
                        Administración académica
                    </p>

                    <h1>Nueva carrera</h1>

                    <p>
                        Registra una carrera para utilizarla posteriormente
                        en estudiantes y materias.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/carreras_listar.php"
                    class="secondary-link"
                >
                    Volver al listado
                </a>
            </div>

            <div class="form-container">
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
                    <div class="form-group">
                        <label for="nombre_carrera">
                            Nombre de la carrera
                        </label>

                        <input
                            type="text"
                            id="nombre_carrera"
                            name="nombre_carrera"
                            value="<?= htmlspecialchars(
                                $nombreCarrera,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="150"
                            required
                            autofocus
                        >

                        <p class="field-help">
                            Escribe el nombre completo, por ejemplo:
                            Ingeniería de Sistemas.
                        </p>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar carrera
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/carreras_listar.php"
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