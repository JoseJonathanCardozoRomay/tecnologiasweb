<?php
require_once __DIR__.'/../includes/funciones.php';
iniciarSesion();
$id=validarId($_SESSION['id_usuario']??null);
if($id){require_once __DIR__.'/../config/conexion.php';registrarAuditoria($pdo,$id,'exitoso','LOGOUT','Autenticación','Cierre de sesión.');}
$_SESSION=[];
if(ini_get('session.use_cookies')){$params=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);}
session_destroy();
header('Location: /views/login/login.php');exit;
