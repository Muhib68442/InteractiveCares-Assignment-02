<?php
class Middleware
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // CHECK IF LOGIN 
    public function check_if_not_logged()
    {
        if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
            header("Location: ../login.php"); // adjust path
            exit();
        } else if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
            // restore session from cookie
            $_SESSION['user_id'] = $_COOKIE['user_id'];
        }
    }

    // LOGIN/SIGNUP
    public function check_if_already_logged()
    {
        if (isset($_SESSION['user_id']) || isset($_COOKIE['user_id'])) {
            header("Location: ../dashboard.php");
            exit();
        }
    }
}


$middleware = new Middleware();
