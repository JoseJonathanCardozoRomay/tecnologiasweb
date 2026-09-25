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
                        Gestión de tutores
                    </p>

                    <h1>Materias asignadas</h1>

                    <p>
                        Selecciona las materias que puede impartir
                        este tutor.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/tutores_listar.php"
                    class="secondary-link"
                >
                    Volver al listado
                </a>
            </div>

            <div class="form-container form-container-wide">
                <div class="profile-summary">
                    <div>
                        <span class="profile-summary-label">
                            Tutor
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $tutor['nombre']
                                . ' '
                                . $tutor['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <div>
                        <span class="profile-summary-label">
                            Especialidad
                        </span>

                        <span>
                            <?= htmlspecialchars(
                                $tutor['especialidad']
                                    ?: 'Sin especialidad registrada',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>
                </div>

                <?php if ($mensaje !== ''): ?>
                    <div
                        class="alert alert-success"
                        role="alert"
                    >
                        <?= htmlspecialchars(
                            $mensaje,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                <?php endif; ?>

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

                <?php if (empty($materias)): ?>
                    <div
                        class="alert alert-warning"
                        role="alert"
                    >
                        No existen materias registradas en el sistema.
                        Primero debes registrar una materia.
                    </div>

                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/materias_crear.php"
                        class="primary-action"
                    >
                        Registrar materia
                    </a>
                <?php else: ?>
                    <form method="POST" class="module-form">
                        <input
                            type="hidden"
                            name="id_tutor"
                            value="<?= (int) $tutor['id_tutor'] ?>"
                        >

                        <div class="form-section-heading">
                            <div>
                                <h2>Materias disponibles</h2>

                                <p>
                                    Puedes seleccionar una o varias materias.
                                    Si no seleccionas ninguna, el tutor quedará
                                    sin materias asignadas.
                                </p>
                            </div>

                            <span class="selection-count">
                                <?= count($idsMateriasAsignadas) ?>
                                seleccionadas
                            </span>
                        </div>

                        <div class="subject-selection-grid">
                            <?php foreach ($materias as $materia): ?>
                                <?php
                                $idMateria = (int) $materia['id_materia'];

                                $estaAsignada = in_array(
                                    $idMateria,
                                    $idsMateriasAsignadas,
                                    true
                                );
                                ?>

                                <label
                                    class="subject-selection-item"
                                    for="materia_<?= $idMateria ?>"
                                >
                                    <input
                                        type="checkbox"
                                        id="materia_<?= $idMateria ?>"
                                        name="materias[]"
                                        value="<?= $idMateria ?>"
                                        <?= $estaAsignada
                                            ? 'checked'
                                            : '' ?>
                                    >

                                    <span class="subject-selection-content">
                                        <strong>
                                            <?= htmlspecialchars(
                                                $materia['nombre_materia'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <small>
                                            <?= htmlspecialchars(
                                                $materia['nombre_carrera'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </small>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>

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
                                ) ?>controllers/tutores_listar.php"
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