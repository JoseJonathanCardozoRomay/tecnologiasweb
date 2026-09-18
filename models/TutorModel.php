<?php
class TutorModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT t.id_tutor, t.id_usuario, t.especialidad, t.biografia,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_tutor = t.id_tutor) AS total_materias,
                       (SELECT COUNT(*) FROM disponibilidad_tutor dt WHERE dt.id_tutor = t.id_tutor) AS total_horarios
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id_tutor)
    {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_tutor = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_tutor]);
        return $stmt->fetch();
    }

    public function obtenerPorUsuario($id_usuario)
    {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.correo, u.telefono
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetch();
    }

    public function actualizarPerfil($id_tutor, $especialidad, $biografia)
    {
        $stmt = $this->pdo->prepare("UPDATE tutores SET especialidad = :esp, biografia = :bio WHERE id_tutor = :id");
        return $stmt->execute([
            ':esp' => trim($especialidad),
            ':bio' => trim($biografia),
            ':id'  => $id_tutor
        ]);
    }

    // Materias que domina el tutor
    public function obtenerMaterias($id_tutor)
    {
        $sql = "SELECT m.id_materia, m.nombre_materia, c.nombre_carrera
                FROM materias m
                INNER JOIN tutor_materia tm ON m.id_materia = tm.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                WHERE tm.id_tutor = :id_tutor
                ORDER BY m.nombre_materia ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetchAll();
    }

    public function asignarMaterias($id_tutor, $materias_ids = [])
    {
        $this->pdo->prepare("DELETE FROM tutor_materia WHERE id_tutor = :id_tutor")->execute([':id_tutor' => $id_tutor]);
        if (!empty($materias_ids)) {
            $stmt = $this->pdo->prepare("INSERT INTO tutor_materia (id_tutor, id_materia) VALUES (:id_tutor, :id_materia)");
            foreach ($materias_ids as $id_materia) {
                $stmt->execute([
                    ':id_tutor'   => $id_tutor,
                    ':id_materia' => $id_materia
                ]);
            }
        }
        return true;
    }

    // Tutores disponibles para una materia específica (para agendar tutorías)
    public function obtenerTutoresPorMateria($id_materia)
    {
        $sql = "SELECT t.id_tutor, u.nombre, u.apellido, t.especialidad
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                INNER JOIN tutor_materia tm ON t.id_tutor = tm.id_tutor
                WHERE tm.id_materia = :id_materia AND u.estado = 'activo'
                ORDER BY u.nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_materia' => $id_materia]);
        return $stmt->fetchAll();
    }

    // Horarios de disponibilidad
    public function obtenerDisponibilidad($id_tutor)
    {
        $sql = "SELECT * FROM disponibilidad_tutor WHERE id_tutor = :id_tutor ORDER BY 
                FIELD(dia_semana, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'), hora_inicio ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetchAll();
    }

    public function agregarDisponibilidad($id_tutor, $dia_semana, $hora_inicio, $hora_fin)
    {
        $stmt = $this->pdo->prepare("INSERT INTO disponibilidad_tutor (id_tutor, dia_semana, hora_inicio, hora_fin) 
                                     VALUES (:id_tutor, :dia, :inicio, :fin)");
        return $stmt->execute([
            ':id_tutor' => $id_tutor,
            ':dia'      => $dia_semana,
            ':inicio'   => $hora_inicio,
            ':fin'      => $hora_fin
        ]);
    }

    public function eliminarDisponibilidad($id_disp, $id_tutor)
    {
        $stmt = $this->pdo->prepare("DELETE FROM disponibilidad_tutor WHERE id_disponibilidad = :id AND id_tutor = :id_tutor");
        return $stmt->execute([
            ':id'       => $id_disp,
            ':id_tutor' => $id_tutor
        ]);
    }
}
