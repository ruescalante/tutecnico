<?php

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Solicitud.php';

class AdminController extends Controller
{
    public function index(): void
    {
        header('Location: /dashboard/admin/usuarios');
        exit;
    }

    // ── USUARIOS ──────────────────────────────────────────────────────────────

    public function usuarios(): void
    {
        $search = trim($_GET['q'] ?? '');
        $status = $_GET['estado'] ?? '';
        $users  = User::listAllUsers($search, $status);

        $stats = [
            'clientes' => User::countByRole('cliente'),
            'tecnicos' => User::countByRole('tecnico'),
            'admins'   => User::countByRole('admin'),
        ];

        $this->render('dashboard/admin/usuarios/index', [
            'pageTitle'     => 'TuTecnico – Usuarios',
            'users'         => $users,
            'stats'         => $stats,
            'activeSection' => 'usuarios',
            'search'        => $search,
            'statusFilter'  => $status,
        ], 'admin');
    }

    public function editUsuario(Request $request): void
    {
        $userId = (int) $request->param('id', 0);
        $user   = User::findById($userId);

        if (!$user) {
            $_SESSION['errors'] = ['auth' => ['Usuario no encontrado']];
            header('Location: /dashboard/admin/usuarios');
            exit;
        }

        $this->render('dashboard/admin/usuarios/edit', [
            'pageTitle'     => 'TuTecnico – Editar Usuario',
            'user'          => $user,
            'activeSection' => 'usuarios',
        ], 'admin');
    }

    public function updateUsuario(Request $request): void
    {
        $userId = (int) $request->param('id', 0);
        if ($userId <= 0) {
            $_SESSION['errors'] = ['auth' => ['Solicitud inválida']];
            header('Location: /dashboard/admin/usuarios');
            exit;
        }

        $input = sanitize_array($request->all());
        Validator::validate($input, [
            'nombre'   => 'required|min:3|max:100',
            'correo'   => 'required|email|max:150',
            'telefono' => 'max:20',
            'rol'      => 'required|in:cliente,tecnico,admin',
        ]);

        User::adminUpdateUser($userId, [
            'nombre'   => $input['nombre'],
            'correo'   => $input['correo'],
            'telefono' => $input['telefono'] ?? null,
            'rol'      => $input['rol'],
        ]);

        $_SESSION['success'] = 'Usuario actualizado correctamente';
        header('Location: /dashboard/admin/usuarios');
        exit;
    }

    public function suspenderUsuario(Request $request): void
    {
        $userId = (int) $request->param('id', 0);
        if ($userId <= 0) {
            $_SESSION['errors'] = ['auth' => ['Solicitud inválida']];
            header('Location: /dashboard/admin/usuarios');
            exit;
        }

        if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
            $_SESSION['errors'] = ['auth' => ['No puedes suspenderte a ti mismo']];
            header('Location: /dashboard/admin/usuarios');
            exit;
        }

        User::toggleActive($userId);
        $_SESSION['success'] = 'Estado del usuario actualizado';
        header('Location: /dashboard/admin/usuarios');
        exit;
    }

    public function eliminarUsuario(Request $request): void
    {
        $userId = (int) $request->param('id', 0);
        if ($userId <= 0) {
            $_SESSION['errors'] = ['auth' => ['Solicitud inválida']];
            header('Location: /dashboard/admin/usuarios');
            exit;
        }

        if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
            $_SESSION['errors'] = ['auth' => ['No puedes eliminar tu propia cuenta']];
            header('Location: /dashboard/admin/usuarios');
            exit;
        }

        try {
            User::deleteUser($userId);
            $_SESSION['success'] = 'Usuario eliminado correctamente';
        } catch (PDOException $e) {
            if (str_starts_with((string) $e->getCode(), '23')) {
                $_SESSION['errors'] = ['auth' => [
                    'No se puede eliminar este usuario porque tiene solicitudes activas. Suspéndalo en su lugar.'
                ]];
            } else {
                $_SESSION['errors'] = ['auth' => ['Error al eliminar el usuario']];
                error_log($e->getMessage());
            }
        }

        header('Location: /dashboard/admin/usuarios');
        exit;
    }

    // ── TÉCNICOS ──────────────────────────────────────────────────────────────

    public function tecnicos(): void
    {
        $search = trim($_GET['q']    ?? '');
        $zona   = trim($_GET['zona'] ?? '');

        $estadosValidos = ['pendiente', 'activo', 'suspendido', 'rechazado'];
        $estado = in_array($_GET['estado'] ?? '', $estadosValidos) ? $_GET['estado'] : '';

        $applications = User::listTechnicianApplications($search, $estado, $zona);

        $this->render('dashboard/admin/tecnicos/index', [
            'pageTitle'     => 'TuTecnico – Técnicos',
            'applications'  => $applications,
            'activeSection' => 'tecnicos',
            'search'        => $search,
            'estadoFilter'  => $estado,
            'zonaFilter'    => $zona,
        ], 'admin');
    }

    public function updateTechnicianStatus(Request $request): void
    {
        $userId = (int) $request->param('id', 0);
        if ($userId <= 0) {
            $_SESSION['errors'] = ['auth' => ['Solicitud inválida']];
            header('Location: /dashboard/admin/tecnicos');
            exit;
        }

        $input = sanitize_array($request->all());
        Validator::validate($input, [
            'estado'           => 'required|in:pendiente,activo,suspendido,rechazado',
            'comentario_admin' => 'max:500',
        ]);

        $profile = User::getTechnicianProfileByUserId($userId);
        if (!$profile) {
            $_SESSION['errors'] = ['auth' => ['No existe solicitud técnica para este usuario']];
            header('Location: /dashboard/admin/tecnicos');
            exit;
        }

        User::setTechnicianStatus($userId, $input['estado'], $input['comentario_admin'] ?? null);
        $_SESSION['success'] = 'Estado técnico actualizado correctamente';

        header('Location: /dashboard/admin/tecnicos');
        exit;
    }

    // ── SOLICITUDES ───────────────────────────────────────────────────────────

    public function solicitudes(): void
    {
        $search = trim($_GET['q'] ?? '');

        $estadosValidos = ['pendiente', 'aceptada', 'en_progreso', 'completada', 'cancelada'];
        $estado = in_array($_GET['estado'] ?? '', $estadosValidos) ? $_GET['estado'] : '';

        $solicitudes = Solicitud::listAll($search, $estado);

        $this->render('dashboard/admin/solicitudes/index', [
            'pageTitle'     => 'TuTecnico – Solicitudes',
            'solicitudes'   => $solicitudes,
            'activeSection' => 'solicitudes',
            'search'        => $search,
            'estadoFilter'  => $estado,
        ], 'admin');
    }
}
