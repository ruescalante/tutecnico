<?php

require_once BASE_PATH . '/models/User.php';

class ProfileController extends Controller
{
    public function index(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $user = User::findById($userId);
        $techProfile = User::getTechnicianProfileByUserId($userId);

        $this->render('profile/index', [
            'pageTitle' => 'TuTecnico - Mi Perfil',
            'user' => $user,
            'techProfile' => $techProfile,
        ]);
    }

    public function showTechnicianForm(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $user = User::findById($userId);
        $techProfile = User::getTechnicianProfileByUserId($userId);

        $this->render('profile/solicitud', [
            'pageTitle' => 'TuTecnico - Solicitud Técnico',
            'user' => $user,
            'techProfile' => $techProfile,
        ]);
    }

    public function applyTechnician(Request $request): void
    {
        $role = $_SESSION['role'] ?? null;
        if ($role === 'admin') {
            $_SESSION['errors'] = ['auth' => ['El administrador no necesita postularse como técnico']];
            header('Location: /perfil');
            exit;
        }

        if ($role === 'tecnico') {
            $_SESSION['errors'] = ['auth' => ['Tu usuario ya tiene rol técnico']];
            header('Location: /perfil');
            exit;
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $input = sanitize_array($request->all());

        Validator::validate($input, [
            'zona_cobertura' => 'required|min:5|max:255',
            'descripcion' => 'required|min:20|max:1000',
            'documentos_verificacion' => 'required|min:5|max:500',
        ]);

        User::upsertTechnicianApplication($userId, [
            'zona_cobertura' => $input['zona_cobertura'],
            'descripcion' => $input['descripcion'],
            'documentos_verificacion' => $input['documentos_verificacion'],
        ]);

        $_SESSION['success'] = 'Tu solicitud para ser técnico fue enviada y está pendiente de validación';
        header('Location: /dashboard/cliente');
        exit;
    }

    public function showEditForm(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $user = User::findById($userId);

        $this->render('profile/edit', [
            'pageTitle' => 'TuTecnico - Editar Perfil',
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $input = sanitize_array($request->all());

        Validator::validate($input, [
            'nombre' => 'required|min:3|max:255',
            'correo' => 'required|email',
            'telefono' => 'max:20',
        ]);

        User::updateUser($userId, [
            'nombre' => $input['nombre'],
            'correo' => $input['correo'],
            'telefono' => $input['telefono'] ?? null,
        ]);

        // Update session with new name
        $_SESSION['user_name'] = $input['nombre'];

        $_SESSION['success'] = 'Tu perfil fue actualizado correctamente';
        header('Location: /perfil');
        exit;
    }
}
