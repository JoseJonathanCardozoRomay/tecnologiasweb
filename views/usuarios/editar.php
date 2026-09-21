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
                        Administración del sistema
                    </p>

                    <h1>Editar usuario</h1>

                    <p>
                        Actualiza los datos personales o asigna una nueva
                        contraseña cuando sea necesario.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/usuarios_listar.php"
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
                        name="id_usuario"
                        value="<?= (int) $idUsuario ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">
                                Nombre
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
                                maxlength="100"
                                autocomplete="given-name"
                                required
                                autofocus
                            >
                        </div>

                        <div class="form-group">
                            <label for="apellido">
                                Apellido
                            </label>

                            <input
                                type="text"
                                id="apellido"
                                name="apellido"
                                value="<?= htmlspecialchars(
                                    $apellido,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="100"
                                autocomplete="family-name"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="correo">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="correo"
                                name="correo"
                                value="<?= htmlspecialchars(
                                    $correo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="150"
                                autocomplete="email"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="telefono">
                                Teléfono
                            </label>

                            <input
                                type="text"
                                id="telefono"
                                name="telefono"
                                value="<?= htmlspecialchars(
                                    $telefono,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="20"
                                autocomplete="tel"
                            >

                            <p class="field-help">
                                Este dato es opcional.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="usuario">
                                Nombre de usuario
                            </label>

                            <input
                                type="text"
                                id="usuario"
                                name="usuario"
                                value="<?= htmlspecialchars(
                                    $usuario,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="50"
                                autocomplete="username"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="rol_actual">
                                Rol asignado
                            </label>

                            <input
                                type="text"
                                id="rol_actual"
                                value="<?= htmlspecialchars(
                                    ucfirst($nombreRol),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                disabled
                            >

                            <p class="field-help">
                                El rol se mantiene para proteger los perfiles
                                relacionados.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="estado_actual">
                                Estado
                            </label>

                            <input
                                type="text"
                                id="estado_actual"
                                value="<?= htmlspecialchars(
                                    ucfirst($estadoUsuario),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                disabled
                            >

                            <p class="field-help">
                                El estado se cambia desde el listado.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="contrasena">
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="contrasena"
                                name="contrasena"
                                minlength="8"
                                autocomplete="new-password"
                            >

                            <p class="field-help">
                                Déjala vacía para conservar la contraseña
                                actual.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="confirmar_contrasena">
                                Confirmar nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="confirmar_contrasena"
                                name="confirmar_contrasena"
                                minlength="8"
                                autocomplete="new-password"
                            >
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
                            ) ?>controllers/usuarios_listar.php"
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