<?php
namespace App\Controllers;

use App\Models\UserModel;
use Core\Controller;
use Core\Security;
use Core\Session;

class UserController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function index(): void
    {
        $this->requireRole('admin', 'super_admin');
        $search = $_GET['search'] ?? '';
        $list = $search ? $this->users->search($search) : $this->users->all();
        $this->view('users.index', ['title' => 'Users', 'users' => $list, 'search' => $search]);
    }

    public function create(): void
    {
        $this->requireRole('admin', 'super_admin');
        $this->view('users.form', ['title' => 'Add User', 'user' => null]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'super_admin');
        $this->validateCsrf();
        $data = $this->collect();
        $data['password'] = password_hash($_POST['password'] ?? 'password', PASSWORD_BCRYPT);
        $this->users->create($data);
        $this->logActivity('create', 'users');
        Session::flash('success', 'User created.');
        redirect('users');
    }

    public function edit(string $id): void
    {
        $this->requireRole('admin', 'super_admin');
        $user = $this->users->find((int)$id);
        if (!$user) redirect('users');
        unset($user['password']);
        $this->view('users.form', ['title' => 'Edit User', 'user' => $user]);
    }

    public function update(string $id): void
    {
        $this->requireRole('admin', 'super_admin');
        $this->validateCsrf();
        $data = $this->collect();
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }
        $this->users->update((int)$id, $data);
        Session::flash('success', 'User updated.');
        redirect('users');
    }

    public function delete(string $id): void
    {
        $this->requireRole('super_admin');
        $this->validateCsrf();
        if ((int)$id === (currentUser()['id'] ?? 0)) {
            Session::flash('error', 'Cannot delete yourself.');
            redirect('users');
        }
        $this->users->delete((int)$id);
        Session::flash('success', 'User deleted.');
        redirect('users');
    }

    private function collect(): array
    {
        return [
            'name'   => Security::sanitize($_POST['name'] ?? ''),
            'email'  => Security::sanitize($_POST['email'] ?? ''),
            'phone'  => Security::sanitize($_POST['phone'] ?? ''),
            'role'   => $_POST['role'] ?? 'cashier',
            'status' => (int)($_POST['status'] ?? 1),
        ];
    }
}
