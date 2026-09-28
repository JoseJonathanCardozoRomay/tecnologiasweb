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
                        Seguimiento académico
                    </p>

                    <h1>
                        <?= htmlspecialchars(
                            $tutorado['nombre_estudiante']
                            . ' '
                            . $tutorado['apellido_estudiante'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        Administra el seguimiento académico del
                        expediente asignado.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/tutorados_listar.php"
                    class="secondary-link"
                >
                    Volver a mis tutorados
                </a>
            </div>

            <div class="form-container form-container-wide">
                <div class="form-section-heading">
                    <div>
                        <h2>
                            <?= htmlspecialchars(
                                $tutorado['titulo_trabajo']
                                    ?: 'Trabajo sin título definido',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p>
                            Expediente
                            #<?= (int) $tutorado['id_expediente'] ?>
                        </p>
                    </div>

                    <span class="status-label status-active">
                        <?= htmlspecialchars(
                            strtoupper(
                                $tutorado['etapa_actual']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Registro universitario</label>

                        <p>
                            <?= htmlspecialchars(
                                $tutorado['registro_universitario']
                                    ?: 'Sin registro',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Correo institucional</label>

                        <p>
                            <?= htmlspecialchars(
                                $tutorado['correo_estudiante'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Modalidad</label>

                        <p>
                            <?= htmlspecialchars(
                                $tutorado['nombre_modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Cohorte</label>

                        <p>
                            <?= htmlspecialchars(
                                $tutorado['nombre_cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>

                <?php if (
                    !empty($tutorado['observaciones'])
                ): ?>
                    <div class="form-group">
                        <label>Observaciones del expediente</label>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $tutorado['observaciones'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-md-6">
                    <article class="form-container h-100">
                        <p class="section-label">
                            Control de actividades
                        </p>

                        <h2>Reuniones</h2>

                        <p>
                            Registra fechas, asistencia, temas tratados,
                            acuerdos y evidencias de cada reunión.
                        </p>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/reuniones_listar.php?expediente=<?= (int) $tutorado['id_expediente'] ?>"
                            class="primary-action"
                        >
                            Gestionar reuniones
                        </a>
                    </article>
                </div>

                <div class="col-md-6">
                    <article class="form-container h-100">
                        <p class="section-label">
                            Avance académico
                        </p>

                        <h2>Informes</h2>

                        <p>
                            Registra el porcentaje de avance,
                            observaciones y recomendaciones del proceso.
                        </p>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/informes_listar.php?expediente=<?= (int) $tutorado['id_expediente'] ?>"
                            class="primary-action"
                        >
                            Gestionar informes
                        </a>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>