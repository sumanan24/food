<?php
namespace Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . '/app/views/' . str_replace('.', '/', $view) . '.php';
        $layoutFile = dirname(__DIR__) . '/app/views/layouts/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: $view");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!isLoggedIn()) {
            redirect('login');
        }
    }

    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        if (!hasRole(...$roles)) {
            Session::flash('error', 'Access denied.');
            redirect(isCashier() ? 'sales' : 'dashboard');
        }
    }

    /** Block cashiers from create/update/delete outside POS */
    protected function requireStaff(): void
    {
        $this->requireAuth();
        if (isCashier()) {
            Session::flash('error', 'Cashiers can only create bills from POS. Edit and delete are not allowed.');
            redirect('sales');
        }
    }

    /** Block edit/update/delete for cashiers (defense in depth) */
    protected function denyCashierMutate(): void
    {
        if (isCashier()) {
            Session::flash('error', 'You do not have permission to edit or delete.');
            redirect('sales');
        }
    }

    protected function validateCsrf(): void
    {
        $config = require dirname(__DIR__) . '/config/app.php';
        $token = $_POST[$config['csrf_token_name']] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Security::validateCsrf($token)) {
            Session::flash('error', 'Invalid security token. Please try again.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard');
        }
    }

    protected function logActivity(string $action, string $module, ?string $details = null): void
    {
        $user = currentUser();
        $stmt = Database::getInstance()->prepare(
            "INSERT INTO activity_logs (user_id, user_name, action, module, details, ip_address) VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            $user['id'] ?? null,
            $user['name'] ?? 'Guest',
            $action,
            $module,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
