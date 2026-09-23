<?php
require_once __DIR__.'/../includes/verificar_sesion.php';require_once __DIR__.'/../includes/funciones.php';requireRole(['administrador']);require_once __DIR__.'/../config/conexion.php';require_once __DIR__.'/../models/UsuarioModel.php';require_once __DIR__.'/../models/RolModel.php';
$m=new UsuarioModel($pdo);$r=new RolModel($pdo);$id=validarId($_GET['id']??$_POST['id_usuario']??null);if(!$id)redirect('usuarios_listar.php');$actual=$m->obtenerPorId($id);if(!$actual)redirect('usuarios_listar.php');
// La vista de edición utiliza $usuario_actual para mostrar y precargar los datos del registro.
// Se conserva también $actual porque la lógica de validación compara los cambios con el estado original.
$usuario_actual=$actual;
$errores=[];$datos=$actual;
if($_SERVER['REQUEST_METHOD']==='POST'){
 exigirCsrf();
 $datos=array_merge($actual,['id_rol'=>$_POST['id_rol']??'','nombre'=>normalizarTexto((string)($_POST['nombre']??'')),'apellido'=>normalizarTexto((string)($_POST['apellido']??'')),'correo'=>strtolower(trim((string)($_POST['correo']??''))),'usuario'=>trim((string)($_POST['usuario']??'')),'telefono'=>trim((string)($_POST['telefono']??'')),'estado'=>$_POST['estado']??'','clave'=>(string)($_POST['clave']??'')]);
 if(!validarId($datos['id_rol'])||!$r->obtenerPorId((int)$datos['id_rol']))$errores[]='Selecciona un rol válido.';
 if(!textoValido($datos['nombre'],2,100)||!preg_match('/^[\p{L} .\'-]+$/u',$datos['nombre']))$errores[]='El nombre debe tener 2-100 caracteres y solo letras.';
 if(!textoValido($datos['apellido'],2,100)||!preg_match('/^[\p{L} .\'-]+$/u',$datos['apellido']))$errores[]='El apellido debe tener 2-100 caracteres y solo letras.';
 if(!filter_var($datos['correo'],FILTER_VALIDATE_EMAIL)||strlen($datos['correo'])>150)$errores[]='El correo no tiene un formato válido.';
 if(!preg_match('/^[A-Za-z0-9._-]{4,50}$/',$datos['usuario']))$errores[]='El usuario no tiene un formato válido.';
 if(!in_array($datos['estado'],['activo','inactivo'],true))$errores[]='Estado inválido.';
 if(!telefonoValido($datos['telefono']))$errores[]='El teléfono no tiene un formato válido.';
 if($datos['clave']!==''&&(strlen($datos['clave'])<6||strlen($datos['clave'])>72))$errores[]='La nueva contraseña debe tener entre 6 y 72 caracteres.';
 $nuevoRol=$r->obtenerPorId((int)$datos['id_rol']);
 if(!$errores&&$nuevoRol&&$nuevoRol['nombre_rol']!==$actual['nombre_rol']){
   $stmt=$pdo->prepare('SELECT (SELECT COUNT(*) FROM tutores WHERE id_usuario=:id1) AS tutor, (SELECT COUNT(*) FROM estudiantes WHERE id_usuario=:id2) AS estudiante');$stmt->execute([':id1'=>$id,':id2'=>$id]);$perfil=$stmt->fetch();
   if(((int)$perfil['tutor']>0)||((int)$perfil['estudiante']>0))$errores[]='No se puede cambiar el rol de un usuario que ya tiene un perfil académico asociado. Modifica primero su perfil.';
   if($actual['nombre_rol']==='administrador' && $nuevoRol['nombre_rol']!=='administrador'){
      $activos=(int)$pdo->query("SELECT COUNT(*) FROM usuarios u INNER JOIN roles rr ON rr.id_rol=u.id_rol WHERE rr.nombre_rol='administrador' AND u.estado='activo'")->fetchColumn();
      if($activos<=1)$errores[]='No puedes convertir al único administrador activo en otro rol.';
   }
 }
 if(!$errores&&$m->existeCorreo($datos['correo'],$id))$errores[]='El correo ya está registrado por otro usuario.';
 if(!$errores&&$m->existeUsuario($datos['usuario'],$id))$errores[]='El usuario ya está registrado por otra cuenta.';
 if(!$errores&&$actual['nombre_rol']==='administrador'&&$datos['estado']==='inactivo'){$activos=(int)$pdo->query("SELECT COUNT(*) FROM usuarios u INNER JOIN roles rr ON rr.id_rol=u.id_rol WHERE rr.nombre_rol='administrador' AND u.estado='activo'")->fetchColumn();if($activos<=1)$errores[]='No puedes desactivar al único administrador activo del sistema.';}
 if(!$errores){try{$m->actualizar($id,$datos);registrarAccion($pdo,'EDITAR','Usuarios','Se actualizó el usuario '.$datos['usuario'].'.');flash('success','Usuario actualizado correctamente.');redirect('usuarios_listar.php');}catch(PDOException $e){$errores[]='No se pudo actualizar el usuario.';}}
}
$roles=$r->obtenerTodos();$tituloPagina='Editar Usuario - Sistema de Tutorías';require __DIR__.'/../views/usuarios/editar.php';
