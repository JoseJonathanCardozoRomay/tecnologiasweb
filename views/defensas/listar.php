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

                    <h1>Defensas académicas</h1>

                    <p>
                        Programa y supervisa las defensas de MG1 y MG2
                        evitando cruces de horarios, ambientes y docentes.
                    </p>
                </div>

                <?php if (
                    $puedeProgramar
                    && !empty($expedientesDisponibles)
                ): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/defensas_programar.php"
                        class="primary-action"
                    >
                        Programar defensa
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

            <?php if (
                $puedeProgramar
                && empty($expedientesDisponibles)
            ): ?>
                <div class="alert alert-info" role="alert">
                    No existen expedientes disponibles para programar.
                    Deben estar activos en MG1 o MG2 y no tener otra
                    defensa vigente en la misma etapa.
                </div>
            <?php endif; ?>

            <form
                method="GET"
                action="<?= htmlspecialchars(
                    $rutaBase,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>controllers/defensas_listar.php"
                class="search-form"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar defensa
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
                        placeholder="Estudiante, trabajo o ambiente"
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
                        ) ?>controllers/defensas_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($defensas) ?>

                    <?= count($defensas) === 1
                        ? 'defensa encontrada'
                        : 'defensas encontradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Etapa</th>
                            <th>Fecha y hora</th>
                            <th>Ambiente</th>
                            <th>Estado</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($defensas)): ?>
                            <tr>
                                <td
                                    colspan="6"
                                    class="empty-result"
                                >
                                    No existen defensas registradas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($defensas as $defensa): ?>
                                <?php
                                $claseEstado = match (
                                    $defensa['estado']
                                ) {
                                    'programada' => 'status-active',
                                    'realizada' => 'status-active',
                                    'reprogramada' => 'status-inactive',
                                    'cancelada' => 'status-inactive',
                                    default => ''
                                };
                                ?>

                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $defensa['nombre']
                                                    . ' '
                                                    . $defensa['apellido'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $defensa['titulo_trabajo']
                                                        ?: 'Trabajo sin título',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            strtoupper(
                                                $defensa['etapa']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $defensa['fecha']
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <br>

                                        <span class="text-secondary">
                                            <?= htmlspecialchars(
                                                substr(
                                                    $defensa['hora_inicio'],
                                                    0,
                                                    5
                                                )
                                                . ' - '
                                                . substr(
                                                    $defensa['hora_fin'],
                                                    0,
                                                    5
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $defensa['ambiente'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="status-label <?= htmlspecialchars(
                                                $claseEstado,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $defensa['estado']
                                                ),
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
                                            ) ?>controllers/defensa_ver.php?id=<?= (int) $defensa['id_defensa'] ?>"
                                            class="table-link"
                                        >
                                            Ver detalle
                                        </a>

                                        <?php if (
                                            $puedeReprogramar
                                            && $defensa['estado']
                                                === 'programada'
                                        ): ?>
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/defensas_reprogramar.php?id=<?= (int) $defensa['id_defensa'] ?>"
                                                class="table-link"
                                            >
                                                Reprogramar
                                            </a>
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