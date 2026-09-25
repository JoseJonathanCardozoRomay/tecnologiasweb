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
                        Perfiles académicos
                    </p>

                    <h1>Editar tutor</h1>

                    <p>
                        Actualiza la especialidad y la información
                        profesional del tutor seleccionado.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/tutores_listar.php"
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
                    <input
                        type="hidden"
                        name="id_tutor"
                        value="<?= (int) $idTutor ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cuenta_tutor">
                                Cuenta del tutor
                            </label>

                            <input
                                type="text"
                                id="cuenta_tutor"
                                value="<?= htmlspecialchars(
                                    $tutor['nombre']
                                    . ' '
                                    . $tutor['apellido']
                                    . ' ('
                                    . $tutor['usuario']
                                    . ')',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                disabled
                            >

                            <p class="field-help">
                                La cuenta asociada al perfil no puede
                                cambiarse.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="especialidad">
                                Especialidad
                            </label>

                            <input
                                type="text"
                                id="especialidad"
                                name="especialidad"
                                value="<?= htmlspecialchars(
                                    $especialidad,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="150"
                                placeholder="Ejemplo: Desarrollo web"
                                autofocus
                            >

                            <p class="field-help">
                                Puedes dejar este dato vacío y completarlo
                                posteriormente.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="biografia">
                                Biografía profesional
                            </label>

                            <textarea
                                id="biografia"
                                name="biografia"
                                rows="6"
                                maxlength="2000"
                                placeholder="Describe brevemente la experiencia y formación del tutor."
                            ><?= htmlspecialchars(
                                $biografia,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>

                            <p class="field-help">
                                Máximo 2000 caracteres.
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
                            ) ?>controllers/tutores_listar.php"
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