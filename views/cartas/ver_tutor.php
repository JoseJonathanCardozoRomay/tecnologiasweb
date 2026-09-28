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
                        Designación de tutor
                    </p>

                    <h1>Carta de designación</h1>

                    <p>
                        Revisa la información del estudiante y registra
                        tu respuesta a la asignación.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/cartas_tutor_listar.php"
                    class="secondary-link"
                >
                    Volver a mis cartas
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
                                $carta['titulo_trabajo']
                                    ?: 'Trabajo sin título definido',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p>
                            Expediente
                            #<?= (int) $carta['id_expediente'] ?>
                        </p>
                    </div>

                    <span
                        class="status-label <?= $carta['estado'] === 'aceptada'
                            ? 'status-active'
                            : (
                                $carta['estado'] === 'pendiente'
                                    ? 'status-pending'
                                    : 'status-inactive'
                            ) ?>"
                    >
                        <?= htmlspecialchars(
                            ucfirst($carta['estado']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Estudiante</label>

                        <p>
                            <?= htmlspecialchars(
                                $carta['nombre_estudiante']
                                . ' '
                                . $carta['apellido_estudiante'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Correo</label>

                        <p>
                            <?= htmlspecialchars(
                                $carta['correo_estudiante'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Modalidad</label>

                        <p>
                            <?= htmlspecialchars(
                                $carta['nombre_modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Cohorte</label>

                        <p>
                            <?= htmlspecialchars(
                                $carta['nombre_cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Etapa actual</label>

                        <p>
                            <?= htmlspecialchars(
                                strtoupper(
                                    $carta['etapa_actual']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Fecha de designación</label>

                        <p>
                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $carta['fecha_generacion']
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>

                <?php if ($documentoCarta): ?>
                    <p><a class="secondary-link" href="documento_ver.php?id=<?= (int) $documentoCarta['id_documento'] ?>">
                        Ver e imprimir carta <?= htmlspecialchars($carta['numero_carta'], ENT_QUOTES, 'UTF-8') ?>
                    </a></p>
                <?php endif; ?>

                <?php if (
                    !empty($carta['responsabilidades'])
                ): ?>
                    <div class="form-group">
                        <label>Responsabilidades</label>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $carta['responsabilidades'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($carta['estado'] === 'pendiente'): ?>
                    <div class="form-section-heading">
                        <div>
                            <h2>Responder designación</h2>

                            <p>
                                La respuesta quedará registrada y no
                                podrá modificarse posteriormente.
                            </p>
                        </div>
                    </div>

                    <form
                        method="POST"
                        class="module-form"
                        onsubmit="return confirm('¿Confirmas que deseas aceptar esta designación?');"
                    >
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_carta"
                            value="<?= (int) $idCarta ?>"
                        >

                        <input
                            type="hidden"
                            name="respuesta"
                            value="aceptada"
                        >

                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Aceptar designación
                        </button>
                    </form>

                    <div class="form-section-heading">
                        <div>
                            <h2>Rechazar designación</h2>

                            <p>
                                El expediente volverá a coordinación
                                para que se asigne otro tutor.
                            </p>
                        </div>
                    </div>

                    <form
                        method="POST"
                        class="module-form"
                        onsubmit="return confirm('¿Confirmas que deseas rechazar esta designación?');"
                    >
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_carta"
                            value="<?= (int) $idCarta ?>"
                        >

                        <input
                            type="hidden"
                            name="respuesta"
                            value="rechazada"
                        >

                        <div class="form-group">
                            <label for="tipo_rechazo">
                                Tipo de rechazo
                            </label>

                            <select
                                id="tipo_rechazo"
                                name="tipo_rechazo"
                                required
                            >
                                <option value="">
                                    Selecciona una opción
                                </option>

                                <option
                                    value="sin_capacidad"
                                    <?= $tipoRechazo === 'sin_capacidad'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Sin capacidad disponible
                                </option>

                                <option
                                    value="sin_tiempo"
                                    <?= $tipoRechazo === 'sin_tiempo'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Sin disponibilidad de tiempo
                                </option>

                                <option
                                    value="no_corresponde"
                                    <?= $tipoRechazo === 'no_corresponde'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    No corresponde a mi especialidad
                                </option>

                                <option
                                    value="otro"
                                    <?= $tipoRechazo === 'otro'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Otro motivo
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="motivo_rechazo">
                                Explicación
                            </label>

                            <textarea
                                id="motivo_rechazo"
                                name="motivo_rechazo"
                                rows="4"
                                maxlength="255"
                                required
                                placeholder="Explica brevemente el motivo"
                            ><?= htmlspecialchars(
                                $motivoRechazo,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>

                            <p class="field-help">
                                Máximo 255 caracteres.
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="secondary-action"
                        >
                            Rechazar designación
                        </button>
                    </form>
                <?php else: ?>
                    <div class="form-section-heading">
                        <div>
                            <h2>Respuesta registrada</h2>

                            <p>
                                Esta carta fue respondida el
                                <?= htmlspecialchars(
                                    date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $carta['fecha_respuesta']
                                        )
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>.
                            </p>
                        </div>
                    </div>

                    <?php if (
                        $carta['estado'] === 'rechazada'
                    ): ?>
                        <div class="form-group">
                            <label>Motivo registrado</label>

                            <p>
                                <?= htmlspecialchars(
                                    $carta['motivo_rechazo']
                                        ?: 'Sin detalle',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>
