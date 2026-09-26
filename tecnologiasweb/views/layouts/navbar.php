<?php

require_once __DIR__ . '/../../includes/sesion.php';

$usuarioSesion = obtenerUsuarioSesion();
$paginaActual = basename($_SERVER['PHP_SELF']);

$inicioActivo = $paginaActual === 'index.php';
$carrerasActivo = strpos($paginaActual, 'carreras_') === 0;
$materiasActivo = strpos($paginaActual, 'materias_') === 0;
$usuariosActivo = strpos($paginaActual, 'usuarios_') === 0;
$estudiantesActivo = strpos($paginaActual, 'estudiantes_') === 0;
$tutoresActivo = strpos($paginaActual, 'tutores_') === 0;

?>

<nav class="navbar navbar-expand-lg app-navbar sticky-top">
    <div class="container">
        <!-- Identidad institucional del sistema -->
        <a
            class="navbar-brand"
            href="<?= htmlspecialchars(
                $rutaBase,
                ENT_QUOTES,
                'UTF-8'
            ) ?>index.php"
        >
            <span class="brand-logo-container">
                <img
                    src="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>assets/img/logo-upds.png"
                    alt="Universidad Privada Domingo Savio"
                    class="brand-logo"
                >
            </span>

            <span class="brand-description">
                Sistema académico
            </span>
        </a>

        <!-- Menú adaptable para pantallas pequeñas -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Mostrar navegación"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <?php if ($usuarioSesion): ?>
                    <a
                        class="nav-link <?= $inicioActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>index.php"
                    >
                        Inicio
                    </a>
                <?php endif; ?>

                <?php if (
                    $usuarioSesion
                    && $usuarioSesion['rol'] === 'administrador'
                ): ?>
                    <a
                        class="nav-link <?= $carrerasActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/carreras_listar.php"
                    >
                        Carreras
                    </a>

                    <a
                        class="nav-link <?= $materiasActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/materias_listar.php"
                    >
                        Materias
                    </a>

                    <a
                        class="nav-link <?= $usuariosActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/usuarios_listar.php"
                    >
                        Usuarios
                    </a>

                    <a
                        class="nav-link <?= $estudiantesActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/estudiantes_listar.php"
                    >
                        Estudiantes
                    </a>

                    <a
                        class="nav-link <?= $tutoresActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/tutores_listar.php"
                    >
                        Tutores
                    </a>
                <?php endif; ?>

                <?php if ($usuarioSesion): ?>
                    <div class="session-user">
                        <span class="session-name">
                            <?= htmlspecialchars(
                                $usuarioSesion['nombre']
                                . ' '
                                . $usuarioSesion['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <span class="session-role">
                            <?= htmlspecialchars(
                                ucfirst($usuarioSesion['rol']),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>

                    <a
                        class="session-link"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/logout.php"
                    >
                        Cerrar sesión
                    </a>
                <?php else: ?>
                    <a
                        class="session-link"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/login.php"
                    >
                        Iniciar sesión
                    </a>
                <?php endif; ?>

                <button
                    class="theme-button"
                    id="botonTema"
                    type="button"
                    aria-label="Cambiar tema"
                >
                    <span id="textoTema">Tema oscuro</span>
                </button>
            </div>
        </div>
    </div>
</nav>