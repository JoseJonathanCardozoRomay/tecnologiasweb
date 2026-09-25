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

                    <h1>Editar modalidad</h1>

                    <p>
                        Actualiza el código, el flujo y los requisitos
                        generales de la modalidad.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/modalidades_listar.php"
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
                        name="id_modalidad"
                        value="<?= (int) $idModalidad ?>"
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
                                minlength="2"
                                maxlength="30"
                                required
                                autofocus
                            >

                            <p class="field-help">
                                Utiliza letras mayúsculas, números y
                                guiones bajos.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="nombre">
                                Nombre de la modalidad
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
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="flujo">
                                Flujo académico
                            </label>

                            <select
                                id="flujo"
                                name="flujo"
                                required
                            >
                                <option
                                    value="perfil_mg"
                                    <?= $flujo === 'perfil_mg'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Perfil MG
                                </option>

                                <option
                                    value="examen_areas"
                                    <?= $flujo === 'examen_areas'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Examen por áreas
                                </option>

                                <option
                                    value="excelencia"
                                    <?= $flujo === 'excelencia'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Graduación por excelencia
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                Acompañamiento
                            </label>

                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    id="requiere_tutor"
                                    name="requiere_tutor"
                                    value="1"
                                    class="form-check-input"
                                    <?= $requiereTutor
                                        ? 'checked'
                                        : '' ?>
                                >

                                <label
                                    for="requiere_tutor"
                                    class="form-check-label"
                                >
                                    Esta modalidad requiere tutor
                                </label>
                            </div>

                            <p class="field-help">
                                Solo corresponde a modalidades con
                                flujo Perfil MG.
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="4"
                            maxlength="255"
                            placeholder="Describe brevemente esta modalidad"
                        ><?= htmlspecialchars(
                            $descripcion,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                        <p class="field-help">
                            La descripción puede contener hasta
                            255 caracteres.
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
                            ) ?>controllers/modalidades_listar.php"
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