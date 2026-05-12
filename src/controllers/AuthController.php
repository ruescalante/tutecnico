<?php

require_once BASE_PATH . '/models/User.php';

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $this->render('auth/login', [
            'layout' => 'tailwind',
            'pageTitle' => 'TuTecnico - Ingresar',
        ]);
    }

    public function showRegister(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $this->render('auth/register', [
            'layout' => 'tailwind',
            'pageTitle' => 'TuTecnico - Registro',
        ]);
    }

    public function login(Request $request): void
    {
        $input = sanitize_array($request->all());

        Validator::validate($input, [
            'correo' => 'required|email|max:150',
            'contrasena' => 'required|min:8|max:72',
        ]);

        $user = User::findByEmail($input['correo']);

        if (!$user || !password_verify($input['contrasena'], $user['contrasena'])) {
            throw new ValidationException(['auth' => ['Credenciales inválidas']]);
        }

        if ((int) $user['activo'] !== 1) {
            throw new ValidationException(['auth' => ['Tu cuenta está desactivada']]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['rol'];
        $_SESSION['user_name'] = $user['nombre'];

        $this->redirectByRole((int) $user['id'], (string) $user['rol']);
    }

    public function register(Request $request): void
    {
        $input = sanitize_array($request->all());

        Validator::validate($input, [
            'nombre' => 'required|min:3|max:100',
            'correo' => 'required|email|max:150',
            'contrasena' => 'required|min:8|max:72',
            'contrasena_confirmacion' => 'required|min:8|max:72',
        ]);

        if (($input['contrasena'] ?? '') !== ($input['contrasena_confirmacion'] ?? '')) {
            throw new ValidationException(['contrasena_confirmacion' => ['Las contraseñas no coinciden']]);
        }

        if (User::existsByEmail($input['correo'])) {
            throw new ValidationException(['correo' => ['El correo ya está registrado']]);
        }

        $newUserId = User::createClient([
            'nombre' => $input['nombre'],
            'correo' => $input['correo'],
            'contrasena' => password_hash($input['contrasena'], PASSWORD_BCRYPT),
            'telefono' => $input['telefono'] ?? null,
        ]);

        $_SESSION['user_id'] = $newUserId;
        $_SESSION['role'] = 'cliente';
        $_SESSION['user_name'] = $input['nombre'];
        $_SESSION['success'] = 'Cuenta creada correctamente';

        header('Location: /dashboard/cliente');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        header('Location: /login');
        exit;
    }

    private function redirectByRole(int $userId, string $role): void
    {
        $intended = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);

        if (is_string($intended) && str_starts_with($intended, '/') && $intended !== '/login' && $intended !== '/registro') {
            header('Location: ' . $intended);
            exit;
        }

        if ($role === 'admin') {
            header('Location: /dashboard/admin');
            exit;
        }

        if ($role === 'tecnico') {
            $profile = User::getTechnicianProfileByUserId($userId);
            $status = $profile['estado'] ?? null;

            if ($status === 'activo') {
                header('Location: /dashboard/tecnico');
                exit;
            }

            if ($status === 'pendiente') {
                header('Location: /dashboard/tecnico/espera');
                exit;
            }

            $this->clearAuthSession();
            $_SESSION['errors'] = ['auth' => ['Tu cuenta técnica no tiene acceso por el momento']];
            header('Location: /login');
            exit;
        }

        header('Location: /dashboard/cliente');
        exit;
    }

    private function clearAuthSession(): void
    {
        unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['user_name'], $_SESSION['intended_url']);
    }
}
