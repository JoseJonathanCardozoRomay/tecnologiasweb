<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/EstudianteModel.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $materia=validarId($_GET['id_materia']??null);
    if(!$materia){echo json_encode(['ok'=>false,'message'=>'Materia inválida.']);exit;}

    $stmt=$pdo->prepare("SELECT COUNT(*) FROM materias m INNER JOIN carreras c ON c.id_carrera=m.id_carrera WHERE m.id_materia=:m AND m.estado='activo' AND c.estado='activo'");
    $stmt->execute([':m'=>$materia]);
    if((int)$stmt->fetchColumn()===0){echo json_encode(['ok'=>false,'message'=>'La materia no está activa o su carrera no está activa.']);exit;}

    if(esEstudiante()){
        $e=(new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
        if(!$e){echo json_encode(['ok'=>false,'message'=>'Perfil estudiante no encontrado.']);exit;}
        $s=$pdo->prepare("SELECT COUNT(*) FROM materias m INNER JOIN carreras c ON c.id_carrera=m.id_carrera WHERE m.id_materia=:m AND m.id_carrera=:c AND m.estado='activo' AND c.estado='activo'");
        $s->execute([':m'=>$materia,':c'=>$e['id_carrera']]);
        if((int)$s->fetchColumn()===0){echo json_encode(['ok'=>false,'message'=>'La materia no pertenece a tu carrera activa.']);exit;}
    }

    $s=$pdo->prepare("SELECT DISTINCT t.id_tutor,CONCAT(u.nombre,' ',u.apellido) AS nombre
                      FROM tutor_materia tm
                      INNER JOIN tutores t ON t.id_tutor=tm.id_tutor
                      INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
                      INNER JOIN materias m ON m.id_materia=tm.id_materia
                      INNER JOIN carreras c ON c.id_carrera=m.id_carrera
                      WHERE tm.id_materia=:m AND u.estado='activo' AND m.estado='activo' AND c.estado='activo'
                      ORDER BY u.apellido,u.nombre");
    $s->execute([':m'=>$materia]);
    echo json_encode(['ok'=>true,'tutores'=>$s->fetchAll()],JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('api_tutores_por_materia.php: '.$e->getMessage());
    http_response_code(500);
    echo json_encode(['ok'=>false,'message'=>'No se pudo consultar los tutores en este momento.'],JSON_UNESCAPED_UNICODE);
}
