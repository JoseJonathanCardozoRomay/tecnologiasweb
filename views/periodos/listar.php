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
                        Configuración académica
                    </p>

                    <h1>Periodos de inscripción</h1>

                    <p>
                        Define las fechas en las que coordinación puede
                        incorporar estudiantes y asignar tutores.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/periodos_crear.php"
                    class="primary-action"
                >
                    Nuevo periodo
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
                ) ?>controllers/periodos_listar.php"
                class="search-form"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar periodo
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
                        placeholder="Código o nombre"
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
                        ) ?>controllers/periodos_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($periodos) ?>
                    <?= count($periodos) === 1
                        ? 'periodo encontrado'
                        : 'periodos encontrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Periodo</th>
                            <th>Fechas</th>
                            <th>Estado</th>
                            <th>Tutores</th>
                            <th>Procesos</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($periodos)): ?>
                            <tr>
                                <td
                                    colspan="6"
                                    class="empty-result"
                                >
                                    No se encontraron periodos.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($periodos as $periodo): ?>
                                <?php
                                $claseEstado = match (
                                    $periodo['estado']
                                ) {
                                    'abierto' => 'status-active',
                                    'cerrado' => 'status-inactive',
                                    default => ''
                                };

                                $textoEstado = match (
                                    $periodo['estado']
                                ) {
                                    'abierto' => 'Abierto',
                                    'cerrado' => 'Cerrado',
                                    default => 'Planificado'
                                };
                                ?>

                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $periodo['nombre'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $periodo['codigo'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $periodo['fecha_inicio']
                                                    )
                                                ) ?>
                                            </strong>

                                            <span>
                                                Hasta
                                                <?= date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $periodo['fecha_fin']
                                                    )
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= $claseEstado ?>"
                                        >
                                            <?= $textoEstado ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= (int) $periodo['tutores_configurados'] ?>
                                        configurados
                                    </td>

                                    <td>
                                        <?= (int) $periodo['procesos_registrados'] ?>
                                        registrados
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/periodos_editar.php?id=<?= (int) $periodo['id_periodo'] ?>"
                                            class="table-link"
                                        >
                                            Editar
                                        </a>

                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/cupos_listar.php?periodo=<?= (int) $periodo['id_periodo'] ?>"
                                            class="table-link"
                                        >
                                            Gestionar cupos
                                        </a>
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