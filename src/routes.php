<?php
declare(strict_types=1);

/** @var \App\Core\Router $router */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\NotebookController;
use App\Controllers\Api\VocabularyController;
use App\Controllers\Api\VerbController;

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'showRegisterForm']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/notebooks', [NotebookController::class, 'index']);
$router->get('/notebooks/flashcard', [NotebookController::class, 'flashcard']);

$router->get('/api/vocabularies', [VocabularyController::class, 'getByNotebook']);
$router->get('/api/verb', [VerbController::class, 'getVerb']);
