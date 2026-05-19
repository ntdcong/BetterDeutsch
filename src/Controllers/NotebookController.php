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
}
