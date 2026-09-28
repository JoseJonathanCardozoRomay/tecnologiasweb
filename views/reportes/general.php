<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$parametrosExportacion = http_build_query([
    'cohorte' => $idCohorte,
    'modalidad' => $idModalidad,
    'etapa' => $etapa,
    'estado' => $estadoExpediente
]);

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Modalidades de Grado
                    </p>

                    <h1>Reporte general</h1>

                    <p>
                        Consulta el estado académico de los estudiantes
                        y exporta los resultados para su análisis.
                    </p>
                </div>

                <?php if ($puedeExportar): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/reportes_exportar.php?<?= htmlspecialchars(
                            $parametrosExportacion,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="primary-action"
                    >
                        Exportar CSV
                    </a>
                <?php endif; ?>
            </div>

            <form
                method="GET"
                action="<?= htmlspecialchars(
                    $rutaBase,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>controllers/reportes_general.php"
                class="search-form subject-search"
            >
                <div class="search-field">
                    <label for="cohorte">
                        Cohorte
                    </label>

                    <select
                        id="cohorte"
                        name="cohorte"
                    >
                        <option value="">
                            Todas
                        </option>

                        <?php foreach ($cohortes as $cohorte): ?>
                            <option
                                value="<?= (int) $cohorte['id_cohorte'] ?>"
                                <?= $idCohorte === (int) $cohorte['id_cohorte']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $cohorte['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="modalidad">
                        Modalidad
                    </label>

                    <select
                        id="modalidad"
                        name="modalidad"
                    >
                        <option value="">
                            Todas
                        </option>

                        <?php foreach (
                            $modalidades as $modalidad
                        ): ?>
                            <option
                                value="<?= (int) $modalidad['id_modalidad'] ?>"
                                <?= $idModalidad === (int) $modalidad['id_modalidad']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $modalidad['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="etapa">
                        Etapa
                    </label>

                    <select
                        id="etapa"
                        name="etapa"
                    >
                        <option value="">Todas</option>
                        <option
                            value="previa"
                            <?= $etapa === 'previa'
                                ? 'selected'
                                : '' ?>
                        >
                            Previa
                        </option>
                        <option
                            value="mg1"
                            <?= $etapa === 'mg1'
                                ? 'selected'
                                : '' ?>
                        >
                            MG1
                        </option>
                        <option
                            value="mg2"
                            <?= $etapa === 'mg2'
                                ? 'selected'
                                : '' ?>
                        >
                            MG2
                        </option>
                        <option
                            value="finalizado"
                            <?= $etapa === 'finalizado'
                                ? 'selected'
                                : '' ?>
                        >
                            Finalizado
                        </option>
                    </select>
                </div>

                <div class="search-field">
                    <label for="estado">
                        Estado
                    </label>

                    <select
                        id="estado"
                        name="estado"
                    >
                        <option value="">Todos</option>

                        <?php foreach (
                            [
                                'activo',
                                'aprobado',
                                'reprobado',
                                'abandono',
                                'retirado'
                            ] as $estado
                        ): ?>
                            <option
                                value="<?= htmlspecialchars(
                                    $estado,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= $estadoExpediente === $estado
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    ucfirst($estado),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button
                    type="submit"
                    class="secondary-action"
                >
                    Aplicar
                </button>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/reportes_general.php"
                    class="clear-action"
                >
                    Limpiar
                </a>
            </form>

            <div class="row g-3 mb-5">
                <div class="col-md">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Total
                        </p>

                        <h2 class="h3 mb-0">
                            <?= count($expedientes) ?>
                        </h2>
                    </div>
                </div>

                <?php foreach (
                    $resumenEtapas as $nombreEtapa => $cantidad
                ): ?>
                    <div class="col-md">
                        <div class="form-container h-100">
                            <p class="section-label mb-2">
                                <?= htmlspecialchars(
                                    strtoupper($nombreEtapa),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>

                            <h2 class="h3 mb-0">
                                <?= (int) $cantidad ?>
                            </h2>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Modalidad</th>
                            <th>Cohorte</th>
                            <th>Etapa</th>
                            <th>Avance</th>
                            <th>Tutor</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($expedientes)): ?>
                            <tr>
                                <td
                                    colspan="8"
                                    class="empty-result"
                                >
                                    No existen resultados con los filtros
                                    seleccionados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $expedientes as $expediente
                            ): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $expediente['nombre']
                                                    . ' '
                                                    . $expediente[
                                                        'apellido'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $expediente[
                                                        'titulo_trabajo'
                                                    ] ?: 'Sin título',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $expediente['modalidad'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $expediente['cohorte'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $expediente[
                                                    'etapa_actual'
                                                ]
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $expediente[
                                            'ultimo_avance'
                                        ] !== null
                                            ? (int) $expediente[
                                                'ultimo_avance'
                                            ] . '%'
                                            : 'Sin datos' ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $expediente['tutor_actual']
                                                ?: 'Sin tutor',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $expediente['estado']
                                                === 'activo'
                                                    ? 'status-active'
                                                    : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $expediente['estado']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/reporte_estudiante.php?id=<?= (int) $expediente['id_expediente'] ?>"
                                            class="table-link"
                                        >
                                            Ver reporte
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