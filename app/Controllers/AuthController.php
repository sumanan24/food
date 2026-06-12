<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function loginForm(): void
    {
        $this->view('auth/login', [
            'title' => 'Login',
        ], 'layouts/auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');

        if ($email === '' || $password === '') {
            Session::flash('error', 'Email and password are required.');
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        Auth::login($user);
        Session::flash('success', 'Welcome back, ' . $user['name'] . '!');
        $this->redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::start();
        Session::flash('success', 'You have been logged out.');
        $this->redirect('/login');
    }

    public function changePasswordForm(): void
    {
        $this->view('auth/change-password', [
            'title' => 'Change Password',
        ]);
    }

    public function changePassword(): void
    {
        $this->validateCsrf();

        $current = (string) $this->input('current_password', '');
        $new = (string) $this->input('new_password', '');
        $confirm = (string) $this->input('confirm_password', '');

        if ($new === '' || strlen($new) < 6) {
            Session::flash('error', 'New password must be at least 6 characters.');
            $this->redirect('/change-password');
        }

        if ($new !== $confirm) {
            Session::flash('error', 'Password confirmation does not match.');
            $this->redirect('/change-password');
        }

        $user = $this->userModel->findById((int) Auth::id());
        if (!$user || !password_verify($current, $user['password'])) {
            Session::flash('error', 'Current password is incorrect.');
            $this->redirect('/change-password');
        }

        $this->userModel->updatePassword((int) $user['id'], $new);
        Session::flash('success', 'Password updated successfully.');
        $this->redirect('/change-password');
    }
}
