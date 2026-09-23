<?php
declare(strict_types=1);

class EstudianteModel
{
    public function __construct(private PDO $pdo) {}

    public function obtenerTodos(?string $busqueda=null): array
    {
        $sql="SELECT e.*,u.nombre,u.apellido,u.correo,u.usuario,u.estado,c.nombre_carrera
              FROM estudiantes e
              INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
              INNER JOIN carreras c ON c.id_carrera=e.id_carrera
              WHERE 1=1";
        $p=[];
        if($busqueda!==null && $busqueda!==''){
            $sql.=" AND (LOWER(CONCAT(u.nombre,' ',u.apellido)) LIKE LOWER(:q)
                     OR LOWER(u.correo) LIKE LOWER(:q2)
                     OR LOWER(e.registro_universitario) LIKE LOWER(:q3)
                     OR LOWER(c.nombre_carrera) LIKE LOWER(:q4))";
            $like='%'.$busqueda.'%';
            $p=[':q'=>$like,':q2'=>$like,':q3'=>$like,':q4'=>$like];
        }
        $sql.=' ORDER BY e.id_estudiante DESC';
        $s=$this->pdo->prepare($sql);$s->execute($p);return $s->fetchAll();
    }

    /**
     * Obtiene el perfil del estudiante junto con la carrera.
     * Esta relación soluciona el caso en el que la vista recibía solo
     * las columnas de `estudiantes` y por eso no podía mostrar el nombre.
     */
    public function obtenerPorId(int $id): array|false
    {
        $s=$this->pdo->prepare(
            "SELECT e.*,u.nombre,u.apellido,u.correo,u.usuario,u.estado,
                    c.nombre_carrera,c.estado AS estado_carrera
             FROM estudiantes e
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN carreras c ON c.id_carrera=e.id_carrera
             WHERE e.id_estudiante=:id"
        );
        $s->execute([':id'=>$id]);return $s->fetch();
    }

    /** Devuelve el perfil completo, incluido el nombre de carrera. */
    public function obtenerPorUsuario(int $idUsuario): array|false
    {
        $s=$this->pdo->prepare(
            "SELECT e.*,c.nombre_carrera,c.estado AS estado_carrera,
                    u.nombre,u.apellido,u.correo,u.usuario,u.estado AS estado_usuario
             FROM estudiantes e
             INNER JOIN carreras c ON c.id_carrera=e.id_carrera
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             WHERE e.id_usuario=:id"
        );
        $s->execute([':id'=>$idUsuario]);return $s->fetch();
    }

    public function usuariosDisponibles(?int $actualIdUsuario=null):array{
        $sql="SELECT u.id_usuario,u.nombre,u.apellido,u.usuario
              FROM usuarios u
              INNER JOIN roles r ON r.id_rol=u.id_rol
              LEFT JOIN estudiantes e ON e.id_usuario=u.id_usuario
              WHERE r.nombre_rol='estudiante' AND u.estado='activo'
                AND (e.id_estudiante IS NULL";
        $p=[];
        if($actualIdUsuario!==null){$sql.=' OR u.id_usuario=:actual';$p[':actual']=$actualIdUsuario;}
        $sql.=') ORDER BY u.apellido,u.nombre';$s=$this->pdo->prepare($sql);$s->execute($p);return $s->fetchAll();
    }

    public function usuarioEsEstudianteActivo(int $idUsuario):bool{
        $s=$this->pdo->prepare("SELECT COUNT(*) FROM usuarios u INNER JOIN roles r ON r.id_rol=u.id_rol WHERE u.id_usuario=:id AND u.estado='activo' AND r.nombre_rol='estudiante'");
        $s->execute([':id'=>$idUsuario]);return (int)$s->fetchColumn()>0;
    }
    public function carreraExiste(int $idCarrera):bool{
        $s=$this->pdo->prepare("SELECT COUNT(*) FROM carreras WHERE id_carrera=:id AND estado='activo'");
        $s->execute([':id'=>$idCarrera]);return (int)$s->fetchColumn()>0;
    }
    public function crear(array $d):bool{return $this->pdo->prepare('INSERT INTO estudiantes(id_usuario,id_carrera,semestre,registro_universitario) VALUES(:u,:c,:s,:r)')->execute([':u'=>$d['id_usuario'],':c'=>$d['id_carrera'],':s'=>$d['semestre'],':r'=>$d['registro_universitario']?:null]);}
    public function actualizar(int $id,array $d):bool{return $this->pdo->prepare('UPDATE estudiantes SET id_usuario=:u,id_carrera=:c,semestre=:s,registro_universitario=:r WHERE id_estudiante=:id')->execute([':u'=>$d['id_usuario'],':c'=>$d['id_carrera'],':s'=>$d['semestre'],':r'=>$d['registro_universitario']?:null,':id'=>$id]);}
    public function eliminar(int $id):bool{return $this->pdo->prepare('DELETE FROM estudiantes WHERE id_estudiante=:id')->execute([':id'=>$id]);}
}
