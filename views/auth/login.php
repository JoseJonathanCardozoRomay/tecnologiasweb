<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-grow-1">
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5">
                    <div class="login-container">
                        <div class="login-heading">
                            <p class="section-label">
                                Acceso al sistema
                            </p>

                            <h1>Iniciar sesión</h1>

                            <p>
                                Ingresa tus datos para acceder a las opciones
                                correspondientes a tu rol.
                            </p>
                        </div>

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

                        <form method="POST" class="login-form">
                            <div class="mb-4">
                                <label
                                    for="usuario"
                                    class="form-label"
                                >
                                    Usuario
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="usuario"
                                    name="usuario"
                                    value="<?= htmlspecialchars(
                                        $usuarioIngresado,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    maxlength="50"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="mb-4">
                                <label
                                    for="contrasena"
                                    class="form-label"
                                >
                                    Contraseña
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="contrasena"
                                    name="contrasena"
                                    autocomplete="current-password"
                                    required
                                >
                            </div>

                            <button
                                type="submit"
                                class="login-button"
                            >
                                Ingresar
                            </button>
                        </form>

                        <p class="login-help">
                            Si no puedes ingresar, solicita la revisión de
                            tu cuenta al administrador.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>