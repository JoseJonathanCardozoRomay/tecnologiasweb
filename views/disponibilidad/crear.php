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
                        Organización semanal
                    </p>

                    <h1>Nuevo horario</h1>

                    <p>
                        Registra un rango de tiempo libre en el que
                        podrás brindar tutorías académicas.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/disponibilidad_listar.php"
                    class="secondary-link"
                >
                    Volver a mi disponibilidad
                </a>
            </div>

            <div class="form-container">
                <div class="profile-summary">
                    <div>
                        <span class="profile-summary-label">
                            Tutor
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $tutor['nombre']
                                . ' '
                                . $tutor['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <div>
                        <span class="profile-summary-label">
                            Especialidad
                        </span>

                        <span>
                            <?= htmlspecialchars(
                                $tutor['especialidad']
                                    ?: 'Sin especialidad registrada',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>
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

                <form method="POST" class="module-form">
                    <div class="form-group">
                        <label for="dia_semana">
                            Día de la semana
                        </label>

                        <select
                            id="dia_semana"
                            name="dia_semana"
                            required
                            autofocus
                        >
                            <option value="">
                                Selecciona un día
                            </option>

                            <?php foreach (
                                $diasPermitidos as $diaPermitido
                            ): ?>
                                <?php
                                $nombreDia = match ($diaPermitido) {
                                    'Miercoles' => 'Miércoles',
                                    'Sabado' => 'Sábado',
                                    default => $diaPermitido
                                };
                                ?>

                                <option
                                    value="<?= htmlspecialchars(
                                        $diaPermitido,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    <?= $diaSemana === $diaPermitido
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $nombreDia,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-grid">
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
                                step="900"
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
                                step="900"
                                required
                            >
                        </div>
                    </div>

                    <p class="field-help">
                        El horario no puede cruzarse con otro periodo
                        registrado para el mismo día.
                    </p>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar horario
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/disponibilidad_listar.php"
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