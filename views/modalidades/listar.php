<?php

require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$nombreFlujos = [
    'perfil_mg' => 'Perfil MG',
    'examen_areas' => 'Examen por áreas',
    'excelencia' => 'Excelencia'
];

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Modalidades de Grado
                    </p>

                    <h1>Modalidades oficiales</h1>

                    <p>
                        Consulta el flujo y los requisitos generales de
                        cada modalidad de graduación.
                    </p>
                </div>

                <?php if ($puedeEditar): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/modalidades_crear.php"
                        class="primary-action"
                    >
                        Nueva modalidad
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
                ) ?>controllers/modalidades_listar.php"
                class="search-form"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar modalidad
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
                        placeholder="Código, nombre o descripción"
                    >
                </div>

                <button
                    type="submit"
                    class="secondary-action"
                >
                    Buscar
                </button>

                <?php if ($busqueda !== ''): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/modalidades_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($modalidades) ?>
                    <?= count($modalidades) === 1
                        ? 'modalidad encontrada'
                        : 'modalidades encontradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Modalidad</th>
                            <th>Código</th>
                            <th>Flujo</th>
                            <th>Tutor</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($modalidades)): ?>
                            <tr>
                                <td
                                    colspan="6"
                                    class="empty-result"
                                >
                                    No se encontraron modalidades.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($modalidades as $modalidad): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $modalidad['nombre'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $modalidad['descripcion']
                                                        ?: 'Sin descripción',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $modalidad['codigo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $nombreFlujos[
                                                $modalidad['flujo']
                                            ] ?? 'Sin definir',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= (int) $modalidad['requiere_tutor'] === 1
                                            ? 'Requiere tutor'
                                            : 'No requiere tutor' ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= (int) $modalidad['activa'] === 1
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= (int) $modalidad['activa'] === 1
                                                ? 'Activa'
                                                : 'Inactiva' ?>
                                        </span>
                                    </td>

                                    <td class="table-actions">
                                        <?php if ($puedeEditar): ?>
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/modalidades_editar.php?id=<?= (int) $modalidad['id_modalidad'] ?>"
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
                                                ) ?>controllers/modalidades_estado.php"
                                                onsubmit="return confirm('¿Deseas cambiar el estado de esta modalidad?');"
                                            >
                                                <?= campoCsrf() ?>

                                                <input
                                                    type="hidden"
                                                    name="id_modalidad"
                                                    value="<?= (int) $modalidad['id_modalidad'] ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="activa"
                                                    value="<?= (int) $modalidad['activa'] === 1
                                                        ? '0'
                                                        : '1' ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="table-link <?= (int) $modalidad['activa'] === 1
                                                        ? 'delete-link'
                                                        : '' ?>"
                                                >
                                                    <?= (int) $modalidad['activa'] === 1
                                                        ? 'Desactivar'
                                                        : 'Activar' ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="field-help">
                                                Solo lectura
                                            </span>
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