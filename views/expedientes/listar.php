<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$nombresEtapa = [
    'previa' => 'Etapa previa',
    'mg1' => 'Modalidad de Grado I',
    'mg2' => 'Modalidad de Grado II',
    'finalizado' => 'Finalizado'
];

$nombresEstado = [
    'activo' => 'Activo',
    'aprobado' => 'Aprobado',
    'reprobado' => 'Reprobado',
    'abandono' => 'Abandono',
    'retirado' => 'Retirado'
];

// Conservamos únicamente los filtros validados en la paginación
$filtrosPaginacion = [];

if ($busqueda !== '') {
    $filtrosPaginacion['buscar'] = $busqueda;
}

if ($idCohorte !== null) {
    $filtrosPaginacion['cohorte'] = $idCohorte;
}

if ($idModalidad !== null) {
    $filtrosPaginacion['modalidad'] = $idModalidad;
}

if ($etapa !== '') {
    $filtrosPaginacion['etapa'] = $etapa;
}

if ($estadoFiltro !== '') {
    $filtrosPaginacion['estado_filtro'] = $estadoFiltro;
}

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Modalidades de Grado
                    </p>

                    <h1>Expedientes</h1>

                    <p>
                        Consulta el estado, etapa y modalidad del proceso
                        de graduación de cada estudiante.
                    </p>
                </div>

                <?php if ($puedeCrear): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/expedientes_crear.php"
                        class="primary-action"
                    >
                        Nuevo expediente
                    </a>
                <?php endif; ?>
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

            <form
                method="GET"
                action="<?= htmlspecialchars(
                    $rutaBase,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>controllers/expedientes_listar.php"
                class="search-form subject-search"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar expediente
                    </label>

                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        value="<?= htmlspecialchars(
                            $busqueda,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="100"
                        placeholder="Estudiante, registro o título"
                    >
                </div>

                <div class="search-field">
                    <label for="cohorte">
                        Cohorte
                    </label>

                    <select id="cohorte" name="cohorte">
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
                                    $cohorte['codigo'],
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

                    <select id="modalidad" name="modalidad">
                        <option value="">
                            Todas
                        </option>

                        <?php foreach ($modalidades as $modalidad): ?>
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

                    <select id="etapa" name="etapa">
                        <option value="">
                            Todas
                        </option>

                        <?php foreach (
                            $nombresEtapa as $valorEtapa => $nombreEtapa
                        ): ?>
                            <option
                                value="<?= htmlspecialchars(
                                    $valorEtapa,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= $etapa === $valorEtapa
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $nombreEtapa,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="estado_filtro">
                        Estado
                    </label>

                    <select
                        id="estado_filtro"
                        name="estado_filtro"
                    >
                        <option value="">
                            Todos
                        </option>

                        <?php foreach (
                            $nombresEstado as $valorEstado => $nombreEstado
                        ): ?>
                            <option
                                value="<?= htmlspecialchars(
                                    $valorEstado,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= $estadoFiltro === $valorEstado
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $nombreEstado,
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

                <?php if (!empty($filtrosPaginacion)): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/expedientes_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= $totalRegistros ?>
                    <?= $totalRegistros === 1
                        ? 'expediente encontrado'
                        : 'expedientes encontrados' ?>
                </span>

                <?php if ($totalRegistros > 0): ?>
                    <span>
                        Página <?= $pagina ?>
                        de <?= $totalPaginas ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Modalidad</th>
                            <th>Cohorte</th>
                            <th>Etapa</th>
                            <th>Estado</th>
                            <th>Fecha de inicio</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($expedientes)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    No se encontraron expedientes.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expedientes as $expediente): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $expediente['nombre']
                                                    . ' '
                                                    . $expediente['apellido'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $expediente['registro_universitario']
                                                        ?: $expediente['usuario'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $expediente['nombre_modalidad'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $expediente['codigo_cohorte'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $expediente['nombre_cohorte'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $nombresEtapa[
                                                $expediente['etapa_actual']
                                            ] ?? $expediente['etapa_actual'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $expediente['estado'] === 'activo'
                                                || $expediente['estado'] === 'aprobado'
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $nombresEstado[
                                                    $expediente['estado']
                                                ] ?? $expediente['estado'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y',
                                                strtotime(
                                                    $expediente['fecha_inicio']
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
                                            ) ?>controllers/expedientes_ver.php?id=<?= (int) $expediente['id_expediente'] ?>"
                                            class="table-link"
                                        >
                                            Ver
                                        </a>

                                        <?php if ($puedeEditar): ?>
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/expedientes_editar.php?id=<?= (int) $expediente['id_expediente'] ?>"
                                                class="table-link"
                                            >
                                                Editar
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPaginas > 1): ?>
                <nav
                    aria-label="Paginación de expedientes"
                    class="mt-4"
                >
                    <ul class="pagination justify-content-center">
                        <li
                            class="page-item <?= $pagina <= 1
                                ? 'disabled'
                                : '' ?>"
                        >
                            <a
                                class="page-link"
                                href="?<?= htmlspecialchars(
                                    http_build_query(
                                        array_merge(
                                            $filtrosPaginacion,
                                            [
                                                'pagina' => max(
                                                    1,
                                                    $pagina - 1
                                                )
                                            ]
                                        )
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                Anterior
                            </a>
                        </li>

                        <li class="page-item disabled">
                            <span class="page-link">
                                <?= $pagina ?> / <?= $totalPaginas ?>
                            </span>
                        </li>

                        <li
                            class="page-item <?= $pagina >= $totalPaginas
                                ? 'disabled'
                                : '' ?>"
                        >
                            <a
                                class="page-link"
                                href="?<?= htmlspecialchars(
                                    http_build_query(
                                        array_merge(
                                            $filtrosPaginacion,
                                            [
                                                'pagina' => min(
                                                    $totalPaginas,
                                                    $pagina + 1
                                                )
                                            ]
                                        )
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>