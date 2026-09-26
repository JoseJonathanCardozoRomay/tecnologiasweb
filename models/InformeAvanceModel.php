<?php
require_once __DIR__ . '/../config/conexion.php';
class InformeAvanceModel {
    private $conexion;
    private $tabla = 'informes_avance';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $rol = $_SESSION['rol_nombre'] ?? '';
        $id_usuario = $_SESSION['id_usuario'] ?? 0;
        
        $sql = "SELECT i.*, 
                    u1.nombre AS nombre_crea, u1.apellido AS apellido_crea,
                    u2.nombre AS nombre_reviza, u2.apellido AS apellido_reviza
                FROM {$this->tabla} i
                LEFT JOIN usuarios u1 ON i.id_usuario_crea = u1.id_usuario
                LEFT JOIN usuarios u2 ON i.id_usuario_reviza = u2.id_usuario";
        
        if ($rol === 'estudiante') {
            $sql .= " INNER JOIN tutorias t ON i.id_tutoria = t.id_tutoria
                      INNER JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                      WHERE e.id_usuario = :id_usuario";
        } elseif ($rol === 'tutor') {
            $sql .= " INNER JOIN tutorias t ON i.id_tutoria = t.id_tutoria
                      WHERE t.id_tutor = :id_usuario";
        }
        
        $sql .= " ORDER BY i.fecha_registro DESC";
        $stmt = $this->conexion->prepare($sql);
        
        if ($rol === 'estudiante' || $rol === 'tutor') {
            $stmt->bindParam(':id_usuario', $id_usuario);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT i.*, 
                    u1.nombre AS nombre_crea, u1.apellido AS apellido_crea,
                    u2.nombre AS nombre_reviza, u2.apellido AS apellido_reviza
                FROM {$this->tabla} i
                LEFT JOIN usuarios u1 ON i.id_usuario_crea = u1.id_usuario
                LEFT JOIN usuarios u2 ON i.id_usuario_reviza = u2.id_usuario
                WHERE i.id_informe = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} 
                (id_tutoria, titulo, descripcion, progreso_porcentaje, id_usuario_crea)
                VALUES (:id_tutoria, :titulo, :descripcion, :progreso_porcentaje, :id_usuario_crea)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_tutoria' => $datos['id_tutoria'],
            ':titulo' => $datos['titulo'],
            ':descripcion' => $datos['descripcion'],
            ':progreso_porcentaje' => $datos['progreso_porcentaje'],
            ':id_usuario_crea' => $datos['id_usuario_crea']
        ]);
        return $this->conexion->lastInsertId();
    }

    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET
                titulo = :titulo,
                descripcion = :descripcion,
                progreso_porcentaje = :progreso_porcentaje,
                estado = :estado
                WHERE id_informe = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':titulo' => $datos['titulo'],
            ':descripcion' => $datos['descripcion'],
            ':progreso_porcentaje' => $datos['progreso_porcentaje'],
            ':estado' => $datos['estado']
        ]);
    }

    public function enviar($id, $id_usuario) {
        $sql = "UPDATE {$this->tabla} SET estado = 'enviado', fecha_envio = NOW() WHERE id_informe = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function revisar($id, $id_usuario_reviza, $estado, $observaciones) {
        $sql = "UPDATE {$this->tabla} SET
                estado = :estado,
                id_usuario_reviza = :id_usuario_reviza,
                observaciones_revision = :observaciones,
                fecha_revision = NOW()
                WHERE id_informe = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':estado' => $estado,
            ':id_usuario_reviza' => $id_usuario_reviza,
            ':observaciones' => $observaciones
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_informe = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}