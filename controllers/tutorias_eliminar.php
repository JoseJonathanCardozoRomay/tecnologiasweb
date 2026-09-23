<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);

// Las tutorías forman parte del historial académico y no se eliminan físicamente.
// La operación correcta es cancelar la tutoría mediante tutorias_accion.php.
flash('danger', 'Las tutorías no se eliminan para preservar el historial. Utiliza la opción Cancelar.');
redirect('tutorias_personales_listar.php');
