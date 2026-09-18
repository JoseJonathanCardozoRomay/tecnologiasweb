<?php
class EstudianteModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT e.id_estudiante, e.id_usuario, e.id_carrera, e.semestre, e.registro_universitario,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado,
                       c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutorias t WHERE t.id_estudiante = e.id_estudiante) AS total_tutorias
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                ORDER BY u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id_estudiante)
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.correo, u.telefono, u.usuario, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_estudiante = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_estudiante]);
        return $stmt->fetch();
    }

    public function obtenerPorUsuario($id_usuario)
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.correo, u.telefono, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetch();
    }

    public function guardarOActualizar($id_usuario, $id_carrera, $semestre, $registro_universitario)
    {
        $sql = "INSERT INTO estudiantes (id_usuario, id_carrera, semestre, registro_universitario)
                VALUES (:id_usuario, :id_carrera, :semestre, :ru)
                ON DUPLICATE KEY UPDATE id_carrera = VALUES(id_carrera), semestre = VALUES(semestre), registro_universitario = VALUES(registro_universitario)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_carrera' => $id_carrera,
            ':semestre'   => $semestre,
            ':ru'         => trim($registro_universitario)
        ]);
    }
}
