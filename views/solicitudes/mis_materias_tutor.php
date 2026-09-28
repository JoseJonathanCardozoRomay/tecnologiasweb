<?php

require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$escapar = static fn($valor): string => htmlspecialchars(
    (string) $valor,
    ENT_QUOTES,
    'UTF-8'
);

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">Tutorías por materia</p>
                    <h1>Mis materias</h1>
                    <p>
                        Aquí aparecen las tutorías que coordinación aprobó,
                        separadas de tus tutorados de Modalidades de Grado.
                    </p>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= $escapar($tipoMensaje) ?>" role="alert">
                    <?= $escapar($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="list-summary">
                <span>
                    <?= count($materiasAprobadas) ?>
                    <?= count($materiasAprobadas) === 1
                        ? 'tutoría aprobada'
                        : 'tutorías aprobadas' ?>
                </span>
            </div>

            <?php if ($materiasAprobadas === []): ?>
                <div class="form-container">
                    <h2>Aún no tienes tutorías aprobadas</h2>
                    <p>
                        Las solicitudes aparecerán aquí después de que
                        coordinación las apruebe.
                    </p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Estudiante</th>
                                <th>Fecha y hora</th>
                                <th>Modalidad</th>
                                <th>Aula o enlace</th>
                                <th>Estado</th>
                                <th>Seguimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materiasAprobadas as $materia): ?>
                                <tr>
                                    <td><?= $escapar($materia['nombre_materia']) ?></td>
                                    <td>
                                        <?= $escapar(
                                            $materia['nombre_estudiante']
                                            . ' '
                                            . $materia['apellido_estudiante']
                                        ) ?>
                                    </td>
                                    <td>
                                        <?= $escapar(
                                            date('d/m/Y', strtotime($materia['fecha']))
                                            . ' · '
                                            . substr($materia['hora_inicio'], 0, 5)
                                            . '–'
                                            . substr($materia['hora_fin'], 0, 5)
                                        ) ?>
                                    </td>
                                    <td><?= $escapar(ucfirst($materia['modalidad'])) ?></td>
                                    <td>
                                        <?= trim((string) ($materia['lugar_o_enlace'] ?? '')) !== ''
                                            ? $escapar($materia['lugar_o_enlace'])
                                            : '—' ?>
                                    </td>
                                    <td><?= $escapar(ucfirst($materia['estado'])) ?></td>
                                    <td>
                                        <?php if ($materia['estado'] === 'confirmada'): ?>
                                            <?php if (
                                                strtotime(
                                                    $materia['fecha'] . ' ' . $materia['hora_fin']
                                                ) <= time()
                                            ): ?>
                                                <form method="POST" action="<?= $escapar($rutaBase) ?>controllers/mis_materias_tutor.php">
                                                    <?= campoCsrf() ?>
                                                    <input type="hidden" name="id_tutoria" value="<?= (int) $materia['id_tutoria'] ?>">
                                                    <button type="submit" class="table-link">
                                                        Marcar realizada
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                Programada
                                            <?php endif; ?>
                                        <?php elseif ($materia['calificacion'] !== null): ?>
                                            Evaluación: <?= (int) $materia['calificacion'] ?>/5
                                        <?php else: ?>
                                            Pendiente de evaluación
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
