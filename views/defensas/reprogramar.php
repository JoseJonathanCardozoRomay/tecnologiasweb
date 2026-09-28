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

                    <h1>Reprogramar defensa</h1>

                    <p>
                        La programación anterior se conservará en el
                        historial del expediente.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/defensa_ver.php?id=<?= (int) $idDefensa ?>"
                    class="secondary-link"
                >
                    Volver al detalle
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
                            Programación anterior
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y',
                                    strtotime($defensa['fecha'])
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <p class="mb-0 text-secondary">
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
                        </p>
                    </div>

                    <div class="col-md-3">
                        <p class="section-label mb-2">
                            Ambiente anterior
                        </p>

                        <strong>
                            <?= htmlspecialchars(
                                $defensa['ambiente'],
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

                <form
                    method="POST"
                    class="module-form"
                >
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_defensa"
                        value="<?= (int) $idDefensa ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fecha">
                                Nueva fecha
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
                                Nuevo ambiente
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
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">
                                Nueva hora de inicio
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
                                Nueva hora de finalización
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

                    <div class="form-group">
                        <label for="motivo_cambio">
                            Motivo de la reprogramación
                        </label>

                        <textarea
                            id="motivo_cambio"
                            name="motivo_cambio"
                            rows="3"
                            minlength="5"
                            maxlength="255"
                            required
                        ><?= htmlspecialchars(
                            $motivoCambio,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>
                    </div>

                    <div class="form-section-heading">
                        <div>
                            <h2>Autorización excepcional</h2>

                            <p>
                                Completa ambos campos solamente cuando
                                corresponda una autorización externa.
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
                            >
                        </div>

                        <div class="form-group">
                            <label for="referencia_autorizacion">
                                Referencia
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
                            >
                        </div>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="primary-action"
                        >
                            Guardar reprogramación
                        </button>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/defensa_ver.php?id=<?= (int) $idDefensa ?>"
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