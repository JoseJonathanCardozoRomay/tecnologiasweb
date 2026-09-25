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

                    <h1>Editar parámetro</h1>

                    <p>
                        Actualiza el valor y la información que respalda
                        este parámetro del sistema.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/parametros_listar.php"
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
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="clave"
                        value="<?= htmlspecialchars(
                            $clave,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="clave_visible">
                                Clave del parámetro
                            </label>

                            <input
                                type="text"
                                id="clave_visible"
                                value="<?= htmlspecialchars(
                                    $clave,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                readonly
                            >

                            <p class="field-help">
                                La clave identifica al parámetro dentro
                                del código y no puede modificarse.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="valor">
                                Valor
                            </label>

                            <input
                                type="text"
                                id="valor"
                                name="valor"
                                value="<?= htmlspecialchars(
                                    $valor,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="100"
                                placeholder="Valor del parámetro"
                                autofocus
                            >

                            <p class="field-help">
                                Puede quedar vacío cuando el valor todavía
                                no haya sido definido.
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="3"
                            maxlength="255"
                            required
                        ><?= htmlspecialchars(
                            $descripcion,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                        <p class="field-help">
                            Explica brevemente para qué se utiliza el
                            parámetro.
                        </p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fuente">
                                Fuente
                            </label>

                            <input
                                type="text"
                                id="fuente"
                                name="fuente"
                                value="<?= htmlspecialchars(
                                    $fuente,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                maxlength="100"
                                required
                            >

                            <p class="field-help">
                                Indica el documento, reglamento o criterio
                                del que proviene este valor.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="estado_evidencia">
                                Estado de evidencia
                            </label>

                            <select
                                id="estado_evidencia"
                                name="estado_evidencia"
                                required
                            >
                                <option
                                    value="confirmado"
                                    <?= $estadoEvidencia === 'confirmado'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Confirmado
                                </option>

                                <option
                                    value="pendiente"
                                    <?= $estadoEvidencia === 'pendiente'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Pendiente
                                </option>

                                <option
                                    value="propuesta"
                                    <?= $estadoEvidencia === 'propuesta'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Propuesta
                                </option>
                            </select>

                            <p class="field-help">
                                Permite diferenciar una regla confirmada
                                de una decisión todavía pendiente.
                            </p>
                        </div>
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
                            ) ?>controllers/parametros_listar.php"
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