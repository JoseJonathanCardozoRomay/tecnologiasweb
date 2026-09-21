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
                        Perfiles académicos
                    </p>

                    <h1>Gestión de estudiantes</h1>

                    <p>
                        Administra la carrera, semestre y registro
                        universitario de cada estudiante.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/estudiantes_crear.php"
                    class="primary-action"
                >
                    Nuevo perfil
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
                ) ?>controllers/estudiantes_listar.php"
                class="search-form subject-search"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar estudiante
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
                        placeholder="Nombre, usuario o registro"
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
                        ) ?>controllers/estudiantes_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($estudiantes) ?>
                    <?= count($estudiantes) === 1
                        ? 'estudiante encontrado'
                        : 'estudiantes encontrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Registro</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($estudiantes)): ?>
                            <tr>
                                <td
                                    colspan="6"
                                    class="empty-result"
                                >
                                    No se encontraron estudiantes.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $estudiante['nombre']
                                                    . ' '
                                                    . $estudiante['apellido'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $estudiante['usuario'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $estudiante['nombre_carrera'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= (int) $estudiante['semestre'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $estudiante['registro_universitario']
                                                ?: 'Sin registro',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $estudiante['estado'] === 'activo'
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst($estudiante['estado']),
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
                                            ) ?>controllers/estudiantes_editar.php?id=<?= (int) $estudiante['id_estudiante'] ?>"
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
                                            ) ?>controllers/estudiantes_eliminar.php"
                                            onsubmit="return confirm('¿Deseas eliminar este perfil académico?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id_estudiante"
                                                value="<?= (int) $estudiante['id_estudiante'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="table-link delete-link"
                                            >
                                                Eliminar perfil
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