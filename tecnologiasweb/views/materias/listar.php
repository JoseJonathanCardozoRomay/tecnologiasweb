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
                        Administración académica
                    </p>

                    <h1>Gestión de materias</h1>

                    <p>
                        Administra las materias y su relación con las
                        carreras registradas.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/materias_crear.php"
                    class="primary-action"
                >
                    Nueva materia
                </a>
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
                ) ?>controllers/materias_listar.php"
                class="search-form subject-search"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar materia
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
                        placeholder="Escribe el nombre de una materia"
                    >
                </div>

                <div class="search-field">
                    <label for="carrera">
                        Carrera
                    </label>

                    <select id="carrera" name="carrera">
                        <option value="">
                            Todas las carreras
                        </option>

                        <?php foreach ($carreras as $carrera): ?>
                            <option
                                value="<?= (int) $carrera['id_carrera'] ?>"
                                <?= $idCarrera === (int) $carrera['id_carrera']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $carrera['nombre_carrera'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="secondary-action">
                    Aplicar
                </button>

                <?php if (
                    $busqueda !== ''
                    || $idCarrera !== null
                ): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/materias_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($materias) ?>
                    <?= count($materias) === 1
                        ? 'materia encontrada'
                        : 'materias encontradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Materia</th>
                            <th>Carrera</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($materias)): ?>
                            <tr>
                                <td
                                    colspan="4"
                                    class="empty-result"
                                >
                                    No se encontraron materias.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($materias as $materia): ?>
                                <tr>
                                    <td>
                                        <?= (int) $materia['id_materia'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $materia['nombre_materia'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $materia['nombre_carrera']
                                                ?? 'Materia general',
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
                                            ) ?>controllers/materias_editar.php?id=<?= (int) $materia['id_materia'] ?>"
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
                                            ) ?>controllers/materias_eliminar.php"
                                            onsubmit="return confirm('¿Deseas eliminar esta materia?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id_materia"
                                                value="<?= (int) $materia['id_materia'] ?>"
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