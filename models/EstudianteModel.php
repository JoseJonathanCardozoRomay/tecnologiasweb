<?php
require_once __DIR__ . '/../config/conexion.php';

class EstudianteModel {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                ORDER BY u.nombre, u.apellido";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ NUEVA FUNCIÓN: Obtener id_estudiante desde id_usuario
    public function obtenerPorUsuario($id_usuario) {
        $consulta = "SELECT id_estudiante FROM estudiantes WHERE id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['id_estudiante'] : null;
    }

    public function guardarEstudiante($datos) {
        try {
            $this->conexion->beginTransaction();

            // 1. Obtener rol de estudiante
            $stmt = $this->conexion->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'estudiante' LIMIT 1");
            $stmt->execute();
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_rol = $rol['id_rol'] ?? 3;

            // 2. Generar usuario único
            $usuario = strtolower($datos['nombre'] . '_' . $datos['apellido']);
            $usuario = preg_replace('/[^a-z0-9_]/', '', $usuario);
            $baseUsuario = $usuario;
            $n = 1;
            while (true) {
                $chk = $this->conexion->prepare("SELECT 1 FROM usuarios WHERE usuario = :u LIMIT 1");
                $chk->bindParam(':u', $usuario);
                $chk->execute();
                if (!$chk->fetch()) break;
                $usuario = $baseUsuario . $n;
                $n++;
            }
            $correo = $usuario . '@correo.com';
            $clave = password_hash('123456', PASSWORD_DEFAULT);

            // 3. Crear usuario
            $stmt = $this->conexion->prepare("INSERT INTO usuarios (id_rol, nombre, apellido, telefono, usuario, correo, contrasena_hash)
                VALUES (:idr, :nom, :ape, :tel, :usu, :cor, :pwd)");
            $stmt->bindParam(':idr', $id_rol);
            $stmt->bindParam(':nom', $datos['nombre']);
            $stmt->bindParam(':ape', $datos['apellido']);
            $stmt->bindParam(':tel', $datos['telefono']);
            $stmt->bindParam(':usu', $usuario);
            $stmt->bindParam(':cor', $correo);
            $stmt->bindParam(':pwd', $clave);
            $stmt->execute();
            $id_usuario = $this->conexion->lastInsertId();

            // 4. Registro único — SIN error si no existe la columna
            $registro = $datos['registro_universitario'];
            $baseReg = $registro;
            $m = 1;
            
            // Verificar si la columna existe antes de buscar duplicados
            $colExiste = $this->conexion->query("SHOW COLUMNS FROM estudiantes LIKE 'registro_universitario'")->fetch();
            if ($colExiste) {
                while (true) {
                    $chk = $this->conexion->prepare("SELECT 1 FROM estudiantes WHERE registro_universitario = :r LIMIT 1");
                    $chk->bindParam(':r', $registro);
                    $chk->execute();
                    if (!$chk->fetch()) break;
                    $registro = $baseReg . '_' . $m;
                    $m++;
                }
            }

            // 5. Crear estudiante
            if ($colExiste) {
                $stmt = $this->conexion->prepare("INSERT INTO estudiantes (id_usuario, id_carrera, semestre, registro_universitario)
                    VALUES (:idu, :idc, :sem, :reg)");
                $stmt->bindParam(':reg', $registro);
            } else {
                $stmt = $this->conexion->prepare("INSERT INTO estudiantes (id_usuario, id_carrera, semestre)
                    VALUES (:idu, :idc, :sem)");
            }
            
            $stmt->bindParam(':idu', $id_usuario);
            $stmt->bindParam(':idc', $datos['id_carrera']);
            $stmt->bindParam(':sem', $datos['semestre']);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            return 'Error: ' . $e->getMessage();
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_estudiante = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $datos) {
        try {
            $this->conexion->beginTransaction();
            $est = $this->obtenerPorId($id);
            $id_usuario = $est['id_usuario'];

            $stmt = $this->conexion->prepare("UPDATE usuarios SET nombre=:nom, apellido=:ape, telefono=:tel WHERE id_usuario=:idu");
            $stmt->bindParam(':nom', $datos['nombre']);
            $stmt->bindParam(':ape', $datos['apellido']);
            $stmt->bindParam(':tel', $datos['telefono']);
            $stmt->bindParam(':idu', $id_usuario);
            $stmt->execute();

            $colExiste = $this->conexion->query("SHOW COLUMNS FROM estudiantes LIKE 'registro_universitario'")->fetch();
            if ($colExiste) {
                $stmt = $this->conexion->prepare("UPDATE estudiantes SET id_carrera=:idc, semestre=:sem, registro_universitario=:reg WHERE id_estudiante=:id");
                $stmt->bindParam(':reg', $datos['registro_universitario']);
            } else {
                $stmt = $this->conexion->prepare("UPDATE estudiantes SET id_carrera=:idc, semestre=:sem WHERE id_estudiante=:id");
            }
            $stmt->bindParam(':idc', $datos['id_carrera']);
            $stmt->bindParam(':sem', $datos['semestre']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            return 'Error: ' . $e->getMessage();
        }
    }

    public function eliminar($id) {
        $est = $this->obtenerPorId($id);
        if ($est) {
            $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id_usuario=:idu");
            $stmt->bindParam(':idu', $est['id_usuario']);
            return $stmt->execute();
        }
        return false;
    }
}