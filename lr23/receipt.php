<?php
session_start();
require_once 'db.php';
$id = $_GET['id'] ?? die('Заказ не найден');
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND status = 'Подтвержден'");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) die('Чек недоступен. Заказ еще не подтвержден администратором.');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><title>Чек №<?= $order['id'] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #eee; padding: 40px; font-family: monospace; }
        .ticket { background: #fff; padding: 40px; max-width: 500px; margin: auto; border: 1px dashed #000; }
        @media print { .btn { display: none; } body { background: #fff; padding: 0; } .ticket { border: none; } }
    </style>
</head>
<body>
    <div class="ticket">
        <h2 class="text-center fw-bold">AMATTO KITCHENS</h2>
        <p class="text-center small">Кишинев, ул. Трандафирилор 15/2</p>
        <hr>
        <p><b>ЗАКАЗ №:</b> <?= $order['id'] ?></p>
        <p><b>ДАТА:</b> <?= $order['created_at'] ?></p>
        <p><b>КЛИЕНТ:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
        <hr>
        <table class="table table-borderless">
            <tr><td>Кухонный гарнитур (комплект)</td><td class="text-end"><?= $order['total_price'] ?> L</td></tr>
        </table>
        <hr>
        <h3 class="text-end fw-bold">ИТОГО: <?= $order['total_price'] ?> LEI</h3>
        <p class="text-center mt-5">Спасибо за покупку!</p>
    </div>
    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-dark">Скачать в PDF</button> 
        <a href="index.php" class="btn btn-link">Вернуться на сайт</a>
    </div>
</body>
</html>