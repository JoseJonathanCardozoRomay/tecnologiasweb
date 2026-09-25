<?php

require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/permisos.php';

$usuarioSesion = obtenerUsuarioSesion();
$paginaActual = basename($_SERVER['PHP_SELF']);

$inicioActivo = $paginaActual === 'index.php';

$carrerasActivo = strpos(
    $paginaActual,
    'carreras_'
) === 0;

$materiasActivo = strpos(
    $paginaActual,
    'materias_'
) === 0;

$modalidadesActivo = strpos(
    $paginaActual,
    'modalidades_'
) === 0;

$parametrosActivo = strpos(
    $paginaActual,
    'parametros_'
) === 0;

$cohortesActivo = strpos(
    $paginaActual,
    'cohortes_'
) === 0;

$calendarioActivo = strpos(
    $paginaActual,
    'calendario_'
) === 0;

$usuariosActivo = strpos(
    $paginaActual,
    'usuarios_'
) === 0;

$estudiantesActivo = strpos(
    $paginaActual,
    'estudiantes_'
) === 0;

$tutoresActivo = strpos(
    $paginaActual,
    'tutores_'
) === 0;

$disponibilidadActivo = strpos(
    $paginaActual,
    'disponibilidad_'
) === 0;

$configuracionActiva = (
    $carrerasActivo
    || $materiasActivo
);

$perfilesActivo = (
    $estudiantesActivo
    || $tutoresActivo
);

$modalidadesGradoActivo = (
    $modalidadesActivo
    || $parametrosActivo
    || $cohortesActivo
    || $calendarioActivo
);

$puedeVerModalidades = (
    $usuarioSesion
    && usuarioTienePermiso(
        'mg.modalidades.ver'
    )
);

$puedeVerParametros = (
    $usuarioSesion
    && usuarioTienePermiso(
        'mg.parametros.ver'
    )
);

$puedeVerCohortes = (
    $usuarioSesion
    && usuarioTienePermiso(
        'mg.cohortes.ver'
    )
);

$puedeVerCalendario = (
    $usuarioSesion
    && usuarioTienePermiso(
        'mg.calendario.ver'
    )
);

$mostrarModalidadesGrado = (
    $puedeVerModalidades
    || $puedeVerParametros
    || $puedeVerCohortes
    || $puedeVerCalendario
);

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

        <div
            class="collapse navbar-collapse"
            id="menuPrincipal"
        >
            <div
                class="navbar-nav ms-auto align-items-lg-center gap-lg-3"
            >
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
                    <!-- Configuración académica -->
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle <?= $configuracionActiva
                                ? 'active'
                                : '' ?>"
                            href="#"
                            id="menuConfiguracion"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Configuración
                        </a>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="menuConfiguracion"
                        >
                            <li>
                                <a
                                    class="dropdown-item <?= $carrerasActivo
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
                            </li>

                            <li>
                                <a
                                    class="dropdown-item <?= $materiasActivo
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
                            </li>
                        </ul>
                    </div>

                    <!-- Cuentas y accesos -->
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

                    <!-- Perfiles académicos -->
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle <?= $perfilesActivo
                                ? 'active'
                                : '' ?>"
                            href="#"
                            id="menuPerfiles"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Perfiles
                        </a>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="menuPerfiles"
                        >
                            <li>
                                <a
                                    class="dropdown-item <?= $estudiantesActivo
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
                            </li>

                            <li>
                                <a
                                    class="dropdown-item <?= $tutoresActivo
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
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($mostrarModalidadesGrado): ?>
                    <!-- Gestión de Modalidades de Grado -->
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle <?= $modalidadesGradoActivo
                                ? 'active'
                                : '' ?>"
                            href="#"
                            id="menuModalidadesGrado"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Modalidades de Grado
                        </a>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="menuModalidadesGrado"
                        >
                            <?php if ($puedeVerModalidades): ?>
                                <li>
                                    <a
                                        class="dropdown-item <?= $modalidadesActivo
                                            ? 'active'
                                            : '' ?>"
                                        href="<?= htmlspecialchars(
                                            $rutaBase,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>controllers/modalidades_listar.php"
                                    >
                                        Modalidades oficiales
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($puedeVerParametros): ?>
                                <li>
                                    <a
                                        class="dropdown-item <?= $parametrosActivo
                                            ? 'active'
                                            : '' ?>"
                                        href="<?= htmlspecialchars(
                                            $rutaBase,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>controllers/parametros_listar.php"
                                    >
                                        Parámetros del sistema
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($puedeVerCohortes): ?>
                                <li>
                                    <a
                                        class="dropdown-item <?= $cohortesActivo
                                            ? 'active'
                                            : '' ?>"
                                        href="<?= htmlspecialchars(
                                            $rutaBase,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>controllers/cohortes_listar.php"
                                    >
                                        Cohortes
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($puedeVerCalendario): ?>
                                <li>
                                    <a
                                        class="dropdown-item <?= $calendarioActivo
                                            ? 'active'
                                            : '' ?>"
                                        href="<?= htmlspecialchars(
                                            $rutaBase,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>controllers/calendario_listar.php"
                                    >
                                        Calendario
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (
                    $usuarioSesion
                    && $usuarioSesion['rol'] === 'tutor'
                ): ?>
                    <a
                        class="nav-link <?= $disponibilidadActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/disponibilidad_listar.php"
                    >
                        Mi disponibilidad
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
                    <span id="textoTema">
                        Tema oscuro
                    </span>
                </button>
            </div>
        </div>
    </div>
</nav>