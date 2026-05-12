<?php
require_once BASE_PATH . '/models/Home.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', [
            'layout' => 'tailwind',
            'pageTitle' => 'TuTecnico - Inicio',
        ]);
    }

}