<?php

require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$faltanDatosBase = (
    empty($estudiantes)
    || empty($modalidades)
    || empty($cohortes)
);

?>

<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">
                        Modalidades de Grado
                    </p>

                    <h1>Nuevo expediente</h1>

                    <p>
                        Incorpora un estudiante a una modalidad y cohorte
                        para iniciar su proceso de graduación.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/expedientes_listar.php"
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

                <?php if ($faltanDatosBase): ?>
                    <div
                        class="alert alert-warning"
                        role="alert"
                    >
                        Para registrar un expediente debe existir al menos
                        un estudiante activo, una modalidad activa y una
                        cohorte activa.
                    </div>

                    <div class="form-actions">
                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/estudiantes_listar.php"
                            class="secondary-action"
                        >
                            Revisar estudiantes
                        </a>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/modalidades_listar.php"
                            class="secondary-action"
                        >
                            Revisar modalidades
                        </a>

                        <a
                            href="<?= htmlspecialchars(
                                $rutaBase,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>controllers/cohortes_listar.php"
                            class="secondary-action"
                        >
                            Revisar cohortes
                        </a>
                    </div>
                <?php else: ?>
                    <form method="POST" class="module-form">
                        <?= campoCsrf() ?>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="id_estudiante">
                                    Estudiante
                                </label>

                                <select
                                    id="id_estudiante"
                                    name="id_estudiante"
                                    required
                                    autofocus
                                >
                                    <option value="">
                                        Selecciona un estudiante
                                    </option>

                                    <?php foreach (
                                        $estudiantes as $estudiante
                                    ): ?>
                                        <option
                                            value="<?= (int) $estudiante['id_estudiante'] ?>"
                                            <?= $idEstudiante === (int) $estudiante['id_estudiante']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= htmlspecialchars(
                                                $estudiante['nombre']
                                                . ' '
                                                . $estudiante['apellido']
                                                . ' - '
                                                . (
                                                    $estudiante['registro_universitario']
                                                    ?: $estudiante['usuario']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="id_modalidad">
                                    Modalidad
                                </label>

                                <select
                                    id="id_modalidad"
                                    name="id_modalidad"
                                    required
                                >
                                    <option value="">
                                        Selecciona una modalidad
                                    </option>

                                    <?php foreach (
                                        $modalidades as $modalidad
                                    ): ?>
                                        <option
                                            value="<?= (int) $modalidad['id_modalidad'] ?>"
                                            <?= $idModalidad === (int) $modalidad['id_modalidad']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= htmlspecialchars(
                                                $modalidad['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                            <?= (int) $modalidad['requiere_tutor'] === 1
                                                ? ' - requiere tutor'
                                                : ' - sin tutor' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="id_cohorte">
                                    Cohorte
                                </label>

                                <select
                                    id="id_cohorte"
                                    name="id_cohorte"
                                    required
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
                                                . $cohorte['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="fecha_inicio">
                                    Fecha de inicio
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

                                <p class="field-help">
                                    Debe encontrarse dentro de las fechas
                                    establecidas para la cohorte.
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="titulo_trabajo">
                                Título provisional del trabajo
                            </label>

                            <input
                                type="text"
                                id="titulo_trabajo"
                                name="titulo_trabajo"
                                value="<?= htmlspecialchars(
                                    $tituloTrabajo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="200"
                                placeholder="Puede completarse posteriormente"
                            >

                            <p class="field-help">
                                Este dato es opcional durante la etapa previa.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">
                                Observaciones
                            </label>

                            <textarea
                                id="observaciones"
                                name="observaciones"
                                rows="4"
                                maxlength="2000"
                                placeholder="Información adicional del proceso"
                            ><?= htmlspecialchars(
                                $observaciones,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button
                                type="submit"
                                class="primary-action"
                            >
                                Guardar expediente
                            </button>

                            <a
                                href="<?= htmlspecialchars(
                                    $rutaBase,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>controllers/expedientes_listar.php"
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