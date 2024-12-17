<?php
require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // Pastikan request method yang digunakan adalah GET
    $stmt = $pdo->prepare("SELECT id, name, price FROM menu");
    $stmt->execute();
    
    $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mengembalikan data dalam format JSON
    echo json_encode($menu);
}
?>
