<?php
declare(strict_types=1);

class TutorModel
{
    public function __construct(private PDO $pdo) {}
    public function obtenerTodos():array{return $this->pdo->query("SELECT t.*,u.nombre,u.apellido,u.correo,u.usuario,u.estado,(SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_tutor=t.id_tutor) total_materias,(SELECT COALESCE(AVG(ev.calificacion),0) FROM evaluaciones_tutoria ev INNER JOIN tutorias tu ON tu.id_tutoria=ev.id_tutoria WHERE tu.id_tutor=t.id_tutor) promedio FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario ORDER BY u.apellido,u.nombre")->fetchAll();}
    public function obtenerPorId(int $id):array|false{$s=$this->pdo->prepare("SELECT t.*,u.nombre,u.apellido,u.correo,u.usuario,u.estado FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario WHERE t.id_tutor=:id");$s->execute([':id'=>$id]);return $s->fetch();}
    public function obtenerPorUsuario(int $idUsuario):array|false{$s=$this->pdo->prepare('SELECT * FROM tutores WHERE id_usuario=:id');$s->execute([':id'=>$idUsuario]);return $s->fetch();}
    public function usuariosDisponibles(?int $actualIdUsuario=null):array{
        $sql="SELECT u.id_usuario,u.nombre,u.apellido,u.usuario FROM usuarios u INNER JOIN roles r ON r.id_rol=u.id_rol LEFT JOIN tutores t ON t.id_usuario=u.id_usuario WHERE r.nombre_rol='tutor' AND u.estado='activo' AND (t.id_tutor IS NULL";$p=[];
        if($actualIdUsuario!==null){$sql.=' OR u.id_usuario=:actual';$p[':actual']=$actualIdUsuario;}$sql.=') ORDER BY u.apellido,u.nombre';$s=$this->pdo->prepare($sql);$s->execute($p);return $s->fetchAll();
    }
    public function usuarioEsTutorActivo(int $idUsuario):bool{$s=$this->pdo->prepare("SELECT COUNT(*) FROM usuarios u INNER JOIN roles r ON r.id_rol=u.id_rol WHERE u.id_usuario=:id AND u.estado='activo' AND r.nombre_rol='tutor'");$s->execute([':id'=>$idUsuario]);return (int)$s->fetchColumn()>0;}
    public function crear(array $d):bool{return $this->pdo->prepare('INSERT INTO tutores(id_usuario,especialidad,biografia) VALUES(:u,:e,:b)')->execute([':u'=>$d['id_usuario'],':e'=>$d['especialidad']?:null,':b'=>$d['biografia']?:null]);}
    public function actualizar(int $id,array $d):bool{return $this->pdo->prepare('UPDATE tutores SET id_usuario=:u,especialidad=:e,biografia=:b WHERE id_tutor=:id')->execute([':u'=>$d['id_usuario'],':e'=>$d['especialidad']?:null,':b'=>$d['biografia']?:null,':id'=>$id]);}
    public function eliminar(int $id):bool{return $this->pdo->prepare('DELETE FROM tutores WHERE id_tutor=:id')->execute([':id'=>$id]);}
}
