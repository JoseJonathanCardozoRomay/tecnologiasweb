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

                    <h1>Programar reunión</h1>

                    <p>
                        Programa una reunión con
                        <?= htmlspecialchars(
                            $tutorado['nombre_estudiante']
                            . ' '
                            . $tutorado['apellido_estudiante'],
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
                    ) ?>controllers/reuniones_listar.php?expediente=<?= (int) $idExpediente ?>"
                    class="secondary-link"
                >
                    Volver a reuniones
                </a>
            </div>

            <div class="form-container form-container-wide">
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

                <form method="POST" class="module-form">
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_expediente"
                        value="<?= (int) $idExpediente ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fecha_reunion">
                                Fecha
                            </label>

                            <input
                                type="date"
                                id="fecha_reunion"
                                name="fecha_reunion"
                                value="<?= htmlspecialchars(
                                    $fechaReunion,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                min="<?= date('Y-m-d') ?>"
                                required
                                autofocus
                            >
                        </div>

                        <div class="form-group">
                            <label for="modalidad">
                                Modalidad
                            </label>

                            <select
                                id="modalidad"
                                name="modalidad"
                                required
                            >
                                <option value="">
                                    Selecciona una modalidad
                                </option>

                                <option
                                    value="presencial"
                                    <?= $modalidad === 'presencial'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Presencial
                                </option>

                                <option
                                    value="virtual"
                                    <?= $modalidad === 'virtual'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Virtual
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">
                                Hora de inicio
                            </label>

                            <input
                                type="time"
                                id="hora_inicio"
                                name="hora_inicio"
                                value="<?= htmlspecialchars(
                                    $horaInicio,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="hora_fin">
                                Hora de finalización
                            </label>

                            <input
                                type="time"
                                id="hora_fin"
                                name="hora_fin"
                                value="<?= htmlspecialchars(
                                    $horaFin,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lugar_enlace">
                            Lugar o enlace
                        </label>

                        <input
                            type="text"
                            id="lugar_enlace"
                            name="lugar_enlace"
                            value="<?= htmlspecialchars(
                                $lugarEnlace,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="255"
                            placeholder="Aula 204 o https://meet.google.com/..."
                            required
                        >

                        <p class="field-help">
                            Para una reunión virtual debes ingresar una
                            dirección web completa.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="tema">
                            Tema de la reunión
                        </label>

                        <input
                            type="text"
                            id="tema"
                            name="tema"
                            value="<?= htmlspecialchars(
                                $tema,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            minlength="3"
                            maxlength="150"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="acuerdos">
                            Acuerdos iniciales
                        </label>

                        <textarea
                            id="acuerdos"
                            name="acuerdos"
                            rows="4"
                            maxlength="3000"
                            placeholder="Puede completarse después de realizar la reunión"
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

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar reunión
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/reuniones_listar.php?expediente=<?= (int) $idExpediente ?>"
                            class="cancel-action"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>