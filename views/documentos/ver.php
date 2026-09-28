<?php

require_once __DIR__ . '/../layouts/header.php';

?>

<main class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <div>
                <p class="section-label mb-1">
                    Documento académico
                </p>

                <h1 class="h3 mb-0">
                    <?= htmlspecialchars(
                        $documento['numero_correlativo'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h1>
            </div>

            <button
                type="button"
                class="primary-action"
                onclick="window.print()"
            >
                Imprimir o guardar PDF
            </button>
        </div>

        <article class="form-container form-container-wide bg-white text-dark">
            <div class="mb-5">
                <p class="mb-1">
                    <strong>Universidad Privada Domingo Savio</strong>
                </p>

                <p class="text-secondary">
                    Sistema Académico de Modalidades de Grado
                </p>
            </div>

            <?= $contenidoSeguro ?>

            <hr class="my-5">

            <div class="small text-secondary">
                <p class="mb-1">
                    Documento:
                    <?= htmlspecialchars(
                        $documento['numero_correlativo'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <p class="mb-0">
                    Generado:
                    <?= htmlspecialchars(
                        date(
                            'd/m/Y H:i',
                            strtotime(
                                $documento['fecha_generacion']
                            )
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
            </div>
        </article>
    </div>
</main>

<?php

require_once __DIR__ . '/../layouts/footer.php';

?>