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
                    <p class="section-label">Correspondencia académica</p>
                    <h1>Plantillas de documentos</h1>
                    <p>
                        Ajusta cartas y citaciones antes de emitirlas.
                        Los documentos emitidos conservan su contenido original.
                    </p>
                </div>
                <a href="<?= $escapar($rutaBase) ?>controllers/documentos_listar.php" class="secondary-link">
                    Ver documentos
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= $escapar($tipoMensaje) ?>" role="alert">
                    <?= $escapar($mensaje) ?>
                </div>
            <?php endif; ?>

            <p class="field-help mb-4">
                Variables disponibles: {{destinatario}}, {{estudiante_nombre}},
                {{registro_universitario}}, {{carrera}}, {{modalidad}}, {{cohorte}},
                {{tema}}, {{titulo_trabajo}}, {{tutor_nombre}}, {{numero_carta}},
                {{referencia_decanatura}}, {{fecha_larga}}, {{etapa}},
                {{fecha_defensa}}, {{horario}}, {{ambiente}},
                {{orden_tribunal}} y {{numero_documento}}.
                Usa solo etiquetas de texto sencillas: h1, h2, h3, p, strong, em y listas.
            </p>

            <?php foreach ($plantillas as $plantilla): ?>
                <article class="form-container form-container-wide mb-4">
                    <div class="form-section-heading">
                        <div>
                            <h2><?= $escapar($plantilla['nombre']) ?></h2>
                            <p>
                                Código <?= $escapar($plantilla['codigo']) ?> ·
                                Versión <?= (int) $plantilla['version'] ?>
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="<?= $escapar($rutaBase) ?>controllers/plantillas_editar.php" class="module-form">
                        <?= campoCsrf() ?>
                        <input type="hidden" name="id_plantilla" value="<?= (int) $plantilla['id_plantilla'] ?>">
                        <div class="form-group">
                            <label for="cuerpo_<?= (int) $plantilla['id_plantilla'] ?>">Contenido HTML</label>
                            <textarea
                                id="cuerpo_<?= (int) $plantilla['id_plantilla'] ?>"
                                name="cuerpo_html"
                                rows="13"
                                maxlength="10000"
                                required
                            ><?= $escapar($plantilla['cuerpo_html']) ?></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="primary-action">Guardar plantilla</button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
