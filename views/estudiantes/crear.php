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

                    <h1>Nuevo estudiante</h1>

                    <p>
                        Completa la información académica de una cuenta
                        que tenga asignado el rol de estudiante.
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

                <?php if (empty($usuariosDisponibles)): ?>
                    <div class="alert alert-warning" role="alert">
                        No existen cuentas de estudiante disponibles.
                        Primero debes crear una cuenta con el rol
                        estudiante.
                    </div>

                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/usuarios_crear.php"
                        class="primary-action"
                    >
                        Crear cuenta de usuario
                    </a>
                <?php else: ?>
                    <form method="POST" class="module-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="id_usuario">
                                    Cuenta del estudiante
                                </label>

                                <select
                                    id="id_usuario"
                                    name="id_usuario"
                                    required
                                    autofocus
                                >
                                    <option value="">
                                        Selecciona una cuenta
                                    </option>

                                    <?php foreach (
                                        $usuariosDisponibles as $usuario
                                    ): ?>
                                        <option
                                            value="<?= (int) $usuario['id_usuario'] ?>"
                                            <?= $idUsuario === (int) $usuario['id_usuario']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= htmlspecialchars(
                                                $usuario['nombre']
                                                . ' '
                                                . $usuario['apellido']
                                                . ' ('
                                                . $usuario['usuario']
                                                . ')',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <p class="field-help">
                                    Solo aparecen cuentas activas sin un
                                    perfil académico.
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
                                    value="<?= $semestre !== null
                                        ? (int) $semestre
                                        : '' ?>"
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
                                    Este dato puede completarse después.
                                </p>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button
                                type="submit"
                                class="primary-action"
                            >
                                Guardar perfil
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
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>