<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador', 'estudiante']);

// Las evaluaciones son retroalimentación registrada sobre una tutoría realizada.
// Para preservar su trazabilidad no se permite modificarlas después de guardarlas.
http_response_code(403);
$tituloPagina = 'Evaluación no editable - Sistema de Tutorías';
require __DIR__.'/../views/errors/403.php';
