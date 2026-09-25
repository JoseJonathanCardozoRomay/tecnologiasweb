<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

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
                        <?= htmlspecialchars(
                            $expediente['nombre']
                            . ' '
                            . $expediente['apellido'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        <?= htmlspecialchars(
                            $expediente['nombre_modalidad']
                            . ' · '
                            . $expediente['codigo_cohorte'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>
                </div>

                <div class="form-actions">
                    <?php if ($puedeEditar): ?>
                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/expedientes_editar.php?id=<?= (int) $idExpediente ?>"
                            class="primary-action"
                        >
                            Editar expediente
                        </a>
                    <?php endif; ?>

                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/expedientes_listar.php"
                        class="secondary-link"
                    >
                        Volver al listado
                    </a>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-<?= htmlspecialchars(
                        $tipoMensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
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
                                        <?= htmlspecialchars(
                                            $expediente['nombre']
                                            . ' '
                                            . $expediente['apellido'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Registro universitario</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $expediente['registro_universitario']
                                                ?: 'Sin registro',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Carrera</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $expediente['nombre_carrera'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Correo</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $expediente['correo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Modalidad</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $expediente['nombre_modalidad'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="user-cell">
                                    <span>Cohorte</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $expediente['codigo_cohorte']
                                            . ' - '
                                            . $expediente['nombre_cohorte'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-cell">
                                    <span>Etapa actual</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $nombresEtapa[
                                                $expediente['etapa_actual']
                                            ] ?? $expediente['etapa_actual'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-cell">
                                    <span>Estado</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $nombresEstado[
                                                $expediente['estado']
                                            ] ?? $expediente['estado'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-cell">
                                    <span>Fecha de inicio</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y',
                                                strtotime(
                                                    $expediente['fecha_inicio']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="user-cell">
                                    <span>Título del trabajo</span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $expediente['titulo_trabajo']
                                                ?: 'Todavía no definido',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="user-cell">
                                    <span>Observaciones</span>

                                    <strong>
                                        <?= nl2br(
                                            htmlspecialchars(
                                                $expediente['observaciones']
                                                    ?: 'Sin observaciones',
                                                ENT_QUOTES,
                                                'UTF-8'
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

                            <?php if (
                                $puedeCambiarEtapa
                                && $expediente['estado'] === 'activo'
                            ): ?>
                                <a
                                    href="<?= htmlspecialchars(
                                        $rutaBase,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>controllers/expedientes_etapa.php?id=<?= (int) $idExpediente ?>"
                                    class="secondary-action"
                                >
                                    Cambiar etapa
                                </a>
                            <?php endif; ?>
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
                                    <?php foreach (
                                        $historialEtapas as $etapaHistorial
                                    ): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars(
                                                    $nombresEtapa[
                                                        $etapaHistorial['etapa']
                                                    ] ?? $etapaHistorial['etapa'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    date(
                                                        'd/m/Y',
                                                        strtotime(
                                                            $etapaHistorial['fecha_inicio']
                                                        )
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= $etapaHistorial['fecha_fin']
                                                    ? htmlspecialchars(
                                                        date(
                                                            'd/m/Y',
                                                            strtotime(
                                                                $etapaHistorial['fecha_fin']
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )
                                                    : 'En curso' ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $etapaHistorial['resultado']
                                                        ?: 'Pendiente',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $etapaHistorial['nombre_registrador']
                                                    . ' '
                                                    . $etapaHistorial['apellido_registrador'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div
                    class="tab-pane fade"
                    id="tutor"
                    role="tabpanel"
                    aria-labelledby="tutor-tab"
                    tabindex="0"
                >
                    <div class="form-container">
                        <h2>Tutor asignado</h2>

                        <?php if (
                            (int) $expediente['requiere_tutor'] === 1
                        ): ?>
                            <p>
                                Todavía no existe un tutor asignado a este
                                expediente.
                            </p>

                            <?php if ($puedeAsignarTutor): ?>
                                <p class="field-help">
                                    La asignación se habilitará en el módulo
                                    siguiente y conservará todo su historial.
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>
                                Esta modalidad no requiere asignación de
                                tutor.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div
                    class="tab-pane fade"
                    id="tribunales"
                    role="tabpanel"
                    aria-labelledby="tribunales-tab"
                    tabindex="0"
                >
                    <div class="form-container">
                        <h2>Tribunales y defensas</h2>

                        <p>
                            Esta sección se habilitará al implementar las
                            historias HU-028 y HU-029.
                        </p>
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

                        <p>
                            Las reuniones forman parte de la segunda etapa
                            del MVP.
                        </p>
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

                        <p>
                            Los informes se vincularán con los hitos del
                            calendario correspondientes a esta cohorte.
                        </p>
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

                        <p>
                            Aquí se mostrarán las cartas y citaciones
                            generadas para el expediente.
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