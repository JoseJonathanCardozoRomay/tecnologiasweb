<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador', 'estudiante']);

// Las evaluaciones no se eliminan para mantener el historial de retroalimentación.
http_response_code(403);
$tituloPagina = 'Evaluación no eliminable - Sistema de Tutorías';
require __DIR__.'/../views/errors/403.php';
