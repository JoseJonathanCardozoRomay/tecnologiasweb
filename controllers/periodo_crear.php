 <?php
require_once __DIR__ . '/../models/PeriodoTutoriaModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = new PeriodoTutoriaModel();
    $modelo->crear(
        $_POST['codigo'],
        $_POST['nombre'],
        $_POST['fecha_inicio'],
        $_POST['fecha_fin'],
        $_POST['activo'],
        !empty($_POST['creado_por']) ? $_POST['creado_por'] : null
    );
    header('Location: index.php?accion=periodos_listar');
    exit;
}

require_once __DIR__ . '/../views/periodos/crear.php';