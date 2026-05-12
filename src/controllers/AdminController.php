<?php

require_once BASE_PATH . '/models/User.php';

class AdminController extends Controller
{
    public function index(): void
    {
        $applications = User::listTechnicianApplications();

        $stats = [
            'clientes' => User::countByRole('cliente'),
            'tecnicos' => User::countByRole('tecnico'),
            'admins' => User::countByRole('admin'),
        ];

        $this->render('dashboard/admin/index', [
            'pageTitle' => 'TuTecnico - Admin',
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }

    public function updateTechnicianStatus(Request $request): void
    {
        $userId = (int) $request->param('id', 0);
        if ($userId <= 0) {
            $_SESSION['errors'] = ['auth' => ['Solicitud inválida']];
            header('Location: /dashboard/admin');
            exit;
        }

        $input = sanitize_array($request->all());
        Validator::validate($input, [
            'estado' => 'required|in:pendiente,activo,suspendido,rechazado',
            'comentario_admin' => 'max:500',
        ]);

        $profile = User::getTechnicianProfileByUserId($userId);
        if (!$profile) {
            $_SESSION['errors'] = ['auth' => ['No existe solicitud técnica para este usuario']];
            header('Location: /dashboard/admin');
            exit;
        }

        User::setTechnicianStatus($userId, $input['estado'], $input['comentario_admin'] ?? null);
        $_SESSION['success'] = 'Estado técnico actualizado correctamente';

        header('Location: /dashboard/admin');
        exit;
    }
}
