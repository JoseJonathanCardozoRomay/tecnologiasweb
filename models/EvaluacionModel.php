<?php
declare(strict_types=1);

class EvaluacionModel
{
    public function __construct(private PDO $pdo) {}

    public function obtenerTodas(?int $idEstudiante=null):array{
        $sql="SELECT ev.*,t.id_tutoria,t.fecha,t.hora_inicio,CONCAT(ue.nombre,' ',ue.apellido) AS estudiante,CONCAT(ut.nombre,' ',ut.apellido) AS tutor,m.nombre_materia, pg.titulo AS proyecto_grado FROM evaluaciones_tutoria ev INNER JOIN tutorias t ON t.id_tutoria=ev.id_tutoria INNER JOIN estudiantes es ON es.id_estudiante=t.id_estudiante INNER JOIN usuarios ue ON ue.id_usuario=es.id_usuario INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor INNER JOIN usuarios ut ON ut.id_usuario=tr.id_usuario LEFT JOIN materias m ON m.id_materia=t.id_materia
                LEFT JOIN proyectos_grado pg ON pg.id_proyecto=t.id_proyecto";$p=[];
        if($idEstudiante!==null){$sql.=' WHERE t.id_estudiante=:e';$p[':e']=$idEstudiante;}$sql.=' ORDER BY ev.fecha_evaluacion DESC';$s=$this->pdo->prepare($sql);$s->execute($p);return $s->fetchAll();
    }
    public function obtenerPorId(int $id):array|false{$s=$this->pdo->prepare('SELECT * FROM evaluaciones_tutoria WHERE id_evaluacion=:id');$s->execute([':id'=>$id]);return $s->fetch();}
    public function tutoriasDisponibles(?int $idEstudiante=null):array{
        $sql="SELECT t.id_tutoria,t.fecha,t.hora_inicio,CONCAT(u.nombre,' ',u.apellido) AS estudiante,CONCAT(ut.nombre,' ',ut.apellido) AS tutor,m.nombre_materia, pg.titulo AS proyecto_grado FROM tutorias t INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante INNER JOIN usuarios u ON u.id_usuario=e.id_usuario INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor INNER JOIN usuarios ut ON ut.id_usuario=tr.id_usuario LEFT JOIN materias m ON m.id_materia=t.id_materia LEFT JOIN proyectos_grado pg ON pg.id_proyecto=t.id_proyecto LEFT JOIN evaluaciones_tutoria ev ON ev.id_tutoria=t.id_tutoria WHERE t.estado='realizada' AND ev.id_evaluacion IS NULL";$p=[];if($idEstudiante!==null){$sql.=' AND t.id_estudiante=:e';$p[':e']=$idEstudiante;}$sql.=' ORDER BY t.fecha DESC,t.hora_inicio DESC';$s=$this->pdo->prepare($sql);$s->execute($p);return $s->fetchAll();
    }
    public function tutoriasParaEditar(int $idEvaluacion):array{
        $actual=$this->obtenerPorId($idEvaluacion);if(!$actual)return [];
        $s=$this->pdo->prepare("SELECT t.id_tutoria,t.fecha,CONCAT(u.nombre,' ',u.apellido) estudiante,m.nombre_materia,pg.titulo AS proyecto_grado FROM tutorias t INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante INNER JOIN usuarios u ON u.id_usuario=e.id_usuario LEFT JOIN materias m ON m.id_materia=t.id_materia LEFT JOIN proyectos_grado pg ON pg.id_proyecto=t.id_proyecto WHERE t.estado='realizada' AND (t.id_tutoria=:t OR NOT EXISTS(SELECT 1 FROM evaluaciones_tutoria ev WHERE ev.id_tutoria=t.id_tutoria)) ORDER BY t.fecha DESC");$s->execute([':t'=>$actual['id_tutoria']]);return $s->fetchAll();
    }
    public function perteneceAEstudiante(int $idTutoria,int $idEstudiante):bool{$s=$this->pdo->prepare('SELECT COUNT(*) FROM tutorias WHERE id_tutoria=:t AND id_estudiante=:e');$s->execute([':t'=>$idTutoria,':e'=>$idEstudiante]);return (int)$s->fetchColumn()>0;}
    public function esTutoriaRealizada(int $idTutoria):bool{$s=$this->pdo->prepare("SELECT COUNT(*) FROM tutorias WHERE id_tutoria=:t AND estado='realizada'");$s->execute([':t'=>$idTutoria]);return (int)$s->fetchColumn()>0;}
    public function yaEvaluada(int $idTutoria,?int $excepto=null):bool{$sql='SELECT COUNT(*) FROM evaluaciones_tutoria WHERE id_tutoria=:t';$p=[':t'=>$idTutoria];if($excepto!==null){$sql.=' AND id_evaluacion<>:id';$p[':id']=$excepto;}$s=$this->pdo->prepare($sql);$s->execute($p);return (int)$s->fetchColumn()>0;}
    public function crear(array $d):bool{return $this->pdo->prepare('INSERT INTO evaluaciones_tutoria(id_tutoria,calificacion,comentario) VALUES(:t,:c,:co)')->execute([':t'=>$d['id_tutoria'],':c'=>$d['calificacion'],':co'=>$d['comentario']?:null]);}
    public function actualizar(int $id,array $d):bool{return $this->pdo->prepare('UPDATE evaluaciones_tutoria SET id_tutoria=:t,calificacion=:c,comentario=:co WHERE id_evaluacion=:id')->execute([':t'=>$d['id_tutoria'],':c'=>$d['calificacion'],':co'=>$d['comentario']?:null,':id'=>$id]);}
    public function eliminar(int $id):bool{return $this->pdo->prepare('DELETE FROM evaluaciones_tutoria WHERE id_evaluacion=:id')->execute([':id'=>$id]);}
}
