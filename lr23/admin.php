<?php
session_start();
require_once 'db.php';
if ($_SESSION['role'] !== 'admin') header("Location: index.php"); // Выкидываем не-админов

// Получаем все товары
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Панель управления Amatto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #1d2327; color: #fff; padding-top: 20px; flex-shrink: 0; }
        .sidebar a { color: #f0f0f1; text-decoration: none; padding: 12px 20px; display: block; }
        .sidebar a:hover { background: #2c3338; color: #72aee6; }
        .sidebar a.active { background: #2271b1; color: #fff; }
        .main-content { flex-grow: 1; padding: 30px; }
        .wp-card { background: #fff; border: 1px solid #ccd0d4; padding: 20px; border-radius: 4px; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>

<!-- Боковое меню в стиле WP -->
<div class="sidebar">
    <div class="px-4 mb-4">
        <h4 class="fw-bold">Amatto <span class="text-info">CMS</span></h4>
    </div>
    <a href="index.php"><i class="fa fa-home me-2"></i> На сайт</a>
    <a href="admin.php" class="active"><i class="fa fa-box me-2"></i> Каталог кухонь</a>
    <a href="tasks.php"><i class="fa fa-list me-2"></i> Задания</a>
    <a href="auth.php?action=logout" class="text-danger mt-5"><i class="fa fa-sign-out-alt me-2"></i> Выйти</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Управление каталогом</h2>
        <!-- Кнопка вызывает модалку добавления, которую мы сделали раньше -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Добавить новую</button>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success py-2">Действие выполнено успешно!</div>
    <?php endif; ?>

    <div class="wp-card shadow-sm">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Фото</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена (лей)</th>
                    <th class="text-end">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><img src="<?= $p['image_path'] ?>" class="product-img"></td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><span class="badge bg-secondary"><?= $p['category'] ?></span></td>
                    <td><?= number_format($p['price'], 0, '.', ' ') ?></td>
                    <td class="text-end">
                        <!-- Кнопка Удалить -->
                        <a href="admin_actions.php?delete_id=<?= $p['id'] ?>" 
                           class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('Удалить эту кухню навсегда?')">
                           <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Модалка добавления (перенесли сюда для удобства) -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="admin_handler.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header"><h5>Добавить кухню</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="text" name="product_name" class="form-control mb-2" placeholder="Название" required>
            <input type="number" name="product_price" class="form-control mb-2" placeholder="Цена" required>
            <select name="product_category" class="form-select mb-2">
                <option value="Modern">Modern</option>
                <option value="Loft">Loft</option>
            </select>
            <input type="file" name="product_image" class="form-control" required>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary w-100">Опубликовать</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>