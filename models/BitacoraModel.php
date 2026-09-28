<?php

class BitacoraModel
{
    private PDO $conexion;

    private const CAMPOS_SENSIBLES = [
        'contrasena',
        'contrasena_hash',
        'password',
        'token',
        'token_csrf',
        'firma_recepcion'
    ];

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Elimina datos sensibles antes de guardarlos.
     */
    private function limpiarDatos(
        ?array $datos
    ): ?array {
        if ($datos === null) {
            return null;
        }

        foreach ($datos as $clave => $valor) {
            if (
                in_array(
                    mb_strtolower((string) $clave),
                    self::CAMPOS_SENSIBLES,
                    true
                )
            ) {
                $datos[$clave] = '[PROTEGIDO]';
            } elseif (is_array($valor)) {
                $datos[$clave] = $this->limpiarDatos(
                    $valor
                );
            }
        }

        return $datos;
    }

    /**
     * Registra una operación sensible.
     */
    public function registrar(
        ?int $idUsuario,
        string $accion,
        string $tabla,
        ?int $idRegistro,
        ?array $datosAntes = null,
        ?array $datosDespues = null
    ): bool {
        $datosAntes = $this->limpiarDatos(
            $datosAntes
        );

        $datosDespues = $this->limpiarDatos(
            $datosDespues
        );

        $direccionIp = $_SERVER['REMOTE_ADDR']
            ?? null;

        if (
            $direccionIp !== null
            && filter_var(
                $direccionIp,
                FILTER_VALIDATE_IP
            ) === false
        ) {
            $direccionIp = null;
        }

        $agenteUsuario = substr(
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            0,
            255
        );

        $sql = "
            INSERT INTO bitacora_mg (
                id_usuario,
                accion,
                tabla_afectada,
                id_registro,
                datos_antes,
                datos_despues,
                direccion_ip,
                agente_usuario
            )
            VALUES (
                :id_usuario,
                :accion,
                :tabla_afectada,
                :id_registro,
                :datos_antes,
                :datos_despues,
                :direccion_ip,
                :agente_usuario
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_usuario' => $idUsuario,
            'accion' => substr(
                trim($accion),
                0,
                60
            ),
            'tabla_afectada' => substr(
                trim($tabla),
                0,
                80
            ),
            'id_registro' => $idRegistro,
            'datos_antes' => $datosAntes !== null
                ? json_encode(
                    $datosAntes,
                    JSON_UNESCAPED_UNICODE
                    | JSON_THROW_ON_ERROR
                )
                : null,
            'datos_despues' => $datosDespues !== null
                ? json_encode(
                    $datosDespues,
                    JSON_UNESCAPED_UNICODE
                    | JSON_THROW_ON_ERROR
                )
                : null,
            'direccion_ip' => $direccionIp,
            'agente_usuario' => $agenteUsuario !== ''
                ? $agenteUsuario
                : null
        ]);
    }

    /**
     * Lista la bitácora con filtros opcionales.
     */
    public function listar(
        string $accion = '',
        string $tabla = '',
        ?int $idUsuario = null,
        string $fechaDesde = '',
        string $fechaHasta = ''
    ): array {
        $sql = "
            SELECT
                b.id_bitacora,
                b.id_usuario,
                b.accion,
                b.tabla_afectada,
                b.id_registro,
                b.datos_antes,
                b.datos_despues,
                b.direccion_ip,
                b.fecha_registro,

                u.nombre,
                u.apellido

            FROM bitacora_mg AS b

            LEFT JOIN usuarios AS u
                ON u.id_usuario = b.id_usuario

            WHERE 1 = 1
        ";

        $parametros = [];

        if ($accion !== '') {
            $sql .= "
                AND b.accion = :accion
            ";

            $parametros['accion'] = $accion;
        }

        if ($tabla !== '') {
            $sql .= "
                AND b.tabla_afectada = :tabla
            ";

            $parametros['tabla'] = $tabla;
        }

        if ($idUsuario !== null) {
            $sql .= "
                AND b.id_usuario = :id_usuario
            ";

            $parametros['id_usuario'] = $idUsuario;
        }

        if ($fechaDesde !== '') {
            $sql .= "
                AND DATE(b.fecha_registro) >= :fecha_desde
            ";

            $parametros['fecha_desde'] = $fechaDesde;
        }

        if ($fechaHasta !== '') {
            $sql .= "
                AND DATE(b.fecha_registro) <= :fecha_hasta
            ";

            $parametros['fecha_hasta'] = $fechaHasta;
        }

        $sql .= "
            ORDER BY b.fecha_registro DESC
            LIMIT 500
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Devuelve valores disponibles para los filtros.
     */
    public function obtenerFiltros(): array
    {
        $acciones = $this->conexion->query(
            "
                SELECT DISTINCT accion
                FROM bitacora_mg
                ORDER BY accion
            "
        )->fetchAll(PDO::FETCH_COLUMN);

        $tablas = $this->conexion->query(
            "
                SELECT DISTINCT tabla_afectada
                FROM bitacora_mg
                ORDER BY tabla_afectada
            "
        )->fetchAll(PDO::FETCH_COLUMN);

        $usuarios = $this->conexion->query(
            "
                SELECT
                    id_usuario,
                    nombre,
                    apellido
                FROM usuarios
                ORDER BY nombre, apellido
            "
        )->fetchAll(PDO::FETCH_ASSOC);

        return [
            'acciones' => $acciones,
            'tablas' => $tablas,
            'usuarios' => $usuarios
        ];
    }
}