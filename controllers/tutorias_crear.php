<?php
require_once __DIR__.'/../includes/verificar_sesion.php';require_once __DIR__.'/../includes/funciones.php';requireRole(['estudiante']);require_once __DIR__.'/../config/conexion.php';require_once __DIR__.'/../models/TutoriaModel.php';require_once __DIR__.'/../models/TutorModel.php';require_once __DIR__.'/../models/EstudianteModel.php';require_once __DIR__.'/../models/MateriaModel.php';
$m=new TutoriaModel($pdo);$tm=new TutorModel($pdo);$em=new EstudianteModel($pdo);$mm=new MateriaModel($pdo);$errores=[];
$datos=['id_estudiante'=>$_POST['id_estudiante']??'','id_tutor'=>$_POST['id_tutor']??'','id_materia'=>$_POST['id_materia']??'','fecha'=>$_POST['fecha']??'','hora_inicio'=>$_POST['hora_inicio']??'','hora_fin'=>$_POST['hora_fin']??'','modalidad'=>$_POST['modalidad']??'presencial','lugar_o_enlace'=>trim((string)($_POST['lugar_o_enlace']??'')),'estado'=>'pendiente','observaciones'=>trim((string)($_POST['observaciones']??''))];
$miEstudiante=esEstudiante()?$em->obtenerPorUsuario((int)$_SESSION['id_usuario']):null;if($miEstudiante)$datos['id_estudiante']=$miEstudiante['id_estudiante'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 exigirCsrf();
 if(!validarId($datos['id_estudiante']))$errores[]='Selecciona un estudiante válido.';
 if(!validarId($datos['id_tutor']))$errores[]='Selecciona un tutor válido.';
 if(!validarId($datos['id_materia']))$errores[]='Selecciona una materia válida.';
 if(!fechaValida($datos['fecha'])||$datos['fecha']<date('Y-m-d'))$errores[]='La fecha debe ser válida y no puede estar en el pasado.';
 if(!horaValida($datos['hora_inicio'])||!horaValida($datos['hora_fin'])||$datos['hora_inicio']>=$datos['hora_fin'])$errores[]='El horario seleccionado no es válido.';
 if(!in_array($datos['modalidad'],['presencial','virtual'],true))$errores[]='Modalidad inválida.';
 if($datos['modalidad']==='virtual' && $datos['lugar_o_enlace']!=='' && !filter_var($datos['lugar_o_enlace'],FILTER_VALIDATE_URL))$errores[]='Para una tutoría virtual, el enlace debe ser una URL válida.';
 if($datos['modalidad']==='presencial' && $datos['lugar_o_enlace']!=='' && !textoValido($datos['lugar_o_enlace'],2,200))$errores[]='El lugar no es válido.';
 if(!textoValido($datos['observaciones'],0,1000))$errores[]='Las observaciones no pueden superar 1000 caracteres.';
 if(esEstudiante()&&$miEstudiante&&(int)$datos['id_estudiante']!==(int)$miEstudiante['id_estudiante'])$errores[]='No puedes solicitar una tutoría para otro estudiante.';
 if(!$errores){$errores=array_merge($errores,$m->validarProgramacion($datos));}
 if(!$errores){try{$m->crear($datos);registrarAccion($pdo,'CREAR','Tutorías','Se solicitó una tutoría para el '.$datos['fecha'].'.');flash('success','La solicitud de tutoría fue registrada y quedó pendiente de confirmación.');redirect('tutorias_listar.php');}catch(PDOException $e){$errores[]='No se pudo registrar la tutoría. Verifica que el horario continúe disponible.';}}
}
if($miEstudiante){$materias=$mm->obtenerPorCarrera((int)$miEstudiante['id_carrera']);}else{$materias=$mm->obtenerTodas(null,null,'activo');}
$estudiantes=$miEstudiante?[$em->obtenerPorId((int)$miEstudiante['id_estudiante'])]:$em->obtenerTodos();

// Cargamos todos los tutores activos agrupados por materia desde el servidor.
// Esto evita depender de una petición AJAX para poblar el selector de tutor
// y hace que la selección funcione incluso cuando el navegador bloquea o
// interrumpe una petición asíncrona.
$tutoresPorMateria=[];
$sqlTutores="SELECT DISTINCT tm.id_materia,t.id_tutor,u.nombre,u.apellido
    FROM tutor_materia tm
    INNER JOIN tutores t ON t.id_tutor=tm.id_tutor
    INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
    INNER JOIN materias m ON m.id_materia=tm.id_materia
    INNER JOIN carreras c ON c.id_carrera=m.id_carrera
    WHERE u.estado='activo' AND m.estado='activo' AND c.estado='activo'";
$paramsTutores=[];
if($miEstudiante){
    $sqlTutores.=' AND m.id_carrera=:carrera_estudiante';
    $paramsTutores[':carrera_estudiante']=(int)$miEstudiante['id_carrera'];
}
$sqlTutores.=' ORDER BY tm.id_materia,u.apellido,u.nombre';
$stmtTutores=$pdo->prepare($sqlTutores);
$stmtTutores->execute($paramsTutores);
foreach($stmtTutores->fetchAll() as $tutorFila){
    $materiaId=(int)$tutorFila['id_materia'];
    $tutoresPorMateria[$materiaId][]=[
        'id_tutor'=>(int)$tutorFila['id_tutor'],
        'nombre'=>trim($tutorFila['nombre'].' '.$tutorFila['apellido']),
    ];
}

// Mantiene la variable $tutores para la carga inicial del formulario en caso
// de que exista una materia seleccionada después de una validación fallida.
$tutores=$tutoresPorMateria[(int)($datos['id_materia']??0)]??[];
$tituloPagina='Solicitar Tutoría - Sistema de Tutorías';require __DIR__.'/../views/tutorias/form.php';
