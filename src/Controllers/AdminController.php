<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\User;

class AdminController extends Controller
{
    public function index(): void
    {
        if (!Auth::isAdmin()) {
            Session::setFlash('error', 'Bạn không có quyền truy cập trang này.');
            $this->redirect('/');
        }

        $userModel = new User();
        $users = $userModel->getAllUsers();

        $db = (new \App\Core\Database())->getConnection();
        
        $stats = [
            'total_users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_notebooks' => $db->query("SELECT COUNT(*) FROM notebooks")->fetchColumn(),
            'total_public_notebooks' => $db->query("SELECT COUNT(*) FROM notebooks WHERE is_public = 1")->fetchColumn(),
            'total_vocabularies' => $db->query("SELECT COUNT(*) FROM vocabularies")->fetchColumn()
        ];

        $this->render('admin/index', [
            'title' => 'Quản lý Admin',
            'users' => $users,
            'stats' => $stats,
            'currentUserId' => Auth::id()
        ]);
    }

    public function notebooks(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/');
        }
        $notebookModel = new \App\Models\Notebook();
        $publicNotebooks = $notebookModel->getAllPublic();
        
        $this->render('admin/notebooks', [
            'title' => 'Quản lý Sổ tay chung',
            'notebooks' => $publicNotebooks
        ]);
    }

    public function updateRole(): void
    {
        header('Content-Type: application/json');
        if (!Auth::isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = (int)($input['user_id'] ?? 0);
        $newRole = $input['role'] ?? '';

        if (!$userId || !in_array($newRole, ['user', 'admin'])) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            return;
        }

        $userModel = new User();
        $targetUser = $userModel->findById($userId);

        if (!$targetUser) {
            echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại.']);
            return;
        }

        $currentUserId = Auth::id();

        // Check if removing admin role from oneself
        if ($userId === $currentUserId && $newRole === 'user') {
            // Check how many admins are left
            $allUsers = $userModel->getAllUsers();
            $adminCount = 0;
            foreach ($allUsers as $u) {
                if ($u['role'] === 'admin') {
                    $adminCount++;
                }
            }

            if ($adminCount <= 1) {
                echo json_encode(['success' => false, 'message' => 'Bạn là Admin duy nhất, không thể tự gỡ quyền của mình.']);
                return;
            }
        }

        $userModel->updateRole($userId, $newRole);
        echo json_encode(['success' => true, 'message' => 'Cập nhật quyền thành công.']);
    }

    public function resetPassword(): void
    {
        header('Content-Type: application/json');
        if (!Auth::isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = (int)($input['user_id'] ?? 0);
        $newPassword = trim($input['password'] ?? '');

        if (!$userId || strlen($newPassword) < 6) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự.']);
            return;
        }

        if ($userId === Auth::id()) {
            echo json_encode(['success' => false, 'message' => 'Bạn không thể tự cài lại mật khẩu của mình từ trang này.']);
            return;
        }

        $userModel = new User();
        $targetUser = $userModel->findById($userId);

        if (!$targetUser) {
            echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại.']);
            return;
        }

        $userModel->updatePassword($userId, $newPassword);
        echo json_encode(['success' => true, 'message' => 'Cài lại mật khẩu thành công.']);
    }
}
