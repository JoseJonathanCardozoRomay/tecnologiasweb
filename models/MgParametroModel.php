 <?php
/**
 * Modelo — Parámetros del Sistema MG
 * Tabla: parametros_mg
 */
require_once __DIR__ . '/../config/conexion.php';

class MgParametroModel {
    private $conexion;
    private $tabla = 'parametros_mg'; // ✅ NOMBRE CORRECTO
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY clave";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizar($clave, $valor, $id_usuario = null) {
        $sql = "UPDATE {$this->tabla} 
                SET valor = :valor, fecha_actualizacion = NOW(), actualizado_por = :usuario 
                WHERE clave = :clave";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':usuario', $id_usuario);
        $stmt->bindParam(':clave', $clave);
        return $stmt->execute();
    }
}