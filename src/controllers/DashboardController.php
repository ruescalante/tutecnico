<?php

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Solicitud.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $role = $_SESSION['role'] ?? null;

        if ($role === 'admin') {
            header('Location: /dashboard/admin');
            exit;
        }

        if ($role === 'tecnico') {
            $profile = User::getTechnicianProfileByUserId((int) $_SESSION['user_id']);
            if (($profile['estado'] ?? null) === 'activo') {
                header('Location: /dashboard/tecnico');
            } else {
                header('Location: /dashboard/tecnico/espera');
            }
            exit;
        }

        header('Location: /dashboard/cliente');
        exit;
    }

    public function client(): void
    {
        if (($_SESSION['role'] ?? null) !== 'cliente') {
            header('Location: /dashboard');
            exit;
        }

        $userId     = (int) ($_SESSION['user_id'] ?? 0);
        $estadoFiltro = $_GET['estado'] ?? '';
        $estadosValidos = ['pendiente', 'aceptada', 'en_progreso', 'completada', 'cancelada'];
        if ($estadoFiltro !== '' && !in_array($estadoFiltro, $estadosValidos, true)) {
            $estadoFiltro = '';
        }

        $solicitudes = Solicitud::findByCliente($userId, $estadoFiltro);

        $this->render('dashboard/client/index', [
            'pageTitle'    => 'TuTecnico - Mis Solicitudes',
            'solicitudes'  => $solicitudes,
            'estadoFiltro' => $estadoFiltro,
        ]);
    }

    // public function technician(): void
    // {
    //     $userId = (int) ($_SESSION['user_id'] ?? 0);
    //     $profile = User::getTechnicianProfileByUserId($userId);
    //     $status = $profile['estado'] ?? null;

    //     if ($status !== 'activo') {
    //         if ($status === 'pendiente') {
    //             header('Location: /dashboard/tecnico/espera');
    //             exit;
    //         }

    //         unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['user_name'], $_SESSION['intended_url']);
    //         $_SESSION['errors'] = ['auth' => ['Tu cuenta técnica no puede acceder al panel']];
    //         header('Location: /login');
    //         exit;
    //     }

    //     $this->render('dashboard/technician/index', [
    //         'pageTitle' => 'TuTecnico - Panel Técnico',
    //         'profile' => $profile,
    //     ]);
    // }

    public function technician(): void
    {
        $userId  = (int) ($_SESSION['user_id'] ?? 0);
        $profile = User::getTechnicianProfileByUserId($userId);
        $status  = $profile['estado'] ?? null;

        if ($status !== 'activo') {
            if ($status === 'pendiente') {
                header('Location: /dashboard/tecnico/espera');
                exit;
            }
            unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['user_name'], $_SESSION['intended_url']);
            $_SESSION['errors'] = ['auth' => ['Tu cuenta técnica no puede acceder al panel']];
            header('Location: /login');
            exit;
        }

        header('Location: /dashboard/tecnico/solicitudes');
        exit;
    }

    public function technicianWaiting(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $profile = User::getTechnicianProfileByUserId($userId);

        if (!$profile) {
            header('Location: /dashboard');
            exit;
        }

        if (($profile['estado'] ?? null) === 'activo') {
            header('Location: /dashboard/tecnico');
            exit;
        }

        $this->render('dashboard/technician/waiting', [
            'pageTitle' => 'TuTecnico - Estado de verificación',
            'profile' => $profile,
        ]);
    }
}
