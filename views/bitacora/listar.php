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
                        Seguridad y trazabilidad
                    </p>

                    <h1>Bitácora de auditoría</h1>

                    <p>
                        Consulta los cambios sensibles registrados
                        dentro del sistema.
                    </p>
                </div>
            </div>

            <form
                method="GET"
                action="<?= htmlspecialchars(
                    $rutaBase,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>controllers/bitacora_listar.php"
                class="search-form subject-search"
            >
                <div class="search-field">
                    <label for="accion">Acción</label>

                    <select id="accion" name="accion">
                        <option value="">Todas</option>

                        <?php foreach (
                            $filtrosDisponibles['acciones']
                            as $opcion
                        ): ?>
                            <option
                                value="<?= htmlspecialchars(
                                    $opcion,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= $accion === $opcion
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $opcion,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="tabla">Módulo</label>

                    <select id="tabla" name="tabla">
                        <option value="">Todos</option>

                        <?php foreach (
                            $filtrosDisponibles['tablas']
                            as $opcion
                        ): ?>
                            <option
                                value="<?= htmlspecialchars(
                                    $opcion,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= $tabla === $opcion
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $opcion,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="usuario">Usuario</label>

                    <select id="usuario" name="usuario">
                        <option value="">Todos</option>

                        <?php foreach (
                            $filtrosDisponibles['usuarios']
                            as $usuario
                        ): ?>
                            <option
                                value="<?= (int) $usuario[
                                    'id_usuario'
                                ] ?>"
                                <?= $idUsuario === (int) $usuario[
                                    'id_usuario'
                                ]
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $usuario['nombre']
                                    . ' '
                                    . $usuario['apellido'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="fecha_desde">Desde</label>

                    <input
                        type="date"
                        id="fecha_desde"
                        name="fecha_desde"
                        value="<?= htmlspecialchars(
                            $fechaDesde,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                </div>

                <div class="search-field">
                    <label for="fecha_hasta">Hasta</label>

                    <input
                        type="date"
                        id="fecha_hasta"
                        name="fecha_hasta"
                        value="<?= htmlspecialchars(
                            $fechaHasta,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
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
                    ) ?>controllers/bitacora_listar.php"
                    class="clear-action"
                >
                    Limpiar
                </a>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($registros) ?>
                    registros encontrados
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Registro</th>
                            <th>IP</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($registros)): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-result"
                                >
                                    No existen registros de auditoría.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $registros as $registro
                            ): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y H:i:s',
                                                strtotime(
                                                    $registro[
                                                        'fecha_registro'
                                                    ]
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $registro['nombre']
                                                ? $registro['nombre']
                                                    . ' '
                                                    . $registro[
                                                        'apellido'
                                                    ]
                                                : 'Usuario eliminado',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $registro['accion'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $registro[
                                                'tabla_afectada'
                                            ],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $registro['id_registro']
                                            !== null
                                                ? (int) $registro[
                                                    'id_registro'
                                                ]
                                                : '—' ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $registro['direccion_ip']
                                                ?: 'No disponible',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <details>
                                            <summary class="table-link">
                                                Ver cambios
                                            </summary>

                                            <?php if (
                                                $registro['datos_antes']
                                            ): ?>
                                                <p class="mt-3 mb-1">
                                                    <strong>Antes</strong>
                                                </p>

                                                <pre class="small"><?= htmlspecialchars(
                                                    json_encode(
                                                        json_decode(
                                                            $registro[
                                                                'datos_antes'
                                                            ],
                                                            true
                                                        ),
                                                        JSON_PRETTY_PRINT
                                                        | JSON_UNESCAPED_UNICODE
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?></pre>
                                            <?php endif; ?>

                                            <?php if (
                                                $registro['datos_despues']
                                            ): ?>
                                                <p class="mt-3 mb-1">
                                                    <strong>Después</strong>
                                                </p>

                                                <pre class="small"><?= htmlspecialchars(
                                                    json_encode(
                                                        json_decode(
                                                            $registro[
                                                                'datos_despues'
                                                            ],
                                                            true
                                                        ),
                                                        JSON_PRETTY_PRINT
                                                        | JSON_UNESCAPED_UNICODE
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?></pre>
                                            <?php endif; ?>
                                        </details>
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