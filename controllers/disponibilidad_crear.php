<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
// La administración solo consulta horarios; cada tutor gestiona su propia disponibilidad.
requireRole(['tutor']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/DisponibilidadModel.php';
require_once __DIR__.'/../models/TutorModel.php';

$m=new DisponibilidadModel($pdo);
$tm=new TutorModel($pdo);
$propio=$tm->obtenerPorUsuario((int)$_SESSION['id_usuario']);
if(!$propio){
    http_response_code(403);
    $tituloPagina='Perfil de tutor no encontrado';
    require __DIR__.'/../views/errors/403.php';
    exit;
}

$errores=[];
$datos=['id_tutor'=>(int)$propio['id_tutor'],'dia_semana'=>$_POST['dia_semana']??'','hora_inicio'=>$_POST['hora_inicio']??'','hora_fin'=>$_POST['hora_fin']??''];
$dias=['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    exigirCsrf();
    if(!in_array($datos['dia_semana'],$dias,true))$errores[]='Selecciona un día válido.';
    if(!horaValida($datos['hora_inicio'])||!horaValida($datos['hora_fin'])||$datos['hora_inicio']>=$datos['hora_fin'])$errores[]='La hora de inicio debe ser menor que la hora de fin.';
    if(!$m->tutorActivo((int)$propio['id_tutor']))$errores[]='Tu cuenta de tutor está inactiva.';
    if(!$errores&&$m->existeCruce($datos))$errores[]='Ese horario se cruza con otra disponibilidad tuya.';
    if(!$errores){
        try{
            $m->crear($datos);
            registrarAccion($pdo,'CREAR','Disponibilidad','El tutor #'.$datos['id_tutor'].' agregó disponibilidad.');
            flash('success','Disponibilidad registrada correctamente.');
            redirect('disponibilidad_listar.php');
        }catch(PDOException $e){$errores[]='No se pudo registrar el horario. Comprueba que no sea un duplicado.';}
    }
}

$tutores=[];
$tituloPagina='Nueva Disponibilidad - Sistema de Tutorías';
require __DIR__.'/../views/disponibilidad/form.php';
