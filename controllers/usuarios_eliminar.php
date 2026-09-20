<?php
require_once __DIR__.'/../includes/verificar_sesion.php';require_once __DIR__.'/../includes/funciones.php';requireRole(['administrador']);require_once __DIR__.'/../config/conexion.php';require_once __DIR__.'/../models/UsuarioModel.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('usuarios_listar.php');}exigirCsrf();$id=validarId($_POST['id']??null);if(!$id){flash('danger','Usuario inválido.');redirect('usuarios_listar.php');}
if((int)($_SESSION['id_usuario']??0)===$id){flash('danger','No puedes eliminar la cuenta con la que estás conectado.');redirect('usuarios_listar.php');}
$pdo->beginTransaction();try{
 $m=new UsuarioModel($pdo);$usuario=$m->obtenerPorId($id);if(!$usuario)throw new RuntimeException('Usuario no encontrado.');
 if($usuario['nombre_rol']==='administrador'){$activos=(int)$pdo->query("SELECT COUNT(*) FROM usuarios u INNER JOIN roles rr ON rr.id_rol=u.id_rol WHERE rr.nombre_rol='administrador' AND u.estado='activo'")->fetchColumn();if($activos<=1)throw new RuntimeException('No puedes eliminar al único administrador del sistema.');}
 $m->eliminar($id);$pdo->commit();registrarAccion($pdo,'ELIMINAR','Usuarios','Se eliminó el usuario '.$usuario['usuario'].'.');flash('success','Usuario eliminado correctamente.');
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('danger',$e instanceof RuntimeException?$e->getMessage():'No se puede eliminar el usuario porque tiene información relacionada.');}redirect('usuarios_listar.php');
