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
                        Control académico
                    </p>

                    <h1>
                        <?= htmlspecialchars(
                            $reunion['tema'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        Reunión registrada por
                        <?= htmlspecialchars(
                            $reunion['nombre_tutor']
                            . ' '
                            . $reunion['apellido_tutor'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/reuniones_revision_listar.php"
                    class="secondary-link"
                >
                    Volver a pendientes
                </a>
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

            <div class="form-container form-container-wide">
                <div class="form-section-heading">
                    <div>
                        <h2>
                            <?= htmlspecialchars(
                                $reunion['titulo_trabajo']
                                    ?: 'Trabajo sin título definido',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars(
                                $reunion['nombre_estudiante']
                                . ' '
                                . $reunion['apellido_estudiante'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <?php
                    $claseValidacion = match (
                        $reunion['estado_validacion']
                    ) {
                        'validada' => 'status-active',
                        'observada' => 'status-inactive',
                        default => 'status-pending'
                    };
                    ?>

                    <span
                        class="status-label <?= $claseValidacion ?>"
                    >
                        <?= htmlspecialchars(
                            ucfirst(
                                $reunion['estado_validacion']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Fecha</label>

                        <p>
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
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Horario</label>

                        <p>
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
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Modalidad</label>

                        <p>
                            <?= htmlspecialchars(
                                ucfirst(
                                    $reunion['modalidad']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Lugar o enlace</label>

                        <p>
                            <?= htmlspecialchars(
                                $reunion['lugar_enlace']
                                    ?: 'No especificado',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Asistencia del tutor</label>

                        <p>
                            <?= htmlspecialchars(
                                ucfirst(
                                    $reunion['asistencia_tutor']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Asistencia del estudiante</label>

                        <p>
                            <?= htmlspecialchars(
                                ucfirst(
                                    $reunion['asistencia_estudiante']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Acuerdos</label>

                    <p>
                        <?= nl2br(
                            htmlspecialchars(
                                $reunion['acuerdos']
                                    ?: 'Sin acuerdos registrados',
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ) ?>
                    </p>
                </div>

                <?php if (
                    !empty($reunion['observaciones'])
                ): ?>
                    <div class="form-group">
                        <label>Observaciones del tutor</label>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $reunion['observaciones'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="form-section-heading">
                    <div>
                        <h2>Evidencias</h2>

                        <p>
                            <?= count($evidencias) ?>
                            <?= count($evidencias) === 1
                                ? 'archivo adjunto'
                                : 'archivos adjuntos' ?>
                        </p>
                    </div>
                </div>

                <?php if (empty($evidencias)): ?>
                    <div class="alert alert-warning">
                        La reunión no tiene evidencias adjuntas.
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Archivo</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Tamaño</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach (
                                    $evidencias as $evidencia
                                ): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars(
                                                $evidencia['nombre_original'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $evidencia['tipo']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $evidencia['descripcion']
                                                    ?: 'Sin descripción',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= number_format(
                                                (int) $evidencia['tamano_bytes']
                                                / 1024,
                                                1
                                            ) ?> KB
                                        </td>

                                        <td>
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/evidencia_revision_descargar.php?id=<?= (int) $evidencia['id_evidencia'] ?>"
                                                class="table-link"
                                            >
                                                Descargar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (
                    $reunion['estado_validacion'] === 'pendiente'
                ): ?>
                    <div class="form-section-heading">
                        <div>
                            <h2>Decisión de coordinación</h2>

                            <p>
                                Valida el registro o solicita una
                                corrección al tutor.
                            </p>
                        </div>
                    </div>

                    <form method="POST" class="module-form">
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_reunion"
                            value="<?= (int) $idReunion ?>"
                        >

                        <div class="form-group">
                            <label for="observacion">
                                Observación
                            </label>

                            <textarea
                                id="observacion"
                                name="observacion"
                                rows="4"
                                maxlength="255"
                                placeholder="Obligatoria cuando la reunión será observada"
                            ><?= htmlspecialchars(
                                $observacion,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button
                                type="submit"
                                name="decision"
                                value="validada"
                                class="primary-action"
                                onclick="return confirm('¿Confirmas la validación de esta reunión?');"
                            >
                                Validar reunión
                            </button>

                            <button
                                type="submit"
                                name="decision"
                                value="observada"
                                class="secondary-action"
                                onclick="return confirm('¿Deseas devolver la reunión con observaciones?');"
                            >
                                Observar reunión
                            </button>
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