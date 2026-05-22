<?php

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/validators/Validator.php';
require_once BASE_PATH . '/helpers/sanitize.php';

class TecnicoController extends Controller
{
    public function show(Request $request): void
    {
        $id = (int) $request->param('id', 0);

        $tecnico = User::getTechnicianPublicProfile($id);

        if (!$tecnico) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $categorias     = User::getTechnicianCategoriasConNombres($id);
        $fotos          = User::getFotosTrabajoPorUserId($id);
        $resenas        = User::getResenasByTecnico($id);
        $avgRating      = User::getAvgRating($id);
        $serviciosCount = User::countCompletedServices($id);

        $unreviewedSolicitud = false;
        $yaCalificado        = false;
        $miResena            = null;

        $selfId = (int) ($_SESSION['user_id'] ?? 0);
        if ($selfId > 0 && ($_SESSION['role'] ?? '') !== 'admin' && $selfId !== $id) {
            $unreviewedSolicitud = User::getUnreviewedSolicitudForClient($selfId, $id);
            if ($unreviewedSolicitud === false) {
                $yaCalificado = User::hasCompletedServiceWith($selfId, $id);
                if ($yaCalificado) {
                    $miResena = User::getResenaByClient($selfId, $id);
                }
            }
        }

        $this->render('tecnico/show', [
            'pageTitle'           => htmlspecialchars($tecnico['nombre']) . ' — TuTecnico',
            'tecnico'             => $tecnico,
            'categorias'          => $categorias,
            'fotos'               => $fotos,
            'resenas'             => $resenas,
            'avgRating'           => $avgRating,
            'serviciosCount'      => $serviciosCount,
            'unreviewedSolicitud' => $unreviewedSolicitud,
            'yaCalificado'        => $yaCalificado,
            'miResena'            => $miResena,
        ]);
    }

    public function storeResena(Request $request): void
    {
        $id   = (int) $request->param('id', 0);
        $data = sanitize_array($_POST);

        Validator::validate($data, [
            'puntuacion'   => 'required|integer',
            'id_solicitud' => 'required|integer',
        ]);

        $solicitudId = (int) $data['id_solicitud'];
        $puntuacion  = (int) $data['puntuacion'];
        $comentario  = isset($data['comentario']) ? trim($data['comentario']) : null;

        if ($puntuacion < 1 || $puntuacion > 5) {
            $_SESSION['errors'] = ['puntuacion' => 'La puntuación debe ser entre 1 y 5.'];
            header('Location: /tecnico/' . $id . '#resenas');
            exit;
        }

        if (!User::verifySolicitudOwnership($solicitudId, (int) $_SESSION['user_id'], $id)) {
            $_SESSION['errors'] = ['general' => 'No puedes calificar esta solicitud.'];
            header('Location: /tecnico/' . $id . '#resenas');
            exit;
        }

        User::createResena($solicitudId, $puntuacion, $comentario ?: null);

        $_SESSION['success'] = '¡Reseña enviada con éxito!';
        header('Location: /tecnico/' . $id . '#resenas');
        exit;
    }

    public function editResena(Request $request): void
    {
        $id   = (int) $request->param('id', 0);
        $data = sanitize_array($_POST);

        Validator::validate($data, [
            'puntuacion'      => 'required|integer',
            'calificacion_id' => 'required|integer',
        ]);

        $calificacionId = (int) $data['calificacion_id'];
        $puntuacion     = (int) $data['puntuacion'];
        $comentario     = trim($data['comentario'] ?? '');

        if ($puntuacion < 1 || $puntuacion > 5) {
            $_SESSION['errors'] = ['puntuacion' => 'La puntuación debe ser entre 1 y 5.'];
            header('Location: /tecnico/' . $id . '#resenas');
            exit;
        }

        User::updateResena($calificacionId, (int) $_SESSION['user_id'], $id, $puntuacion, $comentario ?: null);

        $_SESSION['success'] = '¡Reseña actualizada con éxito!';
        header('Location: /tecnico/' . $id . '#resenas');
        exit;
    }

    public function deleteResena(Request $request): void
    {
        $id             = (int) $request->param('id', 0);
        $data           = sanitize_array($_POST);
        $calificacionId = (int) ($data['calificacion_id'] ?? 0);

        if ($calificacionId > 0) {
            User::deleteResena($calificacionId, (int) $_SESSION['user_id'], $id);
            $_SESSION['success'] = 'Reseña eliminada.';
        }

        header('Location: /tecnico/' . $id . '#resenas');
        exit;
    }
}
