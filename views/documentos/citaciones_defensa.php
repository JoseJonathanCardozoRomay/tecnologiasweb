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
                        Documentación académica
                    </p>

                    <h1>Citaciones de defensa</h1>

                    <p>
                        Genera las citaciones de los tribunales y del
                        estudiante, conservando una copia inmutable.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/defensa_ver.php?id=<?= (int) $idDefensa ?>"
                    class="secondary-link"
                >
                    Volver a la defensa
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="form-container form-container-wide mb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <p class="section-label mb-2">
                            Estudiante
                        </p>

                        <h2 class="h4 mb-1">
                            <?= htmlspecialchars(
                                $defensa['nombre']
                                . ' '
                                . $defensa['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                $defensa['titulo_trabajo']
                                    ?: 'Trabajo sin título registrado',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Fecha
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y',
                                    strtotime($defensa['fecha'])
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                substr(
                                    $defensa['hora_inicio'],
                                    0,
                                    5
                                )
                                . ' - '
                                . substr(
                                    $defensa['hora_fin'],
                                    0,
                                    5
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Ambiente
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                $defensa['ambiente'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>
            </div>

            <?php if (
                empty($documentos)
                && $puedeGenerar
            ): ?>
                <div class="form-container form-container-wide mb-4">
                    <p>
                        Se generará una citación para cada tribunal
                        vigente y otra para el estudiante.
                    </p>

                    <form method="POST">
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_defensa"
                            value="<?= (int) $idDefensa ?>"
                        >

                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Generar citaciones
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Documentos emitidos
                    </p>

                    <h2>Citaciones disponibles</h2>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Tipo</th>
                            <th>Destinatario</th>
                            <th>Fecha de generación</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($documentos)): ?>
                            <tr>
                                <td
                                    colspan="5"
                                    class="empty-result"
                                >
                                    Todavía no existen citaciones
                                    generadas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $documentos as $documento
                            ): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $documento[
                                                    'numero_correlativo'
                                                ],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= $documento['tipo']
                                            === 'citacion_tribunal'
                                                ? 'Tribunal'
                                                : 'Estudiante' ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $documento['destinatario'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $documento[
                                                        'fecha_generacion'
                                                    ]
                                                )
                                            ),
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
                                            ) ?>controllers/documento_ver.php?id=<?= (int) $documento['id_documento'] ?>"
                                            class="table-link"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            Ver e imprimir
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