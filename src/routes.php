<?php
declare(strict_types=1);

/** @var \App\Core\Router $router */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\NotebookController;
use App\Controllers\NotebookGroupController;
use App\Controllers\VocabularyWebController;
use App\Controllers\Api\VocabularyController;
use App\Controllers\Api\VerbController;
use App\Controllers\AdminController;

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'showRegisterForm']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/notebooks', [NotebookController::class, 'index']);
$router->post('/notebooks/create', [NotebookController::class, 'store']);
$router->post('/notebooks/update', [NotebookController::class, 'update']);
$router->post('/notebooks/delete', [NotebookController::class, 'delete']);
$router->get('/notebooks/flashcard', [NotebookController::class, 'flashcard']);
$router->get('/notebooks/practice', [NotebookController::class, 'practice']);

$router->post('/notebook-groups/create', [NotebookGroupController::class, 'store']);
$router->post('/notebook-groups/update', [NotebookGroupController::class, 'update']);
$router->post('/notebook-groups/delete', [NotebookGroupController::class, 'delete']);

$router->get('/vocabularies', [VocabularyWebController::class, 'index']);
$router->post('/vocabularies/create', [VocabularyWebController::class, 'store']);
$router->post('/vocabularies/update', [VocabularyWebController::class, 'update']);
$router->post('/vocabularies/delete', [VocabularyWebController::class, 'delete']);
$router->post('/vocabularies/import-preview', [VocabularyWebController::class, 'importPreview']);
$router->post('/vocabularies/import', [VocabularyWebController::class, 'import']);

$router->get('/api/vocabularies', [VocabularyController::class, 'getByNotebook']);
$router->get('/api/verb', [VerbController::class, 'getVerb']);

$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/notebooks', [AdminController::class, 'notebooks']);
$router->post('/admin/update-role', [AdminController::class, 'updateRole']);
$router->post('/admin/reset-password', [AdminController::class, 'resetPassword']);

