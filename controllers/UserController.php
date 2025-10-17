<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin() {
        include __DIR__ . '/../views/user/login.php';
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = $this->userModel->checkLogin($email, $password);
        if ($user) {
            session_start();
            $_SESSION['user'] = $email;
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
        } else {
            $error = "Invalid email or password";
            include __DIR__ . '/../views/user/login.php';
        }
    }

    public function showRegister() {
        include __DIR__ . '/../views/user/register.php';
    }

    public function register() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->userModel->createUser($email, $password)) {
            header("Location: index.php");
        } else {
            $error = "User already exists!";
            include __DIR__ . '/../views/user/register.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php");
    }
}
?>
