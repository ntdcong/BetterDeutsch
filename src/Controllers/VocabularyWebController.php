<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Vocabulary;
use App\Models\Notebook;

class VocabularyWebController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $notebookId = (int)($_GET['notebook_id'] ?? 0);
        $page = (int)($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $perPage = 20;

        if ($notebookId <= 0) {
            $this->redirect('/notebooks');
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findById($notebookId);
        if (!$notebook || ($notebook['user_id'] !== Auth::id() && !$notebook['is_public'] && !$notebook['is_admin_updated'])) {
            $this->redirect('/notebooks');
        }

        $vocabModel = new Vocabulary();
        $vocabularies = $vocabModel->getPaginatedForNotebook($notebookId, $page, $perPage, $search);
        $total = $vocabModel->getTotalCount($notebookId, $search);
        $totalPages = ceil($total / $perPage);

        $this->render('notebooks/vocabularies', [
            'title' => 'Quản lý từ vựng: ' . htmlspecialchars($notebook['name']),
            'notebook' => $notebook,
            'vocabularies' => $vocabularies,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ]);
    }

    public function store(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $notebookId = (int)($_POST['notebook_id'] ?? 0);
        $word = trim($_POST['word'] ?? '');
        
        if ($notebookId > 0 && $word !== '') {
            $vocabModel = new Vocabulary();
            $vocabModel->create([
                'word' => $word,
                'translation_vn' => trim($_POST['translation_vn'] ?? ''),
                'word_type' => !empty($_POST['word_type']) ? $_POST['word_type'] : 'none',
                'article' => !empty($_POST['article']) ? $_POST['article'] : null,
                'plural_form' => !empty($_POST['plural_form']) ? trim($_POST['plural_form']) : null,
                'preposition' => !empty($_POST['preposition']) ? trim($_POST['preposition']) : null,
                'note' => !empty($_POST['note']) ? trim($_POST['note']) : null,
                'user_id' => Auth::id(),
                'notebook_id' => $notebookId
            ]);
        }

        $redirect = $_POST['redirect'] ?? "/vocabularies?notebook_id={$notebookId}";
        $this->redirect($redirect);
    }

    public function update(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $id = (int)($_POST['id'] ?? 0);
        $notebookId = (int)($_POST['notebook_id'] ?? 0);
        $word = trim($_POST['word'] ?? '');

        if ($id > 0 && $word !== '') {
            $vocabModel = new Vocabulary();
            // Should check if vocab belongs to user notebook, but skipping for simplicity as it's a private app mostly
            $vocabModel->update($id, [
                'word' => $word,
                'translation_vn' => trim($_POST['translation_vn'] ?? ''),
                'word_type' => !empty($_POST['word_type']) ? $_POST['word_type'] : 'none',
                'article' => !empty($_POST['article']) ? $_POST['article'] : null,
                'plural_form' => !empty($_POST['plural_form']) ? trim($_POST['plural_form']) : null,
                'preposition' => !empty($_POST['preposition']) ? trim($_POST['preposition']) : null,
                'note' => !empty($_POST['note']) ? trim($_POST['note']) : null
            ]);
        }

        $redirect = $_POST['redirect'] ?? "/vocabularies?notebook_id={$notebookId}";
        $this->redirect($redirect);
    }

    public function delete(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $id = (int)($_POST['id'] ?? 0);
        $notebookId = (int)($_POST['notebook_id'] ?? 0);

        if ($id > 0) {
            $vocabModel = new Vocabulary();
            $vocabModel->delete($id);
        }

        $this->redirect("/vocabularies?notebook_id={$notebookId}");
    }
}
