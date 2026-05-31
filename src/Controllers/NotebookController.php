<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Notebook;

class NotebookController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $notebookModel = new Notebook();
        $notebooks = $notebookModel->getAllForUser(Auth::id());

        $groupModel = new \App\Models\NotebookGroup();
        $groups = $groupModel->getAllForUser(Auth::id());

        $this->render('notebooks/index', [
            'title' => 'Sổ tay từ vựng',
            'notebooks' => $notebooks,
            'groups' => $groups
        ]);
    }

    public function store(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $name = trim($_POST['name'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $groupId = !empty($_POST['notebook_group_id']) ? (int)$_POST['notebook_group_id'] : null;

        $isPublic = 0;
        $isAdminUpdated = 0;
        if (Auth::isAdmin() && !empty($_POST['is_public'])) {
            $isPublic = 1;
            $isAdminUpdated = 1;
        }

        if ($name !== '') {
            $notebookModel = new Notebook();
            $notebookModel->create(Auth::id(), $name, $note, $groupId, $isPublic, $isAdminUpdated);
        }

        $redirect = trim($_POST['redirect'] ?? '/notebooks');
        if ($redirect === '') $redirect = '/notebooks';
        $this->redirect($redirect);
    }

    public function update(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $groupId = !empty($_POST['notebook_group_id']) ? (int)$_POST['notebook_group_id'] : null;

        $isPublic = 0;
        $isAdminUpdated = 0;
        if (Auth::isAdmin() && !empty($_POST['is_public'])) {
            $isPublic = 1;
            $isAdminUpdated = 1;
        }

        if ($id > 0 && $name !== '') {
            $notebookModel = new Notebook();
            $notebookModel->update($id, Auth::id(), $name, $note, $groupId, $isPublic, $isAdminUpdated, Auth::isAdmin());
        }

        $redirect = trim($_POST['redirect'] ?? '/notebooks');
        if ($redirect === '') $redirect = '/notebooks';
        $this->redirect($redirect);
    }

    public function delete(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $notebookModel = new Notebook();
            $notebookModel->delete($id, Auth::id(), Auth::isAdmin());
        }

        $redirect = trim($_POST['redirect'] ?? '/notebooks');
        if ($redirect === '') $redirect = '/notebooks';
        $this->redirect($redirect);
    }

    public function flashcard(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $notebookId = (int)($_GET['id'] ?? 0);
        if ($notebookId <= 0) {
            $this->redirect('/notebooks');
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findById($notebookId);
        if (!$notebook) {
            $this->redirect('/notebooks');
        }

        $this->render('notebooks/flashcard', [
            'title' => 'Học Flashcard: ' . htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8'),
            'notebook' => $notebook
        ]);
    }

    public function practice(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $notebookId = (int)($_GET['id'] ?? 0);
        if ($notebookId <= 0) {
            $this->redirect('/notebooks');
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findById($notebookId);
        if (!$notebook) {
            $this->redirect('/notebooks');
        }

        $this->render('notebooks/practice', [
            'title' => 'Luyện tập: ' . htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8'),
            'notebook' => $notebook
        ]);
    }

    public function toggleShare(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json');
        
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid notebook']);
            return;
        }

        $notebookModel = new Notebook();
        $token = $notebookModel->toggleShare($id, Auth::id());

        if ($token === null) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            return;
        }

        echo json_encode([
            'success' => true,
            'is_shared' => $token !== "",
            'share_token' => $token
        ]);
    }

    public function sharedIndex(): void
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirect('/');
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findByShareToken($token);

        if (!$notebook) {
            echo "Đường dẫn không hợp lệ hoặc sổ tay đã bị tắt chia sẻ.";
            return;
        }

        $this->render('notebooks/shared_index', [
            'title' => 'Chia sẻ: ' . htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8'),
            'notebook' => $notebook,
            'shareToken' => $token
        ]);
    }

    public function sharedFlashcard(): void
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirect('/');
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findByShareToken($token);

        if (!$notebook) {
            // Either invalid token or sharing is disabled
            echo "Đường dẫn không hợp lệ hoặc sổ tay đã bị tắt chia sẻ.";
            return;
        }

        $this->render('notebooks/flashcard', [
            'title' => 'Học Flashcard: ' . htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8'),
            'notebook' => $notebook,
            'isSharedView' => true,
            'shareToken' => $token
        ]);
    }

    public function sharedPractice(): void
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirect('/');
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findByShareToken($token);

        if (!$notebook) {
            echo "Đường dẫn không hợp lệ hoặc sổ tay đã bị tắt chia sẻ.";
            return;
        }

        $this->render('notebooks/practice', [
            'title' => 'Luyện tập: ' . htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8'),
            'notebook' => $notebook,
            'isSharedView' => true,
            'shareToken' => $token
        ]);
    }
}
