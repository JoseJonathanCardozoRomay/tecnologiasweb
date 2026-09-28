<?php

require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/permisos.php';
require_once __DIR__ . '/../../models/NotificacionModel.php';
require_once __DIR__ . '/../../includes/csrf.php';

$usuarioSesion = obtenerUsuarioSesion();
$paginaActual = basename($_SERVER['PHP_SELF']);

$urlBase = htmlspecialchars(
    $rutaBase,
    ENT_QUOTES,
    'UTF-8'
);

/**
 * Comprueba si la página actual pertenece a un módulo.
 */
$paginaCoincide = static function (
    array $prefijos = [],
    array $paginasExactas = []
) use ($paginaActual): bool {
    if (in_array($paginaActual, $paginasExactas, true)) {
        return true;
    }

    foreach ($prefijos as $prefijo) {
        if (str_starts_with($paginaActual, $prefijo)) {
            return true;
        }
    }

    return false;
};

$inicioActivo = $paginaActual === 'index.php';

$administracionActiva = $paginaCoincide([
    'carreras_',
    'materias_',
    'usuarios_',
    'estudiantes_',
    'tutores_',
    'solicitudes_',
    'periodos_',
    'cupos_'
]);

$gestionMgActiva = $paginaCoincide(
    [
        'panel_coordinacion',
        'modalidades_',
        'parametros_',
        'cohortes_',
        'calendario_',
        'expedientes_',
        'expediente_',
        'importaciones_',
        'importacion_'
    ],
    [
        'solicitudes_listar.php'
    ]
);

$seguimientoMgActivo = $paginaCoincide(
    [
        'tribunales_',
        'tribunal_',
        'defensas_',
        'defensa_',
        'calificaciones_',
        'informes_revision_',
        'informe_revision_',
        'reuniones_revision_',
        'reunion_revision_',
        'documentos_',
        'documento_',
        'plantillas_',
        'reportes_',
        'reporte_',
        'alertas_',
        'bitacora_'
    ],
    [
        'evidencia_revision_descargar.php'
    ]
);

$seguimientoTutorActivo = $paginaCoincide(
    [
        'cartas_tutor_',
        'carta_tutor_',
        'tutorados_',
        'mis_materias',
        'disponibilidad_',
        'reuniones_',
        'reunion_',
        'evidencias_',
        'evidencia_',
        'informes_',
        'informe_'
    ],
    [
        'tutorado_ver.php',
        'mi_calendario_tutor.php',
        'solicitudes_tutor.php'
    ]
);

$seguimientoEstudianteActivo = $paginaCoincide(
    [
        'mi_proceso_'
    ],
    [
        'mi_calendario.php',
        'mis_calificaciones.php',
        'solicitudes_crear.php'
    ]
);

$notificacionesActivo = $paginaCoincide([
    'notificaciones_',
    'notificacion_'
]);

$rolActual = $usuarioSesion['rol'] ?? '';

$esAdministrador = $rolActual === 'administrador';

$esPersonalGestion = in_array(
    $rolActual,
    [
        'administrador',
        'coordinador_mg',
        'auxiliar_mg'
    ],
    true
);

$esTutor = $rolActual === 'tutor';
$esEstudiante = $rolActual === 'estudiante';

$nombresRoles = [
    'administrador' => 'Administrador',
    'coordinador_mg' => 'Coordinador MG',
    'auxiliar_mg' => 'Auxiliar MG',
    'tutor' => 'Tutor',
    'estudiante' => 'Estudiante'
];

/*
 * Las opciones se organizan como datos para evitar repetir
 * grandes bloques de HTML en cada menú.
 */
$menuAdministracion = [];

if ($esAdministrador) {
    $menuAdministracion = [
        [
            'nombre' => 'Carreras',
            'ruta' => 'controllers/carreras_listar.php',
            'activo' => $paginaCoincide(['carreras_'])
        ],
        [
            'nombre' => 'Materias',
            'ruta' => 'controllers/materias_listar.php',
            'activo' => $paginaCoincide(['materias_'])
        ],
        [
            'separador' => true
        ],
        [
            'nombre' => 'Usuarios',
            'ruta' => 'controllers/usuarios_listar.php',
            'activo' => $paginaCoincide(['usuarios_'])
        ],
        [
            'nombre' => 'Estudiantes',
            'ruta' => 'controllers/estudiantes_listar.php',
            'activo' => $paginaCoincide(['estudiantes_'])
        ],
        [
            'nombre' => 'Tutores',
            'ruta' => 'controllers/tutores_listar.php',
            'activo' => $paginaCoincide(['tutores_'])
        ],
        [
            'nombre' => 'Solicitudes de tutoría',
            'ruta' => 'controllers/solicitudes_listar.php',
            'activo' => $paginaActual === 'solicitudes_listar.php'
        ],
        [
            'nombre' => 'Periodos de inscripción',
            'ruta' => 'controllers/periodos_listar.php',
            'activo' => $paginaCoincide([
                'periodos_',
                'cupos_'
            ])
        ]
    ];
}

