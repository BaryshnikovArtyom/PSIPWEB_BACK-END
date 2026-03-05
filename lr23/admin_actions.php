<?php
session_start();
require_once 'db.php';

// Проверка прав
if ($_SESSION['role'] !== 'admin') exit('Доступ запрещен');

// --- УДАЛЕНИЕ ТОВАРА ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Сначала найдем путь к картинке, чтобы удалить файл с диска
    $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product && file_exists($product['image_path'])) {
        unlink($product['image_path']); // Удаляем само фото из папки image/
    }

    // Удаляем запись из БД
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: admin.php?msg=deleted");
    exit;
}

// --- ИЗМЕНЕНИЕ ЦЕНЫ (Быстрое редактирование) ---
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
    $stmt->execute([$name, $price, $id]);
    
    header("Location: admin.php?msg=updated");
    exit;
}