<?php
require_once BASE_PATH . '/models/Home.php';
require_once BASE_PATH . '/models/User.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', [
            'layout'           => 'tailwind',
            'pageTitle'        => 'TuTécnico - Encuentra a tu experto ideal',
            'featuredTecnicos' => User::getTopTechnicians(3),
            'latestReviews'    => User::getLatestReviews(3),
        ]);
    }
}