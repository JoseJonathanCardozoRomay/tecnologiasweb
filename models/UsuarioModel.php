<?php
class UsuarioModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function obtenerTodos(): array {
        $sql = "SELECT u.id_usuario,u.id_rol,u.nombre,u.apellido,u.correo,u.usuario,
                       u.telefono,u.estado,u.fecha_registro,r.nombre_rol
                FROM usuarios u INNER JOIN roles r ON r.id_rol=u.id_rol
                ORDER BY u.id_usuario DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    public function obtenerPorId(int $id): array|false {
        $s=$this->pdo->prepare("SELECT u.*,r.nombre_rol FROM usuarios u JOIN roles r ON r.id_rol=u.id_rol WHERE u.id_usuario=:id");
        $s->execute([':id'=>$id]); return $s->fetch();
    }
    public function obtenerPorUsuario(string $usuario): array|false {
        // Con PDO::ATTR_EMULATE_PREPARES=false no se puede reutilizar
        // el mismo parámetro nombrado más de una vez en la consulta.
        $s=$this->pdo->prepare("SELECT u.*,r.nombre_rol FROM usuarios u JOIN roles r ON r.id_rol=u.id_rol
            WHERE u.usuario=:usuario OR u.correo=:correo LIMIT 1");
        $s->execute([':usuario'=>$usuario, ':correo'=>$usuario]);
        return $s->fetch();
    }
    public function existeCorreo(string $correo, ?int $excepto=null): bool {
        $sql="SELECT COUNT(*) FROM usuarios WHERE correo=:correo".($excepto?" AND id_usuario<>:id":"");
        $p=[':correo'=>$correo]; if($excepto)$p[':id']=$excepto;
        $s=$this->pdo->prepare($sql);$s->execute($p);return (int)$s->fetchColumn()>0;
    }
    public function existeUsuario(string $usuario, ?int $excepto=null): bool {
        $sql="SELECT COUNT(*) FROM usuarios WHERE usuario=:usuario".($excepto?" AND id_usuario<>:id":"");
        $p=[':usuario'=>$usuario]; if($excepto)$p[':id']=$excepto;
        $s=$this->pdo->prepare($sql);$s->execute($p);return (int)$s->fetchColumn()>0;
    }
    public function crear(array $d): bool {
        $s=$this->pdo->prepare("INSERT INTO usuarios(id_rol,nombre,apellido,correo,usuario,contrasena_hash,telefono,estado)
          VALUES(:rol,:nombre,:apellido,:correo,:usuario,:hash,:telefono,:estado)");
        return $s->execute([
          ':rol'=>$d['id_rol'],':nombre'=>$d['nombre'],':apellido'=>$d['apellido'],':correo'=>$d['correo'],
          ':usuario'=>$d['usuario'],':hash'=>password_hash($d['clave'],PASSWORD_DEFAULT),
          ':telefono'=>$d['telefono']??null,':estado'=>$d['estado']??'activo'
        ]);
    }
    public function actualizar(int $id,array $d): bool {
        $sql="UPDATE usuarios SET id_rol=:rol,nombre=:nombre,apellido=:apellido,correo=:correo,usuario=:usuario,
              telefono=:telefono,estado=:estado".(!empty($d['clave'])?",contrasena_hash=:hash":"")." WHERE id_usuario=:id";
        $p=[':rol'=>$d['id_rol'],':nombre'=>$d['nombre'],':apellido'=>$d['apellido'],':correo'=>$d['correo'],
            ':usuario'=>$d['usuario'],':telefono'=>$d['telefono']??null,':estado'=>$d['estado'],':id'=>$id];
        if(!empty($d['clave']))$p[':hash']=password_hash($d['clave'],PASSWORD_DEFAULT);
        return $this->pdo->prepare($sql)->execute($p);
    }

    public function actualizarPerfil(int $id, array $d): bool
    {
        $sql='UPDATE usuarios SET nombre=:nombre,apellido=:apellido,correo=:correo,telefono=:telefono'.(!empty($d['clave'])?',contrasena_hash=:hash':'').' WHERE id_usuario=:id';
        $p=[':nombre'=>$d['nombre'],':apellido'=>$d['apellido'],':correo'=>$d['correo'],':telefono'=>$d['telefono']?:null,':id'=>$id];
        if(!empty($d['clave']))$p[':hash']=password_hash($d['clave'],PASSWORD_DEFAULT);
        return $this->pdo->prepare($sql)->execute($p);
    }


    public function eliminar(int $id): bool {
        return $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario=:id")->execute([':id'=>$id]);
    }
}
