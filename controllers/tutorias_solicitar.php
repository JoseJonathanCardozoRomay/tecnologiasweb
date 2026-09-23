<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['estudiante']);

$tituloPagina = 'Solicitar Tutoría - Sistema de Tutorías';
require __DIR__.'/../views/tutorias/solicitar.php';
