<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
requireRole(['administrador','tutor','estudiante']);

// Ruta legado: las tutorías personales ahora forman parte del módulo Proyectos de grado.
header('Location: /controllers/proyectos_grado_listar.php?seccion=solicitudes');
exit;
