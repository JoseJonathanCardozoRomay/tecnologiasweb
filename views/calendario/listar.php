<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$nombresEtapa = [
    'previa' => 'Etapa previa',
    'mg1' => 'Modalidad de Grado I',
    'mg2' => 'Modalidad de Grado II'
];

$nombresTipo = [
    'taller' => 'Taller',
    'asignacion_tutor' => 'Asignación de tutor',
    'asignacion_tribunal' => 'Asignación de tribunal',
    'informe' => 'Informe',
    'defensa' => 'Defensa',
    'ingreso_mg2' => 'Ingreso a MG II',
    'otro' => 'Otro'
];

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Modalidades de Grado
                    </p>

                    <h1>Calendario académico</h1>

                    <p>
                        Organiza los hitos y fechas límite correspondientes
                        a cada cohorte y etapa del proceso.
                    </p>
                </div>

                <?php if ($puedeCrear): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/calendario_crear.php"
                        class="primary-action"
                    >
                        Nuevo hito
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

            <form
                method="GET"
                action="<?= htmlspecialchars(
                    $rutaBase,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>controllers/calendario_listar.php"
                class="search-form subject-search"
            >
                <div class="search-field">
                    <label for="buscar">
                        Buscar hito
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
                        placeholder="Nombre, tipo o cohorte"
                    >
                </div>

                <div class="search-field">
                    <label for="cohorte">
                        Cohorte
                    </label>

                    <select
                        id="cohorte"
                        name="cohorte"
                    >
                        <option value="">
                            Todas las cohortes
                        </option>

                        <?php foreach ($cohortes as $cohorte): ?>
                            <option
                                value="<?= (int) $cohorte['id_cohorte'] ?>"
                                <?= $idCohorte === (int) $cohorte['id_cohorte']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $cohorte['codigo']
                                    . ' - '
                                    . $cohorte['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="search-field">
                    <label for="etapa">
                        Etapa
                    </label>

                    <select
                        id="etapa"
                        name="etapa"
                    >
                        <option value="">
                            Todas las etapas
                        </option>

                        <?php foreach (
                            $nombresEtapa as $valorEtapa => $nombreEtapa
                        ): ?>
                            <option
                                value="<?= htmlspecialchars(
                                    $valorEtapa,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= $etapa === $valorEtapa
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $nombreEtapa,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button
                    type="submit"
                    class="secondary-action"
                >
                    Aplicar
                </button>

                <?php if (
                    $busqueda !== ''
                    || $idCohorte !== null
                    || $etapa !== ''
                ): ?>
                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/calendario_listar.php"
                        class="clear-action"
                    >
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <div class="list-summary">
                <span>
                    <?= count($hitos) ?>
                    <?= count($hitos) === 1
                        ? 'hito encontrado'
                        : 'hitos encontrados' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Hito</th>
                            <th>Cohorte</th>
                            <th>Etapa</th>
                            <th>Fecha límite</th>
                            <th>Avance esperado</th>

                            <?php if ($puedeEditar): ?>
                                <th class="actions-column">
                                    Acciones
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($hitos)): ?>
                            <tr>
                                <td
                                    colspan="<?= $puedeEditar ? 7 : 6 ?>"
                                    class="empty-result"
                                >
                                    No se encontraron hitos.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($hitos as $hito): ?>
                                <tr>
                                    <td>
                                        <?= (int) $hito['orden'] ?>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $hito['nombre'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $nombresTipo[$hito['tipo']]
                                                        ?? ucfirst(
                                                            str_replace(
                                                                '_',
                                                                ' ',
                                                                $hito['tipo']
                                                            )
                                                        ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $hito['codigo_cohorte'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $hito['nombre_cohorte'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $nombresEtapa[$hito['etapa']]
                                                ?? $hito['etapa'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y',
                                                strtotime(
                                                    $hito['fecha_limite']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $hito['avance_esperado_pct'] !== null
                                            ? (int) $hito['avance_esperado_pct']
                                                . '%'
                                            : 'No aplica' ?>
                                    </td>

                                    <?php if ($puedeEditar): ?>
                                        <td class="table-actions">
                                            <a
                                                href="<?= htmlspecialchars(
                                                    $rutaBase,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>controllers/calendario_editar.php?id=<?= (int) $hito['id_hito'] ?>"
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