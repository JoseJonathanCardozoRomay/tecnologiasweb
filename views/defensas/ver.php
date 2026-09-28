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

                    <h1>Detalle de defensa</h1>

                    <p>
                        Información académica, programación y tribunales
                        asignados.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/defensas_listar.php"
                    class="secondary-link"
                >
                    Volver al listado
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

            <div class="form-container form-container-wide mb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <p class="section-label mb-2">
                            Estudiante
                        </p>

                        <h2 class="h4 mb-1">
                            <?= htmlspecialchars(
                                $defensa['nombre']
                                . ' '
                                . $defensa['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                $defensa['titulo_trabajo']
                                    ?: 'Trabajo sin título registrado',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Modalidad
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                $defensa['modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                $defensa['cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Estado
                        </p>

                        <span
                            class="status-label <?= in_array(
                                $defensa['estado'],
                                ['programada', 'realizada'],
                                true
                            )
                                ? 'status-active'
                                : 'status-inactive' ?>"
                        >
                            <?= htmlspecialchars(
                                ucfirst($defensa['estado']),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <p class="mb-0 mt-2 text-secondary">
                            Etapa
                            <?= htmlspecialchars(
                                strtoupper($defensa['etapa']),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Fecha
                        </p>

                        <h2 class="h4">
                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y',
                                    strtotime($defensa['fecha'])
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Horario
                        </p>

                        <h2 class="h4">
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
                        </h2>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-container h-100">
                        <p class="section-label mb-2">
                            Ambiente
                        </p>

                        <h2 class="h4">
                            <?= htmlspecialchars(
                                $defensa['ambiente'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>
                    </div>
                </div>
            </div>

            <?php if ($defensa['autorizado_por']): ?>
                <div class="alert alert-info">
                    <strong>Autorización excepcional:</strong>

                    <?= htmlspecialchars(
                        $defensa['autorizado_por'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                    — Referencia:

                    <?= htmlspecialchars(
                        $defensa['referencia_autorizacion'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Evaluadores
                    </p>

                    <h2>Tribunales asignados</h2>
                </div>
            </div>

            <div class="table-container mb-5">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th>Docente</th>
                            <th>Especialidad</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($tribunalesDefensa)): ?>
                            <tr>
                                <td
                                    colspan="3"
                                    class="empty-result"
                                >
                                    No existen tribunales vigentes.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (
                                $tribunalesDefensa as $tribunal
                            ): ?>
                                <tr>
                                    <td>
                                        Tribunal
                                        <?= (int) $tribunal['orden'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tribunal['nombre']
                                            . ' '
                                            . $tribunal['apellido'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $tribunal['especialidad']
                                                ?: 'Sin especialidad registrada',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-container form-container-wide">
                <div class="form-actions">
                    <?php if (
                        $puedeReprogramar
                        && $defensa['estado'] === 'programada'
                    ): ?>
                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/defensas_reprogramar.php?id=<?= (int) $idDefensa ?>"
                            class="secondary-action"
                        >
                            Reprogramar
                        </a>

                        <form
                            method="POST"
                            action="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/defensas_estado.php"
                        >
                            <?= campoCsrf() ?>

                            <input
                                type="hidden"
                                name="id_defensa"
                                value="<?= (int) $idDefensa ?>"
                            >

                            <input
                                type="hidden"
                                name="estado"
                                value="realizada"
                            >

                            <button
                                type="submit"
                                class="primary-action"
                            >
                                Marcar realizada
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/defensas_estado.php"
                            onsubmit="return confirm('¿Deseas cancelar esta defensa?');"
                        >
                            <?= campoCsrf() ?>

                            <input
                                type="hidden"
                                name="id_defensa"
                                value="<?= (int) $idDefensa ?>"
                            >

                            <input
                                type="hidden"
                                name="estado"
                                value="cancelada"
                            >

                            <button
                                type="submit"
                                class="cancel-action"
                            >
                                Cancelar defensa
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (
                        $puedeRegistrarCalificacion
                        && $defensa['estado'] === 'realizada'
                    ): ?>
                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/calificaciones_registrar.php?id_defensa=<?= (int) $idDefensa ?>"
                            class="primary-action"
                        >
                            Registrar calificación
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>