<?php

class RegistroAccesoModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // Guardamos cada intento de inicio de sesión
    public function registrar(
        ?int $idUsuario,
        string $resultado,
        string $ipOrigen
    ): bool {
        $sql = "
            INSERT INTO registro_accesos (
                id_usuario,
                ip_origen,
                resultado
            )
            VALUES (
                :id_usuario,
                :ip_origen,
                :resultado
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_usuario' => $idUsuario,
            'ip_origen' => $ipOrigen,
            'resultado' => $resultado
        ]);
    }
}