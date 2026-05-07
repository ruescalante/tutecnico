<?php
require_once BASE_PATH . '/models/Home.php';
require_once BASE_PATH . '/core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', [
            'pageTitle' => 'TuTecnico - Inicio',
        ]);
    }

}