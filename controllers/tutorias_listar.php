<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
requireRole(['administrador','tutor','estudiante']);

// Ruta histórica: el módulo de tutorías personales está integrado en Proyectos de grado.
header('Location: /controllers/proyectos_grado_listar.php?seccion=solicitudes');
exit;
