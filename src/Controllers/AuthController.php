<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Models\User;
use App\Core\Auth;

class AuthController extends Controller
{
    public function showLoginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->render('auth/login', ['title' => 'Đăng nhập']);
    }

    public function login(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Invalid CSRF token");
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Vui lòng nhập đầy đủ email và mật khẩu.');
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            Auth::login((int)$user['id']);
            Session::setFlash('success', 'Đăng nhập thành công!');
            $this->redirect('/');
        } else {
            Session::setFlash('error', 'Email hoặc mật khẩu không chính xác.');
            $this->redirect('/login');
        }
    }

    public function showRegisterForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->render('auth/register', ['title' => 'Đăng ký']);
    }

    public function register(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Invalid CSRF token");
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            Session::setFlash('error', 'Vui lòng nhập đầy đủ thông tin.');
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Email không hợp lệ.');
            $this->redirect('/register');
        }

        if ($password !== $passwordConfirm) {
            Session::setFlash('error', 'Mật khẩu xác nhận không khớp.');
            $this->redirect('/register');
        }

        $userModel = new User();
        
        if ($userModel->findByEmail($email)) {
            Session::setFlash('error', 'Email đã được sử dụng.');
            $this->redirect('/register');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if ($userModel->create($name, $email, $hashedPassword)) {
            Session::setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
            $this->redirect('/login');
        } else {
            Session::setFlash('error', 'Có lỗi xảy ra, vui lòng thử lại.');
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Invalid CSRF token");
        }
        Auth::logout();
        Session::setFlash('success', 'Đã đăng xuất thành công.');
        $this->redirect('/login');
    }
}
