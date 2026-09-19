<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'estudiante']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); echo json_encode(['error' => 'Método no permitido.']); exit; }
$idMateria = filter_var($_GET['id_materia'] ?? null, FILTER_VALIDATE_INT);
if (!$idMateria) { http_response_code(400); echo json_encode(['error' => 'id_materia no es válido.']); exit; }
$modelo = new TutorModel($pdo);
$tutores = $modelo->obtenerTutoresPorMateria($idMateria);
foreach ($tutores as &$tutor) $tutor['horarios'] = $modelo->obtenerDisponibilidad($tutor['id_tutor']);
echo json_encode($tutores, JSON_UNESCAPED_UNICODE);
