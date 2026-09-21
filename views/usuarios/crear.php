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

                    <h1>Nuevo usuario</h1>

                    <p>
                        Registra los datos de acceso y asigna el rol que
                        tendrá la persona dentro del sistema.
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

                            <p class="field-help">
                                Puede contener letras, números, puntos y
                                guiones.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="id_rol">
                                Rol
                            </label>

                            <select
                                id="id_rol"
                                name="id_rol"
                                required
                            >
                                <option value="">
                                    Selecciona un rol
                                </option>

                                <?php foreach ($roles as $rol): ?>
                                    <option
                                        value="<?= (int) $rol['id_rol'] ?>"
                                        <?= $idRol === (int) $rol['id_rol']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            ucfirst($rol['nombre_rol']),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="contrasena">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                id="contrasena"
                                name="contrasena"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                            <p class="field-help">
                                Debe tener al menos 8 caracteres.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="confirmar_contrasena">
                                Confirmar contraseña
                            </label>

                            <input
                                type="password"
                                id="confirmar_contrasena"
                                name="confirmar_contrasena"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar usuario
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