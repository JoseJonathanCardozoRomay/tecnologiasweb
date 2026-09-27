<?php

function notificacion_roles_destino(string $script)
{
    $mapa = [
        'accesos_listar.php'            => ['administrador'],
        'bitacora_listar.php'           => ['administrador', 'auxiliar'],
        'carreras_crear.php'            => ['administrador'],
        'carreras_editar.php'           => ['administrador'],
        'carreras_eliminar.php'         => ['administrador'],
        'carreras_listar.php'           => ['administrador'],
        'cartas_responder.php'          => ['tutor'],
        'expediente_documentos.php'     => ['administrador', 'auxiliar'],
        'historial_listar.php'          => ['administrador'],
        'informes_registrar.php'        => ['tutor'],
        'materias_crear.php'            => ['administrador'],
        'materias_editar.php'           => ['administrador'],
        'materias_eliminar.php'         => ['administrador'],
        'materias_listar.php'           => ['administrador'],
        'mg_alertas.php'                => ['administrador'],
        'mg_alertas_atender.php'        => ['administrador'],
        'mg_calendario_crear.php'       => ['administrador', 'auxiliar'],
        'mg_calendario_editar.php'      => ['administrador', 'auxiliar'],
        'mg_calendario_eliminar.php'    => ['administrador', 'auxiliar'],
        'mg_calendario_listar.php'      => ['administrador', 'auxiliar'],
        'mg_carta_tutor.php'            => ['administrador', 'auxiliar'],
        'mg_citacion_defensa.php'       => ['administrador', 'auxiliar'],
        'mg_cohortes_crear.php'         => ['administrador', 'auxiliar'],
        'mg_cohortes_editar.php'        => ['administrador', 'auxiliar'],
        'mg_cohortes_eliminar.php'      => ['administrador', 'auxiliar'],
        'mg_cohortes_listar.php'        => ['administrador', 'auxiliar'],
        'mg_comprobantes_listar.php'    => ['administrador', 'auxiliar'],
        'mg_comprobantes_registrar.php' => ['estudiante'],
        'mg_defensa_evaluar.php'        => ['administrador', 'auxiliar'],
        'mg_defensas_eliminar.php'      => ['administrador', 'auxiliar'],
        'mg_defensas_listar.php'        => ['administrador', 'auxiliar'],
        'mg_defensas_programar.php'     => ['administrador', 'auxiliar'],
        'mg_expediente.php'             => ['administrador', 'auxiliar'],
        'mg_expediente_asignar.php'     => ['administrador', 'auxiliar'],
        'mg_expediente_estado.php'      => ['administrador', 'auxiliar'],
        'mg_expediente_finalizar_asignacion.php' => ['administrador', 'auxiliar'],
        'mg_expedientes_crear.php'      => ['administrador', 'auxiliar'],
        'mg_expedientes_listar.php'     => ['administrador', 'auxiliar'],
        'mg_modalidades_crear.php'      => ['administrador', 'auxiliar'],
        'mg_modalidades_editar.php'     => ['administrador', 'auxiliar'],
        'mg_modalidades_eliminar.php'   => ['administrador', 'auxiliar'],
        'mg_modalidades_listar.php'     => ['administrador', 'auxiliar'],
        'mg_padron_importar.php'        => ['administrador', 'auxiliar'],
        'mg_parametros_guardar.php'     => ['administrador'],
        'mg_parametros_listar.php'      => ['administrador'],
        'mg_reportes_cohorte.php'       => ['administrador', 'auxiliar'],
        'reuniones_registrar.php'       => ['tutor'],
        'solicitudes_tutor_listar.php'  => ['administrador', 'auxiliar'],
        'solicitudes_tutor_responder.php' => ['administrador', 'auxiliar'],
        'tribunales_asignar.php'        => ['administrador', 'auxiliar'],
        'tutores_crear.php'             => ['administrador'],
        'tutores_disponibilidad.php'    => ['administrador', 'auxiliar', 'tutor'],
        'tutores_listar.php'            => ['administrador', 'auxiliar'],
        'tutorias_asignar.php'          => ['administrador', 'auxiliar'],
        'tutorias_listar.php'           => ['administrador', 'auxiliar'],
        'tutorias_solicitar.php'        => ['estudiante'],
        'tutorias_unirse.php'           => ['estudiante'],
        'usuarios_crear.php'            => ['administrador', 'auxiliar'],
        'usuarios_editar.php'           => ['administrador', 'auxiliar'],
        'usuarios_eliminar.php'         => ['administrador'],
        'usuarios_listar.php'           => ['administrador', 'auxiliar'],
        'panel.php'                     => ['administrador', 'auxiliar', 'tutor', 'estudiante'],
        'historial.php'                 => ['estudiante'],
        'mg_portal.php'                 => ['estudiante'],
        'modalidad_grado.php'           => ['estudiante'],
    ];

    return $mapa[$script] ?? [];
}

function notificacion_enlace_seguro($enlace, $rol)
{
    $enlace = trim((string) $enlace);
    if ($enlace === '' || preg_match('#^([a-z][a-z0-9+.-]*:)?//#i', $enlace)) {
        return null;
    }

    $partes = parse_url($enlace);
    $ruta = $partes['path'] ?? '';
    $script = basename($ruta);
    if ($script === '' || substr($script, -4) !== '.php') {
        return null;
    }

    $permitidos = notificacion_roles_destino($script);
    if ($permitidos !== [] && !in_array((string) $rol, $permitidos, true)) {
        return null;
    }

    if (strpos($ruta, '/') !== 0) {
        $ruta = '/controllers/' . $script;
    }
    if ($script === 'tutores_disponibilidad.php' && !in_array((string) $rol, ['administrador', 'auxiliar'], true)) {
        $ruta = '/controllers/tutores_disponibilidad.php';
        $partes['query'] = '';
    }

    return $ruta . (isset($partes['query']) && $partes['query'] !== '' ? '?' . $partes['query'] : '');
}

function notificacion_panel_por_defecto($rol)
{
    switch ((string) $rol) {
        case 'estudiante':
            return '/views/estudiante/panel.php';
        case 'tutor':
            return '/views/tutor/panel.php';
        default:
            return '/controllers/usuarios_listar.php';
    }
}
