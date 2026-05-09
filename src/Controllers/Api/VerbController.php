<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Models\Verb;

class VerbController
{
    public function getVerb(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $word = $_GET['word'] ?? '';
        if (empty($word)) {
            http_response_code(400);
            echo json_encode(['error' => 'Word is required']);
            return;
        }

        $searchWord = trim(str_replace('sich ', '', $word));

        $verbModel = new Verb();
        $verb = $verbModel->findByInfinitive($searchWord);

        if ($verb) {
            echo json_encode(['data' => $verb]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Verb not found']);
        }
    }
}
