<?php
require_once __DIR__ . '/../src/controllers/ItemController.php';
require_once __DIR__ . '/../src/db.php';

use App\controllers\ItemController;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$controller = new ItemController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';
$id = $_GET['id'] ?? null;

$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if ($id) {
            $controller->get($id);
        } else {
            $controller->getAll();
        }
        break;
    case 'POST':
        $controller->create($input);
        break;
    case 'PUT':
        if ($id) $controller->update($id, $input);
        break;
    case 'DELETE':
        if ($id) $controller->delete($id);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}