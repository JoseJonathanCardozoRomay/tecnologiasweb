<?php

require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$escapar = static fn($valor): string => htmlspecialchars(
    (string) $valor,
    ENT_QUOTES,
    'UTF-8'
);

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">Configuración académica</p>
                    <h1>Cupos por tutor</h1>
                    <p>
                        Configura hasta cinco estudiantes por tutor para
                        <?= $escapar($periodo['nombre']) ?>
                        (<?= $escapar($periodo['codigo']) ?>).
                    </p>
                </div>

                <a
                    href="<?= $escapar($rutaBase) ?>controllers/periodos_listar.php"
                    class="secondary-action"
                >
                    Volver a periodos
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-<?= $escapar($tipoMensaje) ?>"
                    role="alert"
                >
                    <?= $escapar($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="list-summary">
                <span>
                    <?= (int) $resumen['tutores_configurados'] ?> tutores configurados
                    · <?= (int) $resumen['tutores_habilitados'] ?> habilitados
                    · <?= (int) $resumen['cupos_ocupados'] ?> estudiantes inscritos
                    · <?= (int) $resumen['cupos_disponibles'] ?> cupos disponibles
                </span>
            </div>

            <form
                method="GET"
                action="<?= $escapar($rutaBase) ?>controllers/cupos_listar.php"
                class="search-form"
            >
                <input
                    type="hidden"
                    name="periodo"
                    value="<?= (int) $periodo['id_periodo'] ?>"
                >

                <div class="search-field">
                    <label for="buscar">Buscar tutor</label>
                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        maxlength="100"
                        value="<?= $escapar($busqueda) ?>"
                        placeholder="Nombre, usuario o especialidad"
                    >
                </div>

                <button type="submit" class="secondary-action">
                    Buscar
                </button>

                <?php if ($busqueda !== ''): ?>
                    <a
                        href="<?= $escapar($rutaBase) ?>controllers/cupos_listar.php?periodo=<?= (int) $periodo['id_periodo'] ?>"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($tutores) ?>
                    <?= count($tutores) === 1 ? 'tutor encontrado' : 'tutores encontrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tutor</th>
                            <th>Inscritos</th>
                            <th>Cupo máximo</th>
                            <th>Estado</th>
                            <th class="actions-column">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tutores)): ?>
                            <tr>
                                <td colspan="5" class="empty-result">
                                    No se encontraron tutores.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tutores as $tutor): ?>
                                <?php
                                $idTutor = (int) $tutor['id_tutor'];
                                $configurado = $tutor['id_tutor_periodo'] !== null;
                                $cupoActual = $configurado
                                    ? (int) $tutor['cupo_maximo']
                                    : 5;
                                $activoActual = !$configurado
                                    || (int) $tutor['activo'] === 1;
                                ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= $escapar(
                                                    trim($tutor['nombre'] . ' ' . $tutor['apellido'])
                                                ) ?>
                                            </strong>
                                            <span>
                                                <?= $escapar($tutor['usuario']) ?>
                                                <?php if (!empty($tutor['especialidad'])): ?>
                                                    · <?= $escapar($tutor['especialidad']) ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?= (int) $tutor['cupos_ocupados'] ?> estudiantes
                                    </td>
                                    <td>
                                        <label
                                            class="visually-hidden"
                                            for="cupo_<?= $idTutor ?>"
                                        >
                                            Cupo máximo para <?= $escapar($tutor['nombre']) ?>
                                        </label>
                                            <select
                                                id="cupo_<?= $idTutor ?>"
                                                name="cupo_maximo"
                                                class="form-select"
                                                form="form_cupo_<?= $idTutor ?>"
                                                required
                                            >
                                                <?php for ($cupo = 1; $cupo <= 5; $cupo++): ?>
                                                    <option
                                                        value="<?= $cupo ?>"
                                                        <?= $cupo === $cupoActual ? 'selected' : '' ?>
                                                    >
                                                        <?= $cupo ?> estudiante<?= $cupo === 1 ? '' : 's' ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                    </td>
                                    <td>
                                            <label
                                                class="visually-hidden"
                                                for="activo_<?= $idTutor ?>"
                                            >
                                                Estado del tutor <?= $escapar($tutor['nombre']) ?>
                                            </label>
                                            <select
                                                id="activo_<?= $idTutor ?>"
                                                name="activo"
                                                class="form-select"
                                                form="form_cupo_<?= $idTutor ?>"
                                                required
                                            >
                                                <option value="1" <?= $activoActual ? 'selected' : '' ?>>Habilitado</option>
                                                <option value="0" <?= !$activoActual ? 'selected' : '' ?>>Deshabilitado</option>
                                            </select>
                                    </td>
                                    <td>
                                        <form
                                            id="form_cupo_<?= $idTutor ?>"
                                            method="POST"
                                            action="<?= $escapar($rutaBase) ?>controllers/cupos_listar.php?periodo=<?= (int) $periodo['id_periodo'] ?><?= $busqueda !== '' ? '&buscar=' . rawurlencode($busqueda) : '' ?>"
                                        >
                                            <?= campoCsrf() ?>
                                            <input
                                                type="hidden"
                                                name="id_tutor"
                                                value="<?= $idTutor ?>"
                                            >
                                            <button type="submit" class="table-link">
                                                Guardar
                                            </button>
                                        </form>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
