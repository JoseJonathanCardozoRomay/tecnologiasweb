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
                        Resultado de importación
                    </p>

                    <h1>
                        <?= htmlspecialchars(
                            $importacion['nombre_archivo'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        Revisa las filas creadas, omitidas o pendientes
                        de corrección.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/importaciones_listar.php"
                    class="secondary-link"
                >
                    Volver al historial
                </a>
            </div>

            <div class="row g-3 mb-5">
                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Total
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) $importacion['total_filas'] ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Expedientes creados
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) $importacion['filas_creadas'] ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Advertencias
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) $importacion[
                                'filas_advertencia'
                            ] ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Errores
                        </p>

                        <h2 class="h3 mb-0">
                            <?= (int) $importacion['filas_error'] ?>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fila</th>
                            <th>Registro</th>
                            <th>Modalidad</th>
                            <th>Cohorte</th>
                            <th>Resultado</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($detalles)): ?>
                            <tr>
                                <td
                                    colspan="6"
                                    class="empty-result"
                                >
                                    No existen resultados registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $detalles as $detalle
                            ): ?>
                                <?php
                                $resultadoCorrecto = in_array(
                                    $detalle['resultado'],
                                    ['correcta', 'creada'],
                                    true
                                );
                                ?>

                                <tr>
                                    <td>
                                        <?= (int) $detalle['numero_fila'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $detalle[
                                                'registro_universitario'
                                            ] ?: 'Sin registro',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $detalle['modalidad']
                                                ?: 'Sin modalidad',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $detalle['cohorte']
                                                ?: 'Sin cohorte',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $resultadoCorrecto
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    ucfirst(
                                                        $detalle[
                                                            'resultado'
                                                        ]
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $detalle['mensaje']
                                                ?: 'Sin detalle',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        <?php if (
                                            !empty(
                                                $detalle['id_expediente']
                                            )
                                        ): ?>
                                            <br>

                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/expedientes_ver.php?id=<?= (int) $detalle['id_expediente'] ?>"
                                                class="table-link"
                                            >
                                                Abrir expediente
                                            </a>
                                        <?php endif; ?>
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