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
                        Modalidades de Grado
                    </p>

                    <h1>Historial de importaciones</h1>

                    <p>
                        Consulta los archivos procesados y el resultado
                        obtenido en cada fila.
                    </p>
                </div>

                <?php if (
                    usuarioTienePermiso(
                        'mg.importaciones.crear'
                    )
                ): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/importaciones_crear.php"
                        class="primary-action"
                    >
                        Nueva importación
                    </a>
                <?php endif; ?>
            </div>

            <div class="list-summary">
                <span>
                    <?= count($importaciones) ?>

                    <?= count($importaciones) === 1
                        ? 'importación registrada'
                        : 'importaciones registradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Estado</th>
                            <th>Filas</th>
                            <th>Creadas</th>
                            <th>Errores</th>
                            <th>Responsable</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($importaciones)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    No existen importaciones registradas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $importaciones as $importacion
                            ): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $importacion[
                                                        'nombre_archivo'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    date(
                                                        'd/m/Y H:i',
                                                        strtotime(
                                                            $importacion[
                                                                'fecha_registro'
                                                            ]
                                                        )
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $importacion['estado']
                                                === 'completada'
                                                    ? 'status-active'
                                                    : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $importacion['estado']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= (int) $importacion[
                                            'total_filas'
                                        ] ?>
                                    </td>

                                    <td>
                                        <?= (int) $importacion[
                                            'filas_creadas'
                                        ] ?>
                                    </td>

                                    <td>
                                        <?= (int) $importacion[
                                            'filas_error'
                                        ] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $importacion['nombre']
                                            . ' '
                                            . $importacion['apellido'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/importaciones_detalle.php?id=<?= (int) $importacion['id_importacion'] ?>"
                                            class="table-link"
                                        >
                                            Ver resultado
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