$menuGestionMg = [];

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.reportes.ver')
) {
    $menuGestionMg[] = [
        'nombre' => 'Panel de coordinación',
        'ruta' => 'controllers/panel_coordinacion.php',
        'activo' => $paginaActual === 'panel_coordinacion.php'
    ];

    $menuGestionMg[] = [
        'separador' => true
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.modalidades.ver')
) {
    $menuGestionMg[] = [
        'nombre' => 'Modalidades oficiales',
        'ruta' => 'controllers/modalidades_listar.php',
        'activo' => $paginaCoincide(['modalidades_'])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.parametros.ver')
) {
    $menuGestionMg[] = [
        'nombre' => 'Parámetros',
        'ruta' => 'controllers/parametros_listar.php',
        'activo' => $paginaCoincide(['parametros_'])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.cohortes.ver')
) {
    $menuGestionMg[] = [
        'nombre' => 'Cohortes',
        'ruta' => 'controllers/cohortes_listar.php',
        'activo' => $paginaCoincide(['cohortes_'])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.calendario.ver')
) {
    $menuGestionMg[] = [
        'nombre' => 'Calendario académico',
        'ruta' => 'controllers/calendario_listar.php',
        'activo' => $paginaCoincide(['calendario_'])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.expedientes.ver')
) {
    $menuGestionMg[] = [
        'separador' => true
    ];

    $menuGestionMg[] = [
        'nombre' => 'Expedientes',
        'ruta' => 'controllers/expedientes_listar.php',
        'activo' => $paginaCoincide([
            'expedientes_',
            'expediente_'
        ])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.importaciones.ver')
) {
    $menuGestionMg[] = [
        'nombre' => 'Importaciones',
        'ruta' => 'controllers/importaciones_listar.php',
        'activo' => $paginaCoincide([
            'importaciones_',
            'importacion_'
        ])
    ];
}

/*
 * La coordinación MG puede revisar las propuestas de tutoría.
 * El administrador conserva su acceso desde el menú Administración.
 */
if ($rolActual === 'coordinador_mg') {
    array_unshift($menuGestionMg, [
        'nombre' => 'Aprobación de tutorías',
        'ruta' => 'controllers/solicitudes_listar.php',
        'activo' => $paginaActual === 'solicitudes_listar.php'
    ]);
}

$menuSeguimientoMg = [];

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.tribunales.ver')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Tribunales',
        'ruta' => 'controllers/tribunales_listar.php',
        'activo' => $paginaCoincide([
            'tribunales_',
            'tribunal_'
        ])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.defensas.ver')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Defensas y calificaciones',
        'ruta' => 'controllers/defensas_listar.php',
        'activo' => $paginaCoincide([
            'defensas_',
            'defensa_',
            'calificaciones_'
        ])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.reuniones.validar')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Validación de reuniones',
        'ruta' => 'controllers/reuniones_revision_listar.php',
        'activo' => $paginaCoincide(
            [
                'reuniones_revision_',
                'reunion_revision_'
            ],
            [
                'evidencia_revision_descargar.php'
            ]
        )
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.informes.ver')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Revisión de informes',
        'ruta' => 'controllers/informes_revision_listar.php',
        'activo' => $paginaCoincide([
            'informes_revision_',
            'informe_revision_'
        ])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.documentos.ver')
) {
    $menuSeguimientoMg[] = [
        'separador' => true
    ];

    $menuSeguimientoMg[] = [
        'nombre' => 'Documentos',
        'ruta' => 'controllers/documentos_listar.php',
        'activo' => $paginaCoincide([
            'documentos_',
            'documento_',
            'plantillas_'
        ])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.documentos.plantillas')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Plantillas',
        'ruta' => 'controllers/plantillas_editar.php',
        'activo' => $paginaActual === 'plantillas_editar.php'
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.reportes.ver')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Reportes',
        'ruta' => 'controllers/reportes_general.php',
        'activo' => $paginaCoincide([
            'reportes_',
            'reporte_'
        ])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.alertas.ver')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Alertas académicas',
        'ruta' => 'controllers/alertas_listar.php',
        'activo' => $paginaCoincide(['alertas_'])
    ];
}

if (
    $esPersonalGestion
    && usuarioTienePermiso('mg.bitacora.ver')
) {
    $menuSeguimientoMg[] = [
        'nombre' => 'Bitácora',
        'ruta' => 'controllers/bitacora_listar.php',
        'activo' => $paginaCoincide(['bitacora_'])
    ];
}

$menuTutor = [];

if ($esTutor) {
    $menuTutor = [
        [
            'nombre' => 'Mis cartas',
            'ruta' => 'controllers/cartas_tutor_listar.php',
            'activo' => $paginaCoincide([
                'cartas_tutor_',
                'carta_tutor_'
            ])
        ],
        [
            'nombre' => 'Mis materias',
            'ruta' => 'controllers/mis_materias_tutor.php',
            'activo' => $paginaActual === 'mis_materias_tutor.php'
        ],
        [
            'nombre' => 'Mis tutorados',
            'ruta' => 'controllers/tutorados_listar.php',
            'activo' => $paginaCoincide(
                [
                    'tutorados_',
                    'reuniones_',
                    'reunion_',
                    'evidencias_',
                    'evidencia_',
                    'informes_',
                    'informe_'
                ],
                [
                    'tutorado_ver.php'
                ]
            )
        ],
        [
            'nombre' => 'Solicitudes recibidas',
            'ruta' => 'controllers/solicitudes_tutor.php',
            'activo' => $paginaActual === 'solicitudes_tutor.php'
        ],
        [
            'nombre' => 'Mi calendario',
            'ruta' => 'controllers/mi_calendario_tutor.php',
            'activo' => $paginaActual === 'mi_calendario_tutor.php'
        ],
        [
            'nombre' => 'Mi disponibilidad',
            'ruta' => 'controllers/disponibilidad_listar.php',
            'activo' => $paginaCoincide(['disponibilidad_'])
        ]
    ];
}

$menuEstudiante = [];

if ($esEstudiante) {
    $menuEstudiante = [
        [
            'nombre' => 'Seguimiento académico',
            'ruta' => 'controllers/mi_proceso_listar.php',
            'activo' => $paginaCoincide(['mi_proceso_'])
        ],
        [
            'nombre' => 'Solicitar tutoría',
            'ruta' => 'controllers/solicitudes_crear.php',
            'activo' => $paginaActual === 'solicitudes_crear.php'
        ],
        [
            'nombre' => 'Mi calendario',
            'ruta' => 'controllers/mi_calendario.php',
            'activo' => $paginaActual === 'mi_calendario.php'
        ],
        [
            'nombre' => 'Mis calificaciones',
            'ruta' => 'controllers/mis_calificaciones.php',
            'activo' => $paginaActual === 'mis_calificaciones.php'
        ]
    ];
}

/*
 * Sincronizamos las notificaciones como máximo una vez cada cinco
 * minutos para no repetir consultas pesadas en cada página.
 */
$notificacionesNoLeidas = 0;

if ($usuarioSesion) {
    try {
        $modeloNotificacion = new NotificacionModel($pdo);
        $ahora = time();

        $ultimaSincronizacion = (int) (
            $_SESSION['sincronizacion_notificaciones'] ?? 0
        );

        if (
            $ultimaSincronizacion === 0
            || ($ahora - $ultimaSincronizacion) >= 300
        ) {
            $_SESSION['sincronizacion_notificaciones'] = $ahora;
            $modeloNotificacion->sincronizar();
        }

        $notificacionesNoLeidas =
            $modeloNotificacion->contarNoLeidas(
                (int) $usuarioSesion['id_usuario']
            );
    } catch (Throwable $e) {
        error_log(
            'No se pudo cargar el contador de notificaciones: '
            . $e->getMessage()
        );
    }
}

?>

<nav class="navbar navbar-expand-xl app-navbar sticky-top">
    <div class="container">
        <a
            class="navbar-brand"
            href="<?= $urlBase ?>index.php"
        >
            <span class="brand-logo-container">
                <img
                    src="<?= $urlBase ?>assets/img/logo-upds.png"
                    alt="Universidad Privada Domingo Savio"
                    class="brand-logo"
                >
            </span>

            <span class="brand-description">
                Sistema académico
            </span>
        </a>

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
                class="navbar-nav ms-auto
                    align-items-xl-center gap-xl-2"
            >
                <?php if ($usuarioSesion): ?>
                    <a
                        class="nav-link <?= $inicioActivo
                            ? 'active'
                            : '' ?>"
                        href="<?= $urlBase ?>index.php"
                    >
                        Inicio
                    </a>
                <?php endif; ?>

                <?php if (!empty($menuAdministracion)): ?>
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle
                                <?= $administracionActiva
                                    ? 'active'
                                    : '' ?>"
                            href="#"
                            id="menuAdministracion"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Administración
                        </a>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="menuAdministracion"
                        >
                            <?php foreach (
                                $menuAdministracion as $opcion
                            ): ?>
                                <?php if (
                                    !empty($opcion['separador'])
                                ): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a
                                            class="dropdown-item
                                                <?= $opcion['activo']
                                                    ? 'active'
                                                    : '' ?>"
                                            href="<?= $urlBase
                                                . $opcion['ruta'] ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $opcion['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($menuGestionMg)): ?>
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle
                                <?= $gestionMgActiva
                                    ? 'active'
                                    : '' ?>"
                            href="#"
                            id="menuGestionMg"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Gestión MG
                        </a>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="menuGestionMg"
                        >
                            <?php foreach (
                                $menuGestionMg as $opcion
                            ): ?>
                                <?php if (
                                    !empty($opcion['separador'])
                                ): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a
                                            class="dropdown-item
                                                <?= $opcion['activo']
                                                    ? 'active'
                                                    : '' ?>"
                                            href="<?= $urlBase
                                                . $opcion['ruta'] ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $opcion['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($menuSeguimientoMg)): ?>
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle
                                <?= $seguimientoMgActivo
                                    ? 'active'
                                    : '' ?>"
                            href="#"
                            id="menuSeguimientoMg"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Seguimiento
                        </a>

                        <ul
                            class="dropdown-menu dropdown-menu-end"
                            aria-labelledby="menuSeguimientoMg"
                        >
                            <?php foreach (
                                $menuSeguimientoMg as $opcion
                            ): ?>
                                <?php if (
                                    !empty($opcion['separador'])
                                ): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a
                                            class="dropdown-item
                                                <?= $opcion['activo']
                                                    ? 'active'
                                                    : '' ?>"
                                            href="<?= $urlBase
                                                . $opcion['ruta'] ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $opcion['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($menuTutor)): ?>
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle
                                <?= $seguimientoTutorActivo
                                    ? 'active'
                                    : '' ?>"
                            href="#"
                            id="menuTutor"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Mi seguimiento
                        </a>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="menuTutor"
                        >
                            <?php foreach (
                                $menuTutor as $opcion
                            ): ?>
                                <li>
                                    <a
                                        class="dropdown-item
                                            <?= $opcion['activo']
                                                ? 'active'
                                                : '' ?>"
                                        href="<?= $urlBase
                                            . $opcion['ruta'] ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $opcion['nombre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($menuEstudiante)): ?>
                    <div class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle
                                <?= $seguimientoEstudianteActivo
                                    ? 'active'
                                    : '' ?>"
                            href="#"
                            id="menuEstudiante"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Mi proceso
                        </a>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="menuEstudiante"
                        >
                            <?php foreach (
                                $menuEstudiante as $opcion
                            ): ?>
                                <li>
                                    <a
                                        class="dropdown-item
                                            <?= $opcion['activo']
                                                ? 'active'
                                                : '' ?>"
                                        href="<?= $urlBase
                                            . $opcion['ruta'] ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $opcion['nombre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($usuarioSesion): ?>
                    <a
                        class="nav-link position-relative
                            d-flex align-items-center gap-2
                            <?= $notificacionesActivo
                                ? 'active'
                                : '' ?>"
                        href="<?= $urlBase ?>controllers/notificaciones_listar.php"
                        aria-label="Notificaciones"
                        title="Notificaciones"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path
                                d="M18 8a6 6 0 0 0-12 0
                                    c0 7-3 7-3 9h18
                                    c0-2-3-2-3-9"
                            ></path>

                            <path d="M13.73 21a2 2 0 0 1-3.46 0">
                            </path>
                        </svg>

                        <span class="d-xl-none">
                            Notificaciones
                        </span>

                        <?php if (
                            $notificacionesNoLeidas > 0
                        ): ?>
                            <span
                                class="badge rounded-pill
                                    text-bg-danger"
                            >
                                <?= $notificacionesNoLeidas > 99
                                    ? '99+'
                                    : $notificacionesNoLeidas ?>
                            </span>
                        <?php endif; ?>
                    </a>

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
                                $nombresRoles[$rolActual]
                                    ?? ucfirst($rolActual),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>

                    <form method="POST" action="<?= $urlBase ?>controllers/logout.php" class="session-logout">
                        <?= campoCsrf() ?>
                        <button type="submit" class="session-link">
                            Cerrar sesión
                        </button>
                    </form>
                <?php else: ?>
                    <a
                        class="session-link"
                        href="<?= $urlBase ?>controllers/login.php"
                    >
                        Iniciar sesión
                    </a>
                <?php endif; ?>

                <button
                    class="theme-button"
                    id="botonTema"
                    type="button"
                    aria-label="Activar modo oscuro"
                    aria-pressed="false"
                >
                    <span class="theme-track" aria-hidden="true">
                        <svg class="theme-icon theme-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
                        </svg>
                        <svg class="theme-icon theme-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M20.9 13.1A9 9 0 0 1 10.9 3.1a9 9 0 1 0 10 10Z"></path>
                        </svg>
                        <span class="theme-knob"></span>
                    </span>
                    <span class="theme-label" id="textoTema">Modo claro</span>
                </button>
            </div>
        </div>
    </div>
</nav>
