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
                        Seguimiento de reuniones
                    </p>

                    <h1>
                        <?= htmlspecialchars(
                            $reunion['tema'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        Tutoría de
                        <?= htmlspecialchars(
                            $reunion['nombre_estudiante']
                            . ' '
                            . $reunion['apellido_estudiante'],
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
                    ) ?>controllers/reuniones_listar.php?expediente=<?= (int) $reunion['id_expediente'] ?>"
                    class="secondary-link"
                >
                    Volver a reuniones
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-success"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

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
                        <h2>Información programada</h2>

                        <p>
                            Datos principales de la reunión.
                        </p>
                    </div>

                    <?php
                    $claseEstado = match (
                        $reunion['estado']
                    ) {
                        'realizada' => 'status-active',
                        'cancelada' => 'status-inactive',
                        default => 'status-pending'
                    };
                    ?>

                    <span
                        class="status-label <?= $claseEstado ?>"
                    >
                        <?= htmlspecialchars(
                            ucfirst($reunion['estado']),
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
                </div>

                <?php if (
                    $reunion['estado'] === 'realizada'
                ): ?>
                    <div class="form-section-heading">
                        <div>
                            <h2>Resultado de la reunión</h2>

                            <p>
                                Registro de asistencia y acuerdos.
                            </p>
                        </div>
                    </div>

                    <div class="form-grid">
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
                                    $reunion['acuerdos'],
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
                            <label>Observaciones</label>

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
                                Adjunta documentos o fotografías que
                                respalden la realización de la reunión.
                            </p>
                        </div>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/evidencias_reunion.php?reunion=<?= (int) $idReunion ?>"
                            class="primary-action"
                        >
                            Gestionar evidencias
                        </a>
                    </div>
                <?php elseif (
                    $reunion['estado'] === 'programada'
                ): ?>
                    <div class="form-section-heading">
                        <div>
                            <h2>Registrar resultado</h2>

                            <p>
                                Completa la asistencia y los acuerdos
                                después de realizar la reunión.
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

                        <input
                            type="hidden"
                            name="accion"
                            value="registrar_resultado"
                        >

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="asistencia_tutor">
                                    Asistencia del tutor
                                </label>

                                <select
                                    id="asistencia_tutor"
                                    name="asistencia_tutor"
                                    required
                                >
                                    <option value="">
                                        Selecciona una opción
                                    </option>

                                    <?php foreach (
                                        [
                                            'presente' => 'Presente',
                                            'ausente' => 'Ausente',
                                            'justificada' => 'Justificada'
                                        ] as $valor => $texto
                                    ): ?>
                                        <option
                                            value="<?= $valor ?>"
                                            <?= $asistenciaTutor === $valor
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= $texto ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="asistencia_estudiante">
                                    Asistencia del estudiante
                                </label>

                                <select
                                    id="asistencia_estudiante"
                                    name="asistencia_estudiante"
                                    required
                                >
                                    <option value="">
                                        Selecciona una opción
                                    </option>

                                    <?php foreach (
                                        [
                                            'presente' => 'Presente',
                                            'ausente' => 'Ausente',
                                            'justificada' => 'Justificada'
                                        ] as $valor => $texto
                                    ): ?>
                                        <option
                                            value="<?= $valor ?>"
                                            <?= $asistenciaEstudiante === $valor
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= $texto ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="acuerdos">
                                Acuerdos alcanzados
                            </label>

                            <textarea
                                id="acuerdos"
                                name="acuerdos"
                                rows="5"
                                maxlength="3000"
                                required
                            ><?= htmlspecialchars(
                                $acuerdos,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">
                                Observaciones
                            </label>

                            <textarea
                                id="observaciones"
                                name="observaciones"
                                rows="4"
                                maxlength="3000"
                            ><?= htmlspecialchars(
                                $observaciones,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>

                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Registrar reunión realizada
                        </button>
                    </form>

                    <div class="form-section-heading">
                        <div>
                            <h2>Cancelar reunión</h2>

                            <p>
                                La reunión permanecerá en el historial.
                            </p>
                        </div>
                    </div>

                    <form
                        method="POST"
                        class="module-form"
                        onsubmit="return confirm('¿Confirmas que deseas cancelar esta reunión?');"
                    >
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_reunion"
                            value="<?= (int) $idReunion ?>"
                        >

                        <input
                            type="hidden"
                            name="accion"
                            value="cancelar"
                        >

                        <div class="form-group">
                            <label for="motivo_cancelacion">
                                Motivo de cancelación
                            </label>

                            <textarea
                                id="motivo_cancelacion"
                                name="motivo_cancelacion"
                                rows="3"
                                minlength="5"
                                maxlength="255"
                                required
                            ><?= htmlspecialchars(
                                $motivoCancelacion,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>

                        <button
                            type="submit"
                            class="secondary-action"
                        >
                            Cancelar reunión
                        </button>
                    </form>
                <?php else: ?>
                    <div class="form-group">
                        <label>Motivo de cancelación</label>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $reunion['observaciones']
                                        ?: 'Sin motivo registrado',
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>