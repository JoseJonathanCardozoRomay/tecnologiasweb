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

                    <h1>Nuevo tutor</h1>

                    <p>
                        Completa la información profesional de una cuenta
                        que tenga asignado el rol de tutor.
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

                <?php if (empty($usuariosDisponibles)): ?>
                    <div class="alert alert-warning" role="alert">
                        No existen cuentas de tutor disponibles.
                        Primero debes crear una cuenta con el rol
                        tutor.
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
                                    Cuenta del tutor
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
                                    perfil de tutor.
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
                                >

                                <p class="field-help">
                                    Puedes completar este dato después.
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
                                Guardar perfil
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
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>