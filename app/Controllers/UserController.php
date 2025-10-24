<?php
namespace App\Controllers;

use App\Repository\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class UserController {
    private UserRepositoryInterface $userRepository;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function showLogin() {
        include __DIR__ . '/../Views/user/login.php';
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = $this->userRepository->findUserByEmail($email);

        if ($user && password_verify($password, $user->getPasswordHash())) {
            $_SESSION['user'] = $email;
            $_SESSION['user_id'] = $user->getId();
            $this->logger->info('User logged in (web)', ['email' => $email, 'user_id' => $user->getId()]);
            header("Location: /");
            return;
        }

        $this->logger->warning('Invalid login attempt (web)', ['email' => $email]);
        $error = "Invalid email or password";
        include __DIR__ . '/../Views/user/login.php';
    }

    public function showRegister() {
        include __DIR__ . '/../Views/user/register.php';
    }

    public function register() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->userRepository->createUser($email, $password)) {
            $this->logger->info('User registered (web)', ['email' => $email]);
            header("Location: /");
        } else {
            $this->logger->warning('Attempt to register existing user (web)', ['email' => $email]);
            $error = "User already exists!";
            include __DIR__ . '/../Views/user/register.php';
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /");
    }
}

