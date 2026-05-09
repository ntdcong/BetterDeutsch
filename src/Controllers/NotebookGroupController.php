<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\NotebookGroup;

class NotebookGroupController extends Controller
{
    public function store(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name !== '') {
            $groupModel = new NotebookGroup();
            $groupModel->create(Auth::id(), $name);
        }

        $this->redirect('/notebooks');
    }

    public function delete(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $groupModel = new NotebookGroup();
            $groupModel->delete($id, Auth::id());
        }

        $this->redirect('/notebooks');
    }

    public function update(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($id > 0 && $name !== '') {
            $groupModel = new NotebookGroup();
            $groupModel->update($id, Auth::id(), $name);
        }

        $this->redirect('/notebooks');
    }
}
