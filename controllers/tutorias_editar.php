<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);

/*
 * La edición administrativa de una tutoría se deshabilita para evitar
 * modificaciones silenciosas sobre fecha, tutor, estudiante o materia.
 * Los cambios de ciclo de vida se realizan mediante tutorias_accion.php.
 */
http_response_code(403);
$tituloPagina = 'Tutoría no editable - Sistema de Tutorías';
require __DIR__.'/../views/errors/403.php';
