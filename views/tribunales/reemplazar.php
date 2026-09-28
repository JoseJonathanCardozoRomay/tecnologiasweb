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

                    <h1>Reemplazar tribunal</h1>

                    <p>
                        El registro anterior se conservará dentro del
                        historial del expediente.
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
                    <div class="col-md-5">
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
                            Posición
                        </p>

                        <strong>
                            Tribunal
                            <?= (int) $tribunalActual['orden'] ?>
                        </strong>

                        <p class="mb-0 text-secondary">
                            <?= htmlspecialchars(
                                strtoupper(
                                    $tribunalActual['etapa']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>

                    <div class="col-md-4">
                        <p class="section-label mb-2">
                            Docente actual
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                $tribunalActual['nombre']
                                . ' '
                                . $tribunalActual['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
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

                <?php if (empty($docentesDisponibles)): ?>
                    <div
                        class="alert alert-warning"
                        role="alert"
                    >
                        No existen otros docentes activos disponibles.
                        Para realizar el reemplazo debe registrarse o
                        activarse otro perfil de tutor.
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
                            name="id_tribunal"
                            value="<?= (int) $idTribunal ?>"
                        >

                        <div class="form-group">
                            <label for="id_nuevo_tutor">
                                Nuevo docente evaluador
                            </label>

                            <select
                                id="id_nuevo_tutor"
                                name="id_nuevo_tutor"
                                required
                                autofocus
                            >
                                <option value="">
                                    Selecciona un docente
                                </option>

                                <?php foreach (
                                    $docentesDisponibles as $docente
                                ): ?>
                                    <?php
                                    $esTutorPrincipal = $tutorPrincipal
                                        && (int) $tutorPrincipal['id_tutor']
                                            === (int) $docente['id_tutor'];
                                    ?>

                                    <option
                                        value="<?= (int) $docente['id_tutor'] ?>"
                                        <?= $idNuevoTutor === (int) $docente['id_tutor']
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
                        </div>

                        <div class="form-group">
                            <label for="motivo_cambio">
                                Motivo del reemplazo
                            </label>

                            <textarea
                                id="motivo_cambio"
                                name="motivo_cambio"
                                rows="4"
                                minlength="5"
                                maxlength="255"
                                placeholder="Explica brevemente la razón del cambio"
                                required
                            ><?= htmlspecialchars(
                                $motivoCambio,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>

                            <p class="field-help">
                                Este motivo quedará guardado en el
                                historial del expediente.
                            </p>
                        </div>

                        <?php if ($advertencia !== ''): ?>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="confirmar_tutor_principal"
                                    name="confirmar_tutor_principal"
                                    value="1"
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
                                Confirmar reemplazo
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