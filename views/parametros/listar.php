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
                        Modalidades de Grado
                    </p>

                    <h1>Parámetros del sistema</h1>

                    <p>
                        Configura los valores utilizados por las reglas
                        y procesos de Modalidades de Grado.
                    </p>
                </div>
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
                ) ?>controllers/parametros_listar.php"
                class="search-form"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar parámetro
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
                        placeholder="Clave, descripción o fuente"
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
                        ) ?>controllers/parametros_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($parametros) ?>
                    <?= count($parametros) === 1
                        ? 'parámetro encontrado'
                        : 'parámetros encontrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Parámetro</th>
                            <th>Valor</th>
                            <th>Fuente</th>
                            <th>Evidencia</th>
                            <th>Última actualización</th>

                            <?php if ($puedeEditar): ?>
                                <th class="actions-column">
                                    Acciones
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($parametros)): ?>
                            <tr>
                                <td
                                    colspan="<?= $puedeEditar ? 6 : 5 ?>"
                                    class="empty-result"
                                >
                                    No se encontraron parámetros.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($parametros as $parametro): ?>
                                <?php
                                $actualizador = 'Sin modificaciones';

                                if (
                                    !empty($parametro['nombre_actualizador'])
                                    || !empty($parametro['apellido_actualizador'])
                                ) {
                                    $actualizador = trim(
                                        ($parametro['nombre_actualizador'] ?? '')
                                        . ' '
                                        . ($parametro['apellido_actualizador'] ?? '')
                                    );
                                }

                                $fechaActualizacion = date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $parametro['fecha_actualizacion']
                                    )
                                );

                                $estadoEvidencia = match (
                                    $parametro['estado_evidencia']
                                ) {
                                    'confirmado' => 'Confirmado',
                                    'pendiente' => 'Pendiente',
                                    default => 'Propuesta'
                                };
                                ?>

                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $parametro['descripcion'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $parametro['clave'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $parametro['valor']
                                                ?? 'Sin definir',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $parametro['fuente'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $parametro['estado_evidencia'] === 'confirmado'
                                                ? 'status-active'
                                                : 'status-inactive' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $estadoEvidencia,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $fechaActualizacion,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $actualizador,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <?php if ($puedeEditar): ?>
                                        <td class="table-actions">
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/parametros_editar.php?clave=<?= rawurlencode(
                                                    $parametro['clave']
                                                ) ?>"
                                                class="table-link"
                                            >
                                                Editar
                                            </a>
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