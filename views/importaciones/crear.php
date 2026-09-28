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

                    <h1>Importar estudiantes</h1>

                    <p>
                        Crea expedientes a partir de un archivo CSV
                        exportado desde SATS.
                    </p>
                </div>

                <a
                    href="<?= htmlspecialchars(
                        $rutaBase,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>controllers/importaciones_listar.php"
                    class="secondary-link"
                >
                    Ver historial
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
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
                            enctype="multipart/form-data"
                            class="module-form"
                        >
                            <?= campoCsrf() ?>

                            <div class="form-group">
                                <label for="archivo_csv">
                                    Archivo CSV
                                </label>

                                <input
                                    type="file"
                                    id="archivo_csv"
                                    name="archivo_csv"
                                    accept=".csv,text/csv"
                                    required
                                >

                                <p class="field-help">
                                    Tamaño máximo: 2 MB. Se procesan hasta
                                    1000 filas por archivo.
                                </p>
                            </div>

                            <div class="alert alert-info">
                                El sistema no crea cuentas automáticamente.
                                Si un estudiante, modalidad o cohorte no
                                existe, la fila quedará pendiente para su
                                revisión.
                            </div>

                            <div class="form-actions">
                                <button
                                    type="submit"
                                    class="primary-action"
                                >
                                    Procesar archivo
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
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="form-container h-100">
                        <p class="section-label">
                            Formato requerido
                        </p>

                        <h2 class="h4">
                            Columnas obligatorias
                        </h2>

                        <ul class="mb-4">
                            <li>registro_universitario</li>
                            <li>modalidad</li>
                            <li>cohorte</li>
                            <li>titulo_trabajo</li>
                            <li>fecha_inicio</li>
                        </ul>

                        <p>
                            La fecha debe utilizar el formato
                            <strong>AAAA-MM-DD</strong>.
                        </p>

                        <p class="mb-2">
                            Ejemplo:
                        </p>

                        <pre class="p-3 border rounded small overflow-auto"><code>registro_universitario;modalidad;cohorte;titulo_trabajo;fecha_inicio
RU-2026-001;PROYECTO_GRADO;G1-2026-03;Sistema de inventarios;2026-03-01</code></pre>

                        <p class="field-help mb-0">
                            Se aceptan archivos separados por coma o
                            punto y coma.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>