<?php
session_start();
require_once 'db.php';

// Проверка прав администратора
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен");
}

// --- 1. УДАЛЕНИЕ ТОВАРА ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Сначала найдем путь к фото, чтобы удалить его из папки image/
    $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product && file_exists($product['image_path'])) {
        unlink($product['image_path']); // Удаляем файл
    }

    // Удаляем запись из MySQL
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php?msg=deleted");
    exit;
}

// --- 2. ДОБАВЛЕНИЕ ИЛИ РЕДАКТИРОВАНИЕ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $category = $_POST['product_category'];
    $id = isset($_POST['product_id']) ? $_POST['product_id'] : null;

    // Работа с картинкой (если загружена новая)
    $image_path = isset($_POST['current_image']) ? $_POST['current_image'] : '';
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $uploaddir = 'image/';
        $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . rand(100, 999) . '.' . $file_extension;
        $target_file = $uploaddir . $new_filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            // Если была старая картинка при редактировании - удаляем её
            if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                unlink($_POST['current_image']);
            }
            $image_path = $target_file;
        }
    }

    if ($id) {
        // РЕДАКТИРОВАНИЕ СУЩЕСТВУЮЩЕГО
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$name, $price, $category, $image_path, $id]);
        header("Location: index.php?msg=updated");
    } else {
        // ДОБАВЛЕНИЕ НОВОГО
        $stmt = $pdo->prepare("INSERT INTO products (name, price, category, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $category, $image_path]);
        header("Location: index.php?upload=success");
    }
    exit;
}