 <?php
/**
 * Listar Disponibilidad de Tutores — Alias exactos
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$consulta = "SELECT d.*,
                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_tutor,
                    d.dia_semana AS dia,
                    d.hora_inicio,
                    d.hora_fin,
                    'disponible' AS estado
             FROM disponibilidad_tutor d
             LEFT JOIN tutores t ON d.id_tutor = t.id_tutor
             LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
             ORDER BY d.id_disponibilidad DESC";
$disponibilidad = $conexion->prepare($consulta);
$disponibilidad->execute();

require_once __DIR__ . '/../views/disponibilidad/listar.php';