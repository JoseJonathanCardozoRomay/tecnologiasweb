<?php
declare(strict_types=1);

class DashboardModel
{
    public function __construct(private PDO $pdo) {}

    /**
     * Ejecuta una consulta que devuelve un único valor numérico.
     *
     * PDO/MySQL puede devolver COUNT(), AVG(), COALESCE(), etc.
     * como string aunque el resultado sea conceptualmente numérico.
     * Como este método declara int|float, convertimos explícitamente
     * el valor antes de retornarlo para evitar TypeError.
     *
     * @param string $sql Consulta SQL que devuelve un único valor.
     * @param array $params Parámetros de la consulta preparada.
     * @return int|float Valor numérico obtenido.
     */
    private function escalar(string $sql, array $params = []): int|float
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $valor = $stmt->fetchColumn();

        // Sin resultado: devolvemos cero, que sí cumple int|float.
        if ($valor === false || $valor === null || $valor === '') {
            return 0;
        }

        // MySQL/PDO suele entregar los valores numéricos como strings.
        if (is_numeric($valor)) {
            return str_contains((string)$valor, '.')
                ? (float)$valor
                : (int)$valor;
        }

        // Nunca devolver un string porque el método exige int|float.
        return 0;
    }

    public function resumenAdmin(): array
    {
        return [
            'usuarios' => (int)$this->escalar('SELECT COUNT(*) FROM usuarios'),
            'estudiantes' => (int)$this->escalar('SELECT COUNT(*) FROM estudiantes'),
            'tutores' => (int)$this->escalar('SELECT COUNT(*) FROM tutores'),
            'carreras' => (int)$this->escalar('SELECT COUNT(*) FROM carreras'),
            'materias' => (int)$this->escalar('SELECT COUNT(*) FROM materias'),
            'tutorias' => (int)$this->escalar('SELECT COUNT(*) FROM tutorias'),
            'pendientes' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='pendiente'"),
            'confirmadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='confirmada'"),
            'realizadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='realizada'"),
            'canceladas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE estado='cancelada'"),
            'promedio' => (float)$this->escalar('SELECT COALESCE(AVG(calificacion),0) FROM evaluaciones_tutoria'),
        ];
    }

    public function resumenTutor(int $idTutor): array
    {
        return [
            'materias' => (int)$this->escalar('SELECT COUNT(*) FROM tutor_materia WHERE id_tutor=:id', [':id'=>$idTutor]),
            'dias' => (int)$this->escalar('SELECT COUNT(DISTINCT dia_semana) FROM disponibilidad_tutor WHERE id_tutor=:id', [':id'=>$idTutor]),
            'pendientes' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_tutor=:id AND estado='pendiente'", [':id'=>$idTutor]),
            'confirmadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_tutor=:id AND estado='confirmada'", [':id'=>$idTutor]),
            'realizadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_tutor=:id AND estado='realizada'", [':id'=>$idTutor]),
            'promedio' => (float)$this->escalar('SELECT COALESCE(AVG(ev.calificacion),0) FROM evaluaciones_tutoria ev INNER JOIN tutorias tu ON tu.id_tutoria=ev.id_tutoria WHERE tu.id_tutor=:id', [':id'=>$idTutor]),
        ];
    }

    public function resumenEstudiante(int $idEstudiante): array
    {
        return [
            'pendientes' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_estudiante=:id AND estado='pendiente'", [':id'=>$idEstudiante]),
            'confirmadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_estudiante=:id AND estado='confirmada'", [':id'=>$idEstudiante]),
            'realizadas' => (int)$this->escalar("SELECT COUNT(*) FROM tutorias WHERE id_estudiante=:id AND estado='realizada'", [':id'=>$idEstudiante]),
            'evaluadas' => (int)$this->escalar('SELECT COUNT(*) FROM evaluaciones_tutoria ev INNER JOIN tutorias tu ON tu.id_tutoria=ev.id_tutoria WHERE tu.id_estudiante=:id', [':id'=>$idEstudiante]),
        ];
    }

    public function proximasTutoriasTutor(int $idTutor): array
    {
        $stmt=$this->pdo->prepare("SELECT t.*, CONCAT(u.nombre,' ',u.apellido) estudiante, m.nombre_materia FROM tutorias t INNER JOIN estudiantes e ON e.id_estudiante=t.id_estudiante INNER JOIN usuarios u ON u.id_usuario=e.id_usuario INNER JOIN materias m ON m.id_materia=t.id_materia WHERE t.id_tutor=:id AND t.estado IN ('pendiente','confirmada') AND TIMESTAMP(t.fecha,t.hora_inicio) >= NOW() ORDER BY t.fecha,t.hora_inicio LIMIT 5");
        $stmt->execute([':id'=>$idTutor]);
        return $stmt->fetchAll();
    }

    public function proximasTutoriasEstudiante(int $idEstudiante): array
    {
        $stmt=$this->pdo->prepare("SELECT t.*, CONCAT(u.nombre,' ',u.apellido) tutor, m.nombre_materia FROM tutorias t INNER JOIN tutores tr ON tr.id_tutor=t.id_tutor INNER JOIN usuarios u ON u.id_usuario=tr.id_usuario INNER JOIN materias m ON m.id_materia=t.id_materia WHERE t.id_estudiante=:id AND t.estado IN ('pendiente','confirmada') AND TIMESTAMP(t.fecha,t.hora_inicio) >= NOW() ORDER BY t.fecha,t.hora_inicio LIMIT 5");
        $stmt->execute([':id'=>$idEstudiante]);
        return $stmt->fetchAll();
    }
}
