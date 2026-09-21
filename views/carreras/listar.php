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

                    <h1>Gestión de carreras</h1>

                    <p>
                        Registra y administra las carreras disponibles
                        dentro del sistema.
                    </p>
                </div>

                <a
                    href="../../controllers/carreras_crear.php"
                    class="primary-action"
                >
                    Nueva carrera
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
                action="../../controllers/carreras_listar.php"
                class="search-form"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar carrera
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
                        placeholder="Escribe el nombre de una carrera"
                    >
                </div>

                <button type="submit" class="secondary-action">
                    Buscar
                </button>

                <?php if ($busqueda !== ''): ?>
                    <a
                        href="../../controllers/carreras_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($carreras) ?>
                    <?= count($carreras) === 1
                        ? 'carrera encontrada'
                        : 'carreras encontradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de la carrera</th>
                            <th class="actions-column">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($carreras)): ?>
                            <tr>
                                <td colspan="3" class="empty-result">
                                    No se encontraron carreras.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($carreras as $carrera): ?>
                                <tr>
                                    <td>
                                        <?= (int) $carrera['id_carrera'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $carrera['nombre_carrera'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="../../controllers/carreras_editar.php?id=<?= (int) $carrera['id_carrera'] ?>"
                                            class="table-link"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            method="POST"
                                            action="../../controllers/carreras_eliminar.php"
                                            onsubmit="return confirm('¿Deseas eliminar esta carrera?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id_carrera"
                                                value="<?= (int) $carrera['id_carrera'] ?>"
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