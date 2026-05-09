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

        $this->render('notebooks/index', [
            'title' => 'Sổ tay từ vựng',
            'notebooks' => $notebooks
        ]);
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
}
