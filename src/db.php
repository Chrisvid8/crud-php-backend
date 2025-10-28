<?php
// Enable CORS for frontend requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection using environment variables
$host = $_ENV['DB_HOST'] ?? 'sql201.byetcluster.com';
$db   = $_ENV['DB_NAME'] ?? 'if0_40278149_crudphp';
$user = $_ENV['DB_USER'] ?? 'if0_40278149';
$pass = $_ENV['DB_PASS'] ?? 'your-vPanel-password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Determine HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Parse input for POST/PUT
$input = json_decode(file_get_contents('php://input'), true);

// Get item id from query string if present
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// CRUD Operations
switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
        } else {
            $stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
            $data = $stmt->fetchAll();
        }
        echo json_encode($data);
        break;

    case 'POST':
        $stmt = $pdo->prepare("INSERT INTO items (name, description, price) VALUES (?, ?, ?)");
        $stmt->execute([$input['name'], $input['description'], $input['price']]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        if (!$id) {
            echo json_encode(['error' => 'ID required for update']);
            break;
        }
        $stmt = $pdo->prepare("UPDATE items SET name = ?, description = ?, price = ? WHERE id = ?");
        $stmt->execute([$input['name'], $input['description'], $input['price'], $id]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(['error' => 'ID required for delete']);
            break;
        }
        $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'OPTIONS':
        // Preflight request for CORS
        http_response_code(200);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
