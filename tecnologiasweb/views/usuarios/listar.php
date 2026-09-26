<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$usuarioActual = obtenerUsuarioSesion();

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Administración del sistema
                    </p>

                    <h1>Gestión de usuarios</h1>

                    <p>
                        Administra los datos, roles y estados de las
                        personas que utilizan el sistema.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/usuarios_crear.php"
                    class="primary-action"
                >
                    Nuevo usuario
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
                ) ?>controllers/usuarios_listar.php"
                class="search-form user-search"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar usuario
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
                        placeholder="Nombre, correo o usuario"
                    >
                </div>

                <div class="search-field">
                    <label for="rol">
                        Rol
                    </label>

                    <select id="rol" name="rol">
                        <option value="">
                            Todos los roles
                        </option>

                        <?php foreach ($roles as $rol): ?>
                            <option
                                value="<?= (int) $rol['id_rol'] ?>"
                                <?= $idRol === (int) $rol['id_rol']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    ucfirst($rol['nombre_rol']),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="estado_usuario">
                        Estado
                    </label>

                    <select
                        id="estado_usuario"
                        name="estado_usuario"
                    >
                        <option value="">
                            Todos
                        </option>

                        <option
                            value="activo"
                            <?= $estadoFiltro === 'activo'
                                ? 'selected'
                                : '' ?>
                        >
                            Activo
                        </option>

                        <option
                            value="inactivo"
                            <?= $estadoFiltro === 'inactivo'
                                ? 'selected'
                                : '' ?>
                        >
                            Inactivo
                        </option>
                    </select>
                </div>

                <button type="submit" class="secondary-action">
                    Aplicar
                </button>

                <?php if (
                    $busqueda !== ''
                    || $idRol !== null
                    || $estadoFiltro !== ''
                ): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/usuarios_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($usuarios) ?>
                    <?= count($usuarios) === 1
                        ? 'usuario encontrado'
                        : 'usuarios encontrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td
                                    colspan="5"
                                    class="empty-result"
                                >
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $usuario['nombre']
                                                    . ' '
                                                    . $usuario['apellido'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $usuario['usuario'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            ucfirst($usuario['nombre_rol']),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <span>
                                                <?= htmlspecialchars(
                                                    $usuario['correo'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $usuario['telefono']
                                                        ?: 'Sin teléfono',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $usuario['estado'] === 'activo'
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst($usuario['estado']),
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
                                            ) ?>controllers/usuarios_editar.php?id=<?= (int) $usuario['id_usuario'] ?>"
                                            class="table-link"
                                        >
                                            Editar
                                        </a>

                                        <?php if (
                                            (int) $usuario['id_usuario']
                                            !== (int) $usuarioActual['id_usuario']
                                        ): ?>
                                            <form
                                                method="POST"
                                                action="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/usuarios_estado.php"
                                                onsubmit="return confirm('¿Deseas cambiar el estado de este usuario?');"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="id_usuario"
                                                    value="<?= (int) $usuario['id_usuario'] ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="<?= $usuario['estado'] === 'activo'
                                                        ? 'inactivo'
                                                        : 'activo' ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="table-link <?= $usuario['estado'] === 'activo'
                                                        ? 'delete-link'
                                                        : '' ?>"
                                                >
                                                    <?= $usuario['estado'] === 'activo'
                                                        ? 'Inactivar'
                                                        : 'Activar' ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="current-session">
                                                Sesión actual
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