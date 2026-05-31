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
        header('Location: /perfil');
        exit;
    }

    public function showEditForm(): void
    {
        $userId      = (int) ($_SESSION['user_id'] ?? 0);
        $user        = User::findById($userId);
        $techProfile = User::getTechnicianProfileByUserId($userId);
        $categorias  = User::getAllCategorias();
        $misCategs   = $techProfile ? User::getCategoriasbyTecnico($userId) : [];
        $fotosTrabajo = $techProfile ? User::getFotosTrabajo($techProfile['id']) : [];

        $this->render('profile/edit', [
            'pageTitle'    => 'TuTecnico - Editar Perfil',
            'user'         => $user,
            'techProfile'  => $techProfile,
            'categorias'   => $categorias,
            'misCategs'    => $misCategs,
            'fotosTrabajo' => $fotosTrabajo,
        ]);
    }

    public function updateProfile(Request $request): void
    {
        $userId      = (int) ($_SESSION['user_id'] ?? 0);
        $user        = User::findById($userId);
        $techProfile = User::getTechnicianProfileByUserId($userId);
        $input       = sanitize_array($request->all());

        Validator::validate($input, [
            'nombre'   => 'required|min:3|max:255',
            'correo'   => 'required|email',
            'telefono' => 'max:20',
        ]);

        // Foto de perfil
        $fotoRuta = $user['foto_perfil'] ?? null;
        if (!empty($_FILES['foto_perfil']['tmp_name'])) {
            $archivo    = $_FILES['foto_perfil'];
            $ext        = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $permitidos)) {
                $_SESSION['errors'] = ['general' => ['Formato de imagen no permitido']];
                header('Location: /perfil/editar'); exit;
            }
            if ($archivo['size'] > 2 * 1024 * 1024) {
                $_SESSION['errors'] = ['general' => ['La imagen no debe superar 2MB']];
                header('Location: /perfil/editar'); exit;
            }

            $carpeta = BASE_PATH . '/public/uploads/fotos/';
            if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
            $nombreArchivo = 'user_' . $userId . '_' . time() . '.' . $ext;
            move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo);
            $fotoRuta = '/uploads/fotos/' . $nombreArchivo;
        }

        // Actualizar datos personales (todos los roles)
        User::updateUser($userId, [
            'nombre'      => $input['nombre'],
            'correo'      => $input['correo'],
            'telefono'    => $input['telefono']  ?? null,
            'direccion'   => $input['direccion'] ?? null,
            'ciudad'      => $input['ciudad']    ?? null,
            'pais'        => $input['pais']      ?? 'El Salvador',
            'foto_perfil' => $fotoRuta,
        ]);

        // Solo si es técnico
        if ($techProfile && ($user['rol'] ?? '') === 'tecnico') {

            // Actualizar descripción y zona de cobertura
            User::updateTechnicianProfile($userId, [
                'descripcion'    => $input['descripcion_tecnico'] ?? $techProfile['descripcion'],
                'zona_cobertura' => $input['zona_cobertura']      ?? $techProfile['zona_cobertura'],
            ]);

            // Sincronizar categorías
            $categoriaIds = isset($input['categorias']) && is_array($input['categorias'])
                ? array_map('intval', $input['categorias'])
                : [];
            User::syncCategoriasTecnico($userId, $categoriaIds);

            // Subir fotos de trabajos
            if (!empty($_FILES['fotos_trabajo']['tmp_name'][0])) {
                $carpetaFotos = BASE_PATH . '/public/uploads/trabajos/';
                if (!is_dir($carpetaFotos)) mkdir($carpetaFotos, 0755, true);

                $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
                foreach ($_FILES['fotos_trabajo']['tmp_name'] as $i => $tmpName) {
                    if (empty($tmpName)) continue;

                    $ext = strtolower(pathinfo($_FILES['fotos_trabajo']['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, $permitidos)) continue;
                    if ($_FILES['fotos_trabajo']['size'][$i] > 5 * 1024 * 1024) continue;

                    $carpetaFotos = BASE_PATH . '/public/uploads/trabajos/';
                    $nombreFoto   = 'trabajo_' . $techProfile['id'] . '_' . time() . '_' . $i . '.' . $ext;
                    move_uploaded_file($tmpName, $carpetaFotos . $nombreFoto);

                    User::addFotoTrabajo(
                        $techProfile['id'],
                        '/uploads/trabajos/' . $nombreFoto,
                        $input['fotos_descripcion'][$i] ?? null
                    );
                }
            }
        }

        $_SESSION['user_name'] = $input['nombre'];
        $_SESSION['success']   = 'Tu perfil fue actualizado correctamente';
        header('Location: /perfil');
        exit;
    }

    // public function showEditForm(): void
    // {
    //     $userId = (int) ($_SESSION['user_id'] ?? 0);
    //     $user = User::findById($userId);

    //     $this->render('profile/edit', [
    //         'pageTitle' => 'TuTecnico - Editar Perfil',
    //         'user' => $user,
    //     ]);
    // }

    // public function updateProfile(Request $request): void
    // {
    //     $userId = (int) ($_SESSION['user_id'] ?? 0);
    //     $user   = User::findById($userId);
    //     $input = sanitize_array($request->all());

    //     Validator::validate($input, [
    //         'nombre'   => 'required|min:3|max:255',
    //         'correo'   => 'required|email',
    //         'telefono' => 'max:20',
    //     ]);

    //     // Manejo de foto de perfil
    //     $fotoRuta = $user['foto_perfil'] ?? null; // mantiene la actual por defecto

    //     if (!empty($_FILES['foto_perfil']['tmp_name'])) {
    //         $archivo  = $_FILES['foto_perfil'];
    //         $ext      = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    //         $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    //         if (!in_array($ext, $permitidos)) {
    //             $_SESSION['errors'] = ['general' => ['Formato de imagen no permitido']];
    //             header('Location: /perfil/editar');
    //             exit;
    //         }

    //         if ($archivo['size'] > 2 * 1024 * 1024) {
    //             $_SESSION['errors'] = ['general' => ['La imagen no debe superar 2MB']];
    //             header('Location: /perfil/editar');
    //             exit;
    //         }

    //         $carpeta = BASE_PATH . '/public/uploads/fotos/';
    //         if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    //         $nombreArchivo = 'user_' . $userId . '_' . time() . '.' . $ext;
    //         move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo);
    //         $fotoRuta = '/uploads/fotos/' . $nombreArchivo;
    //     }


    //     User::updateUser($userId, [
    //         'nombre'    => $input['nombre'],
    //         'correo'    => $input['correo'],
    //         'telefono'  => $input['telefono']  ?? null,
    //         'direccion' => $input['direccion'] ?? null,
    //         'ciudad'    => $input['ciudad']    ?? null,
    //         'pais'      => $input['pais']      ?? 'El Salvador',
    //         'foto_perfil' => $fotoRuta,
    //     ]);

    //     $_SESSION['user_name'] = $input['nombre'];
    //     $_SESSION['success']   = 'Tu perfil fue actualizado correctamente';
    //     header('Location: /perfil');
    //     exit;
    // }

}
