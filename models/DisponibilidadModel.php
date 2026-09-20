<?php
declare(strict_types=1);

/**
 * Modelo de disponibilidad semanal de los tutores.
 */
class DisponibilidadModel
{
    public function __construct(private PDO $pdo) {}

    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT d.*,u.nombre,u.apellido,u.estado AS estado_usuario
             FROM disponibilidad_tutor d
             INNER JOIN tutores t ON t.id_tutor=d.id_tutor
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             ORDER BY u.apellido,u.nombre,
                      FIELD(d.dia_semana,'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'),
                      d.hora_inicio"
        )->fetchAll();
    }

    /**
     * Lista tutores con la cantidad de bloques registrados.
     * Se usa en la vista administrativa para evitar mostrar todos los horarios de golpe.
     */
    public function resumenTutores(): array
    {
        return $this->pdo->query(
            "SELECT t.id_tutor,u.nombre,u.apellido,u.usuario,u.estado,
                    COUNT(d.id_disponibilidad) AS total_horarios
             FROM tutores t
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             LEFT JOIN disponibilidad_tutor d ON d.id_tutor=t.id_tutor
             GROUP BY t.id_tutor,u.nombre,u.apellido,u.usuario,u.estado
             ORDER BY u.apellido,u.nombre"
        )->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $stmt=$this->pdo->prepare('SELECT * FROM disponibilidad_tutor WHERE id_disponibilidad=:id LIMIT 1');
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }

    public function porTutor(int $id): array
    {
        $stmt=$this->pdo->prepare(
            "SELECT d.*,u.nombre,u.apellido,u.usuario,u.estado AS estado_usuario
             FROM disponibilidad_tutor d
             INNER JOIN tutores t ON t.id_tutor=d.id_tutor
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             WHERE d.id_tutor=:id
             ORDER BY FIELD(d.dia_semana,'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'),d.hora_inicio"
        );
        $stmt->execute([':id'=>$id]);
        return $stmt->fetchAll();
    }

    public function tutorActivo(int $idTutor): bool
    {
        $stmt=$this->pdo->prepare(
            "SELECT COUNT(*) FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             WHERE t.id_tutor=:id AND u.estado='activo'"
        );
        $stmt->execute([':id'=>$idTutor]);
        return (int)$stmt->fetchColumn()>0;
    }

    public function crear(array $d): bool
    {
        return $this->pdo->prepare(
            'INSERT INTO disponibilidad_tutor(id_tutor,dia_semana,hora_inicio,hora_fin) VALUES(:t,:d,:hi,:hf)'
        )->execute([':t'=>$d['id_tutor'],':d'=>$d['dia_semana'],':hi'=>$d['hora_inicio'],':hf'=>$d['hora_fin']]);
    }

    public function actualizar(int $id,array $d): bool
    {
        return $this->pdo->prepare(
            'UPDATE disponibilidad_tutor SET id_tutor=:t,dia_semana=:d,hora_inicio=:hi,hora_fin=:hf WHERE id_disponibilidad=:id'
        )->execute([':t'=>$d['id_tutor'],':d'=>$d['dia_semana'],':hi'=>$d['hora_inicio'],':hf'=>$d['hora_fin'],':id'=>$id]);
    }

    public function eliminar(int $id): bool
    {
        return $this->pdo->prepare('DELETE FROM disponibilidad_tutor WHERE id_disponibilidad=:id')->execute([':id'=>$id]);
    }

    public function existeCruce(array $d,?int $excepto=null): bool
    {
        $sql='SELECT COUNT(*) FROM disponibilidad_tutor WHERE id_tutor=:t AND dia_semana=:d AND hora_inicio<:hf AND hora_fin>:hi';
        $p=[':t'=>$d['id_tutor'],':d'=>$d['dia_semana'],':hi'=>$d['hora_inicio'],':hf'=>$d['hora_fin']];
        if($excepto!==null){$sql.=' AND id_disponibilidad<>:id';$p[':id']=$excepto;}
        $stmt=$this->pdo->prepare($sql);$stmt->execute($p);
        return (int)$stmt->fetchColumn()>0;
    }
}
