<?php
require_once __DIR__.'/includes/funciones.php';
iniciarSesion();
if(!isset($_SESSION['id_usuario'],$_SESSION['rol'])){header('Location: /views/login/login.php');exit;}
header('Location: '.dashboardPorRol());
exit;
