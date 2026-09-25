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

                    <h1>Mi disponibilidad</h1>

                    <p>
                        <p>
                        Registra los días y rangos de horario en los que
                        estás disponible para brindar tutorías académicas.
                        </p>
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/disponibilidad_crear.php"
                    class="primary-action"
                >
                    Nuevo horario
                </a>
            </div>

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

            <div class="list-summary">
                <span>
                    <?= count($disponibilidades) ?>
                    <?= count($disponibilidades) === 1
                        ? 'horario registrado'
                        : 'horarios registrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Hora de inicio</th>
                            <th>Hora de finalización</th>
                            <th>Duración</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($disponibilidades)): ?>
                            <tr>
                                <td
                                    colspan="5"
                                    class="empty-result"
                                >
                                    Todavía no registraste horarios
                                    disponibles.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $disponibilidades as $disponibilidad
                            ): ?>
                                <?php
                                $horaInicio = strtotime(
                                    $disponibilidad['hora_inicio']
                                );

                                $horaFin = strtotime(
                                    $disponibilidad['hora_fin']
                                );

                                $duracionMinutos = (int) (
                                    ($horaFin - $horaInicio) / 60
                                );

                                $horas = intdiv(
                                    $duracionMinutos,
                                    60
                                );

                                $minutos = $duracionMinutos % 60;
                                ?>

                                <tr>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $disponibilidad['dia_semana'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= date(
                                            'H:i',
                                            $horaInicio
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'H:i',
                                            $horaFin
                                        ) ?>
                                    </td>

                                    <td>
                                        <?php if ($horas > 0): ?>
                                            <?= $horas ?>
                                            <?= $horas === 1
                                                ? 'hora'
                                                : 'horas' ?>
                                        <?php endif; ?>

                                        <?php if ($minutos > 0): ?>
                                            <?= $minutos ?> min
                                        <?php endif; ?>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/disponibilidad_editar.php?id=<?= (int) $disponibilidad['id_disponibilidad'] ?>"
                                            class="table-link"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            method="POST"
                                            action="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/disponibilidad_eliminar.php"
                                            onsubmit="return confirm('¿Deseas eliminar este horario?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id_disponibilidad"
                                                value="<?= (int) $disponibilidad['id_disponibilidad'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="table-link delete-link"
                                            >
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>