<?php
require_once __DIR__.'/../layouts/header.php';
require_once __DIR__.'/../../includes/funciones.php';
mostrarFlash();
$edit=isset($actual);

// Generamos opciones cada 15 minutos para que el tutor seleccione el horario
// con un desplegable sencillo, evitando introducir manualmente horas y segundos.
$opcionesHora=[];
for($min=0;$min<24*60;$min+=15){
    $hora=sprintf('%02d:%02d', intdiv($min,60), $min%60);
    $opcionesHora[]=$hora;
}
$horaInicioSeleccionada=substr((string)($datos['hora_inicio']??''),0,5);
$horaFinSeleccionada=substr((string)($datos['hora_fin']??''),0,5);

// Si un horario existente usa minutos fuera del intervalo de 15 minutos,
// conservamos su valor para que pueda editarse sin modificarlo accidentalmente.
foreach([$horaInicioSeleccionada,$horaFinSeleccionada] as $horaActual){
    if($horaActual!==''&&!in_array($horaActual,$opcionesHora,true)){
        $opcionesHora[]=$horaActual;
    }
}
sort($opcionesHora, SORT_STRING);
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between mb-3">
            <h2 class="fw-bold"><?= $edit?'Editar horario':'Nuevo horario' ?></h2>
            <a href="disponibilidad_listar.php" class="btn btn-outline-secondary">Volver</a>
        </div>

        <?php if($errores): ?>
            <div class="alert alert-danger">
                <?php foreach($errores as $x): ?>
                    <div><?=e($x)?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card card-custom p-4">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i>
                Solo el tutor propietario puede crear, modificar o eliminar sus horarios.
            </div>

            <form method="POST">
                <?=csrfField()?>
                <input type="hidden" name="id_disponibilidad" value="<?=e($actual['id_disponibilidad']??'')?>">

                <label class="form-label fw-semibold">Día *</label>
                <select name="dia_semana" class="form-select mb-3" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach(['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'] as $d): ?>
                        <option value="<?=$d?>" <?=($datos['dia_semana']??'')===$d?'selected':''?>><?=$d?></option>
                    <?php endforeach; ?>
                </select>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="hora_inicio">Inicio *</label>
                        <select id="hora_inicio" name="hora_inicio" class="form-select" required>
                            <option value="">Seleccionar hora...</option>
                            <?php foreach($opcionesHora as $hora): ?>
                                <option value="<?=e($hora)?>" <?=$horaInicioSeleccionada===$hora?'selected':''?>><?=e($hora)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="hora_fin">Fin *</label>
                        <select id="hora_fin" name="hora_fin" class="form-select" required>
                            <option value="">Seleccionar hora...</option>
                            <?php foreach($opcionesHora as $hora): ?>
                                <option value="<?=e($hora)?>" <?=$horaFinSeleccionada===$hora?'selected':''?>><?=e($hora)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-text mt-2">Selecciona las horas en intervalos de 15 minutos.</div>

                <button class="btn btn-primary w-100 mt-4">Guardar horario</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__.'/../layouts/footer.php'; ?>
