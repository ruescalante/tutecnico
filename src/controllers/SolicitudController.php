<?php

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Solicitud.php';
require_once BASE_PATH . '/models/Cotizacion.php';
require_once BASE_PATH . '/models/Notificacion.php';
require_once BASE_PATH . '/models/Mensaje.php';
require_once BASE_PATH . '/validators/Validator.php';
require_once BASE_PATH . '/helpers/sanitize.php';

class SolicitudController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // CLIENTE — Crear solicitud
    // ──────────────────────────────────────────────────────────

    public function create(Request $request): void
    {
        $tecnicoId = (int) $request->param('id');
        $tecnico   = User::getTechnicianPublicProfile($tecnicoId);

        if (!$tecnico) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Técnico no encontrado']);
            return;
        }

        $categorias = User::getTechnicianCategoriasConNombres($tecnicoId);

        $this->render('solicitudes/create', [
            'pageTitle'  => 'Solicitar Servicio — TuTécnico',
            'tecnico'    => $tecnico,
            'categorias' => $categorias,
            'old'        => $_SESSION['old'] ?? [],
            'errors'     => $_SESSION['errors'] ?? [],
        ]);
    }

    public function store(Request $request): void
    {
        $input = sanitize_array($_POST);

        $backUrl   = $input['_back_url'] ?? '/';
        $tecnicoId = (int) ($input['id_tecnico'] ?? 0);

        try {
            Validator::validate($input, [
                'titulo'      => 'required|max:200',
                'descripcion' => 'required',
                'direccion'   => 'required|max:300',
                'id_tecnico'  => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            $_SESSION['errors'] = $e->getErrors();
            $_SESSION['old']    = $input;
            header('Location: ' . $backUrl);
            exit;
        }

        $tecnico = User::getTechnicianPublicProfile($tecnicoId);
        if (!$tecnico) {
            $_SESSION['errors'] = ['general' => ['El técnico seleccionado no está disponible.']];
            header('Location: ' . $backUrl);
            exit;
        }

        $clienteId = (int) $_SESSION['user_id'];
        $newId = Solicitud::create(
            $clienteId,
            $tecnicoId,
            $input['titulo'],
            $input['descripcion'],
            $input['direccion']
        );

        Notificacion::create($tecnicoId, 'solicitud_nueva', $newId);

        $_SESSION['success'] = 'Solicitud enviada correctamente. El técnico revisará tu solicitud pronto.';
        header('Location: /solicitudes/' . $newId);
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // CLIENTE — Ver detalle de solicitud
    // ──────────────────────────────────────────────────────────

    public function show(Request $request): void
    {
        $id        = (int) $request->param('id');
        $userId    = (int) $_SESSION['user_id'];
        $userRole  = $_SESSION['role'] ?? '';

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Solicitud no encontrada']);
            return;
        }

        $esCliente = ($userRole === 'cliente' && (int) $solicitud['id_cliente'] === $userId);
        $esAdmin   = ($userRole === 'admin');

        if (!$esCliente && !$esAdmin) {
            $_SESSION['errors'] = ['general' => ['No tienes acceso a esta solicitud.']];
            header('Location: /dashboard/cliente');
            exit;
        }

        $cotizacion = Cotizacion::findBySolicitud($id);

        $tecnicoId          = (int) ($solicitud['id_tecnico'] ?? 0);
        $tecnicoAvgRating   = $tecnicoId > 0 ? User::getAvgRating($tecnicoId) : 0;
        $tecnicoResenas     = $tecnicoId > 0 ? User::getResenasByTecnico($tecnicoId) : [];
        $tecnicoCompletados = $tecnicoId > 0 ? User::countCompletedServices($tecnicoId) : 0;

        $mensajes = Mensaje::findBySolicitud($id);
        Mensaje::markReadForUser($id, 'cliente');

        $this->render('solicitudes/show', [
            'pageTitle'           => 'Solicitud #' . $id . ' — TuTécnico',
            'solicitud'           => $solicitud,
            'cotizacion'          => $cotizacion ?: null,
            'tecnicoAvgRating'    => $tecnicoAvgRating,
            'tecnicoTotalResenas' => count($tecnicoResenas),
            'tecnicoCompletados'  => $tecnicoCompletados,
            'mensajes'            => $mensajes,
        ]);
    }

    public function cancel(Request $request): void
    {
        $id       = (int) $request->param('id');
        $userId   = (int) $_SESSION['user_id'];
        $userRole = $_SESSION['role'] ?? '';

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud) {
            header('Location: /dashboard/cliente');
            exit;
        }

        $esCliente  = ($userRole === 'cliente' && (int) $solicitud['id_cliente'] === $userId);
        $esTecnico  = ($userRole === 'tecnico' && (int) $solicitud['id_tecnico'] === $userId);

        if (!$esCliente && !$esTecnico) {
            header('Location: /dashboard');
            exit;
        }

        if ($solicitud['estado'] === 'completada') {
            $_SESSION['errors'] = ['general' => ['No se puede cancelar una solicitud ya completada.']];
            header('Location: /solicitudes/' . $id);
            exit;
        }

        $actor = $esCliente ? 'cliente' : 'tecnico';
        Solicitud::cancel($id, $actor);

        if ($esCliente && isset($solicitud['id_tecnico'])) {
            Notificacion::create((int) $solicitud['id_tecnico'], 'solicitud_cancelada', $id);
        } elseif ($esTecnico) {
            Notificacion::create((int) $solicitud['id_cliente'], 'solicitud_cancelada', $id);
        }

        $_SESSION['success'] = 'Solicitud cancelada.';
        header('Location: /solicitudes/' . $id);
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // CLIENTE — Responder a cotización
    // ──────────────────────────────────────────────────────────

    public function acceptQuote(Request $request): void
    {
        $id       = (int) $request->param('id');
        $userId   = (int) $_SESSION['user_id'];
        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_cliente'] !== $userId) {
            header('Location: /dashboard/cliente');
            exit;
        }

        if ($solicitud['estado'] !== 'aceptada') {
            header('Location: /solicitudes/' . $id);
            exit;
        }

        $cotizacion = Cotizacion::findBySolicitud($id);
        if (!$cotizacion) {
            header('Location: /solicitudes/' . $id);
            exit;
        }

        Cotizacion::updateEstado((int) $cotizacion['id'], 'aceptada');
        Solicitud::updateStatus($id, 'en_progreso');

        Notificacion::create((int) $solicitud['id_tecnico'], 'cotizacion_aceptada', $id);

        $_SESSION['success'] = '¡Cotización aceptada! El servicio está en progreso.';
        header('Location: /solicitudes/' . $id);
        exit;
    }

    public function rejectQuote(Request $request): void
    {
        $id       = (int) $request->param('id');
        $userId   = (int) $_SESSION['user_id'];
        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_cliente'] !== $userId) {
            header('Location: /dashboard/cliente');
            exit;
        }

        if ($solicitud['estado'] !== 'aceptada') {
            header('Location: /solicitudes/' . $id);
            exit;
        }

        $cotizacion = Cotizacion::findBySolicitud($id);
        if ($cotizacion) {
            Cotizacion::updateEstado((int) $cotizacion['id'], 'rechazada');
        }

        Solicitud::cancel($id, 'cliente');

        Notificacion::create((int) $solicitud['id_tecnico'], 'cotizacion_rechazada', $id);

        $_SESSION['success'] = 'Cotización rechazada. La solicitud ha sido cancelada.';
        header('Location: /solicitudes/' . $id);
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // TÉCNICO — Lista de solicitudes
    // ──────────────────────────────────────────────────────────

    public function techIndex(Request $request): void
    {
        $userId  = (int) $_SESSION['user_id'];
        $estado  = $_GET['estado'] ?? '';

        $estadosValidos = ['pendiente', 'aceptada', 'en_progreso', 'completada', 'cancelada'];
        if ($estado !== '' && !in_array($estado, $estadosValidos, true)) {
            $estado = '';
        }

        $solicitudes = Solicitud::findByTecnico($userId, $estado);

        $pendientes   = count(Solicitud::findByTecnico($userId, 'pendiente'));
        $activas      = count(Solicitud::findByTecnico($userId, 'aceptada'))
                      + count(Solicitud::findByTecnico($userId, 'en_progreso'));
        $completadas  = count(Solicitud::findByTecnico($userId, 'completada'));
        $ganancias    = Cotizacion::totalEarningsByTecnico($userId);

        $this->render('dashboard/technician/solicitudes/index', [
            'pageTitle'    => 'Mis Solicitudes — TuTécnico',
            'solicitudes'  => $solicitudes,
            'estadoFiltro' => $estado,
            'pendientes'   => $pendientes,
            'activas'      => $activas,
            'completadas'  => $completadas,
            'ganancias'    => $ganancias,
            'activeSection'=> 'solicitudes',
        ], 'technician');
    }

    public function techShow(Request $request): void
    {
        $id      = (int) $request->param('id');
        $userId  = (int) $_SESSION['user_id'];

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_tecnico'] !== $userId) {
            $_SESSION['errors'] = ['general' => ['No tienes acceso a esta solicitud.']];
            header('Location: /dashboard/tecnico/solicitudes');
            exit;
        }

        $cotizacion = Cotizacion::findBySolicitud($id);

        $mensajes = Mensaje::findBySolicitud($id);
        Mensaje::markReadForUser($id, 'tecnico');

        $this->render('dashboard/technician/solicitudes/show', [
            'pageTitle'    => 'Solicitud #' . $id . ' — Panel Técnico',
            'solicitud'    => $solicitud,
            'cotizacion'   => $cotizacion ?: null,
            'mensajes'     => $mensajes,
            'activeSection'=> 'solicitudes',
        ], 'technician');
    }

    // ──────────────────────────────────────────────────────────
    // TÉCNICO — Enviar cotización
    // ──────────────────────────────────────────────────────────

    public function sendQuote(Request $request): void
    {
        $id      = (int) $request->param('id');
        $userId  = (int) $_SESSION['user_id'];
        $input   = sanitize_array($_POST);

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_tecnico'] !== $userId) {
            header('Location: /dashboard/tecnico/solicitudes');
            exit;
        }

        if ($solicitud['estado'] !== 'pendiente') {
            $_SESSION['errors'] = ['general' => ['Esta solicitud ya no está pendiente.']];
            header('Location: /dashboard/tecnico/solicitudes/' . $id);
            exit;
        }

        try {
            Validator::validate($input, [
                'precio_estimado' => 'required',
                'descripcion'     => 'max:1000',
            ]);
        } catch (ValidationException $e) {
            $_SESSION['errors'] = $e->getErrors();
            $_SESSION['old']    = $input;
            header('Location: /dashboard/tecnico/solicitudes/' . $id);
            exit;
        }

        $precio = (float) str_replace(',', '.', $input['precio_estimado']);

        if ($precio <= 0) {
            $_SESSION['errors'] = ['precio_estimado' => ['El precio debe ser mayor a 0.']];
            $_SESSION['old']    = $input;
            header('Location: /dashboard/tecnico/solicitudes/' . $id);
            exit;
        }

        Cotizacion::create($id, $precio, $input['descripcion'] ?? null);
        Solicitud::updateStatus($id, 'aceptada');

        Notificacion::create((int) $solicitud['id_cliente'], 'cotizacion_nueva', $id);

        $_SESSION['success'] = 'Cotización enviada correctamente. El cliente recibirá tu propuesta.';
        header('Location: /dashboard/tecnico/solicitudes/' . $id);
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // TÉCNICO — Completar solicitud
    // ──────────────────────────────────────────────────────────

    public function complete(Request $request): void
    {
        $id     = (int) $request->param('id');
        $userId = (int) $_SESSION['user_id'];

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_tecnico'] !== $userId) {
            header('Location: /dashboard/tecnico/solicitudes');
            exit;
        }

        if ($solicitud['estado'] !== 'en_progreso') {
            $_SESSION['errors'] = ['general' => ['Solo puedes completar solicitudes en progreso.']];
            header('Location: /dashboard/tecnico/solicitudes/' . $id);
            exit;
        }

        Solicitud::updateStatus($id, 'completada');

        Notificacion::create((int) $solicitud['id_cliente'], 'solicitud_completada', $id);

        $_SESSION['success'] = '¡Servicio marcado como completado!';
        header('Location: /dashboard/tecnico/solicitudes/' . $id);
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // TÉCNICO — Rechazar solicitud
    // ──────────────────────────────────────────────────────────

    public function techReject(Request $request): void
    {
        $id     = (int) $request->param('id');
        $userId = (int) $_SESSION['user_id'];

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_tecnico'] !== $userId) {
            header('Location: /dashboard/tecnico/solicitudes');
            exit;
        }

        if ($solicitud['estado'] !== 'pendiente') {
            $_SESSION['errors'] = ['general' => ['Solo puedes rechazar solicitudes pendientes.']];
            header('Location: /dashboard/tecnico/solicitudes/' . $id);
            exit;
        }

        Solicitud::cancel($id, 'tecnico');

        Notificacion::create((int) $solicitud['id_cliente'], 'solicitud_cancelada', $id);

        $_SESSION['success'] = 'Solicitud rechazada.';
        header('Location: /dashboard/tecnico/solicitudes');
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // CLIENTE — Enviar mensaje de chat
    // ──────────────────────────────────────────────────────────

    public function sendMessage(Request $request): void
    {
        $id     = (int) $request->param('id');
        $userId = (int) $_SESSION['user_id'];
        $input  = sanitize_array($_POST);

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_cliente'] !== $userId) {
            header('Location: /dashboard/cliente');
            exit;
        }

        if ($solicitud['estado'] === 'cancelada') {
            $_SESSION['errors'] = ['general' => ['No se puede enviar mensajes en una solicitud cancelada.']];
            header('Location: /solicitudes/' . $id);
            exit;
        }

        try {
            Validator::validate($input, ['contenido' => 'required|max:2000']);
        } catch (ValidationException $e) {
            $_SESSION['errors'] = $e->getErrors();
            header('Location: /solicitudes/' . $id . '#chat');
            exit;
        }

        Mensaje::create($id, 'cliente', $userId, $input['contenido']);

        if (!empty($solicitud['id_tecnico'])) {
            Notificacion::create((int) $solicitud['id_tecnico'], 'mensaje_nuevo', $id);
        }

        header('Location: /solicitudes/' . $id . '#chat');
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // TÉCNICO — Enviar mensaje de chat
    // ──────────────────────────────────────────────────────────

    public function techSendMessage(Request $request): void
    {
        $id     = (int) $request->param('id');
        $userId = (int) $_SESSION['user_id'];
        $input  = sanitize_array($_POST);

        $solicitud = Solicitud::findWithParticipants($id);

        if (!$solicitud || (int) $solicitud['id_tecnico'] !== $userId) {
            header('Location: /dashboard/tecnico/solicitudes');
            exit;
        }

        if ($solicitud['estado'] === 'cancelada') {
            $_SESSION['errors'] = ['general' => ['No se puede enviar mensajes en una solicitud cancelada.']];
            header('Location: /dashboard/tecnico/solicitudes/' . $id);
            exit;
        }

        try {
            Validator::validate($input, ['contenido' => 'required|max:2000']);
        } catch (ValidationException $e) {
            $_SESSION['errors'] = $e->getErrors();
            header('Location: /dashboard/tecnico/solicitudes/' . $id . '#chat');
            exit;
        }

        Mensaje::create($id, 'tecnico', $userId, $input['contenido']);

        Notificacion::create((int) $solicitud['id_cliente'], 'mensaje_nuevo', $id);

        header('Location: /dashboard/tecnico/solicitudes/' . $id . '#chat');
        exit;
    }
}
