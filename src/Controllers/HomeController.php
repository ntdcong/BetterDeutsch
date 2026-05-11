<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $userId = \App\Core\Auth::id();
        $stats = null;
        if ($userId) {
            $db = (new \App\Core\Database())->getConnection();
            
            // Get total notebooks
            $stmt = $db->prepare("SELECT COUNT(*) FROM notebooks WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $totalNotebooks = (int)$stmt->fetchColumn();

            // Get total vocabularies
            $stmt = $db->prepare("SELECT COUNT(*) FROM vocabularies WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $totalVocabularies = (int)$stmt->fetchColumn();
            
            // Get username
            $stmt = $db->prepare("SELECT name FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $username = $stmt->fetchColumn() ?: 'Học viên';

            $stats = [
                'total_notebooks' => $totalNotebooks,
                'total_vocabularies' => $totalVocabularies,
                'username' => $username,
            ];
        }

        $this->render('home', [
            'title' => 'Trang chủ - BetterDeutsch',
            'stats' => $stats
        ]);
    }
}
