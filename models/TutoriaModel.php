<?php
// models/TutoriaModel.php
require_once __DIR__ . '/../config/conexion.php';

class TutoriaModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function obtenerTodas() {
        $stmt = $this->pdo->query("SELECT * FROM tutorias");
        return $stmt->fetchAll();
    }

    public function crear($estudiante_id, $tutor_id, $materia, $fecha) {
        $stmt = $this->pdo->prepare("INSERT INTO tutorias (estudiante_id, tutor_id, materia, fecha, estado) VALUES (?, ?, ?, ?, 'pendiente')");
        return $stmt->execute([$estudiante_id, $tutor_id, $materia, $fecha]);
    }
}
