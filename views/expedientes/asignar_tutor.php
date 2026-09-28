<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$puedeRealizarAsignacion = (
    (int) $expediente['requiere_tutor'] === 1
    && $expediente['estado'] === 'activo'
    && $expediente['etapa_actual'] === 'previa'
    && !$asignacionActual
    && !empty($tutores)
);

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Gestión de tutores
                    </p>

                    <h1>Asignar tutor</h1>

                    <p>
                        Selecciona al tutor que acompañará al estudiante
                        durante su proceso de Modalidades de Grado.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/expedientes_ver.php?id=<?= (int) $idExpediente ?>"
                    class="secondary-link"
                >
                    Volver al expediente
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

                <div class="form-section-heading">
                    <div>
                        <h2>Información del estudiante</h2>

                        <p>
                            Verifica el expediente antes de confirmar
                            la asignación.
                        </p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Estudiante</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['nombre']
                                . ' '
                                . $expediente['apellido'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Registro universitario</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['registro_universitario']
                                    ?: 'Sin registro',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Modalidad</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['nombre_modalidad'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Cohorte</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['nombre_cohorte'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Etapa actual</label>

                        <input
                            type="text"
                            value="Etapa previa"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Título del trabajo</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $expediente['titulo_trabajo']
                                    ?: 'Pendiente de definición',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>
                </div>

                <?php if ($puedeRealizarAsignacion): ?>
                    <div class="form-section-heading">
                        <div>
                            <h2>Selección del tutor</h2>

                            <p>
                                La carga representa la cantidad de
                                expedientes que el tutor atiende actualmente.
                            </p>
                        </div>
                    </div>

                    <div
                        id="advertenciaCarga"
                        class="alert alert-warning d-none"
                        role="alert"
                    >
                        El tutor seleccionado alcanzó o superó la carga
                        recomendada de
                        <?= (int) $cargaRecomendada ?>
                        estudiantes. La asignación está permitida, pero
                        conviene revisar su disponibilidad.
                    </div>

                    <form method="POST" class="module-form">
                        <?= campoCsrf() ?>

                        <input
                            type="hidden"
                            name="id_expediente"
                            value="<?= (int) $idExpediente ?>"
                        >

                        <div class="form-group">
                            <label for="id_tutor">
                                Tutor
                            </label>

                            <select
                                id="id_tutor"
                                name="id_tutor"
                                required
                                autofocus
                            >
                                <option value="">
                                    Selecciona un tutor
                                </option>

                                <?php foreach ($tutores as $tutor): ?>
                                    <?php
                                    $cargaActual = (int) $tutor[
                                        'carga_actual'
                                    ];

                                    $superaCarga = (
                                        $cargaActual
                                        >= $cargaRecomendada
                                    );
                                    ?>

                                    <option
                                        value="<?= (int) $tutor['id_tutor'] ?>"
                                        data-carga="<?= $cargaActual ?>"
                                        <?= $idTutor === (int) $tutor['id_tutor']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $tutor['nombre']
                                            . ' '
                                            . $tutor['apellido'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        — <?= htmlspecialchars(
                                            $tutor['especialidad'] ?: 'Sin especialidad',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        — <?= $cargaActual ?>

                                        <?= $cargaActual === 1
                                            ? 'expediente'
                                            : 'expedientes' ?>

                                        <?= $superaCarga
                                            ? ' — carga recomendada alcanzada'
                                            : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <p class="field-help">
                                La carga recomendada configurada es de
                                <?= (int) $cargaRecomendada ?>
                                estudiantes por tutor.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="responsabilidades">
                                Responsabilidades de la designación
                            </label>

                            <textarea
                                id="responsabilidades"
                                name="responsabilidades"
                                rows="6"
                                maxlength="2000"
                                placeholder="Responsabilidades específicas del tutor dentro de este proceso"
                            ><?= htmlspecialchars(
                                $responsabilidades,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>

                            <p class="field-help">
                                Esta información quedará asociada a la
                                carta de designación.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="referencia_decanatura">Referencia de Decanatura</label>
                            <input id="referencia_decanatura" name="referencia_decanatura"
                                maxlength="100" required
                                value="<?= htmlspecialchars($referenciaDecanatura, ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Número de nota o resolución">
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="1"
                                name="disponibilidad_consultada" id="disponibilidad_consultada" required
                                <?= ($_POST['disponibilidad_consultada'] ?? '') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="disponibilidad_consultada">
                                Confirmo que consulté la disponibilidad del tutor.
                            </label>
                        </div>

                        <div class="alert alert-info" role="alert">
                            Al confirmar la asignación se cerrará la etapa
                            previa, se abrirá MG1 y se generará el registro
                            inicial de la carta de designación.
                        </div>

                        <div class="form-actions">
                            <button
                                type="submit"
                                class="primary-action"
                            >
                                Confirmar asignación
                            </button>

                            <a
                                href="<?= htmlspecialchars(
                                    $rutaBase,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>controllers/expedientes_ver.php?id=<?= (int) $idExpediente ?>"
                                class="cancel-action"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="form-actions">
                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/expedientes_ver.php?id=<?= (int) $idExpediente ?>"
                            class="primary-action"
                        >
                            Volver al expediente
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectorTutor = document.getElementById('id_tutor');
    const advertencia = document.getElementById('advertenciaCarga');
    const cargaRecomendada = <?= (int) $cargaRecomendada ?>;

    if (!selectorTutor || !advertencia) {
        return;
    }

    function actualizarAdvertencia() {
        const opcion = selectorTutor.options[
            selectorTutor.selectedIndex
        ];

        const cargaActual = Number(
            opcion?.dataset.carga ?? 0
        );

        advertencia.classList.toggle(
            'd-none',
            !selectorTutor.value
            || cargaActual < cargaRecomendada
        );
    }

    selectorTutor.addEventListener(
        'change',
        actualizarAdvertencia
    );

    actualizarAdvertencia();
});
</script>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>
