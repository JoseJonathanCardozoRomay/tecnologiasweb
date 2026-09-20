<?php
require_once __DIR__.'/../includes/verificar_sesion.php';require_once __DIR__.'/../includes/funciones.php';requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';require_once __DIR__.'/../models/UsuarioModel.php';require_once __DIR__.'/../models/RolModel.php';
$m=new UsuarioModel($pdo);$r=new RolModel($pdo);$errores=[];
$datos=['id_rol'=>$_POST['id_rol']??'','nombre'=>normalizarTexto((string)($_POST['nombre']??'')),'apellido'=>normalizarTexto((string)($_POST['apellido']??'')),'correo'=>strtolower(trim((string)($_POST['correo']??''))),'usuario'=>trim((string)($_POST['usuario']??'')),'telefono'=>trim((string)($_POST['telefono']??'')),'clave'=>(string)($_POST['clave']??'')];
if($_SERVER['REQUEST_METHOD']==='POST'){
 exigirCsrf();
 if(!validarId($datos['id_rol'])||!$r->obtenerPorId((int)$datos['id_rol']))$errores[]='Selecciona un rol válido.';
 if(!textoValido($datos['nombre'],2,100)||!preg_match('/^[\p{L} .\'-]+$/u',$datos['nombre']))$errores[]='El nombre debe tener 2-100 caracteres y solo letras.';
 if(!textoValido($datos['apellido'],2,100)||!preg_match('/^[\p{L} .\'-]+$/u',$datos['apellido']))$errores[]='El apellido debe tener 2-100 caracteres y solo letras.';
 if(!filter_var($datos['correo'],FILTER_VALIDATE_EMAIL)||strlen($datos['correo'])>150)$errores[]='El correo no tiene un formato válido.';
 if(!preg_match('/^[A-Za-z0-9._-]{4,50}$/',$datos['usuario']))$errores[]='El usuario debe tener 4-50 caracteres y solo letras, números, punto, guion o guion bajo.';
 if(strlen($datos['clave'])<6||strlen($datos['clave'])>72)$errores[]='La contraseña debe tener entre 6 y 72 caracteres.';
 if(!telefonoValido($datos['telefono']))$errores[]='El teléfono no tiene un formato válido.';
 if(!$errores&&$m->existeCorreo($datos['correo']))$errores[]='El correo ya está registrado.';
 if(!$errores&&$m->existeUsuario($datos['usuario']))$errores[]='El nombre de usuario ya está registrado.';
 if(!$errores){try{$m->crear($datos);registrarAccion($pdo,'CREAR','Usuarios','Se registró el usuario '.$datos['usuario'].'.');flash('success','Usuario registrado correctamente.');redirect('usuarios_listar.php');}catch(PDOException $e){$errores[]='No se pudo registrar el usuario. Verifica que los datos únicos no estén repetidos.';}}
}
$roles=$r->obtenerTodos();$tituloPagina='Nuevo Usuario - Sistema de Tutorías';require __DIR__.'/../views/usuarios/crear.php';
