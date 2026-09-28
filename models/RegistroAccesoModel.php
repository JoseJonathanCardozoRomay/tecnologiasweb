<?php

class RegistroAccesoModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Guarda cada intento de inicio de sesión.
     */
    public function registrar(
        ?int $idUsuario,
        string $resultado,
        string $ipOrigen
    ): bool {
        if (
            !in_array(
                $resultado,
                ['exitoso', 'fallido'],
                true
            )
        ) {
            return false;
        }

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
            'ip_origen' => mb_substr(
                $ipOrigen,
                0,
                45
            ),
            'resultado' => $resultado
        ]);
    }

    /**
     * Cuenta los intentos fallidos recientes realizados desde una IP
     * o dirigidos hacia una misma cuenta.
     */
    public function contarFallidosRecientes(
        string $ipOrigen,
        ?int $idUsuario,
        int $minutos = 15
    ): int {
        $minutos = max(
            1,
            min($minutos, 60)
        );

        $sql = "
            SELECT COUNT(*)
            FROM registro_accesos
            WHERE resultado = 'fallido'
                AND fecha_hora >= DATE_SUB(
                    CURRENT_TIMESTAMP,
                    INTERVAL {$minutos} MINUTE
                )
                AND (
                    ip_origen = :ip_origen
        ";

        $parametros = [
            'ip_origen' => mb_substr(
                $ipOrigen,
                0,
                45
            )
        ];

        if ($idUsuario !== null) {
            $sql .= "
                    OR id_usuario = :id_usuario
            ";

            $parametros['id_usuario'] = $idUsuario;
        }

        $sql .= "
                )
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Determina si temporalmente deben rechazarse nuevos intentos.
     */
    public function estaBloqueado(
        string $ipOrigen,
        ?int $idUsuario,
        int $maximoIntentos = 5,
        int $minutos = 15
    ): bool {
        return $this->contarFallidosRecientes(
            $ipOrigen,
            $idUsuario,
            $minutos
        ) >= $maximoIntentos;
    }
}