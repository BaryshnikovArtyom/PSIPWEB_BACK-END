<?php
session_start();
require_once 'db.php'; 


// ФИЛЬТРАЦИЯ, ПОИСК И СОРТИРОВКА
$cat = isset($_GET['cat']) ? $_GET['cat'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_desc';
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; 

$sql = "SELECT * FROM products WHERE 1=1"; 
$params = [];

if ($cat !== 'all') { 
    $sql .= " AND category = ?"; 
    $params[] = $cat; 
}

if ($search !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%"; // Ищем вхождение текста
}

if ($sort === 'price_asc') $sql .= " ORDER BY price ASC";
elseif ($sort === 'price_desc') $sql .= " ORDER BY price DESC";
else $sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products_list = $stmt->fetchAll();

$role = $_SESSION['role'] ?? 'guest';
$user_name = $_SESSION['user_name'] ?? 'Гость';

// ДАННЫЕ ПОЛЬЗОВАТЕЛЯ
$is_logged = isset($_SESSION['role']);
$role = $is_logged ? $_SESSION['role'] : 'guest'; 
$user_name = $is_logged ? $_SESSION['user_name'] : 'Гость';
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amatto - Премиальные Кухни</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Commissioner:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        body { background-color: #f8f9fa; font-family: 'Commissioner', sans-serif; }
        .hero-title { font-family: 'Playfair Display', serif; font-weight: 800; }
        
        /* ИДЕАЛЬНЫЕ КАРТОЧКИ ТОВАРОВ */
        .portfolio-item { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #f0f0f0; transition: 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .portfolio-item:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .portfolio-img-wrapper { height: 260px; width: 100%; position: relative; background: #eaeaea; }
        .portfolio-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s; }
        .portfolio-item:hover .portfolio-img-wrapper img { transform: scale(1.05); }
        .portfolio-category-badge { position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.9); padding: 5px 15px; border-radius: 30px; font-size: 12px; font-weight: 600; color: #000; backdrop-filter: blur(5px); }
        
        /* ФИЛЬТРЫ */
        .filter-link { text-decoration: none; color: #555; padding: 10px 25px; border-radius: 50px; border: 1px solid #ddd; transition: 0.3s; font-weight: 600; background: #fff; }
        .filter-link.active, .filter-link:hover { background: #FFD700; color: #000; border-color: #FFD700; }
        
        /* МОДАЛКИ */
        .modal-content { border-radius: 30px; border: none; }
        .admin-table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        #scrollProgress { position: fixed; top: 0; left: 0; height: 4px; background: #FFD700; z-index: 9999; }
    </style>
</head>

<body>

<div id="scrollProgress"></div>

<!-- УВЕДОМЛЕНИЯ -->
<?php if(isset($_GET['order']) && $_GET['order'] == 'wait'): ?>
    <div class="alert alert-warning text-center m-0 border-0 rounded-0 py-3 shadow-sm sticky-top" style="z-index: 1050;">
        <i class="fa fa-clock me-2"></i> Ваш заказ оформлен и отправлен администратору на подтверждение! Следите за статусом в Личном кабинете.
        <button type="button" class="btn-close ms-3" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- ПАНЕЛЬ АДМИНА -->
<?php if ($role === 'admin'): ?>
<div class="bg-dark text-warning p-2 text-center shadow-sm">
    <small><i class="fa fa-shield-alt"></i> <b>CMS AMATTO</b> | Администратор: <?= $user_name ?> </small>
    <button class="btn btn-sm btn-warning ms-3 fw-bold rounded-pill px-4 py-0" data-bs-toggle="modal" data-bs-target="#profileModal">КОНСОЛЬ</button>
</div>
<?php endif; ?>

<!-- НАВИГАЦИЯ -->
<nav class="navbar navbar-expand-lg bg-white py-3 shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><img src="https://c.animaapp.com/mb6e6uyr72SZ5R/img/logo-en-black-2-1.png" height="40" alt="Amatto"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link fw-bold text-dark" href="#">Главная</a></li>
        <li class="nav-item"><a class="nav-link" href="#catalogSection">Каталог</a></li>
        <li class="nav-item"><a class="nav-link" href="#installmentSection">Рассрочка</a></li>
        <li class="nav-item"><a class="nav-link text-primary fw-bold" href="tasks.php">ЛР Задания</a></li>
        
        <?php if ($is_logged): ?>
            <li class="nav-item dropdown ms-lg-3">
                <a class="nav-link dropdown-toggle text-success fw-bold" href="#" data-bs-toggle="dropdown">
                    <i class="fa fa-user-circle"></i> <?= $user_name ?>
                </a>
                <ul class="dropdown-menu border-0 shadow-lg rounded-4 mt-2">
                    <li><a class="dropdown-item py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fa fa-sliders-h me-2"></i> Личный Кабинет</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger py-2" href="auth.php?action=logout"><i class="fa fa-sign-out-alt me-2"></i> Выйти</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item ms-lg-3">
                <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#guestModal">Войти</button>
            </li>
        <?php endif; ?>
      </ul>
      <div class="d-flex ms-lg-3 align-items-center">
        <button type="button" class="btn btn-outline-dark position-relative ms-2 rounded-circle" id="cartBtn" style="width:45px; height:45px;">
            <i class="fa fa-shopping-cart"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount">0</span>
        </button>
      </div>
    </div>
  </div>
</nav>

<main>
    <!-- ГЕРОЙ СЛАЙДЕР -->
    <div id="heroCarousel" class="hero-carousel-wrapper">
      <div class="slides-wrapper">
        <div class="slide active" style="background-image: linear-gradient(rgba(0,0,0,0.4),rgba(0,0,0,0.4)), url('image/SL1.jpg');"></div>
        <div class="slide" style="background-image: linear-gradient(rgba(0,0,0,0.4),rgba(0,0,0,0.4)), url('image/SL2.jpg');"></div>
      </div>
      <div class="carousel-caption text-start">
        <h1 class="hero-title display-2 mb-3 text-white">КУХНИ НА ЗАКАЗ</h1>
        <p class="fs-4 mb-4 text-white">Стильная кухня от производителя по цене на 20% ниже рынка</p>
        <ul class="benefits-list list-unstyled fs-5 text-white mb-5">
          <li class="mb-2"><i class="fa fa-check text-warning me-2"></i> Бесплатный дизайн проект</li>
          <li class="mb-2"><i class="fa fa-check text-warning me-2"></i> Гарантия лучшей Цены</li>
          <li class="mb-2"><i class="fa fa-check text-warning me-2"></i> Производство от 25 дней</li>
        </ul>
        <button class="btn btn-warning btn-lg rounded-pill px-5 py-3 fw-bold shadow" onclick="document.getElementById('catalogSection').scrollIntoView({behavior:'smooth'})">В КАТАЛОГ</button>
      </div>
    </div>

    <!-- КРАСИВЫЙ КАТАЛОГ С ФИЛЬТРАМИ -->
    <section class="py-5" id="catalogSection">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold display-4 hero-title text-dark">Каталог Кухонь</h2>
                <div style="width: 60px; height: 4px; background: #FFD700; margin: 15px auto;"></div>
            </div>

            <!-- ПАНЕЛЬ ФИЛЬТРОВ -->
           <div class="filter-bar d-flex flex-wrap justify-content-between align-items-center gap-3">
    <!-- Поиск -->
    <div class="search-box flex-grow-1" style="max-width: 400px;">
        <form action="index.php#catalogSection" method="GET" class="d-flex">
            <input type="hidden" name="cat" value="<?= $cat ?>">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input type="text" name="search" class="form-control search-input" placeholder="Поиск кухни по названию..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-dark ms-2"><i class="fa fa-search"></i></button>
        </form>
    </div>

    <!-- Кнопки категорий -->
    <div class="d-flex gap-2">
        <a href="index.php?cat=all&sort=<?= $sort ?>#catalogSection" class="filter-link <?= $cat=='all'?'active':'' ?>">Все</a>
        <a href="index.php?cat=Modern&sort=<?= $sort ?>#catalogSection" class="filter-link <?= $cat=='Modern'?'active':'' ?>">Modern</a>
        <a href="index.php?cat=Loft&sort=<?= $sort ?>#catalogSection" class="filter-link <?= $cat=='Loft'?'active':'' ?>">Loft</a>
    </div>

    <!-- Сортировка -->
    <select class="form-select border-0 bg-light rounded-pill px-4" style="width: auto;" onchange="location.href='index.php?cat=<?= $cat ?>&search=<?= $search ?>&sort='+this.value+'#catalogSection'">
        <option value="id_desc" <?= $sort=='id_desc'?'selected':'' ?>>Новинки</option>
        <option value="price_asc" <?= $sort=='price_asc'?'selected':'' ?>>Дешевле</option>
        <option value="price_desc" <?= $sort=='price_desc'?'selected':'' ?>>Дороже</option>
    </select>
</div>

            <!-- СЕТКА ТОВАРОВ -->
            <div class="row g-4">
                <?php if (empty($products_list)): ?>
                    <div class="col-12 text-center py-5">
                        <h4 class="text-muted">В этой категории пока нет товаров.</h4>
                    </div>
                <?php else: ?>
                    <?php foreach ($products_list as $row): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="portfolio-item h-100">
                            <div class="portfolio-img-wrapper">
                                <img src="<?= $row['image_path'] ?>" alt="Кухня">
                                <div class="portfolio-category-badge"><?= $row['category'] ?></div>
                            </div>
                            <div class="p-4 d-flex flex-column flex-grow-1 bg-white">
                                <h5 class="fw-bold text-dark mb-4"><?= htmlspecialchars($row['name']) ?></h5>
                                <div class="mt-auto d-flex justify-content-between align-items-end">
                                    <div>
                                        <small class="text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">СТОИМОСТЬ</small><br>
                                        <span class="fs-4 fw-bold text-warning"><?= number_format($row['price'], 0, '.', ' ') ?> L</span>
                                    </div>
                                    <button class="btn btn-dark rounded-pill px-4 py-2 fw-bold add-to-cart-action" 
                                            data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['name']) ?>" data-price="<?= $row['price'] ?>">
                                        В КОРЗИНУ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ПРЕИМУЩЕСТВА -->
    <section class="py-5 bg-white text-center">
        <div class="container py-4">
            <h2 class="fw-bold mb-5 hero-title">Наши преимущества</h2>
            <div class="row g-4">
                <div class="col-md-3"><div class="p-4 bg-light rounded-5 h-100 shadow-sm"><img src="image/NP1.png" width="60" class="mb-3"><h6 class="fw-bold">Лучшая Цена</h6></div></div>
                <div class="col-md-3"><div class="p-4 bg-light rounded-5 h-100 shadow-sm"><img src="image/NP2.png" width="60" class="mb-3"><h6 class="fw-bold">Рассрочка 0%</h6></div></div>
                <div class="col-md-3"><div class="p-4 bg-light rounded-5 h-100 shadow-sm"><img src="image/NP3.png" width="60" class="mb-3"><h6 class="fw-bold">Сроки от 25 дней</h6></div></div>
                <div class="col-md-3"><div class="p-4 bg-light rounded-5 h-100 shadow-sm"><img src="image/NP4.png" width="60" class="mb-3"><h6 class="fw-bold">Качество 5 лет</h6></div></div>
            </div>
        </div>
    </section>

    <!-- КАЛЬКУЛЯТОР -->
    <section class="py-5 bg-light" id="installmentSection">
      <div class="container py-5">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <div class="form-box p-5 bg-white shadow-lg rounded-5 border-top border-warning border-5">
              <h2 class="fw-bold mb-2">Покупай в рассрочку!</h2>
              <form action="mail_handler.php" method="POST">
                <input type="hidden" name="form_type" value="Заявка на рассрочку">
                <div class="mb-4">
                  <label class="form-label small fw-bold text-muted uppercase">Ваш Бюджет (€) :</label>
                  <input name="budget" id="budget" type="number" class="form-control form-control-lg bg-light border-0 shadow-none" value="3000">
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6"><input type="text" name="user_name" class="form-control bg-light border-0" placeholder="Ваше Имя" required></div>
                    <div class="col-6"><input type="tel" name="user_phone" class="form-control bg-light border-0" placeholder="Телефон" required></div>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold py-3 rounded-pill shadow-lg">ОТПРАВИТЬ ЗАЯВКУ</button>
              </form>
            </div>
          </div>
          <div class="col-lg-6 text-center"><img src="image/RS.png" alt="Promo" class="img-fluid rounded-5 shadow-2xl"></div>
        </div>
      </div>
    </section>
</main>

<footer class="bg-white py-5 text-center mt-auto border-top">
    <div class="container"><img src="https://c.animaapp.com/mb6e6uyr72SZ5R/img/logo-en-black-2-1.png" height="30" class="mb-4"><p class="text-muted small">&copy; 2024 Amatto Kitchens. Все права защищены.</p></div>
</footer>

<!-- ========================================== -->
<!-- МОДАЛЬНЫЕ ОКНА И ЛОГИКА ЗАКАЗОВ            -->
<!-- ========================================== -->

<!-- КОРЗИНА -->
<div class="modal fade" id="cartModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg p-3">
      <div class="modal-header border-0"><h4 class="fw-bold">🛒 Корзина</h4><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <table class="table align-middle"><tbody id="cartItemsContainer"></tbody></table>
        <div class="text-end pt-3 border-top mt-3"><h4 class="fw-bold">Итого: <span id="cartTotal" class="text-warning">0</span> лей</h4></div>
      </div>
      <div class="modal-footer border-0">
        <button class="btn btn-dark rounded-pill px-5 py-3 fw-bold w-100" id="btnInitCheckout">ПЕРЕЙТИ К ОФОРМЛЕНИЮ</button>
      </div>
    </div>
  </div>
</div>

<!-- ОФОРМЛЕНИЕ ЗАКАЗА -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-5 shadow-lg border-0">
        <h4 class="fw-bold text-center mb-4">Данные для заказа</h4>
        <form action="order_handler.php" method="POST">
            <input type="hidden" name="total_hidden" id="total_hidden">
            <div class="mb-3"><input type="text" name="orderName" class="form-control rounded-pill bg-light border-0 px-4" value="<?= htmlspecialchars($user_name) ?>" required placeholder="Имя"></div>
            <div class="mb-4"><input type="email" name="orderEmail" class="form-control rounded-pill bg-light border-0 px-4" value="<?= htmlspecialchars($user_email) ?>" required placeholder="Email"></div>
            <div class="alert alert-warning text-center fw-bold fs-4 rounded-4 border-0">К оплате: <span id="checkoutTotal">0</span></div>
            <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow">ОТПРАВИТЬ АДМИНИСТРАТОРУ</button>
        </form>
    </div>
  </div>
</div>

<!-- ВХОД / РЕГИСТРАЦИЯ -->
<div class="modal fade" id="guestModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content p-4 shadow-lg border-0">
      <ul class="nav nav-pills mb-4 justify-content-center bg-light p-1 rounded-pill" role="tablist">
          <li class="nav-item w-50 text-center"><button class="nav-link active w-100 rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#tabLogin">Вход</button></li>
          <li class="nav-item w-50 text-center"><button class="nav-link w-100 rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#tabReg">Рег.</button></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="tabLogin">
            <form action="auth.php" method="POST">
                <input type="email" name="loginEmail" class="form-control mb-3 rounded-pill bg-light border-0 px-3" placeholder="Email" required>
                <input type="password" name="loginPassword" class="form-control mb-4 rounded-pill bg-light border-0 px-3" placeholder="Пароль" required>
                <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">ВОЙТИ</button>
            </form>
        </div>
        <div class="tab-pane fade" id="tabReg">
             <form action="auth.php" method="POST">
                <input type="text" name="regName" class="form-control mb-2 rounded-pill bg-light border-0 px-3" placeholder="Имя" required>
                <input type="email" name="regEmail" class="form-control mb-2 rounded-pill bg-light border-0 px-3" placeholder="Email" required>
                <input type="password" name="regPassword" class="form-control mb-4 rounded-pill bg-light border-0 px-3" placeholder="Пароль" required>
                <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold">СОЗДАТЬ АККАУНТ</button>
             </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ЛИЧНЫЙ КАБИНЕТ (БД ЗАКАЗОВ) -->
<div class="modal fade" id="profileModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg overflow-hidden">
      <div class="modal-header bg-light border-0 px-4 pt-4">
        <h5 class="fw-bold"><?= ($role === 'admin') ? '👑 Консоль Управления' : '👤 Личный кабинет' ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 bg-light">
        
        <?php if ($role === 'admin'): ?>
            <ul class="nav nav-pills mb-4 bg-white p-1 rounded-pill shadow-sm" role="tablist">
                <li class="nav-item flex-fill"><button class="nav-link active rounded-pill w-100 fw-bold" data-bs-toggle="tab" data-bs-target="#adm1">Управление Каталогом</button></li>
                <li class="nav-item flex-fill"><button class="nav-link rounded-pill w-100 fw-bold" data-bs-toggle="tab" data-bs-target="#adm2">Заказы клиентов</button></li>
            </ul>
            <div class="tab-content">
                <!-- АДМИН: КАТАЛОГ -->
                <div class="tab-pane fade show active" id="adm1">
                    <form action="admin_handler.php" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded-4 shadow-sm border mb-4" id="adminProductForm">
                        <h6 class="fw-bold mb-3" id="adminFormTitle">Добавить кухню:</h6>
                        <input type="hidden" name="product_id" id="form_product_id">
                        <input type="hidden" name="current_image" id="form_current_image">
                        <div class="row g-2">
                            <div class="col-md-4"><input type="text" name="product_name" id="form_name" class="form-control" placeholder="Название" required></div>
                            <div class="col-md-3"><input type="number" name="product_price" id="form_price" class="form-control" placeholder="Цена" required></div>
                            <div class="col-md-3"><select name="product_category" id="form_category" class="form-select"><option value="Modern">Modern</option><option value="Loft">Loft</option><option value="Classic">Classic</option></select></div>
                            <div class="col-md-2"><input type="file" name="product_image" class="form-control" accept="image/*"></div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold mt-3 rounded-pill" id="formSubmitBtn">ОПУБЛИКОВАТЬ</button>
                        <button type="button" class="btn btn-light w-100 mt-2 rounded-pill" id="formCancelBtn" style="display:none;">Отменить редактирование</button>
                    </form>
                    
                    <div class="bg-white rounded-4 shadow-sm border p-2 table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-light"><tr><th class="ps-3">Фото</th><th>Название</th><th>Цена</th><th class="text-end pe-3">Действия</th></tr></thead>
                            <tbody>
                                <?php 
                                // Получаем ВСЕ товары для админки без фильтров
                                $admin_products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
                                foreach($admin_products as $p): ?>
                                <tr>
                                    <td class="ps-3"><img src="<?= $p['image_path'] ?>" class="admin-table-img"></td>
                                    <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                                    <td class="text-warning fw-bold"><?= $p['price'] ?> L</td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle btn-edit-product"
                                                data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                                data-price="<?= $p['price'] ?>" data-cat="<?= $p['category'] ?>" data-img="<?= htmlspecialchars($p['image_path'], ENT_QUOTES) ?>">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <a href="admin_handler.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('Удалить товар?')"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- АДМИН: ЗАКАЗЫ -->
                <div class="tab-pane fade" id="adm2">
                    <div class="bg-white p-4 rounded-4 shadow-sm border">
                        <table class="table align-middle">
                            <thead><tr><th>ID</th><th>Клиент</th><th>Сумма</th><th>Статус / Действие</th></tr></thead>
                            <tbody>
                                <?php $orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll(); foreach($orders as $o): ?>
                                <tr>
                                    <td>#<?= $o['id'] ?></td>
                                    <td><?= htmlspecialchars($o['customer_name']) ?> <br><small class="text-muted"><?= htmlspecialchars($o['customer_email']) ?></small></td>
                                    <td class="fw-bold"><?= $o['total_price'] ?> L</td>
                                    <td>
                                        <?php if($o['status'] === 'Новый'): ?>
                                            <a href="order_handler.php?action=confirm&id=<?= $o['id'] ?>" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">Подтвердить</a>
                                        <?php else: ?>
                                            <span class="badge bg-light text-success border">Подтвержден</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ИНТЕРФЕЙС КЛИЕНТА (ИСТОРИЯ ЕГО ЗАКАЗОВ) -->
            <div class="bg-white p-4 rounded-4 shadow-sm border mb-4">
                <h6 class="fw-bold border-bottom pb-3 mb-3">Мои Заказы</h6>
                <table class="table align-middle">
                    <thead><tr><th>№ Заказа</th><th>Сумма</th><th>Статус</th></tr></thead>
                    <tbody>
                        <?php 
                        // Ищем заказы конкретно этого клиента по email
                        $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_email = ? ORDER BY id DESC");
                        $stmt->execute([$user_email]);
                        $my_orders = $stmt->fetchAll();

                        if(empty($my_orders)): ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">Вы еще ничего не заказывали.</td></tr>
                        <?php else: 
                            foreach($my_orders as $o): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $o['id'] ?></td>
                                <td class="fw-bold"><?= $o['total_price'] ?> LEI</td>
                                <td>
                                    <?php if($o['status'] === 'Новый'): ?>
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Ожидает подтверждения</span>
                                    <?php else: ?>
                                        <span class="badge bg-success px-3 py-2 rounded-pill mb-1">Одобрено</span><br>
                                        <a href="receipt.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill mt-1" style="font-size: 11px;">СКАЧАТЬ ЧЕК PDF</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; 
                        endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
      </div>
      <div class="modal-footer bg-light border-0 px-4 pb-4">
        <a href="auth.php?action=logout" class="btn btn-danger w-100 rounded-pill py-2 fw-bold shadow">ВЫЙТИ ИЗ СИСТЕМЫ</a>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        
        // КОРЗИНА
        let cart =[];
        $('.add-to-cart-action').on('click', function(e) {
            e.preventDefault();
            let item = { id: $(this).data('id'), name: $(this).data('name'), price: $(this).data('price') };
            cart.push(item);
            updateUI();
            let btn = $(this);
            btn.text('✓ ДОБАВЛЕНО').addClass('btn-success').removeClass('btn-dark');
            setTimeout(() => btn.text('В КОРЗИНУ').removeClass('btn-success').addClass('btn-dark'), 1000);
        });

        function updateUI() {
            $('#cartCount').text(cart.length);
            let h = ''; let t = 0;
            cart.forEach(i => { t += parseInt(i.price); h += `<tr class="border-bottom"><td class="py-2 fw-bold small">${i.name}</td><td class="text-end text-warning fw-bold">${i.price} L</td></tr>`; });
            $('#cartItemsContainer').html(h || '<tr><td colspan="2" class="text-center py-4">Корзина пуста</td></tr>');
            $('#cartTotal').text(t);
            $('#checkoutTotal').text(t + ' LEI');
            $('#total_hidden').val(t);
        }

        $('#cartBtn').click(() => new bootstrap.Modal('#cartModal').show());
        
        $('#btnInitCheckout').click(function() {
            if(cart.length === 0) return alert('Ваша корзина пуста!');
            bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
            new bootstrap.Modal(document.getElementById('checkoutModal')).show();
        });

        // СКРИПТ РЕДАКТИРОВАНИЯ ДЛЯ АДМИНА
        $('.btn-edit-product').click(function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let price = $(this).data('price');
            let cat = $(this).data('cat');
            let img = $(this).data('img');

            $('#adminFormTitle').html('<i class="fa fa-edit text-primary"></i> Редактировать: ' + name);
            $('#formSubmitBtn').text('СОХРАНИТЬ ИЗМЕНЕНИЯ').removeClass('btn-warning').addClass('btn-primary');
            $('#formCancelBtn').show();
            
            $('#form_product_id').val(id);
            $('#form_current_image').val(img);
            $('#form_name').val(name);
            $('#form_price').val(price);
            $('#form_category').val(cat);
            
            document.getElementById('adminProductForm').scrollIntoView({behavior: 'smooth'});
        });
        
        $('#formCancelBtn').click(function() {
            $('#adminProductForm')[0].reset();
            $('#adminFormTitle').html('Добавить кухню:');
            $('#formSubmitBtn').text('ОПУБЛИКОВАТЬ').removeClass('btn-primary').addClass('btn-warning');
            $(this).hide();
            $('#form_product_id').val('');
            $('#form_current_image').val('');
        });

        // Прогресс бар
        $(window).scroll(function() {
            let winScroll = $(window).scrollTop();
            let height = $(document).height() - $(window).height();
            $('#scrollProgress').css('width', (winScroll / height) * 100 + "%");
        });
    });
</script>
</body>
</html>