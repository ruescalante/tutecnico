<?php
require_once BASE_PATH . '/models/Ejemplo.php';
require_once BASE_PATH . '/core/Controller.php';

class EjemploController extends Controller
{
    public function index(): void
    {
        $this->render('ejemplo/index', [
            'solicitudes' => Ejemplo::all(),
            'pageTitle'   => 'TuTecnico - Solicitudes',
        ]);
    }

    public function create(): void
    {
        $this->render('ejemplo/create', [
            'pageTitle' => 'TuTecnico - Nueva Solicitud',
        ]);
    }

    public function store(): void
    {
        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $direccion   = trim($_POST['direccion'] ?? '');

        if (empty($titulo) || empty($descripcion) || empty($direccion)) {
            $error = 'Todos los campos son obligatorios';
            $this->render('ejemplo/create', [
                'pageTitle' => 'TuTecnico - Nueva Solicitud',
                'error'     => $error,
            ]);
            return;
        }

        Ejemplo::create([
            'id_cliente'  => 1, // hardcoded hasta tener auth
            'titulo'      => $titulo,
            'descripcion' => $descripcion,
            'direccion'   => $direccion,
        ]);

        header('Location: /ejemplo');
        exit;
    }

    public function edit(): void
    {
        $id        = (int) ($_GET['id'] ?? 0);
        $solicitud = Ejemplo::find($id);

        if (!$solicitud) {
            header('Location: /ejemplo');
            exit;
        }

        $this->render('ejemplo/edit', [
            'pageTitle' => 'TuTecnico - Editar Solicitud',
            'solicitud' => $solicitud,
        ]);
    }

    public function update(): void
    {
        $id          = (int) ($_POST['id'] ?? 0);
        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $direccion   = trim($_POST['direccion'] ?? '');
        $estado      = $_POST['estado'] ?? 'pendiente';

        if (empty($titulo) || empty($descripcion) || empty($direccion)) {
            $error     = 'Todos los campos son obligatorios';
            $solicitud = Ejemplo::find($id);
            $this->render('ejemplo/edit', [
                'pageTitle' => 'TuTecnico - Editar Solicitud',
                'solicitud' => $solicitud,
                'error'     => $error,
            ]);
            return;
        }

        Ejemplo::update($id, [
            'titulo'      => $titulo,
            'descripcion' => $descripcion,
            'direccion'   => $direccion,
            'estado'      => $estado,
        ]);

        header('Location: /ejemplo');
        exit;
    }

    public function destroy(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        Ejemplo::delete($id);
        header('Location: /ejemplo');
        exit;
    }
}
