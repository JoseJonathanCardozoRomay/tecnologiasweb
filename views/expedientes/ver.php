<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

// Funciones pequeñas para mantener la vista más limpia
$escapar = static function ($valor): string {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
};

$formatearFecha = static function (
    ?string $fecha,
    bool $incluirHora = false
): string {
    if (!$fecha) {
        return 'Pendiente';
    }

    return date(
        $incluirHora ? 'd/m/Y H:i' : 'd/m/Y',
        strtotime($fecha)
    );
};

$nombresEtapa = [
    'previa' => 'Etapa previa',
    'mg1' => 'Modalidad de Grado I',
    'mg2' => 'Modalidad de Grado II',
    'finalizado' => 'Finalizado'
];

$nombresEstado = [
    'activo' => 'Activo',
    'aprobado' => 'Aprobado',
    'reprobado' => 'Reprobado',
    'abandono' => 'Abandono',
    'retirado' => 'Retirado'
];

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Expediente académico
                    </p>

                    <h1>
                        <?= $escapar(
                            $expediente['nombre']
                            . ' '
                            . $expediente['apellido']
                        ) ?>
                    </h1>

                    <p>
                        <?= $escapar(
                            $expediente['nombre_modalidad']
                            . ' · '
                            . $expediente['codigo_cohorte']
                        ) ?>
                    </p>
                </div>

                <div class="form-actions">
                    <?php if ($puedeEditar): ?>
                        <a
                            href="<?= $escapar(
                                $rutaBase
                            ) ?>controllers/expedientes_editar.php?id=<?= (int) $idExpediente ?>"
                            class="primary-action"
                        >
                            Editar expediente
                        </a>
                    <?php endif; ?>

                    <a
                        href="<?= $escapar(
                            $rutaBase
                        ) ?>controllers/expedientes_listar.php"
                        class="secondary-link"
                    >
                        Volver al listado
                    </a>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-<?= $escapar(
                        $tipoMensaje
                    ) ?>"
                    role="alert"
                >
                    <?= $escapar($mensaje) ?>
                </div>
            <?php endif; ?>

            <ul
                class="nav nav-tabs mb-4"
                id="pestanasExpediente"
                role="tablist"
            >
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link active"
                        id="datos-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#datos"
                        type="button"
                        role="tab"
                        aria-controls="datos"
                        aria-selected="true"
                    >
                        Datos
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="tutor-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#tutor"
                        type="button"
                        role="tab"
                        aria-controls="tutor"
                        aria-selected="false"
                    >
                        Tutor
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="tribunales-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#tribunales"
                        type="button"
                        role="tab"
                        aria-controls="tribunales"
                        aria-selected="false"
                    >
                        Tribunales y defensas
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="reuniones-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#reuniones"
                        type="button"
                        role="tab"
                        aria-controls="reuniones"
                        aria-selected="false"
                    >
                        Reuniones
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="informes-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#informes"
                        type="button"
                        role="tab"
                        aria-controls="informes"
                        aria-selected="false"
                    >
                        Informes
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="documentos-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#documentos"
                        type="button"
                        role="tab"
                        aria-controls="documentos"
                        aria-selected="false"
                    >
                        Documentos
                    </button>
                </li>
            </ul>

            <div
                class="tab-content"
                id="contenidoExpediente"
            >
                <!-- Datos generales -->
                <div
                    class="tab-pane fade show active"
                    id="datos"
                    role="tabpanel"
                    aria-labelledby="datos-tab"
                    tabindex="0"
                >
                    <div class="form-container form-container-wide">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Estudiante</span>

                                    <strong>
                                        <?= $escapar(
                                            $expediente['nombre']
                                            . ' '
                                            . $expediente['apellido']
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Registro universitario</span>

                                    <strong>
                                        <?= $escapar(
                                            $expediente[
                                                'registro_universitario'
                                            ] ?: 'Sin registro'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Carrera</span>

                                    <strong>
                                        <?= $escapar(
                                            $expediente['nombre_carrera']
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Correo</span>

                                    <strong>
                                        <?= $escapar(
                                            $expediente['correo']
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Modalidad</span>

                                    <strong>
                                        <?= $escapar(
                                            $expediente[
                                                'nombre_modalidad'
                                            ]
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Cohorte</span>

                                    <strong>
                                        <?= $escapar(
                                            $expediente['codigo_cohorte']
                                            . ' - '
                                            . $expediente[
                                                'nombre_cohorte'
                                            ]
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-cell">
                                    <span>Etapa actual</span>

                                    <strong>
                                        <?= $escapar(
                                            $nombresEtapa[
                                                $expediente[
                                                    'etapa_actual'
                                                ]
                                            ] ?? $expediente[
                                                'etapa_actual'
                                            ]
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-cell">
                                    <span>Estado</span>

                                    <strong>
                                        <?= $escapar(
                                            $nombresEstado[
                                                $expediente['estado']
                                            ] ?? $expediente['estado']
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-cell">
                                    <span>Fecha de inicio</span>

                                    <strong>
                                        <?= $escapar(
                                            $formatearFecha(
                                                $expediente[
                                                    'fecha_inicio'
                                                ]
                                            )
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="user-cell">
                                    <span>Título del trabajo</span>

                                    <strong>
                                        <?= $escapar(
                                            $expediente[
                                                'titulo_trabajo'
                                            ] ?: 'Todavía no definido'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="user-cell">
                                    <span>Observaciones</span>

                                    <strong>
                                        <?= nl2br(
                                            $escapar(
                                                $expediente[
                                                    'observaciones'
                                                ] ?: 'Sin observaciones'
                                            )
                                        ) ?>
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="form-section-heading mt-5">
                            <div>
                                <h2>Historial de etapas</h2>

                                <p>
                                    Registro cronológico del avance del
                                    estudiante dentro del proceso.
                                </p>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Etapa</th>
                                        <th>Inicio</th>
                                        <th>Finalización</th>
                                        <th>Resultado</th>
                                        <th>Registrado por</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (empty($historialEtapas)): ?>
                                        <tr>
                                            <td
                                                colspan="5"
                                                class="empty-result"
                                            >
                                                No existe historial de etapas.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach (
                                            $historialEtapas
                                            as $etapaHistorial
                                        ): ?>
                                            <tr>
                                                <td>
                                                    <?= $escapar(
                                                        $nombresEtapa[
                                                            $etapaHistorial[
                                                                'etapa'
                                                            ]
                                                        ] ?? $etapaHistorial[
                                                            'etapa'
                                                        ]
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        $formatearFecha(
                                                            $etapaHistorial[
                                                                'fecha_inicio'
                                                            ]
                                                        )
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        $etapaHistorial[
                                                            'fecha_fin'
                                                        ]
                                                            ? $formatearFecha(
                                                                $etapaHistorial[
                                                                    'fecha_fin'
                                                                ]
                                                            )
                                                            : 'En curso'
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        $etapaHistorial[
                                                            'resultado'
                                                        ] ?: 'Pendiente'
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        $etapaHistorial[
                                                            'nombre_registrador'
                                                        ]
                                                        . ' '
                                                        . $etapaHistorial[
                                                            'apellido_registrador'
                                                        ]
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Asignación del tutor -->
                <div
                    class="tab-pane fade"
                    id="tutor"
                    role="tabpanel"
                    aria-labelledby="tutor-tab"
                    tabindex="0"
                >
                    <div class="form-container form-container-wide">
                        <div class="form-section-heading">
                            <div>
                                <h2>Tutor asignado</h2>

                                <p>
                                    Designación vigente e historial de
                                    tutores del expediente.
                                </p>
                            </div>

                            <?php if (
                                !$asignacionTutor
                                && (int) $expediente[
                                    'requiere_tutor'
                                ] === 1
                                && $expediente['estado'] === 'activo'
                                && $expediente[
                                    'etapa_actual'
                                ] === 'previa'
                                && $puedeAsignarTutor
                            ): ?>
                                <a
                                    href="<?= $escapar(
                                        $rutaBase
                                    ) ?>controllers/expedientes_asignar_tutor.php?id=<?= (int) $idExpediente ?>"
                                    class="primary-action"
                                >
                                    Asignar tutor
                                </a>
                            <?php endif; ?>
                            <?php if (
                                $asignacionTutor && $puedeCambiarTutor
                                && $expediente['estado'] === 'activo'
                                && $expediente['etapa_actual'] !== 'previa'
                            ): ?>
                                <a href="<?= $escapar($rutaBase) ?>controllers/expedientes_cambiar_tutor.php?id=<?= (int) $idExpediente ?>"
                                   class="secondary-action">Cambiar tutor</a>
                            <?php endif; ?>
                        </div>

                        <?php if (
                            (int) $expediente[
                                'requiere_tutor'
                            ] !== 1
                        ): ?>
                            <div
                                class="alert alert-info"
                                role="alert"
                            >
                                Esta modalidad no requiere la asignación
                                de un tutor.
                            </div>
                        <?php elseif ($asignacionTutor): ?>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="user-cell">
                                        <span>Tutor</span>

                                        <strong>
                                            <?= $escapar(
                                                $asignacionTutor['nombre']
                                                . ' '
                                                . $asignacionTutor[
                                                    'apellido'
                                                ]
                                            ) ?>
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="user-cell">
                                        <span>Especialidad</span>

                                        <strong>
                                            <?= $escapar(
                                                $asignacionTutor[
                                                    'especialidad'
                                                ] ?: 'Sin especialidad'
                                            ) ?>
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="user-cell">
                                        <span>Correo</span>

                                        <strong>
                                            <?= $escapar(
                                                $asignacionTutor['correo']
                                            ) ?>
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="user-cell">
                                        <span>Fecha de asignación</span>

                                        <strong>
                                            <?= $escapar(
                                                $formatearFecha(
                                                    $asignacionTutor[
                                                        'fecha_asignacion'
                                                    ],
                                                    true
                                                )
                                            ) ?>
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="user-cell">
                                        <span>Número de carta</span>

                                        <strong>
                                            <?= $escapar(
                                                $asignacionTutor[
                                                    'numero_carta'
                                                ] ?: 'Pendiente de numeración'
                                            ) ?>
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="user-cell">
                                        <span>Referencia de Decanatura</span>
                                        <strong><?= $escapar($asignacionTutor['referencia_decanatura'] ?: 'Asignación anterior') ?></strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="user-cell">
                                        <span>Estado de la carta</span>

                                        <strong>
                                            <?= $escapar(
                                                ucfirst(
                                                    $asignacionTutor[
                                                        'estado_carta'
                                                    ] ?? 'pendiente'
                                                )
                                            ) ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div
                                class="alert alert-warning"
                                role="alert"
                            >
                                El expediente todavía no tiene un tutor
                                asignado.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($historialTutores)): ?>
                            <div class="form-section-heading mt-5">
                                <div>
                                    <h2>Historial de tutores</h2>

                                    <p>
                                        Las asignaciones anteriores se
                                        conservan como respaldo.
                                    </p>
                                </div>
                            </div>

                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Tutor</th>
                                            <th>Inicio</th>
                                            <th>Finalización</th>
                                            <th>Estado</th>
                                            <th>Carta</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach (
                                            $historialTutores
                                            as $tutorHistorial
                                        ): ?>
                                            <tr>
                                                <td>
                                                    <div class="user-cell">
                                                        <strong>
                                                            <?= $escapar(
                                                                $tutorHistorial[
                                                                    'nombre'
                                                                ]
                                                                . ' '
                                                                . $tutorHistorial[
                                                                    'apellido'
                                                                ]
                                                            ) ?>
                                                        </strong>

                                                        <span>
                                                            <?= $escapar(
                                                                $tutorHistorial[
                                                                    'especialidad'
                                                                ] ?: 'Sin especialidad'
                                                            ) ?>
                                                        </span>
                                                        <?php if ($tutorHistorial['motivo_fin']): ?>
                                                            <span><?= $escapar($tutorHistorial['motivo_fin']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        $formatearFecha(
                                                            $tutorHistorial[
                                                                'fecha_asignacion'
                                                            ]
                                                        )
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        $tutorHistorial[
                                                            'fecha_fin'
                                                        ]
                                                            ? $formatearFecha(
                                                                $tutorHistorial[
                                                                    'fecha_fin'
                                                                ]
                                                            )
                                                            : 'En curso'
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        ucfirst(
                                                            $tutorHistorial[
                                                                'estado'
                                                            ]
                                                        )
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= $escapar(
                                                        $tutorHistorial[
                                                            'numero_carta'
                                                        ] ?: 'Pendiente'
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Módulos siguientes -->
                <div
                    class="tab-pane fade"
                    id="tribunales"
                    role="tabpanel"
                    aria-labelledby="tribunales-tab"
                    tabindex="0"
                >
                    <div class="form-container">
                        <h2>Tribunales y defensas</h2>

                        <p>Consulta asignaciones de tribunal y defensas de MG1 y MG2.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="secondary-link" href="tribunales_listar.php">Ver tribunales</a>
                            <a class="secondary-link" href="defensas_listar.php">Ver defensas</a>
                        </div>
                    </div>
                </div>

                <div
                    class="tab-pane fade"
                    id="reuniones"
                    role="tabpanel"
                    aria-labelledby="reuniones-tab"
                    tabindex="0"
                >
                    <div class="form-container">
                        <h2>Reuniones</h2>

                        <p>Consulta reuniones, asistencia y evidencias del proceso.</p>
                        <a class="secondary-link" href="reuniones_listar.php">Ver reuniones</a>
                    </div>
                </div>

                <div
                    class="tab-pane fade"
                    id="informes"
                    role="tabpanel"
                    aria-labelledby="informes-tab"
                    tabindex="0"
                >
                    <div class="form-container">
                        <h2>Informes de avance</h2>

                        <p>Consulta informes vinculados a los hitos de la cohorte.</p>
                        <a class="secondary-link" href="informes_listar.php">Ver informes</a>
                    </div>
                </div>

                <div
                    class="tab-pane fade"
                    id="documentos"
                    role="tabpanel"
                    aria-labelledby="documentos-tab"
                    tabindex="0"
                >
                    <div class="form-container">
                        <h2>Documentos generados</h2>

                        <p>Las cartas emitidas y las citaciones conservan su contenido original.</p>
                        <a class="secondary-link" href="documentos_listar.php">Ver documentos</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>
