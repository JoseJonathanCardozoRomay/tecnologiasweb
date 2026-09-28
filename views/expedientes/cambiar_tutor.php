<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
$escapar = fn($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<main class="flex-grow-1">
    <section class="module-section">
        <div class="container">
            <div class="module-heading">
                <div>
                    <p class="section-label">Modalidades de Grado</p>
                    <h1>Cambiar tutor</h1>
                    <p>La asignación anterior quedará en el historial. La etapa actual se conserva.</p>
                </div>
                <a class="secondary-link" href="expedientes_ver.php?id=<?= (int) $idExpediente ?>">Volver al expediente</a>
            </div>
            <div class="form-container form-container-wide">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= $escapar($error) ?></div>
                <?php endif; ?>
                <p><strong>Tutor actual:</strong>
                    <?= $escapar($asignacionActual['nombre'] . ' ' . $asignacionActual['apellido']) ?>
                </p>
                <form method="post" class="module-form">
                    <?= campoCsrf() ?>
                    <input type="hidden" name="id_expediente" value="<?= (int) $idExpediente ?>">
                    <div class="form-group">
                        <label for="id_tutor">Nuevo tutor</label>
                        <select id="id_tutor" name="id_tutor" required>
                            <option value="">Seleccionar</option>
                            <?php foreach ($tutores as $tutor): ?>
                                <option value="<?= (int) $tutor['id_tutor'] ?>"
                                    <?= (int) $datos['id_tutor'] === (int) $tutor['id_tutor'] ? 'selected' : '' ?>>
                                    <?= $escapar($tutor['nombre'] . ' ' . $tutor['apellido']) ?>
                                    — <?= $escapar($tutor['especialidad'] ?: 'Sin especialidad') ?>
                                    — <?= (int) $tutor['carga_actual'] ?> expedientes
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="motivo">Motivo del cambio o renuncia</label>
                        <textarea id="motivo" name="motivo" maxlength="255" required><?= $escapar($datos['motivo']) ?></textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fecha_nota_renuncia">Fecha de la nota</label>
                            <input id="fecha_nota_renuncia" name="fecha_nota_renuncia" type="date"
                                max="<?= date('Y-m-d') ?>" value="<?= $escapar($datos['fecha_nota_renuncia']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="referencia_decanatura">Referencia de Decanatura</label>
                            <input id="referencia_decanatura" name="referencia_decanatura"
                                maxlength="100" value="<?= $escapar($datos['referencia_decanatura']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="responsabilidades">Responsabilidades para la nueva carta</label>
                        <textarea id="responsabilidades" name="responsabilidades" maxlength="2000"><?= $escapar($datos['responsabilidades']) ?></textarea>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="disponibilidad_consultada"
                            name="disponibilidad_consultada" value="1" required
                            <?= ($_POST['disponibilidad_consultada'] ?? '') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="disponibilidad_consultada">Consulté la disponibilidad del nuevo tutor.</label>
                    </div>
                    <button class="primary-action" type="submit" <?= !$tutores ? 'disabled' : '' ?>>Confirmar cambio</button>
                </form>
            </div>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
