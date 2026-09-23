<?php
declare(strict_types=1);

/**
 * Fuente única de métricas para dashboards.
 * Combina tutorías personales y grupales sin mezclar sus modelos de datos.
 */
class DashboardModel
{
    public function __construct(private PDO $pdo) {}

    private function escalar(string $sql, array $params = []): int|float
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $valor = $stmt->fetchColumn();
        if ($valor === false || $valor === null || $valor === '') return 0;
        return is_numeric($valor) ? (str_contains((string)$valor, '.') ? (float)$valor : (int)$valor) : 0;
    }

    public function resumenAdmin(): array
    {
        $personales = (int)$this->escalar('SELECT COUNT(*) FROM tutorias');
        $grupales = (int)$this->escalar('SELECT COUNT(*) FROM tutorias_grupales');
        return [
            'usuarios' => (int)$this->escalar('SELECT COUNT(*) FROM usuarios'),
            'estudiantes' => (int)$this->escalar('SELECT COUNT(*) FROM estudiantes'),
            'tutores' => (int)$this->escalar('SELECT COUNT(*) FROM tutores'),
            'carreras' => (int)$this->escalar('SELECT COUNT(*) FROM carreras'),
            'materias' => (int)$this->escalar('SELECT COUNT(*) FROM materias'),
            'tutorias' => $personales + $grupales,
            'tutorias_personales' => $personales,
            'tutorias_grupales' => $grupales,
            'pendientes' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='pendiente'") + (int)$this->escalar("SELECT COUNT(*) FROM tutoria_grupal_estudiante WHERE estado='pendiente'"),
            'programadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='programada'") + (int)$this->escalar("SELECT COUNT(*) FROM tutoria_grupal_estudiante WHERE estado IN ('aprobada','inscrito')"),
            'realizadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='realizada'") + (int)$this->escalar("SELECT COUNT(*) FROM tutorias_grupales WHERE estado='realizada'"),
            'canceladas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='cancelada'") + (int)$this->escalar("SELECT COUNT(*) FROM tutorias_grupales WHERE estado='cancelada'"),
            'promedio' => (float)$this->escalar('SELECT COALESCE(AVG(calificacion),0) FROM evaluaciones_tutoria'),
            'estudiantes_grupales' => (int)$this->escalar("SELECT COUNT(DISTINCT id_estudiante) FROM tutoria_grupal_estudiante WHERE estado IN ('aprobada','inscrito','asistio')"),
        ];
    }

    public function resumenTutor(int $idTutor): array
    {
        $grupalEstudiantes = (int)$this->escalar(
            "SELECT COUNT(DISTINCT ge.id_estudiante)
             FROM tutoria_grupal_estudiante ge
             INNER JOIN tutorias_grupales tg ON tg.id_tutoria_grupal=ge.id_tutoria_grupal
             INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
             WHERE h.id_tutor=:id AND ge.estado IN ('aprobada','inscrito','asistio')",
            [':id'=>$idTutor]
        );
        return [
            'materias' => (int)$this->escalar('SELECT COUNT(*) FROM tutor_materia WHERE id_tutor=:id', [':id'=>$idTutor]),
            'dias' => (int)$this->escalar('SELECT COUNT(DISTINCT dia_semana) FROM disponibilidad_tutor WHERE id_tutor=:id', [':id'=>$idTutor]),
            'pendientes' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_tutor=:id AND estado='pendiente' AND estado_tutor='pendiente'", [':id'=>$idTutor]),
            'programadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_tutor=:id AND estado='programada'", [':id'=>$idTutor]),
            'realizadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_tutor=:id AND estado='realizada'", [':id'=>$idTutor]),
            'grupales' => (int)$this->escalar('SELECT COUNT(*) FROM tutorias_grupales tg INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario WHERE h.id_tutor=:id', [':id'=>$idTutor]),
            'estudiantes' => (int)$this->escalar('SELECT COUNT(DISTINCT id_estudiante) FROM tutorias WHERE id_tutor=:id', [':id'=>$idTutor]) + $grupalEstudiantes,
            'promedio' => (float)$this->escalar('SELECT COALESCE(AVG(ev.calificacion),0) FROM evaluaciones_tutoria ev INNER JOIN tutorias tu ON tu.id_tutoria=ev.id_tutoria WHERE tu.id_tutor=:id', [':id'=>$idTutor]),
        ];
    }

    public function resumenEstudiante(int $idEstudiante): array
    {
        return [
            'pendientes' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_estudiante=:id AND estado='pendiente'", [':id'=>$idEstudiante]) + (int)$this->escalar("SELECT COUNT(*) FROM tutoria_grupal_estudiante WHERE id_estudiante=:id AND estado='pendiente'", [':id'=>$idEstudiante]),
            'programadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_estudiante=:id AND estado='programada'", [':id'=>$idEstudiante]) + (int)$this->escalar("SELECT COUNT(*) FROM tutoria_grupal_estudiante WHERE id_estudiante=:id AND estado IN ('aprobada','inscrito')", [':id'=>$idEstudiante]),
            'realizadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_estudiante=:id AND estado='realizada'", [':id'=>$idEstudiante]),
            'evaluadas' => (int)$this->escalar('SELECT COUNT(*) FROM evaluaciones_tutoria ev INNER JOIN tutorias tu ON tu.id_tutoria=ev.id_tutoria WHERE tu.id_estudiante=:id', [':id'=>$idEstudiante]),
            'grupales' => (int)$this->escalar("SELECT COUNT(*) FROM tutoria_grupal_estudiante WHERE id_estudiante=:id AND estado IN ('aprobada','inscrito','asistio')", [':id'=>$idEstudiante]),
        ];
    }

    public function proximasTutoriasTutor(int $idTutor): array
    {
        $stmt=$this->pdo->prepare(
            "SELECT t.id_tutoria,t.fecha,t.hora_inicio,t.hora_fin,t.estado,t.modalidad,t.lugar_o_enlace,m.nombre_materia,
                    CONCAT(u.nombre,' ',u.apellido) AS estudiante,'personal' AS tipo
             FROM tutorias t
             INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             LEFT JOIN materias m ON m.id_materia=t.id_materia
             WHERE t.id_tutor=:id AND t.estado='programada'
               AND TIMESTAMP(t.fecha,t.hora_inicio)>=NOW()
             ORDER BY t.fecha,t.hora_inicio LIMIT 6"
        );
        $stmt->execute([':id'=>$idTutor]);
        return $stmt->fetchAll();
    }

    public function proximasTutoriasEstudiante(int $idEstudiante): array
    {
        $stmt=$this->pdo->prepare(
            "SELECT t.id_tutoria,t.fecha,t.hora_inicio,t.hora_fin,t.estado,t.modalidad,t.lugar_o_enlace,m.nombre_materia,
                    CONCAT(u.nombre,' ',u.apellido) AS tutor,'personal' AS tipo
             FROM tutorias t
             INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor
             INNER JOIN usuarios u ON u.id_usuario=tr.id_usuario
             LEFT JOIN materias m ON m.id_materia=t.id_materia
             WHERE t.id_estudiante=:id AND t.estado='programada'
               AND TIMESTAMP(t.fecha,t.hora_inicio)>=NOW()
             ORDER BY t.fecha,t.hora_inicio LIMIT 6"
        );
        $stmt->execute([':id'=>$idEstudiante]);
        return $stmt->fetchAll();
    }

    public function proximasGrupalesTutor(int $idTutor): array
    {
        $stmt=$this->pdo->prepare(
            "SELECT tg.id_tutoria_grupal,tg.fecha,h.hora_inicio,h.hora_fin,tg.estado,
                    m.nombre_materia,h.turno,COUNT(ge.id_estudiante) AS total_estudiantes,
                    'grupal' AS tipo
             FROM tutorias_grupales tg
             INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
             INNER JOIN materias m ON m.id_materia=h.id_materia
             LEFT JOIN tutoria_grupal_estudiante ge ON ge.id_tutoria_grupal=tg.id_tutoria_grupal AND ge.estado IN ('aprobada','inscrito','asistio')
             WHERE h.id_tutor=:id AND tg.estado IN ('programada','en_curso')
               AND TIMESTAMP(tg.fecha,h.hora_inicio)>=NOW()
             GROUP BY tg.id_tutoria_grupal
             ORDER BY tg.fecha,h.hora_inicio LIMIT 6"
        );
        $stmt->execute([':id'=>$idTutor]);
        return $stmt->fetchAll();
    }

    public function proximasGrupalesEstudiante(int $idEstudiante): array
    {
        $stmt=$this->pdo->prepare(
            "SELECT tg.id_tutoria_grupal,tg.fecha,h.hora_inicio,h.hora_fin,tg.estado,
                    m.nombre_materia,CONCAT(u.nombre,' ',u.apellido) AS tutor,h.turno,
                    'grupal' AS tipo
             FROM tutoria_grupal_estudiante ge
             INNER JOIN tutorias_grupales tg ON tg.id_tutoria_grupal=ge.id_tutoria_grupal
             INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
             INNER JOIN materias m ON m.id_materia=h.id_materia
             INNER JOIN tutores tr ON tr.id_tutor=h.id_tutor
             INNER JOIN usuarios u ON u.id_usuario=tr.id_usuario
             WHERE ge.id_estudiante=:id AND ge.estado IN ('aprobada','inscrito','asistio')
               AND tg.estado IN ('programada','en_curso')
               AND TIMESTAMP(tg.fecha,h.hora_inicio)>=NOW()
             ORDER BY tg.fecha,h.hora_inicio LIMIT 6"
        );
        $stmt->execute([':id'=>$idEstudiante]);
        return $stmt->fetchAll();
    }

    public function graficoTipos(): array
    {
        return [
            'labels'=>['Fin de carrera','Materias (grupales)'],
            'data'=>[(int)$this->escalar('SELECT COUNT(*) FROM tutorias WHERE id_proyecto IS NOT NULL'),(int)$this->escalar('SELECT COUNT(*) FROM tutorias_grupales')]
        ];
    }

    public function graficoEstados(): array
    {
        return [
            'labels'=>['Pendientes','Programadas / aprobadas','Realizadas','Canceladas'],
            'data'=>[
                (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='pendiente'")+ (int)$this->escalar("SELECT COUNT(*) FROM tutoria_grupal_estudiante WHERE estado='pendiente'"),
                (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='programada'")+ (int)$this->escalar("SELECT COUNT(*) FROM tutoria_grupal_estudiante WHERE estado IN ('aprobada','inscrito')"),
                (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='realizada'")+ (int)$this->escalar("SELECT COUNT(*) FROM tutorias_grupales WHERE estado='realizada'"),
                (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='cancelada'")+ (int)$this->escalar("SELECT COUNT(*) FROM tutorias_grupales WHERE estado='cancelada'")
            ]
        ];
    }

    public function graficoMaterias(): array
    {
        $stmt=$this->pdo->query(
            "SELECT nombre_materia, SUM(total) AS total FROM (
                SELECT COALESCE(m.nombre_materia,'Fin de carrera') nombre_materia, COUNT(*) total
                FROM tutorias t LEFT JOIN materias m ON m.id_materia=t.id_materia
                GROUP BY COALESCE(m.nombre_materia,'Fin de carrera')
                UNION ALL
                SELECT m.nombre_materia, COUNT(*) total
                FROM tutorias_grupales tg
                INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
                INNER JOIN materias m ON m.id_materia=h.id_materia
                GROUP BY m.nombre_materia
            ) x GROUP BY nombre_materia ORDER BY total DESC"
        );
        $rows=$stmt->fetchAll();
        return ['labels'=>array_column($rows,'nombre_materia'),'data'=>array_map('intval',array_column($rows,'total'))];
    }

    public function graficoTutores(): array
    {
        $stmt=$this->pdo->query(
            "SELECT tutor,SUM(total) total FROM (
                SELECT CONCAT(u.nombre,' ',u.apellido) tutor,COUNT(*) total
                FROM tutorias t INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor INNER JOIN usuarios u ON u.id_usuario=tr.id_usuario
                GROUP BY tr.id_tutor
                UNION ALL
                SELECT CONCAT(u.nombre,' ',u.apellido) tutor,COUNT(*) total
                FROM tutorias_grupales tg INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
                INNER JOIN tutores tr ON tr.id_tutor=h.id_tutor INNER JOIN usuarios u ON u.id_usuario=tr.id_usuario
                GROUP BY h.id_tutor
            ) x GROUP BY tutor ORDER BY total DESC LIMIT 10"
        );
        $rows=$stmt->fetchAll();
        return ['labels'=>array_column($rows,'tutor'),'data'=>array_map('intval',array_column($rows,'total'))];
    }
}
