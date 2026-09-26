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

                    <h1>Editar estudiante</h1>

                    <p>
                        Actualiza la carrera, semestre o registro
                        universitario del estudiante.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/estudiantes_listar.php"
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
                        name="id_estudiante"
                        value="<?= (int) $idEstudiante ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cuenta_estudiante">
                                Cuenta del estudiante
                            </label>

                            <input
                                type="text"
                                id="cuenta_estudiante"
                                value="<?= htmlspecialchars(
                                    $estudiante['nombre']
                                    . ' '
                                    . $estudiante['apellido']
                                    . ' ('
                                    . $estudiante['usuario']
                                    . ')',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                disabled
                            >

                            <p class="field-help">
                                La cuenta vinculada no puede cambiarse.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="estado_cuenta">
                                Estado de la cuenta
                            </label>

                            <input
                                type="text"
                                id="estado_cuenta"
                                value="<?= htmlspecialchars(
                                    ucfirst($estudiante['estado']),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                disabled
                            >

                            <p class="field-help">
                                El estado se administra desde Usuarios.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="id_carrera">
                                Carrera
                            </label>

                            <select
                                id="id_carrera"
                                name="id_carrera"
                                required
                            >
                                <option value="">
                                    Selecciona una carrera
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
                        </div>

                        <div class="form-group">
                            <label for="semestre">
                                Semestre
                            </label>

                            <input
                                type="number"
                                id="semestre"
                                name="semestre"
                                value="<?= (int) $semestre ?>"
                                min="1"
                                max="10"
                                required
                            >

                            <p class="field-help">
                                Ingresa un valor entre 1 y 10.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="registro_universitario">
                                Registro universitario
                            </label>

                            <input
                                type="text"
                                id="registro_universitario"
                                name="registro_universitario"
                                value="<?= htmlspecialchars(
                                    $registroUniversitario,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="30"
                                placeholder="Ejemplo: RU-2026-12345"
                            >

                            <p class="field-help">
                                Este dato es opcional, pero no puede
                                repetirse.
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
                            ) ?>controllers/estudiantes_listar.php"
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