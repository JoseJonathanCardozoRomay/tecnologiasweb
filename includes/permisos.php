<?php

require_once __DIR__ . '/sesion.php';

/**
 * Permisos disponibles para cada rol.
 */
function obtenerPermisosPorRol(): array
{
    return [
        'coordinador_mg' => [
            'mg.parametros.ver',
            'mg.parametros.editar',

            'mg.modalidades.ver',
            'mg.modalidades.editar',

            'mg.cohortes.ver',
            'mg.cohortes.crear',
            'mg.cohortes.editar',

            'mg.calendario.ver',
            'mg.calendario.crear',
            'mg.calendario.editar',

            'mg.importaciones.ver',
            'mg.importaciones.crear',

            'mg.expedientes.ver',
            'mg.expedientes.crear',
            'mg.expedientes.editar',
            'mg.expedientes.cambiar_etapa',

            'mg.tutores.ver',
            'mg.tutores.asignar',
            'mg.tutores.cambiar',

            'mg.tribunales.ver',
            'mg.tribunales.asignar',
            'mg.tribunales.cambiar',

            'mg.defensas.ver',
            'mg.defensas.programar',
            'mg.defensas.reprogramar',

            'mg.calificaciones.ver',
            'mg.calificaciones.editar',
            'mg.calificaciones.publicar',

            'mg.reuniones.ver',
            'mg.reuniones.validar',

            'mg.informes.ver',
            'mg.informes.crear',
            'mg.informes.editar',

            'mg.documentos.ver',
            'mg.documentos.generar',
            'mg.documentos.plantillas',

            'mg.reportes.ver',
            'mg.reportes.exportar',

            'mg.alertas.ver',
            'mg.alertas.atender',

            'mg.bitacora.ver'
        ],

        'auxiliar_mg' => [
            'mg.modalidades.ver',
            'mg.cohortes.ver',
            'mg.calendario.ver',

            'mg.importaciones.ver',
            'mg.importaciones.crear',

            'mg.expedientes.ver',
            'mg.expedientes.crear',
            'mg.expedientes.editar',

            'mg.tutores.ver',

            'mg.tribunales.ver',
            'mg.tribunales.asignar',
            'mg.tribunales.cambiar',

            'mg.defensas.ver',
            'mg.defensas.programar',
            'mg.defensas.reprogramar',

            'mg.calificaciones.ver',

            'mg.reuniones.ver',
            'mg.reuniones.validar',

            'mg.informes.ver',
            'mg.informes.crear',
            'mg.informes.editar',

            'mg.documentos.ver',
            'mg.documentos.generar',

            'mg.reportes.ver',
            'mg.reportes.exportar',

            'mg.alertas.ver',
            'mg.alertas.atender'
        ],

        'tutor' => [
            'mg.cohortes.ver',
            'mg.calendario.ver',

            'mg.expedientes.ver_propios',
            'mg.tutores.ver_asignacion_propia',

            'mg.tribunales.ver_propios',
            'mg.defensas.ver_propias',
            'mg.calificaciones.ver_propias',

            'mg.reuniones.ver_propias',
            'mg.reuniones.crear_propias',
            'mg.reuniones.editar_propias',

            'mg.informes.ver_propios',
            'mg.informes.crear_propios',
            'mg.informes.editar_propios',

            'mg.documentos.ver_propios'
        ],

        'estudiante' => [
            'mg.cohortes.ver',
            'mg.calendario.ver',

            'mg.expedientes.ver_propio',
            'mg.tutores.ver_asignacion_propia',

            'mg.tribunales.ver_propios',
            'mg.defensas.ver_propias',
            'mg.calificaciones.ver_publicadas',

            'mg.reuniones.ver_propias',
            'mg.informes.ver_propios',
            'mg.documentos.ver_propios'
        ]
    ];
}

/**
 * Comprueba si el usuario autenticado tiene un permiso.
 */
function usuarioTienePermiso(string $permiso): bool
{
    $usuario = obtenerUsuarioSesion();

    if (!$usuario) {
        return false;
    }

    $rol = $usuario['rol'] ?? '';

    // El administrador conserva acceso completo
    if ($rol === 'administrador') {
        return true;
    }

    $permisosPorRol = obtenerPermisosPorRol();
    $permisosActuales = $permisosPorRol[$rol] ?? [];

    return in_array(
        $permiso,
        $permisosActuales,
        true
    );
}

/**
 * Restringe el acceso cuando falta un permiso.
 */
function requerirPermiso(
    string $permiso,
    string $rutaSegura = '../index.php'
): void {
    requerirSesion();

    if (!usuarioTienePermiso($permiso)) {
        header(
            'Location: '
            . $rutaSegura
            . '?estado=acceso_denegado'
        );
        exit;
    }
}