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

                    <h1>Nuevo periodo</h1>

                    <p>
                        Define las fechas en las que se podrán incorporar
                        estudiantes y realizar asignaciones.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/periodos_listar.php"
                    class="secondary-link"
                >
                    Volver al listado
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
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="codigo">
                                Código del periodo
                            </label>

                            <input
                                type="text"
                                id="codigo"
                                name="codigo"
                                value="<?= htmlspecialchars(
                                    $codigo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="30"
                                placeholder="Ejemplo: 2026-2"
                                required
                                autofocus
                            >

                            <p class="field-help">
                                Utiliza letras, números y guiones.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="nombre">
                                Nombre del periodo
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
                                maxlength="120"
                                placeholder="Ejemplo: Segundo semestre 2026"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">
                                Fecha de apertura
                            </label>

                            <input
                                type="date"
                                id="fecha_inicio"
                                name="fecha_inicio"
                                value="<?= htmlspecialchars(
                                    $fechaInicio,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="fecha_fin">
                                Fecha de cierre
                            </label>

                            <input
                                type="date"
                                id="fecha_fin"
                                name="fecha_fin"
                                value="<?= htmlspecialchars(
                                    $fechaFin,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="estado">
                                Estado inicial
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                required
                            >
                                <option
                                    value="planificado"
                                    <?= $estadoPeriodo === 'planificado'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Planificado
                                </option>

                                <option
                                    value="abierto"
                                    <?= $estadoPeriodo === 'abierto'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Abierto
                                </option>

                                <option
                                    value="cerrado"
                                    <?= $estadoPeriodo === 'cerrado'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Cerrado
                                </option>
                            </select>

                            <p class="field-help">
                                Planificado todavía no permite incorporar
                                estudiantes. Abierto habilita el periodo.
                            </p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar periodo
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/periodos_listar.php"
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