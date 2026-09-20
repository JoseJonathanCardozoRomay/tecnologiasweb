<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

header('Content-Type: application/json; charset=utf-8');
$tutor=validarId($_GET['id_tutor']??null);
$materia=validarId($_GET['id_materia']??null);
$fecha=(string)($_GET['fecha']??'');
if(!$tutor||!$materia||!fechaValida($fecha)||$fecha<date('Y-m-d')){echo json_encode(['ok'=>false,'message'=>'Parámetros inválidos.']);exit;}

$m=new TutoriaModel($pdo);

$stmt=$pdo->prepare("SELECT COUNT(*) FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario WHERE t.id_tutor=:t AND u.estado='activo'");
$stmt->execute([':t'=>$tutor]);
if((int)$stmt->fetchColumn()===0){echo json_encode(['ok'=>false,'message'=>'El tutor no está activo.']);exit;}

$stmt=$pdo->prepare("SELECT COUNT(*) FROM materias m INNER JOIN carreras c ON c.id_carrera=m.id_carrera WHERE m.id_materia=:m AND m.estado='activo' AND c.estado='activo'");
$stmt->execute([':m'=>$materia]);
if((int)$stmt->fetchColumn()===0){echo json_encode(['ok'=>false,'message'=>'La materia no está activa.']);exit;}

if(!$m->tutorTieneMateria($tutor,$materia)){echo json_encode(['ok'=>false,'message'=>'El tutor no está asignado a esa materia.']);exit;}

if(esEstudiante()){
    $e=(new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    if(!$e){echo json_encode(['ok'=>false,'message'=>'Perfil estudiante no encontrado.']);exit;}
    if(!fechaValida($fecha)){echo json_encode(['ok'=>false,'message'=>'Fecha inválida.']);exit;}
    if(!$m->materiaPerteneceACarrera($materia,(int)$e['id_carrera'])){echo json_encode(['ok'=>false,'message'=>'La materia no corresponde a tu carrera.']);exit;}
    echo json_encode(['ok'=>true,'slots'=>$m->obtenerSlotsDisponibles($tutor,$fecha,60,null,(int)$e['id_estudiante'])]);
    exit;
}

echo json_encode(['ok'=>true,'slots'=>$m->obtenerSlotsDisponibles($tutor,$fecha,60)]);
