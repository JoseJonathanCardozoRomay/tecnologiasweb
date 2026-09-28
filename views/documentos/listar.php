<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$escapar = static fn ($valor) => htmlspecialchars(
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
                    <p class="section-label">Modalidades de Grado</p>
                    <h1>Documentos generados</h1>
                    <p>
                        Consulta las cartas y citaciones emitidas
                        para cada expediente.
                    </p>
                </div>
            </div>

            <form
                method="get"
                action="<?= $escapar($rutaBase) ?>controllers/documentos_listar.php"
                class="search-form subject-search mb-4"
            >
                <div class="search-field">
                    <label for="q">Buscar documento</label>
                    <input
                        id="q"
                        name="q"
                        type="search"
                        maxlength="100"
                        value="<?= $escapar($busqueda) ?>"
                        placeholder="Número, destinatario o estudiante"
                    >
                </div>

                <button type="submit" class="primary-action">
                    Buscar
                </button>
            </form>

            <?php if (!$documentos): ?>
                <div class="alert alert-info">
                    <?= $busqueda !== ''
                        ? 'No se encontraron documentos con esa búsqueda.'
                        : 'Todavía no se generaron documentos.' ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Tipo</th>
                                <th>Destinatario</th>
                                <th>Estudiante</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documentos as $documento): ?>
                                <tr>
                                    <td>
                                        <?= $escapar(
                                            $documento['numero_correlativo']
                                        ) ?>
                                    </td>
                                    <td>
                                        <?= $escapar(
                                            ucfirst(str_replace(
                                                '_',
                                                ' ',
                                                $documento['tipo']
                                            ))
                                        ) ?>
                                    </td>
                                    <td>
                                        <?= $escapar(
                                            $documento['destinatario']
                                        ) ?>
                                    </td>
                                    <td>
                                        <?= $escapar(
                                            $documento['estudiante_nombre']
                                            . ' '
                                            . $documento['estudiante_apellido']
                                        ) ?>
                                    </td>
                                    <td>
                                        <?= $escapar(date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $documento['fecha_generacion']
                                            )
                                        )) ?>
                                    </td>
                                    <td>
                                        <a href="<?= $escapar($rutaBase) ?>controllers/documento_ver.php?id=<?= (int) $documento['id_documento'] ?>">
                                            Ver documento
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($documentos) === 200): ?>
                    <p class="text-muted small">
                        Se muestran los 200 documentos más recientes.
                        Usa la búsqueda para localizar otro.
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>