<?php
require_once __DIR__ . '/../config/sesion.php';
cerrarSesion();
header('Location: index.php?accion=login');
exit;