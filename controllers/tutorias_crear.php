<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['estudiante']);
redirect('tutorias_solicitar.php');
