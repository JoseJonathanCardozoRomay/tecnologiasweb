<?php
// Estos datos permiten reutilizar el encabezado en las demás páginas
$tituloPagina = $tituloPagina ?? 'Inicio';
$rutaBase = $rutaBase ?? '';
?>
<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta
        name="description"
        content="Sistema para la gestión de tutorías académicas."
    >

    <title>
        <?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?>
        | Sistema de Tutorías
    </title>

    <!-- Aplicamos el tema guardado antes de mostrar la página -->
    <script src="<?= htmlspecialchars($rutaBase, ENT_QUOTES, 'UTF-8') ?>assets/js/tema.js"></script>

    <!-- Bootstrap se utiliza como base para el diseño responsive -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Estilos propios del sistema -->
    <link
        href="<?= htmlspecialchars($rutaBase, ENT_QUOTES, 'UTF-8') ?>assets/css/estilos.css"
        rel="stylesheet"
    >
</head>

<body class="d-flex flex-column min-vh-100">