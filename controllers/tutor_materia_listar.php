<?php
require_once __DIR__ . '/../models/TutorMateriaModel.php';

$modelo = new TutorMateriaModel();
$relaciones = $modelo->listarTodos();

require_once __DIR__ . '/../views/tutor_materia/listar.php';