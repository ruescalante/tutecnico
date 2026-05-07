<?php
require_once BASE_PATH . '/models/Ejemplo.php';

class EjemploController extends Controller
{
    public function index(): void  //Pagina Principal de ejemplo, lista solicitudes
    {
        //Se pone el nombre de la vista sin la extension .php. carpetas/archivo dentro de views.
        //Si queremos renderizar views/ejemplo/index.php, ponemos 'ejemplo/index'
        $this->render('ejemplo/index', [   
            'solicitudes' => Ejemplo::all(),
            'pageTitle'   => 'TuTecnico - Solicitudes',
        ]);
    }

    public function create(): void  //Formulario para crear nueva solicitud
    {
        $this->render('ejemplo/create', [
            'pageTitle' => 'TuTecnico - Nueva Solicitud',
        ]);
    }

    public function store(Request $request): void //Procesa el formulario de creación de solicitud
    {
        $input = sanitize_array($request->all());

        Validator::validate($input, [
            'titulo' => 'required|min:5|max:120',
            'descripcion' => 'required|min:10|max:1000',
            'direccion' => 'required|min:5|max:255',
        ]);

        Ejemplo::create([
            'id_cliente'  => 1, // hardcoded hasta tener auth
            'titulo'      => $input['titulo'],
            'descripcion' => $input['descripcion'],
            'direccion'   => $input['direccion'],
        ]);

        header('Location: /ejemplo');
        exit;
    }

    public function edit(Request $request): void  //Formulario para editar una solicitud existente
    {
        $id = (int) $request->input('id', 0);
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

    public function update(Request $request): void //Procesa el formulario de edición de solicitud
    {
        $input = sanitize_array($request->all());
        $id = (int) ($input['id'] ?? 0);
        $input['estado'] = $input['estado'] ?? 'pendiente';

        Validator::validate($input, [
            'titulo' => 'required|min:5|max:120',
            'descripcion' => 'required|min:10|max:1000',
            'direccion' => 'required|min:5|max:255',
        ]);

        Ejemplo::update($id, [
            'titulo'      => $input['titulo'],
            'descripcion' => $input['descripcion'],
            'direccion'   => $input['direccion'],
            'estado'      => $input['estado'],
        ]);

        header('Location: /ejemplo');
        exit;
    }

    public function destroy(Request $request): void  //Procesa la eliminación de una solicitud
    {
        $id = (int) ($request->input('id') ?? 0);
        Ejemplo::delete($id);
        header('Location: /ejemplo');
        exit;
    }
}
