<?php
class Logout extends Controllers
{

    public function __construct()
    {
        session_start();
        parent::__construct();
    }

   public function index()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $origen = $_SESSION['login_origen'] ?? 'admin';

    session_unset();
    session_destroy();

    if ($origen === 'tienda') {
        header('Location: ' . base_url() . '/login/tienda');
    } else {
        header('Location: ' . base_url() . '/login');
    }
    exit;
}
}
