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
                        Modalidades de grado
                    </p>

                    <h1>Panel de coordinación</h1>

                    <p>
                        Resumen general del seguimiento académico,
                        asignaciones y actividades pendientes.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/expedientes_listar.php"
                    class="primary-action"
                >
                    Ver expedientes
                </a>
            </div>

            <?php if ($error !== ''): ?>
                <div
                    class="alert alert-warning"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-5">
                <div class="col-sm-6 col-xl-3">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-secondary mb-2">
                                Expedientes activos
                            </p>

                            <h2 class="display-6 mb-0">
                                <?= (int)
                                    $resumen['expedientes_activos'] ?>
                            </h2>
                        </div>
                    </article>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-secondary mb-2">
                                Sin tutor vigente
                            </p>

                            <h2 class="display-6 mb-0">
                                <?= (int)
                                    $resumen['expedientes_sin_tutor'] ?>
                            </h2>
                        </div>
                    </article>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-secondary mb-2">
                                Cartas pendientes
                            </p>

                            <h2 class="display-6 mb-0">
                                <?= (int)
                                    $resumen['cartas_pendientes'] ?>
                            </h2>
                        </div>
                    </article>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-secondary mb-2">
                                Alertas activas
                            </p>

                            <h2 class="display-6 mb-0">
                                <?= (int)
                                    $resumen['alertas_activas'] ?>
                            </h2>

                            <a
                                href="<?= htmlspecialchars(
                                    $rutaBase,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>controllers/alertas_listar.php"
                                class="small"
                            >
                                Revisar alertas
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-sm-6 col-xl-4">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-secondary mb-2">
                                Reuniones por validar
                            </p>

                            <h2 class="display-6 mb-0">
                                <?= (int)
                                    $resumen['reuniones_pendientes'] ?>
                            </h2>
                        </div>
                    </article>
                </div>

                <div class="col-sm-6 col-xl-4">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-secondary mb-2">
                                Informes por revisar
                            </p>

                            <h2 class="display-6 mb-0">
                                <?= (int)
                                    $resumen['informes_pendientes'] ?>
                            </h2>
                        </div>
                    </article>
                </div>

                <div class="col-sm-6 col-xl-4">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <p class="text-secondary mb-2">
                                Defensas programadas
                            </p>

                            <h2 class="display-6 mb-0">
                                <?= (int)
                                    $resumen['defensas_programadas'] ?>
                            </h2>

                            <a
                                href="<?= htmlspecialchars(
                                    $rutaBase,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>controllers/defensas_listar.php"
                                class="small"
                            >
                                Ver defensas
                            </a>
                        </div>
                    </article>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between
                                    align-items-center mb-3"
                            >
                                <div>
                                    <p class="section-label mb-1">
                                        Agenda
                                    </p>

                                    <h2 class="h4 mb-0">
                                        Próximas defensas
                                    </h2>
                                </div>

                                <a
                                    href="<?= htmlspecialchars(
                                        $rutaBase,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>controllers/defensas_listar.php"
                                    class="secondary-link"
                                >
                                    Ver todas
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table
                                    class="table align-middle mb-0"
                                >
                                    <thead>
                                        <tr>
                                            <th>Estudiante</th>
                                            <th>Fecha</th>
                                            <th>Ambiente</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (
                                            empty($proximasDefensas)
                                        ): ?>
                                            <tr>
                                                <td
                                                    colspan="4"
                                                    class="text-center
                                                        text-secondary py-4"
                                                >
                                                    No existen defensas
                                                    próximas.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach (
                                                $proximasDefensas
                                                as $defensa
                                            ): ?>
                                                <tr>
                                                    <td>
                                                        <strong>
                                                            <?= htmlspecialchars(
                                                                $defensa['nombre']
                                                                . ' '
                                                                . $defensa['apellido'],
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>
                                                        </strong>

                                                        <div
                                                            class="small
                                                                text-secondary"
                                                        >
                                                            <?= htmlspecialchars(
                                                                $defensa['titulo_trabajo']
                                                                    ?: 'Sin título',
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>
                                                        </div>
                                                    </td>

                                                    <td>
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

                                                        <div
                                                            class="small
                                                                text-secondary"
                                                        >
                                                            <?= htmlspecialchars(
                                                                substr(
                                                                    $defensa['hora_inicio'],
                                                                    0,
                                                                    5
                                                                ),
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <?= htmlspecialchars(
                                                            $defensa['ambiente'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </td>

                                                    <td class="text-end">
                                                        <a
                                                            href="<?= htmlspecialchars(
                                                                $rutaBase,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>controllers/defensa_ver.php?id=<?= (int) $defensa['id_defensa'] ?>"
                                                            class="table-link"
                                                        >
                                                            Revisar
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between
                                    align-items-center mb-3"
                            >
                                <div>
                                    <p class="section-label mb-1">
                                        Seguimiento
                                    </p>

                                    <h2 class="h4 mb-0">
                                        Expedientes recientes
                                    </h2>
                                </div>
                            </div>

                            <?php if (
                                empty($expedientesRecientes)
                            ): ?>
                                <p class="text-secondary mb-0">
                                    No existen expedientes registrados.
                                </p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach (
                                        $expedientesRecientes
                                        as $expediente
                                    ): ?>
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/expedientes_ver.php?id=<?= (int) $expediente['id_expediente'] ?>"
                                            class="list-group-item
                                                list-group-item-action px-0"
                                        >
                                            <div
                                                class="d-flex
                                                    justify-content-between
                                                    gap-3"
                                            >
                                                <div>
                                                    <strong>
                                                        <?= htmlspecialchars(
                                                            $expediente['nombre']
                                                            . ' '
                                                            . $expediente['apellido'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </strong>

                                                    <div
                                                        class="small
                                                            text-secondary"
                                                    >
                                                        <?= htmlspecialchars(
                                                            $expediente['modalidad']
                                                            . ' · '
                                                            . strtoupper(
                                                                $expediente['etapa_actual']
                                                            ),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </div>
                                                </div>

                                                <span
                                                    class="badge
                                                        text-bg-light
                                                        align-self-start"
                                                >
                                                    <?= htmlspecialchars(
                                                        ucfirst(
                                                            $expediente['estado']
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>