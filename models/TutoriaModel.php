<?php
declare(strict_types=1);

class TutoriaModel
{
    public function __construct(private PDO $pdo) {}

    public function obtenerTodas(): array
    {
        $sql = "SELECT t.*, CONCAT(ue.nombre,' ',ue.apellido) AS estudiante,
                       CONCAT(ut.nombre,' ',ut.apellido) AS tutor,
                       m.nombre_materia, c.nombre_carrera,
                       pg.titulo AS proyecto_grado
                FROM tutorias t
                INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante
                INNER JOIN usuarios ue ON ue.id_usuario=e.id_usuario
                INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor
                INNER JOIN usuarios ut ON ut.id_usuario=tr.id_usuario
                LEFT JOIN materias m ON m.id_materia=t.id_materia
                LEFT JOIN carreras c ON c.id_carrera=m.id_carrera
                LEFT JOIN proyectos_grado pg ON pg.id_proyecto=t.id_proyecto
                LEFT JOIN evaluaciones_tutoria ev ON ev.id_tutoria=t.id_tutoria
                ORDER BY t.fecha DESC,t.hora_inicio DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT t.*, e.id_carrera AS carrera_estudiante,
                       m.id_carrera AS carrera_materia,
                       CONCAT(ue.nombre,' ',ue.apellido) AS estudiante,
                       CONCAT(ut.nombre,' ',ut.apellido) AS tutor,
                       m.nombre_materia
                FROM tutorias t
                INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante
                INNER JOIN usuarios ue ON ue.id_usuario=e.id_usuario
                INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor
                INNER JOIN usuarios ut ON ut.id_usuario=tr.id_usuario
                LEFT JOIN materias m ON m.id_materia=t.id_materia
                LEFT JOIN proyectos_grado pg ON pg.id_proyecto=t.id_proyecto
                LEFT JOIN evaluaciones_tutoria ev ON ev.id_tutoria=t.id_tutoria
                WHERE t.id_tutoria=:id";
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }

