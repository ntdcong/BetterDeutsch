<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Models\Vocabulary;

class VocabularyController
{
    public function getByNotebook(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $notebookId = (int)($_GET['notebook_id'] ?? 0);
        if ($notebookId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid notebook ID']);
            return;
        }

        $vocabModel = new Vocabulary();
        $vocabularies = $vocabModel->getByNotebookId($notebookId);

        echo json_encode(['data' => $vocabularies]);
    }
}
