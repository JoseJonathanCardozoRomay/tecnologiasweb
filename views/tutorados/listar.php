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

                    <h1>Mis tutorados</h1>

                    <p>
                        Consulta los expedientes que tienes asignados
                        y cuya designación ya fue aceptada.
                    </p>
                </div>
            </div>

            <div class="list-summary">
                <span>
                    <?= count($tutorados) ?>
                    <?= count($tutorados) === 1
                        ? 'tutorado asignado'
                        : 'tutorados asignados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Trabajo</th>
                            <th>Modalidad</th>
                            <th>Cohorte</th>
                            <th>Etapa</th>
                            <th>Asignación aceptada</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($tutorados)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    No tienes tutorados con una
                                    designación aceptada.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tutorados as $tutorado): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $tutorado['nombre_estudiante']
                                                    . ' '
                                                    . $tutorado['apellido_estudiante'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $tutorado['registro_universitario']
                                                        ?: 'Sin registro',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tutorado['titulo_trabajo']
                                                ?: 'Sin título definido',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tutorado['nombre_modalidad'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tutorado['nombre_cohorte'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="status-label status-active">
                                            <?= htmlspecialchars(
                                                strtoupper(
                                                    $tutorado['etapa_actual']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= $tutorado['fecha_aceptacion']
                                            ? htmlspecialchars(
                                                date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $tutorado['fecha_aceptacion']
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Sin fecha' ?>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/tutorado_ver.php?id=<?= (int) $tutorado['id_expediente'] ?>"
                                            class="table-link"
                                        >
                                            Ver seguimiento
                                        </a>
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