<?php
require_once __DIR__.'/../includes/verificar_sesion.php';require_once __DIR__.'/../includes/funciones.php';requireRole(['administrador','tutor','estudiante']);require_once __DIR__.'/../config/conexion.php';require_once __DIR__.'/../models/TutoriaModel.php';require_once __DIR__.'/../models/TutorModel.php';require_once __DIR__.'/../models/EstudianteModel.php';
$m=new TutoriaModel($pdo);$registros=$m->obtenerTodas();$filtro=$_GET['estado']??'';
if(esTutor()){$t=(new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);$registros=$t?$m->porTutor((int)$t['id_tutor']):[];}elseif(esEstudiante()){$e=(new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);$registros=$e?$m->porEstudiante((int)$e['id_estudiante']):[];}
if(in_array($filtro,['pendiente','confirmada','rechazada','realizada','cancelada'],true))$registros=array_values(array_filter($registros,fn($r)=>(string)$r['estado']===$filtro));
$tituloPagina='Tutorías - Sistema de Tutorías';require __DIR__.'/../views/tutorias/listar.php';
