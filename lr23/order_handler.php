<?php
session_start();
require_once 'db.php';

// Оформление нового заказа клиентом
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['total_hidden'])) {
    $name = $_POST['orderName'];
    $email = $_POST['orderEmail'];
    $total = (int)$_POST['total_hidden'];

    if ($total > 0) {
        // Статус по умолчанию будет 'Новый'
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, total_price, status) VALUES (?, ?, ?, 'Новый')");
        $stmt->execute([$name, $email, $total]);
        
        // Перекидываем на главную с сообщением об ожидании
        header("Location: index.php?order=wait");
    } else {
        header("Location: index.php?order=error");
    }
    exit;
}

// Админ подтверждает заказ
if (isset($_GET['action']) && $_GET['action'] === 'confirm') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Подтвержден' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=confirmed");
    exit;
}
?>