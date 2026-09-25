<?php

require_once __DIR__ . '/../../includes/csrf.php';
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

                    <h1>Gestión de cohortes</h1>

                    <p>
                        Administra los periodos académicos utilizados
                        para incorporar estudiantes al proceso.
                    </p>
                </div>

                <?php if ($puedeCrear): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/cohortes_crear.php"
                        class="primary-action"
                    >
                        Nueva cohorte
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
                ) ?>controllers/cohortes_listar.php"
                class="search-form subject-search"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar cohorte
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
                        placeholder="Código o nombre"
                    >
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
                            Todas
                        </option>

                        <option
                            value="activas"
                            <?= $estadoFiltro === 'activas'
                                ? 'selected'
                                : '' ?>
                        >
                            Activas
                        </option>

                        <option
                            value="inactivas"
                            <?= $estadoFiltro === 'inactivas'
                                ? 'selected'
                                : '' ?>
                        >
                            Inactivas
                        </option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="secondary-action"
                >
                    Aplicar
                </button>

                <?php if (
                    $busqueda !== ''
                    || $estadoFiltro !== ''
                ): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/cohortes_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($cohortes) ?>
                    <?= count($cohortes) === 1
                        ? 'cohorte encontrada'
                        : 'cohortes encontradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cohorte</th>
                            <th>Fecha de inicio</th>
                            <th>Fecha de finalización</th>
                            <th>Estado</th>

                            <?php if ($puedeEditar): ?>
                                <th class="actions-column">
                                    Acciones
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($cohortes)): ?>
                            <tr>
                                <td
                                    colspan="<?= $puedeEditar ? 5 : 4 ?>"
                                    class="empty-result"
                                >
                                    No se encontraron cohortes.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cohortes as $cohorte): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $cohorte['nombre'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $cohorte['codigo'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y',
                                                strtotime(
                                                    $cohorte['fecha_inicio']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?php if ($cohorte['fecha_fin']): ?>
                                            <?= htmlspecialchars(
                                                date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $cohorte['fecha_fin']
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        <?php else: ?>
                                            Sin fecha definida
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= (int) $cohorte['activa'] === 1
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= (int) $cohorte['activa'] === 1
                                                ? 'Activa'
                                                : 'Inactiva' ?>
                                        </span>
                                    </td>

                                    <?php if ($puedeEditar): ?>
                                        <td class="table-actions">
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/cohortes_editar.php?id=<?= (int) $cohorte['id_cohorte'] ?>"
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
                                                ) ?>controllers/cohortes_estado.php"
                                                onsubmit="return confirm('¿Deseas cambiar el estado de esta cohorte?');"
                                            >
                                                <?= campoCsrf() ?>

                                                <input
                                                    type="hidden"
                                                    name="id_cohorte"
                                                    value="<?= (int) $cohorte['id_cohorte'] ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="activa"
                                                    value="<?= (int) $cohorte['activa'] === 1
                                                        ? '0'
                                                        : '1' ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="table-link <?= (int) $cohorte['activa'] === 1
                                                        ? 'delete-link'
                                                        : '' ?>"
                                                >
                                                    <?= (int) $cohorte['activa'] === 1
                                                        ? 'Desactivar'
                                                        : 'Activar' ?>
                                                </button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
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