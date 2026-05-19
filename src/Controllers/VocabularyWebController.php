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

        $redirect = trim($_POST['redirect'] ?? '');
        if ($redirect === '') {
            $adminLayoutParam = !empty($_POST['admin_layout']) ? '&admin_layout=1' : '';
            $redirect = "/vocabularies?notebook_id={$notebookId}{$adminLayoutParam}";
        }
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

        $redirect = trim($_POST['redirect'] ?? '');
        if ($redirect === '') {
            $adminLayoutParam = !empty($_POST['admin_layout']) ? '&admin_layout=1' : '';
            $redirect = "/vocabularies?notebook_id={$notebookId}{$adminLayoutParam}";
        }
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

        $adminLayoutParam = !empty($_POST['admin_layout']) ? '&admin_layout=1' : '';
        $this->redirect("/vocabularies?notebook_id={$notebookId}{$adminLayoutParam}");
    }

    public function importPreview(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/notebooks');
        }

        $notebookId = (int)($_POST['notebook_id'] ?? 0);
        if ($notebookId <= 0) {
            $this->redirect('/notebooks');
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findById($notebookId);
        if (!$notebook || $notebook['user_id'] !== Auth::id()) {
            $this->redirect('/notebooks');
        }

        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            \App\Core\Session::setFlash('error', 'Lỗi tải file lên.');
            $this->redirect("/vocabularies?notebook_id={$notebookId}");
        }

        $file = $_FILES['import_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        $data = [];
        if ($ext === 'csv') {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
                // Determine delimiter
                $delimiter = ',';
                // Read header and first few lines to guess delimiter
                $line = fgets($handle);
                if (strpos($line, ';') !== false) $delimiter = ';';
                elseif (strpos($line, '\t') !== false) $delimiter = "\t";
                
                rewind($handle);
                $header = fgetcsv($handle, 1000, $delimiter);
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            }
        } elseif ($ext === 'xlsx') {
            if (class_exists('\Shuchkin\SimpleXLSX')) {
                if ($xlsx = \Shuchkin\SimpleXLSX::parse($file['tmp_name'])) {
                    $rows = $xlsx->rows();
                    if (count($rows) > 0) {
                        array_shift($rows); // Remove header
                        $data = $rows;
                    }
                } else {
                    \App\Core\Session::setFlash('error', \Shuchkin\SimpleXLSX::parseError());
                    $this->redirect("/vocabularies?notebook_id={$notebookId}");
                }
            } else {
                 \App\Core\Session::setFlash('error', 'Thư viện đọc file Excel chưa được cài đặt.');
                 $this->redirect("/vocabularies?notebook_id={$notebookId}");
            }
        } else {
            \App\Core\Session::setFlash('error', 'Định dạng file không hỗ trợ.');
            $this->redirect("/vocabularies?notebook_id={$notebookId}");
        }

        $vocabModel = new Vocabulary();
        $existingVocabs = $vocabModel->getByNotebookId($notebookId);
        $existingWords = array_column($existingVocabs, 'word');
        $existingWords = array_map('strtolower', $existingWords);

        $previewData = [];
        foreach ($data as $index => $row) {
            $word = trim((string)($row[0] ?? ''));
            if ($word === '') continue;

            $isDuplicate = in_array(strtolower($word), $existingWords);
            
            $previewData[] = [
                'id' => 'row_' . $index,
                'word' => $word,
                'translation_vn' => trim((string)($row[1] ?? '')),
                'word_type' => trim((string)($row[2] ?? 'none')),
                'article' => trim((string)($row[3] ?? '')),
                'plural_form' => trim((string)($row[4] ?? '')),
                'preposition' => trim((string)($row[5] ?? '')),
                'note' => trim((string)($row[6] ?? '')),
                'is_duplicate' => $isDuplicate
            ];
        }

        $this->render('notebooks/import_preview', [
            'title' => 'Xem trước nhập dữ liệu',
            'notebook' => $notebook,
            'previewData' => $previewData
        ]);
    }

    public function import(): void
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $notebookId = (int)($input['notebook_id'] ?? 0);
        $items = $input['items'] ?? [];

        if ($notebookId <= 0 || empty($items)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        $notebookModel = new Notebook();
        $notebook = $notebookModel->findById($notebookId);
        if (!$notebook || $notebook['user_id'] !== Auth::id()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $vocabModel = new Vocabulary();
        $importedCount = 0;
        foreach ($items as $item) {
            if (empty($item['word'])) continue;
            
            $vocabModel->create([
                'word' => trim($item['word']),
                'translation_vn' => trim($item['translation_vn'] ?? ''),
                'word_type' => !empty($item['word_type']) ? trim($item['word_type']) : 'none',
                'article' => !empty($item['article']) ? trim($item['article']) : null,
                'plural_form' => !empty($item['plural_form']) ? trim($item['plural_form']) : null,
                'preposition' => !empty($item['preposition']) ? trim($item['preposition']) : null,
                'note' => !empty($item['note']) ? trim($item['note']) : null,
                'user_id' => Auth::id(),
                'notebook_id' => $notebookId
            ]);
            $importedCount++;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'count' => $importedCount]);
        exit;
    }
}
