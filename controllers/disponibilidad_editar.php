<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['tutor']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/DisponibilidadModel.php';
require_once __DIR__.'/../models/TutorModel.php';

$m=new DisponibilidadModel($pdo);
$tm=new TutorModel($pdo);
$id=validarId($_GET['id']??$_POST['id_disponibilidad']??null);
if(!$id)redirect('disponibilidad_listar.php');
$actual=$m->obtenerPorId($id);
$propio=$tm->obtenerPorUsuario((int)$_SESSION['id_usuario']);
if(!$actual||!$propio||(int)$actual['id_tutor']!==(int)$propio['id_tutor']){
    flash('danger','No tienes permiso sobre ese horario.');
    redirect('disponibilidad_listar.php');
}

$datos=$actual;
$errores=[];
$dias=['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    exigirCsrf();
    $datos=['id_tutor'=>(int)$propio['id_tutor'],'dia_semana'=>$_POST['dia_semana']??'','hora_inicio'=>$_POST['hora_inicio']??'','hora_fin'=>$_POST['hora_fin']??''];
    if(!in_array($datos['dia_semana'],$dias,true))$errores[]='Día inválido.';
    if(!horaValida($datos['hora_inicio'])||!horaValida($datos['hora_fin'])||$datos['hora_inicio']>=$datos['hora_fin'])$errores[]='El horario no es válido.';
    if(!$errores&&$m->existeCruce($datos,$id))$errores[]='El horario se cruza con otra disponibilidad.';
    if(!$errores){
        try{
            $m->actualizar($id,$datos);
            registrarAccion($pdo,'EDITAR','Disponibilidad','El tutor actualizó su disponibilidad #'.$id.'.');
            flash('success','Disponibilidad actualizada correctamente.');
            redirect('disponibilidad_listar.php');
        }catch(PDOException $e){$errores[]='No se pudo actualizar la disponibilidad.';}
    }
}

$tutores=[];
$tituloPagina='Editar Disponibilidad - Sistema de Tutorías';
require __DIR__.'/../views/disponibilidad/form.php';
