<?php

require_once __DIR__ . '/../../includes/csrf.php';
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

                    <h1>Editar hito</h1>

                    <p>
                        Actualiza la actividad, fecha límite y avance
                        esperado dentro del calendario académico.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/calendario_listar.php"
                    class="secondary-link"
                >
                    Volver al calendario
                </a>
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

                <form method="POST" class="module-form">
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_hito"
                        value="<?= (int) $idHito ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="id_cohorte">
                                Cohorte
                            </label>

                            <select
                                id="id_cohorte"
                                name="id_cohorte"
                                required
                                autofocus
                            >
                                <option value="">
                                    Selecciona una cohorte
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
                                            . $cohorte['nombre']
                                            . (
                                                (int) $cohorte['activa'] === 1
                                                    ? ''
                                                    : ' (inactiva)'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="etapa">
                                Etapa
                            </label>

                            <select
                                id="etapa"
                                name="etapa"
                                required
                            >
                                <option
                                    value="previa"
                                    <?= $etapa === 'previa'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Etapa previa
                                </option>

                                <option
                                    value="mg1"
                                    <?= $etapa === 'mg1'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Modalidad de Grado I
                                </option>

                                <option
                                    value="mg2"
                                    <?= $etapa === 'mg2'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Modalidad de Grado II
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tipo">
                                Tipo de hito
                            </label>

                            <select
                                id="tipo"
                                name="tipo"
                                required
                            >
                                <option
                                    value="taller"
                                    <?= $tipo === 'taller'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Taller
                                </option>

                                <option
                                    value="asignacion_tutor"
                                    <?= $tipo === 'asignacion_tutor'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Asignación de tutor
                                </option>

                                <option
                                    value="asignacion_tribunal"
                                    <?= $tipo === 'asignacion_tribunal'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Asignación de tribunal
                                </option>

                                <option
                                    value="informe"
                                    <?= $tipo === 'informe'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Informe
                                </option>

                                <option
                                    value="defensa"
                                    <?= $tipo === 'defensa'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Defensa
                                </option>

                                <option
                                    value="ingreso_mg2"
                                    <?= $tipo === 'ingreso_mg2'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Ingreso a Modalidad de Grado II
                                </option>

                                <option
                                    value="otro"
                                    <?= $tipo === 'otro'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Otro
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="orden">
                                Orden
                            </label>

                            <input
                                type="number"
                                id="orden"
                                name="orden"
                                value="<?= is_int($orden)
                                    ? $orden
                                    : '' ?>"
                                min="1"
                                max="999"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="fecha_limite">
                                Fecha límite
                            </label>

                            <input
                                type="date"
                                id="fecha_limite"
                                name="fecha_limite"
                                value="<?= htmlspecialchars(
                                    $fechaLimite,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="avance_esperado_pct">
                                Avance esperado
                            </label>

                            <input
                                type="number"
                                id="avance_esperado_pct"
                                name="avance_esperado_pct"
                                value="<?= is_int($avanceEsperado)
                                    ? $avanceEsperado
                                    : '' ?>"
                                min="0"
                                max="100"
                                placeholder="Ejemplo: 50"
                            >

                            <p class="field-help">
                                Este porcentaje servirá como referencia para
                                alertas y no bloqueará la presentación.
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre">
                            Nombre del hito
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="<?= htmlspecialchars(
                                $nombre,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            minlength="3"
                            maxlength="150"
                            required
                        >
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar cambios
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/calendario_listar.php"
                            class="cancel-action"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>