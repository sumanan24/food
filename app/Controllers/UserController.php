<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class UserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): void
    {
        $this->view('users/index', [
            'title' => 'Manage Users',
            'users' => $this->userModel->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('users/create', ['title' => 'Add User']);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $role = (string) $this->input('role', 'cashier');

        if ($name === '' || $email === '' || $password === '') {
            Session::flash('error', 'All fields are required.');
            $this->redirect('/users/create');
        }

        if (!in_array($role, ['admin', 'cashier'], true)) {
            $role = 'cashier';
        }

        if ($this->userModel->findByEmail($email)) {
            Session::flash('error', 'Email already exists.');
            $this->redirect('/users/create');
        }

        $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);

        Session::flash('success', 'User created successfully.');
        $this->redirect('/users');
    }
}
