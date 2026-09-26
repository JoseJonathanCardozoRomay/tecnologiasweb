<?php
require_once __DIR__ . '/../config/sesion.php';

// ✅ SOLO entran estos roles
requerirRol(['administrador', 'tutor', 'estudiante']);

require_once __DIR__ . '/../models/AlertasSeguimientoModel.php';
// ... resto de tu código