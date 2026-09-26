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

                    <h1>Gestión de tutores</h1>

                    <p>
                        Administra la especialidad y la información
                        profesional de los tutores del sistema.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/tutores_crear.php"
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
                ) ?>controllers/tutores_listar.php"
                class="search-form"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar tutor
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
                        placeholder="Nombre, usuario o especialidad"
                    >
                </div>

                <button type="submit" class="secondary-action">
                    Buscar
                </button>

                <?php if ($busqueda !== ''): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/tutores_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($tutores) ?>
                    <?= count($tutores) === 1
                        ? 'tutor encontrado'
                        : 'tutores encontrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tutor</th>
                            <th>Especialidad</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($tutores)): ?>
                            <tr>
                                <td
                                    colspan="6"
                                    class="empty-result"
                                >
                                    No se encontraron tutores.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tutores as $tutor): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $tutor['nombre']
                                                    . ' '
                                                    . $tutor['apellido'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $tutor['usuario'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tutor['especialidad']
                                                ?: 'Sin especialidad',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tutor['correo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tutor['telefono']
                                                ?: 'Sin teléfono',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $tutor['estado'] === 'activo'
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst($tutor['estado']),
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
                                            ) ?>controllers/tutores_editar.php?id=<?= (int) $tutor['id_tutor'] ?>"
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
                                            ) ?>controllers/tutores_eliminar.php"
                                            onsubmit="return confirm('¿Deseas eliminar este perfil de tutor?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id_tutor"
                                                value="<?= (int) $tutor['id_tutor'] ?>"
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