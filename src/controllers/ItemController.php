<?php
namespace App\controllers;

require_once __DIR__ . '/../db.php';

class ItemController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM items ORDER BY id DESC");
        echo json_encode($stmt->fetchAll());
    }

    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO items (name, description, price) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['description'], $data['price']]);
        echo json_encode(['id' => $this->pdo->lastInsertId()]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE items SET name = ?, description = ?, price = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['description'], $data['price'], $id]);
        echo json_encode(['updated' => $stmt->rowCount()]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
    }
}