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

                    <h1>Nueva materia</h1>

                    <p>
                        Registra una materia y relaciónala con una carrera
                        cuando corresponda.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/materias_listar.php"
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
                        <label for="nombre_materia">
                            Nombre de la materia
                        </label>

                        <input
                            type="text"
                            id="nombre_materia"
                            name="nombre_materia"
                            value="<?= htmlspecialchars(
                                $nombreMateria,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="150"
                            required
                            autofocus
                        >

                        <p class="field-help">
                            Utiliza el nombre completo de la materia.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="id_carrera">
                            Carrera
                        </label>

                        <select
                            id="id_carrera"
                            name="id_carrera"
                        >
                            <option value="">
                                Materia general
                            </option>

                            <?php foreach ($carreras as $carrera): ?>
                                <option
                                    value="<?= (int) $carrera['id_carrera'] ?>"
                                    <?= $idCarrera === (int) $carrera['id_carrera']
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $carrera['nombre_carrera'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <p class="field-help">
                            Selecciona una carrera o déjala como materia
                            general si puede utilizarse en varias carreras.
                        </p>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar materia
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/materias_listar.php"
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