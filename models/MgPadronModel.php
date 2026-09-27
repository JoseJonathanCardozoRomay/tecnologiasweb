<?php
class MgPadronModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function carreras(): array
    {
        $stmt = $this->pdo->query("SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera");
        return $stmt->fetchAll();
    }

    public function resolverCarreraId($ref, array $carreras): ?int
    {
        $ref = trim((string) $ref);
        if ($ref === '') {
            return null;
        }
        if (ctype_digit($ref)) {
            return (int) $ref;
        }
        $busqueda = mb_strtolower($ref);
        foreach ($carreras as $c) {
            if (mb_strtolower($c['nombre_carrera']) === $busqueda) {
                return (int) $c['id_carrera'];
            }
        }

        return null;
    }

    private function existeExpediente($idEstudiante, $idCohorte): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM expedientes_mg WHERE id_estudiante = :est AND id_cohorte_mg = :coh"
        );
        $stmt->execute([':est' => (int) $idEstudiante, ':coh' => (int) $idCohorte]);

        return $stmt->fetchColumn() > 0;
    }

    private function correoOcupado($correo, $excluirUsuario = 0): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM usuarios WHERE correo = :correo AND id_usuario <> :usuario"
        );
        $stmt->execute([':correo' => $correo, ':usuario' => (int) $excluirUsuario]);

        return $stmt->fetchColumn() > 0;
    }

    private function generarLogin($ru): string
    {
        $digitos = preg_replace('/\D+/', '', (string) $ru);
        $base = 'est' . substr($digitos, -6);
        $login = $base;
        $contador = 2;
        while ($this->loginOcupado($login)) {
            $login = $base . $contador;
            $contador++;
        }

        return $login;
    }

    private function loginOcupado($login): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $login]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Importa una tanda de filas del padrón a la cohorte.
     * Devuelve resumen con contadores y errores por fila.
     */
    public function importarFilas(array $filas, int $idCohorte, int $idUsuario): array
    {
        $resumen = ['creados' => 0, 'actualizados' => 0, 'expedientes' => 0, 'errores' => []];
        $carreras = $this->carreras();
        $idModalidad = $this->modalidadPerfilDefault();

        foreach ($filas as $nro => $n) {
            $fila = $this->normalizarFila($n);
            $ru = $fila['registro_universitario'];
            $nombre = $fila['nombre'];
            $apellidos = trim($fila['paterno'] . ' ' . $fila['materno']);
            $correo = strtolower($fila['correo']);
            $idCarrera = $fila['id_carrera'];
            $semestre = $fila['semestre'];
            $materias = $fila['materias'];

            if ($ru === '') {
                $resumen['errores'][] = "Fila {$nro}: falta el registro universitario.";
                continue;
            }
            if ($nombre === '') {
                $resumen['errores'][] = "Fila {$nro} ({$ru}): falta el/los nombre(s).";
                continue;
            }
            if ($correo === '') {
                $resumen['errores'][] = "Fila {$nro} ({$ru}): falta el correo.";
                continue;
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $resumen['errores'][] = "Fila {$nro} ({$ru}): el correo '{$correo}' no es válido.";
                continue;
            }
            $idCarrera = $this->resolverCarreraId($idCarrera, $carreras);
            if ($idCarrera === null) {
                $resumen['errores'][] = "Fila {$nro} ({$ru}): la carrera indicada no existe.";
                continue;
            }

            $estudiante = $this->buscarPorRegistro($ru);
            if ($estudiante) {
                $this->actualizarEstudiante($estudiante['id_estudiante'], $idCarrera, $semestre,
                    $materias, $idUsuario, $nombre, $apellidos, $correo, $estudiante['id_usuario']);
                $idEstudiante = (int) $estudiante['id_estudiante'];
                $resumen['actualizados']++;
            } else {
                if ($this->correoOcupado($correo)) {
                    $resumen['errores'][] = "Fila {$nro} ({$ru}): el correo '{$correo}' ya está en uso.";
                    continue;
                }
                $idEstudiante = $this->crearEstudiante($ru, $nombre, $apellidos, $correo,
                    $idCarrera, $semestre, $materias, $idUsuario);
                $resumen['creados']++;
            }

            if (!$this->existeExpediente($idEstudiante, $idCohorte)) {
                $this->crearExpediente($idEstudiante, $idModalidad, $idCohorte);
                $resumen['expedientes']++;
            }
        }

        return $resumen;
    }

    private function normalizarFila(array $fila): array
    {
        return [
            'registro_universitario' => trim($fila['registro_universitario'] ?? ''),
            'nombre' => trim($fila['nombre'] ?? ''),
            'paterno' => trim($fila['paterno'] ?? ''),
            'materno' => trim($fila['materno'] ?? ''),
            'correo' => trim($fila['correo'] ?? ''),
            'id_carrera' => trim($fila['carrera'] ?? ''),
            'semestre' => max(0, (int) ($fila['semestre'] ?? 0)),
            'materias' => max(0, (int) ($fila['materias'] ?? 0)),
        ];
    }

    private function modalidadPerfilDefault(): int
    {
        $stmt = $this->pdo->query(
            "SELECT id_modalidad_grado FROM modalidades_grado WHERE flujo = 'perfil_mg' AND activa = 1 ORDER BY id_modalidad_grado LIMIT 1"
        );
        $id = (int) $stmt->fetchColumn();

        return $id > 0 ? $id : 1;
    }

    private function buscarPorRegistro($ru)
    {
        $stmt = $this->pdo->prepare(
            "SELECT e.*, e.id_usuario FROM estudiantes e WHERE e.registro_universitario = :ru"
        );
        $stmt->execute([':ru' => $ru]);

        return $stmt->fetch();
    }

    private function crearEstudiante($ru, $nombre, $apellidos, $correo, $idCarrera, $semestre, $materias, $idUsuario): int
    {
        $login = $this->generarLogin($ru);
        $hash = password_hash('Control123+', PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO usuarios (id_rol, nombre, apellido, correo, usuario, contrasena_hash)
             VALUES (3, :nombre, :apellido, :correo, :usuario, :hash)"
        );
        $stmt->execute([':nombre' => $nombre, ':apellido' => $apellidos, ':correo' => $correo,
                        ':usuario' => $login, ':hash' => $hash]);
        $idUsuarioNuevo = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare(
            "INSERT INTO estudiantes (id_usuario, id_carrera, semestre, materias_completadas,
                                      acceso_mg_desbloqueado, mg_desbloqueado_por, mg_desbloqueado_fecha,
                                      registro_universitario)
             VALUES (:usuario, :carrera, :semestre, :materias, 1, :por, NOW(), :ru)"
        );
        $stmt->execute([':usuario' => $idUsuarioNuevo, ':carrera' => (int) $idCarrera,
                        ':semestre' => (int) $semestre, ':materias' => (int) $materias,
                        ':por' => (int) $idUsuario, ':ru' => $ru]);

        return (int) $this->pdo->lastInsertId();
    }

    private function actualizarEstudiante($idEstudiante, $idCarrera, $semestre, $materias, $idUsuario, $nombre, $apellidos, $correo, $idUsuarioEstud)
    {
        if (!$this->correoOcupado($correo, $idUsuarioEstud)) {
            $stmt = $this->pdo->prepare(
                "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo WHERE id_usuario = :id"
            );
            $stmt->execute([':nombre' => $nombre, ':apellido' => $apellidos, ':correo' => $correo,
                            ':id' => (int) $idUsuarioEstud]);
        }
        $stmt = $this->pdo->prepare(
            "UPDATE estudiantes SET id_carrera = :carrera, semestre = :semestre,
                   materias_completadas = :materias, acceso_mg_desbloqueado = 1,
                   mg_desbloqueado_por = :por, mg_desbloqueado_fecha = NOW()
             WHERE id_estudiante = :id"
        );
        $stmt->execute([':carrera' => (int) $idCarrera, ':semestre' => (int) $semestre,
                        ':materias' => (int) $materias, ':por' => (int) $idUsuario,
                        ':id' => (int) $idEstudiante]);
    }

    private function crearExpediente($idEstudiante, $idModalidad, $idCohorte)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO expedientes_mg (id_estudiante, id_modalidad_grado, id_cohorte_mg, estado, solicitud_detalle)
             VALUES (:est, :mod, :coh, 'solicitado', 'Inscrito mediante importacion de padron.')"
        );
        $stmt->execute([':est' => (int) $idEstudiante, ':mod' => (int) $idModalidad,
                        ':coh' => (int) $idCohorte]);
    }
}