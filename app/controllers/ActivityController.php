<?php
namespace App\Controllers;

use Core\Controller;
use Core\Database;

class ActivityController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin', 'super_admin');
        $stmt = Database::getInstance()->query(
            "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 200"
        );
        $this->view('activity.index', [
            'title' => 'Activity Logs',
            'logs'  => $stmt->fetchAll(),
        ]);
    }
}
