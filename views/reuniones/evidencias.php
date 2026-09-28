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
                        Evidencias de reunión
                    </p>

                    <h1>
                        <?= htmlspecialchars(
                            $reunion['tema'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        Adjunta archivos que respalden la realización
                        y los acuerdos de la reunión.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/reunion_ver.php?id=<?= (int) $idReunion ?>"
                    class="secondary-link"
                >
                    Volver a la reunión
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div
                    class="alert alert-success"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

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

            <div class="form-container form-container-wide">
                <div class="form-section-heading">
                    <div>
                        <h2>Adjuntar evidencia</h2>

                        <p>
                            Se permiten archivos PDF, JPG y PNG de
                            hasta 5 MB.
                        </p>
                    </div>
                </div>

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="module-form"
                >
                    <?= campoCsrf() ?>

                    <input
                        type="hidden"
                        name="id_reunion"
                        value="<?= (int) $idReunion ?>"
                    >

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tipo">
                                Tipo de evidencia
                            </label>

                            <select
                                id="tipo"
                                name="tipo"
                                required
                            >
                                <option
                                    value="acta"
                                    <?= $tipo === 'acta'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Acta
                                </option>

                                <option
                                    value="fotografia"
                                    <?= $tipo === 'fotografia'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Fotografía
                                </option>

                                <option
                                    value="documento"
                                    <?= $tipo === 'documento'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Documento
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
                            <label for="archivo">
                                Archivo
                            </label>

                            <input
                                type="file"
                                id="archivo"
                                name="archivo"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">
                            Descripción
                        </label>

                        <input
                            type="text"
                            id="descripcion"
                            name="descripcion"
                            value="<?= htmlspecialchars(
                                $descripcion,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="255"
                            placeholder="Describe brevemente el contenido"
                        >
                    </div>

                    <button
                        type="submit"
                        class="primary-action"
                    >
                        Subir evidencia
                    </button>
                </form>
            </div>

            <div class="list-summary mt-4">
                <span>
                    <?= count($evidencias) ?>
                    <?= count($evidencias) === 1
                        ? 'evidencia registrada'
                        : 'evidencias registradas' ?>
                </span>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Tamaño</th>
                            <th>Fecha</th>
                            <th class="actions-column">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($evidencias)): ?>
                            <tr>
                                <td
                                    colspan="6"
                                    class="empty-result"
                                >
                                    No se adjuntaron evidencias.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($evidencias as $evidencia): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            $evidencia['nombre_original'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $evidencia['tipo']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $evidencia['descripcion']
                                                ?: 'Sin descripción',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= number_format(
                                            (int) $evidencia['tamano_bytes']
                                            / 1024,
                                            1
                                        ) ?> KB
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $evidencia['fecha_registro']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="table-actions">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $rutaBase,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>controllers/evidencia_descargar.php?id=<?= (int) $evidencia['id_evidencia'] ?>"
                                            class="table-link"
                                        >
                                            Descargar
                                        </a>
                                    </td>
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