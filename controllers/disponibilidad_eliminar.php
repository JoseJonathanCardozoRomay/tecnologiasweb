<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['tutor']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/DisponibilidadModel.php';
require_once __DIR__.'/../models/TutorModel.php';

if($_SERVER['REQUEST_METHOD']!=='POST')redirect('disponibilidad_listar.php');
exigirCsrf();
$id=validarId($_POST['id']??null);
$m=new DisponibilidadModel($pdo);
$tm=new TutorModel($pdo);
$d=$id?$m->obtenerPorId($id):false;
$propio=$tm->obtenerPorUsuario((int)$_SESSION['id_usuario']);
if(!$d||!$propio||(int)$d['id_tutor']!==(int)$propio['id_tutor']){
    flash('danger','No tienes permiso sobre ese horario.');
    redirect('disponibilidad_listar.php');
}

try{
    $m->eliminar($id);
    registrarAccion($pdo,'ELIMINAR','Disponibilidad','El tutor eliminó su horario #'.$id.'.');
    flash('success','Disponibilidad eliminada correctamente.');
}catch(PDOException $e){
    flash('danger','No se pudo eliminar el horario.');
}
redirect('disponibilidad_listar.php');