    public function porTutor(int $id): array
    {
        $stmt=$this->pdo->prepare(
            "SELECT t.*,
                    CONCAT(u.nombre,' ',u.apellido) AS estudiante,
                    CONCAT(ut.nombre,' ',ut.apellido) AS tutor,
                    m.nombre_materia,
                    pg.titulo AS proyecto_grado,
                    ev.id_evaluacion
             FROM tutorias t
             INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor
             INNER JOIN usuarios ut ON ut.id_usuario=tr.id_usuario
             LEFT JOIN materias m ON m.id_materia=t.id_materia
             LEFT JOIN proyectos_grado pg ON pg.id_proyecto=t.id_proyecto
             LEFT JOIN evaluaciones_tutoria ev ON ev.id_tutoria=t.id_tutoria
             WHERE t.id_tutor=:id
             ORDER BY t.fecha DESC,t.hora_inicio DESC"
        );
        $stmt->execute([':id'=>$id]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene las tutorías del estudiante incluyendo su nombre completo.
     *
     * El nombre del estudiante es útil en listados compartidos con otros roles
     * y evita que la vista tenga que reconstruir la identidad desde la sesión.
     */
    public function porEstudiante(int $id): array
    {
        $stmt=$this->pdo->prepare(
            "SELECT t.*,
                    CONCAT(ue.nombre,' ',ue.apellido) AS estudiante,
                    CONCAT(ut.nombre,' ',ut.apellido) AS tutor,
                    m.nombre_materia,
                    pg.titulo AS proyecto_grado,
                    ev.id_evaluacion
             FROM tutorias t
             INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante
             INNER JOIN usuarios ue ON ue.id_usuario=e.id_usuario
             INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor
             INNER JOIN usuarios ut ON ut.id_usuario=tr.id_usuario
             LEFT JOIN materias m ON m.id_materia=t.id_materia
             LEFT JOIN proyectos_grado pg ON pg.id_proyecto=t.id_proyecto
             LEFT JOIN evaluaciones_tutoria ev ON ev.id_tutoria=t.id_tutoria
             WHERE t.id_estudiante=:id
             ORDER BY t.fecha DESC,t.hora_inicio DESC"
        );
        $stmt->execute([':id'=>$id]);
        return $stmt->fetchAll();
    }

    public function crear(array $d): bool
    {
        $stmt=$this->pdo->prepare("INSERT INTO tutorias(id_estudiante,id_tutor,id_materia,fecha,hora_inicio,hora_fin,modalidad,lugar_o_enlace,estado,observaciones) VALUES(:e,:t,:m,:f,:hi,:hf,:mo,:l,:es,:o)");
        return $stmt->execute([
            ':e'=>$d['id_estudiante'], ':t'=>$d['id_tutor'], ':m'=>$d['id_materia'],
            ':f'=>$d['fecha'], ':hi'=>$d['hora_inicio'], ':hf'=>$d['hora_fin'],
            ':mo'=>$d['modalidad'], ':l'=>$d['lugar_o_enlace'] ?: null,
            ':es'=>$d['estado'] ?? 'pendiente', ':o'=>$d['observaciones'] ?: null
        ]);
    }

    public function actualizar(int $id,array $d): bool
    {
        $stmt=$this->pdo->prepare("UPDATE tutorias SET id_estudiante=:e,id_tutor=:t,id_materia=:m,fecha=:f,hora_inicio=:hi,hora_fin=:hf,modalidad=:mo,lugar_o_enlace=:l,estado=:es,observaciones=:o WHERE id_tutoria=:id");
        return $stmt->execute([
            ':e'=>$d['id_estudiante'], ':t'=>$d['id_tutor'], ':m'=>$d['id_materia'],
            ':f'=>$d['fecha'], ':hi'=>$d['hora_inicio'], ':hf'=>$d['hora_fin'],
            ':mo'=>$d['modalidad'], ':l'=>$d['lugar_o_enlace'] ?: null,
            ':es'=>$d['estado'], ':o'=>$d['observaciones'] ?: null, ':id'=>$id
        ]);
    }

    public function eliminar(int $id): bool
    {
        return $this->pdo->prepare('DELETE FROM tutorias WHERE id_tutoria=:id')->execute([':id'=>$id]);
    }

    public function hayConflictoTutor(array $d, ?int $excepto=null): bool
    {
        $sql="SELECT COUNT(*) FROM tutorias WHERE id_tutor=:t AND fecha=:f AND estado IN ('pendiente','confirmada','realizada') AND hora_inicio<:hf AND hora_fin>:hi";
        $p=[':t'=>$d['id_tutor'],':f'=>$d['fecha'],':hi'=>$d['hora_inicio'],':hf'=>$d['hora_fin']];
        if($excepto!==null){$sql.=' AND id_tutoria<>:id';$p[':id']=$excepto;}
        $stmt=$this->pdo->prepare($sql);$stmt->execute($p);return (int)$stmt->fetchColumn()>0;
    }

    public function hayConflictoEstudiante(array $d, ?int $excepto=null): bool
    {
        $sql="SELECT COUNT(*) FROM tutorias WHERE id_estudiante=:e AND fecha=:f AND estado IN ('pendiente','confirmada','realizada') AND hora_inicio<:hf AND hora_fin>:hi";
        $p=[':e'=>$d['id_estudiante'],':f'=>$d['fecha'],':hi'=>$d['hora_inicio'],':hf'=>$d['hora_fin']];
        if($excepto!==null){$sql.=' AND id_tutoria<>:id';$p[':id']=$excepto;}
        $stmt=$this->pdo->prepare($sql);$stmt->execute($p);return (int)$stmt->fetchColumn()>0;
    }

    public function tutorTieneMateria(int $idTutor,int $idMateria): bool
    {
        $stmt=$this->pdo->prepare('SELECT COUNT(*) FROM tutor_materia WHERE id_tutor=:t AND id_materia=:m');
        $stmt->execute([':t'=>$idTutor,':m'=>$idMateria]);
        return (int)$stmt->fetchColumn()>0;
    }

    public function materiaPerteneceACarrera(int $idMateria,int $idCarrera): bool
    {
        $stmt=$this->pdo->prepare('SELECT COUNT(*) FROM materias WHERE id_materia=:m AND id_carrera=:c');
        $stmt->execute([':m'=>$idMateria,':c'=>$idCarrera]);
        return (int)$stmt->fetchColumn()>0;
    }

    public function dentroDeDisponibilidad(int $idTutor,string $fecha,string $horaInicio,string $horaFin): bool
    {
        $dias=[1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado',7=>null];
        $dia=$dias[(int)date('N',strtotime($fecha))] ?? null;
        if($dia===null)return false;
        $stmt=$this->pdo->prepare('SELECT COUNT(*) FROM disponibilidad_tutor WHERE id_tutor=:t AND dia_semana=:d AND hora_inicio<=:hi AND hora_fin>=:hf');
        $stmt->execute([':t'=>$idTutor,':d'=>$dia,':hi'=>$horaInicio,':hf'=>$horaFin]);
        return (int)$stmt->fetchColumn()>0;
    }

    public function validarProgramacion(array $d, ?int $excepto=null): array
    {
        $errores=[];
        // El estudiante debe existir y pertenecer a una cuenta activa.
        $stmt=$this->pdo->prepare("SELECT e.id_carrera, c.estado AS estado_carrera, u.estado AS estado_usuario
            FROM estudiantes e
            INNER JOIN carreras c ON c.id_carrera=e.id_carrera
            INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
            WHERE e.id_estudiante=:id");
        $stmt->execute([':id'=>$d['id_estudiante']]);
        $estudiante=$stmt->fetch();
        if(!$estudiante){
            $errores[]='El estudiante seleccionado no existe.';
            return $errores;
        }
        if($estudiante['estado_usuario']!=='activo')$errores[]='El estudiante seleccionado tiene su cuenta inactiva.';
        if($estudiante['estado_carrera']!=='activo')$errores[]='El estudiante pertenece a una carrera inactiva.';
        $carrera=(int)$estudiante['id_carrera'];

        // El tutor debe existir, estar activo y tener un perfil habilitado.
        $stmt=$this->pdo->prepare("SELECT COUNT(*) FROM tutores t
            INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
            WHERE t.id_tutor=:id AND u.estado='activo'");
        $stmt->execute([':id'=>$d['id_tutor']]);
        if((int)$stmt->fetchColumn()===0)$errores[]='El tutor seleccionado no existe o está inactivo.';

        // La materia debe existir, estar activa y pertenecer a una carrera activa.
        $stmt=$this->pdo->prepare("SELECT m.id_carrera, m.estado AS estado_materia, c.estado AS estado_carrera
            FROM materias m
            INNER JOIN carreras c ON c.id_carrera=m.id_carrera
            WHERE m.id_materia=:id");
        $stmt->execute([':id'=>$d['id_materia']]);
        $materia=$stmt->fetch();
        if(!$materia){
            $errores[]='La materia seleccionada no existe.';
            return $errores;
        }
        if($materia['estado_materia']!=='activo')$errores[]='La materia seleccionada está inactiva.';
        if($materia['estado_carrera']!=='activo')$errores[]='La carrera de la materia está inactiva.';
        $carreraMateria=(int)$materia['id_carrera'];
        if($carrera !== $carreraMateria)$errores[]='La materia no pertenece a la carrera del estudiante.';
        if(!$this->tutorTieneMateria((int)$d['id_tutor'],(int)$d['id_materia']))$errores[]='El tutor no está asignado a la materia seleccionada.';
        if(!$this->dentroDeDisponibilidad((int)$d['id_tutor'],$d['fecha'],$d['hora_inicio'],$d['hora_fin']))$errores[]='El horario está fuera de la disponibilidad del tutor.';
        if($this->hayConflictoTutor($d,$excepto))$errores[]='El tutor ya tiene otra tutoría en ese horario.';
        if($this->hayConflictoEstudiante($d,$excepto))$errores[]='El estudiante ya tiene otra tutoría en ese horario.';
        return $errores;
    }

    /**
     * Cancela una tutoría conservando su historial y registrando quién
     * realizó la cancelación, cuándo ocurrió y el motivo informado.
     *
     * @param int $id Identificador de la tutoría.
     * @param string $motivo Motivo obligatorio de la cancelación.
     * @param int $idUsuario Usuario que realiza la operación.
     * @return bool TRUE si la actualización fue exitosa.
     */
    public function cancelar(int $id, string $motivo, int $idUsuario): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE tutorias
             SET estado='cancelada',
                 motivo_cancelacion=:motivo,
                 cancelado_por_usuario=:usuario,
                 fecha_cancelacion=NOW()
             WHERE id_tutoria=:id"
        );

        return $stmt->execute([
            ':motivo' => trim($motivo),
            ':usuario' => $idUsuario,
            ':id' => $id,
        ]);
    }

    public function cambiarEstado(int $id,string $nuevoEstado): bool
    {
        $stmt=$this->pdo->prepare('UPDATE tutorias SET estado=:estado WHERE id_tutoria=:id');
        return $stmt->execute([':estado'=>$nuevoEstado,':id'=>$id]);
    }

    public function transicionPermitida(string $actual,string $nuevo): bool
    {
        $permitidas=[
            'pendiente'=>['confirmada','rechazada','cancelada'],
            'confirmada'=>['realizada','cancelada'],
            'realizada'=>[],
            'rechazada'=>[],
            'cancelada'=>[],
        ];
        return in_array($nuevo,$permitidas[$actual]??[],true);
    }

    public function obtenerSlotsDisponibles(int $idTutor,string $fecha,int $duracionMinutos=60,?int $excepto=null,?int $idEstudiante=null): array
    {
        $dias=[1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado',7=>null];
        $dia=$dias[(int)date('N',strtotime($fecha))] ?? null;
        if($dia===null)return [];

        $s=$this->pdo->prepare('SELECT hora_inicio,hora_fin FROM disponibilidad_tutor WHERE id_tutor=:t AND dia_semana=:d ORDER BY hora_inicio');
        $s->execute([':t'=>$idTutor,':d'=>$dia]);
        $disponibilidades=$s->fetchAll();

        $s=$this->pdo->prepare("SELECT hora_inicio,hora_fin FROM tutorias WHERE id_tutor=:t AND fecha=:f AND estado IN ('pendiente','confirmada','realizada')" . ($excepto ? ' AND id_tutoria<>:id' : '') . ' ORDER BY hora_inicio');
        $params=[':t'=>$idTutor,':f'=>$fecha];
        if($excepto)$params[':id']=$excepto;
        $s->execute($params);
        $ocupadas=$s->fetchAll();

        $bloqueosEstudiante=[];
        if($idEstudiante!==null){
            $s=$this->pdo->prepare("SELECT hora_inicio,hora_fin FROM tutorias WHERE id_estudiante=:e AND fecha=:f AND estado IN ('pendiente','confirmada','realizada')" . ($excepto ? ' AND id_tutoria<>:id' : '') . ' ORDER BY hora_inicio');
            $paramsEst=[':e'=>$idEstudiante,':f'=>$fecha];
            if($excepto)$paramsEst[':id']=$excepto;
            $s->execute($paramsEst);
            $bloqueosEstudiante=$s->fetchAll();
        }

        $slots=[];
        foreach($disponibilidades as $d){
            $inicio=strtotime($fecha.' '.$d['hora_inicio']);
            $fin=strtotime($fecha.' '.$d['hora_fin']);
            while($inicio+($duracionMinutos*60) <= $fin){
                $slotIni=date('H:i:s',$inicio);
                $slotFin=date('H:i:s',$inicio+($duracionMinutos*60));
                $ocupado=false;
                foreach($ocupadas as $o){
                    if($slotIni < $o['hora_fin'] && $slotFin > $o['hora_inicio']){$ocupado=true;break;}
                }
                if(!$ocupado){
                    foreach($bloqueosEstudiante as $o){
                        if($slotIni < $o['hora_fin'] && $slotFin > $o['hora_inicio']){$ocupado=true;break;}
                    }
                }
                if(!$ocupado && strtotime($fecha.' '.$slotIni) >= time()){
                    $slots[]=['hora_inicio'=>$slotIni,'hora_fin'=>$slotFin,'etiqueta'=>date('H:i',strtotime($slotIni)).' - '.date('H:i',strtotime($slotFin))];
                }
                $inicio += $duracionMinutos*60;
            }
        }
        return $slots;
    }
}
