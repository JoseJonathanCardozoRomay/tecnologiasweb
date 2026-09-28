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

                    <h1>Asignar tribunal</h1>

                    <p>
                        Selecciona un docente evaluador para la etapa
                        <?= htmlspecialchars(
                            strtoupper($etapaActual),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/tribunales_gestionar.php?id=<?= (int) $idExpediente ?>"
                    class="secondary-link"
                >
                    Volver a tribunales
                </a>
            </div>

            <div class="form-container form-container-wide mb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <p class="section-label mb-2">
                            Estudiante
                        </p>

                        <h2 class="h4 mb-1">
                            <?= htmlspecialchars(
                                $expediente['nombre']
                                . ' '
                                . $expediente['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                $expediente['titulo_trabajo']
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
                                $expediente['modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Etapa
                        </p>

                        <span class="status-label status-active">
                            <?= htmlspecialchars(
                                strtoupper($etapaActual),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-container form-container-wide">
                <?php if ($error !== ''): ?>
                    <div
                        class="alert alert-danger"
                        role="alert"
                    >
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                <?php endif; ?>

                <?php if ($advertencia !== ''): ?>
                    <div
                        class="alert alert-warning"
                        role="alert"
                    >
                        <?= htmlspecialchars(
                            $advertencia,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                <?php endif; ?>

                <?php if ($tutorPrincipal): ?>
                    <div
                        class="alert alert-info"
                        role="alert"
                    >
                        <strong>Tutor principal actual:</strong>

                        <?= htmlspecialchars(
                            $tutorPrincipal['nombre']
                            . ' '
                            . $tutorPrincipal['apellido'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>.

                        Puede ser seleccionado, pero el sistema solicitará
                        una confirmación para dejar constancia de la revisión.
                    </div>
                <?php endif; ?>

                <?php if (empty($docentes)): ?>
                    <div
                        class="alert alert-warning"
                        role="alert"
                    >
                        No existen docentes activos disponibles.
                        Primero debes crear o activar una cuenta de tutor.
                    </div>

                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/tutores_listar.php"
                        class="primary-action"
                    >
                        Ir a tutores
                    </a>
                <?php else: ?>
                    <form
                        method="POST"
                        class="module-form"
                    >
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_expediente"
                            value="<?= (int) $idExpediente ?>"
                        >

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="id_tutor">
                                    Docente evaluador
                                </label>

                                <select
                                    id="id_tutor"
                                    name="id_tutor"
                                    required
                                    autofocus
                                >
                                    <option value="">
                                        Selecciona un docente
                                    </option>

                                    <?php foreach ($docentes as $docente): ?>
                                        <?php
                                        $esTutorPrincipal = $tutorPrincipal
                                            && (int) $tutorPrincipal['id_tutor']
                                                === (int) $docente['id_tutor'];
                                        ?>

                                        <option
                                            value="<?= (int) $docente['id_tutor'] ?>"
                                            <?= $idTutor === (int) $docente['id_tutor']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= htmlspecialchars(
                                                $docente['nombre']
                                                . ' '
                                                . $docente['apellido']
                                                . (
                                                    $esTutorPrincipal
                                                        ? ' — Tutor principal'
                                                        : ''
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <p class="field-help">
                                    Solamente aparecen docentes con una
                                    cuenta activa.
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="orden">
                                    Posición en el tribunal
                                </label>

                                <input
                                    type="number"
                                    id="orden"
                                    name="orden"
                                    value="<?= (int) $orden ?>"
                                    min="1"
                                    max="10"
                                    required
                                >

                                <p class="field-help">
                                    Normalmente se utilizan las posiciones
                                    Tribunal 1 y Tribunal 2.
                                </p>
                            </div>
                        </div>

                        <?php if ($advertencia !== ''): ?>
                            <div class="form-check mt-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="confirmar_tutor_principal"
                                    name="confirmar_tutor_principal"
                                    value="1"
                                    <?= $confirmarTutorPrincipal
                                        ? 'checked'
                                        : '' ?>
                                    required
                                >

                                <label
                                    class="form-check-label"
                                    for="confirmar_tutor_principal"
                                >
                                    Confirmo que se revisó la coincidencia
                                    entre tutor principal y tribunal.
                                </label>
                            </div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <button
                                type="submit"
                                class="primary-action"
                            >
                                Guardar asignación
                            </button>

                            <a
                                href="<?= htmlspecialchars(
                                    $rutaBase,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>controllers/tribunales_gestionar.php?id=<?= (int) $idExpediente ?>"
                                class="cancel-action"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>