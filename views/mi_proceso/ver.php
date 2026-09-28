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
                        Mi proceso de grado
                    </p>

                    <h1>
                        <?= htmlspecialchars(
                            $expediente['titulo_trabajo']
                                ?: 'Trabajo sin título definido',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        <?= htmlspecialchars(
                            $expediente['nombre_modalidad'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        ·
                        <?= htmlspecialchars(
                            $expediente['nombre_cohorte'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/mi_proceso_listar.php"
                    class="secondary-link"
                >
                    Volver a mis procesos
                </a>
            </div>

            <div class="form-container form-container-wide">
                <div class="form-section-heading">
                    <div>
                        <h2>Información académica</h2>

                        <p>
                            Expediente
                            #<?= (int) $expediente['id_expediente'] ?>
                        </p>
                    </div>

                    <span
                        class="status-label <?= $expediente['estado'] === 'activo'
                            ? 'status-active'
                            : 'status-inactive' ?>"
                    >
                        <?= htmlspecialchars(
                            ucfirst($expediente['estado']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Etapa actual</label>

                        <p>
                            <strong>
                                <?= htmlspecialchars(
                                    strtoupper(
                                        $expediente['etapa_actual']
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Fecha de inicio</label>

                        <p>
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
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Tutor asignado</label>

                        <p>
                            <?php if (
                                $expediente['nombre_tutor']
                            ): ?>
                                <?= htmlspecialchars(
                                    $expediente['nombre_tutor']
                                    . ' '
                                    . $expediente['apellido_tutor'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            <?php else: ?>
                                Pendiente de asignación
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Especialidad</label>

                        <p>
                            <?= htmlspecialchars(
                                $expediente['especialidad']
                                    ?: 'No especificada',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>

                <?php if (
                    !empty($expediente['observaciones'])
                ): ?>
                    <div class="form-group">
                        <label>Observaciones del expediente</label>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $expediente['observaciones'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-container form-container-wide mt-4">
                <div class="form-section-heading">
                    <div>
                        <h2>Reuniones</h2>

                        <p>
                            Consulta las reuniones programadas y el
                            seguimiento registrado.
                        </p>
                    </div>

                    <span>
                        <?= count($reuniones) ?>
                        <?= count($reuniones) === 1
                            ? 'reunión'
                            : 'reuniones' ?>
                    </span>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tema</th>
                                <th>Modalidad</th>
                                <th>Tutor</th>
                                <th>Estado</th>
                                <th>Asistencia</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($reuniones)): ?>
                                <tr>
                                    <td
                                        colspan="6"
                                        class="empty-result"
                                    >
                                        Todavía no tienes reuniones registradas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (
                                    $reuniones as $reunion
                                ): ?>
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <strong>
                                                    <?= htmlspecialchars(
                                                        date(
                                                            'd/m/Y',
                                                            strtotime(
                                                                $reunion['fecha_reunion']
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </strong>

                                                <span>
                                                    <?= htmlspecialchars(
                                                        substr(
                                                            $reunion['hora_inicio'],
                                                            0,
                                                            5
                                                        )
                                                        . ' - '
                                                        . substr(
                                                            $reunion['hora_fin'],
                                                            0,
                                                            5
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $reunion['tema'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $reunion['modalidad']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $reunion['nombre_tutor']
                                                . ' '
                                                . $reunion['apellido_tutor'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?php
                                            $claseEstado = $reunion['estado']
                                                === 'realizada'
                                                ? 'status-active'
                                                : 'status-pending';
                                            ?>

                                            <span
                                                class="status-label <?= $claseEstado ?>"
                                            >
                                                <?= htmlspecialchars(
                                                    ucfirst(
                                                        $reunion['estado']
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= $reunion['estado'] === 'realizada'
                                                ? htmlspecialchars(
                                                    ucfirst(
                                                        $reunion['asistencia_estudiante']
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                                : 'Pendiente' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-container form-container-wide mt-4">
                <div class="form-section-heading">
                    <div>
                        <h2>Informes aprobados</h2>

                        <p>
                            Solo se muestran informes revisados y
                            aprobados por coordinación.
                        </p>
                    </div>

                    <span>
                        <?= count($informes) ?>
                        <?= count($informes) === 1
                            ? 'informe'
                            : 'informes' ?>
                    </span>
                </div>

                <?php if (empty($informes)): ?>
                    <div class="empty-result">
                        Todavía no existen informes aprobados.
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach (
                            $informes as $informe
                        ): ?>
                            <div class="col-lg-6">
                                <article class="form-container h-100">
                                    <div class="form-section-heading">
                                        <div>
                                            <h3>
                                                Informe
                                                <?= (int) $informe['numero_informe'] ?>
                                            </h3>

                                            <p>
                                                <?= htmlspecialchars(
                                                    strtoupper(
                                                        $informe['etapa']
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                                ·
                                                <?= (int) $informe['porcentaje_avance'] ?>%
                                            </p>
                                        </div>

                                        <span class="status-label status-active">
                                            Aprobado
                                        </span>
                                    </div>

                                    <div class="form-group">
                                        <label>Resumen</label>

                                        <p>
                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $informe['resumen_avance'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                            ) ?>
                                        </p>
                                    </div>

                                    <?php if (
                                        !empty(
                                            $informe['proximas_actividades']
                                        )
                                    ): ?>
                                        <div class="form-group">
                                            <label>
                                                Próximas actividades
                                            </label>

                                            <p>
                                                <?= nl2br(
                                                    htmlspecialchars(
                                                        $informe['proximas_actividades'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )
                                                ) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>