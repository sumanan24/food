<?php
namespace App\Controllers;

use App\Models\UserModel;
use Core\Controller;
use Core\Database;
use Core\Security;
use Core\Session;

class AuthController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function loginForm(): void
    {
        if (isLoggedIn()) {
            redirect(isCashier() ? 'sales' : 'dashboard');
        }
        $this->view('auth.login', ['title' => 'Login'], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();
        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        $errors = Security::validateRequired(['email' => 'Email', 'password' => 'Password'], $_POST);
        if ($errors) {
            Session::set('_old', $_POST);
            Session::flash('error', implode(' ', $errors));
            redirect('login');
        }

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Invalid email or password.');
            redirect('login');
        }

        Session::set('user_id', $user['id']);
        Session::set('user', [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        Database::getInstance()->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->users->setRememberToken($user['id'], $token);
            setcookie('remember_token', $token, time() + 86400 * 30, '/', '', false, true);
        }

        $this->logActivity('login', 'auth', 'User logged in');
        redirect($user['role'] === 'cashier' ? 'sales' : 'dashboard');
    }

    public function logout(): void
    {
        $this->logActivity('logout', 'auth');
        setcookie('remember_token', '', time() - 3600, '/');
        Session::destroy();
        redirect('login');
    }

    public function forgotForm(): void
    {
        $this->view('auth.forgot', ['title' => 'Forgot Password'], 'auth');
    }

    public function forgotSend(): void
    {
        $this->validateCsrf();
        $email = Security::sanitize($_POST['email'] ?? '');
        $user = $this->users->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            Database::getInstance()->prepare(
                "INSERT INTO password_resets (email, token, expires_at) VALUES (?,?,?)"
            )->execute([$email, password_hash($token, PASSWORD_BCRYPT), $expires]);

            Session::set('reset_link', url('reset-password/' . $token));
            Session::flash('success', 'Reset link generated. Check the link below (demo mode).');
        } else {
            Session::flash('success', 'If that email exists, a reset link was sent.');
        }
        redirect('forgot-password');
    }

    public function resetForm(string $token): void
    {
        $this->view('auth.reset', ['title' => 'Reset Password', 'token' => $token], 'auth');
    }

    public function resetPassword(): void
    {
        $this->validateCsrf();
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (strlen($password) < 6 || $password !== $confirm) {
            Session::flash('error', 'Passwords must match and be at least 6 characters.');
            redirect('reset-password/' . $token);
        }

        $db = Database::getInstance();
        $rows = $db->query("SELECT * FROM password_resets WHERE expires_at > NOW() ORDER BY id DESC LIMIT 50")->fetchAll();
        $email = null;
        foreach ($rows as $row) {
            if (password_verify($token, $row['token'])) {
                $email = $row['email'];
                break;
            }
        }

        if (!$email) {
            Session::flash('error', 'Invalid or expired reset token.');
            redirect('forgot-password');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db->prepare("UPDATE users SET password = ? WHERE email = ?")->execute([$hash, $email]);
        $db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
        Session::flash('success', 'Password updated. Please login.');
        redirect('login');
    }
}
