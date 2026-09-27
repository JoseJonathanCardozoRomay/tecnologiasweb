<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ComprobanteModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/vista_helpers.php';

$comprobanteModel = new ComprobanteModel($pdo);
$estudianteModel = new EstudianteModel($pdo);
$errores = [];

$esAdministrador = (($_SESSION['rol'] ?? '') === 'administrador');
$pideJson = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
    || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

function responderJson($payload, $codigo = 200)
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_token_valido()) {
        if ($pideJson) {
            responderJson(['success' => false, 'message' => 'La sesión expiró. Recarga la página e intenta nuevamente.'], 403);
        }
        csrf_validar();
    }
    csrf_validar();

    $accion = $_POST['accion'] ?? '';

    if (!$esAdministrador) {
        $errores[] = 'Solo el administrador puede aprobar o rechazar comprobantes y modificar el acceso a Modalidad de Grado.';
        if ($pideJson) {
            responderJson(['success' => false, 'message' => $errores[0]], 403);
        }
    } elseif ($accion === 'validar') {
        $id_comprobante = (int) ($_POST['id_comprobante'] ?? 0);
        $nuevo_estado = (string) ($_POST['estado'] ?? '');
        $motivo = trim((string) ($_POST['motivo'] ?? ''));

        if ($id_comprobante <= 0) {
            $mensajeError = 'El comprobante solicitado no es válido.';
            if ($pideJson) {
                responderJson(['success' => false, 'message' => $mensajeError], 400);
            }
            $errores[] = $mensajeError;
        } else {
            $comprobante = $comprobanteModel->obtenerPorId($id_comprobante);
            if (!$comprobante) {
                $mensajeError = 'El comprobante no existe.';
                if ($pideJson) {
                    responderJson(['success' => false, 'message' => $mensajeError], 404);
                }
                $errores[] = $mensajeError;
            } elseif (($comprobante['estado'] ?? '') !== 'pendiente') {
                $mensajeError = 'Este comprobante ya fue validado anteriormente.';
                if ($pideJson) {
                    responderJson(['success' => false, 'message' => $mensajeError], 409);
                }
                $errores[] = $mensajeError;
            } else {
                try {
                    $comprobanteModel->validar($id_comprobante, $nuevo_estado, (int) $_SESSION['id_usuario'], $motivo ?: null);
                    $notifs = new NotificationModel($pdo);
                    $destEstudiante = $notifs->idUsuarioDeEstudiante((int) $comprobante['id_estudiante']);

                    if ($nuevo_estado === 'aprobado') {
                        $desbloqueado = $estudianteModel->desbloquearMG(
                            (int) $comprobante['id_estudiante'],
                            (int) $_SESSION['id_usuario']
                        );
                        $historial = new HistorialModel($pdo);
                        $historial->registrar(
                            (int) ($_SESSION['id_usuario'] ?? 0),
                            'COMPROBANTE_MG_APROBADO',
                            'Comprobante #' . $id_comprobante . ' aprobado y acceso a Modalidad de Grado ' . ($desbloqueado ? 'habilitado.' : 'pendiente de verificación de requisitos.')
                        );
                        if ($destEstudiante > 0) {
                            $notifs->crear($destEstudiante, 'COMPROBANTE_MG_APROBADO', 'Tu comprobante #' . $id_comprobante . ' fue aprobado y quedó habilitada tu Modalidad de Grado.', 'mg_comprobantes_registrar.php', (int) $_SESSION['id_usuario']);
                        }
                        $mensajeOk = 'Comprobante aprobado. Acceso a Modalidad de Grado habilitado.';
                    } else {
                        $historial = new HistorialModel($pdo);
                        $historial->registrar(
                            (int) ($_SESSION['id_usuario'] ?? 0),
                            'COMPROBANTE_MG_RECHAZADO',
                            'Comprobante #' . $id_comprobante . ' rechazado: ' . $motivo
                        );
                        if ($destEstudiante > 0) {
                            $notifs->crear($destEstudiante, 'COMPROBANTE_MG_RECHAZADO', 'Tu comprobante #' . $id_comprobante . ' fue rechazado: ' . mb_substr($motivo, 0, 200), 'mg_comprobantes_registrar.php', (int) $_SESSION['id_usuario']);
                        }
                        $mensajeOk = 'Comprobante rechazado correctamente.';
                    }

                    $comprobanteActualizado = $comprobanteModel->obtenerPorId($id_comprobante) ?: $comprobante;
                    $pendientesRestantes = (int) $comprobanteModel->contarPendientes();

                    if ($pideJson) {
                        responderJson([
                            'success' => true,
                            'message' => $mensajeOk,
                            'comprobante' => [
                                'id' => (int) $id_comprobante,
                                'estado' => (string) $comprobanteActualizado['estado'],
                                'badge' => estado_badge($comprobanteActualizado['estado']),
                                'motivo' => $comprobanteActualizado['motivo_rechazo'] ?: '',
                                'validador' => trim(($comprobanteActualizado['validador_nombre'] ?? '') . ' ' . ($comprobanteActualizado['validador_apellido'] ?? '')),
                            ],
                            'pendientes' => $pendientesRestantes,
                        ]);
                    }

                    flash_set('success', $mensajeOk);
                    header('Location: mg_comprobantes_listar.php');
                    exit;
                } catch (InvalidArgumentException $e) {
                    if ($pideJson) {
                        responderJson(['success' => false, 'message' => $e->getMessage()], 422);
                    }
                    $errores[] = $e->getMessage();
                } catch (Throwable $e) {
                    error_log('Error al validar comprobante #' . $id_comprobante . ': ' . $e->getMessage());
                    $mensajeError = 'No se pudo registrar la validación del comprobante. Intenta nuevamente.';
                    if ($pideJson) {
                        responderJson(['success' => false, 'message' => $mensajeError], 500);
                    }
                    $errores[] = $mensajeError;
                }
            }
        }
    } elseif ($accion === 'inhabilitar_mg') {
        $id_estudiante = (int) ($_POST['id_estudiante'] ?? 0);
        if ($estudianteModel->obtenerPorId($id_estudiante)) {
            try {
                $estudianteModel->bloquearMG($id_estudiante);
                $historial = new HistorialModel($pdo);
                $historial->registrar(
                    (int) ($_SESSION['id_usuario'] ?? 0),
                    'MG_DESHABILITADA',
                    'Se deshabilitó el acceso a Modalidad de Grado del estudiante #' . $id_estudiante
                );
                flash_set('success', 'Acceso a Modalidad de Grado deshabilitado.');
            } catch (Throwable $e) {
                error_log('Error al deshabilitar MG del estudiante #' . $id_estudiante . ': ' . $e->getMessage());
                flash_set('danger', 'No se pudo deshabilitar el acceso a Modalidad de Grado.');
            }
        } else {
            flash_set('danger', 'El estudiante no existe.');
        }
        header('Location: mg_comprobantes_listar.php');
        exit;
    }
}

$comprobantes = $comprobanteModel->obtenerTodos();
$pendientes = (int) $comprobanteModel->contarPendientes();

// SPRINT 6: búsqueda por teclado + filtro de estado + paginación dinámicas.
$p = parametrosListado();
$comprobantes = filtrarRegistros($comprobantes, $p['q'], ['nombre', 'apellido', 'usuario', 'correo']);
$estadoMG = $p['estado'];
$estadosMG = ComprobanteModel::estados();
if ($estadoMG !== '' && in_array($estadoMG, $estadosMG, true)) {
    $comprobantes = array_values(array_filter($comprobantes, fn($c) => ($c['estado'] ?? '') === $estadoMG));
}
[$comprobantes, $pag] = paginarRegistros($comprobantes, $p['pagina'], $p['por_pagina']);
$totalRegistros = (int) $pag['total'];
$q = $p['q'];

require_once __DIR__ . '/../views/admin/mg_comprobantes.php';