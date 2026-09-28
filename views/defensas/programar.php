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

                    <h1>Programar defensa</h1>

                    <p>
                        Define la fecha, horario y ambiente de la defensa.
                        El sistema comprobará automáticamente los cruces.
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

                <?php if (empty($expedientesDisponibles)): ?>
                    <div
                        class="alert alert-info"
                        role="alert"
                    >
                        No existen expedientes disponibles. Deben estar
                        activos en MG1 o MG2, contar con tribunales y no
                        tener otra defensa vigente en la misma etapa.
                    </div>

                    <a
                        href="<?= htmlspecialchars(
                            $rutaBase,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>controllers/defensas_listar.php"
                        class="secondary-action"
                    >
                        Volver
                    </a>
                <?php else: ?>
                    <form
                        method="POST"
                        class="module-form"
                    >
                        <?= campoCsrf() ?>

                        <div class="form-group">
                            <label for="id_expediente">
                                Expediente
                            </label>

                            <select
                                id="id_expediente"
                                name="id_expediente"
                                required
                                autofocus
                            >
                                <option value="">
                                    Selecciona un expediente
                                </option>

                                <?php foreach (
                                    $expedientesDisponibles as $expediente
                                ): ?>
                                    <option
                                        value="<?= (int) $expediente['id_expediente'] ?>"
                                        <?= $idExpediente === (int) $expediente['id_expediente']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $expediente['nombre']
                                            . ' '
                                            . $expediente['apellido']
                                            . ' — '
                                            . strtoupper(
                                                $expediente['etapa_actual']
                                            )
                                            . ' — '
                                            . $expediente['modalidad']
                                            . ' — '
                                            . (int) $expediente[
                                                'tribunales_asignados'
                                            ]
                                            . ' tribunal(es)',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="fecha">
                                    Fecha
                                </label>

                                <input
                                    type="date"
                                    id="fecha"
                                    name="fecha"
                                    value="<?= htmlspecialchars(
                                        $fecha,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    min="<?= htmlspecialchars(
                                        date('Y-m-d'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="ambiente">
                                    Ambiente
                                </label>

                                <input
                                    type="text"
                                    id="ambiente"
                                    name="ambiente"
                                    value="<?= htmlspecialchars(
                                        $ambiente,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    minlength="2"
                                    maxlength="100"
                                    placeholder="Ejemplo: Aula 204"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="hora_inicio">
                                    Hora de inicio
                                </label>

                                <input
                                    type="time"
                                    id="hora_inicio"
                                    name="hora_inicio"
                                    value="<?= htmlspecialchars(
                                        $horaInicio,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="hora_fin">
                                    Hora de finalización
                                </label>

                                <input
                                    type="time"
                                    id="hora_fin"
                                    name="hora_fin"
                                    value="<?= htmlspecialchars(
                                        $horaFin,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-section-heading">
                            <div>
                                <h2>Autorización excepcional</h2>

                                <p>
                                    Completa estos datos únicamente cuando
                                    la defensa esté fuera del calendario
                                    institucional.
                                </p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="autorizado_por">
                                    Autoridad que aprobó
                                </label>

                                <input
                                    type="text"
                                    id="autorizado_por"
                                    name="autorizado_por"
                                    value="<?= htmlspecialchars(
                                        $autorizadoPor,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    maxlength="150"
                                    placeholder="Ejemplo: Decanatura"
                                >
                            </div>

                            <div class="form-group">
                                <label for="referencia_autorizacion">
                                    Referencia de autorización
                                </label>

                                <input
                                    type="text"
                                    id="referencia_autorizacion"
                                    name="referencia_autorizacion"
                                    value="<?= htmlspecialchars(
                                        $referenciaAutorizacion,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    maxlength="100"
                                    placeholder="Número de nota o resolución"
                                >
                            </div>
                        </div>

                        <div class="form-actions">
                            <button
                                type="submit"
                                class="primary-action"
                            >
                                Programar defensa
                            </button>

                            <a
                                href="<?= htmlspecialchars(
                                    $rutaBase,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>controllers/defensas_listar.php"
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