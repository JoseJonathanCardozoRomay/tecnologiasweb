<?php

class DefensaOperacionModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Marca una defensa programada como realizada o cancelada.
     */
    public function cambiarEstado(
        int $idDefensa,
        string $nuevoEstado
    ): bool {
        if (
            !in_array(
                $nuevoEstado,
                ['realizada', 'cancelada'],
                true
            )
        ) {
            return false;
        }

        try {
            $this->conexion->beginTransaction();

            $sqlBuscar = "
                SELECT
                    fecha,
                    estado,
                    defensa_vigente
                FROM defensas_mg
                WHERE id_defensa = :id_defensa
                LIMIT 1
                FOR UPDATE
            ";

            $consultaBuscar = $this->conexion->prepare(
                $sqlBuscar
            );

            $consultaBuscar->execute([
                'id_defensa' => $idDefensa
            ]);

            $defensa = $consultaBuscar->fetch(
                PDO::FETCH_ASSOC
            );

            if (
                !$defensa
                || $defensa['estado'] !== 'programada'
                || (int) $defensa['defensa_vigente'] !== 1
            ) {
                $this->conexion->rollBack();
                return false;
            }

            // Una defensa futura no puede marcarse como realizada.
            if (
                $nuevoEstado === 'realizada'
                && $defensa['fecha'] > date('Y-m-d')
            ) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlActualizar = "
                UPDATE defensas_mg
                SET
                    estado = :estado,
                    defensa_vigente = :defensa_vigente
                WHERE id_defensa = :id_defensa
            ";

            $consultaActualizar = $this->conexion->prepare(
                $sqlActualizar
            );

            $consultaActualizar->execute([
                'estado' => $nuevoEstado,
                'defensa_vigente' => $nuevoEstado === 'cancelada'
                    ? null
                    : 1,
                'id_defensa' => $idDefensa
            ]);

            $this->conexion->commit();
            return true;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    /**
     * Conserva la defensa anterior y crea una nueva programación.
     */
    public function reprogramar(
        int $idDefensa,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        string $ambiente,
        string $motivoCambio,
        ?string $autorizadoPor,
        ?string $referenciaAutorizacion,
        int $registradoPor
    ): int {
        try {
            $this->conexion->beginTransaction();

            $sqlBuscar = "
                SELECT
                    id_expediente,
                    etapa,
                    estado,
                    defensa_vigente
                FROM defensas_mg
                WHERE id_defensa = :id_defensa
                LIMIT 1
                FOR UPDATE
            ";

            $consultaBuscar = $this->conexion->prepare(
                $sqlBuscar
            );

            $consultaBuscar->execute([
                'id_defensa' => $idDefensa
            ]);

            $defensaAnterior = $consultaBuscar->fetch(
                PDO::FETCH_ASSOC
            );

            if (
                !$defensaAnterior
                || $defensaAnterior['estado'] !== 'programada'
                || (int) $defensaAnterior['defensa_vigente'] !== 1
            ) {
                $this->conexion->rollBack();
                return 0;
            }

            $sqlCerrar = "
                UPDATE defensas_mg
                SET
                    estado = 'reprogramada',
                    motivo_cambio = :motivo_cambio,
                    defensa_vigente = NULL
                WHERE id_defensa = :id_defensa
            ";

            $consultaCerrar = $this->conexion->prepare(
                $sqlCerrar
            );

            $consultaCerrar->execute([
                'motivo_cambio' => trim($motivoCambio),
                'id_defensa' => $idDefensa
            ]);

            $sqlCrear = "
                INSERT INTO defensas_mg (
                    id_expediente,
                    etapa,
                    fecha,
                    hora_inicio,
                    hora_fin,
                    ambiente,
                    autorizado_por,
                    referencia_autorizacion,
                    registrado_por
                )
                VALUES (
                    :id_expediente,
                    :etapa,
                    :fecha,
                    :hora_inicio,
                    :hora_fin,
                    :ambiente,
                    :autorizado_por,
                    :referencia_autorizacion,
                    :registrado_por
                )
            ";

            $consultaCrear = $this->conexion->prepare(
                $sqlCrear
            );

            $consultaCrear->execute([
                'id_expediente'
                    => $defensaAnterior['id_expediente'],
                'etapa' => $defensaAnterior['etapa'],
                'fecha' => $fecha,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'ambiente' => trim($ambiente),
                'autorizado_por' => $autorizadoPor,
                'referencia_autorizacion'
                    => $referenciaAutorizacion,
                'registrado_por' => $registradoPor
            ]);

            $idNuevaDefensa = (int) $this->conexion
                ->lastInsertId();

            $this->conexion->commit();

            return $idNuevaDefensa;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }
}