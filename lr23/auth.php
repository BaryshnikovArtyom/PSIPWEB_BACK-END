<?php
session_start();
require_once 'db.php';

// ВЫХОД
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- ВХОД (Рабочий вариант) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loginEmail'])) {
    $email = trim($_POST['loginEmail']);
    $pass  = $_POST['loginPassword'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        // УСПЕХ: Записываем данные в сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Перекидываем на главную
        header("Location: index.php");
        exit;
    } else {
        // ОШИБКА: Выводим алерт и возвращаем назад
        echo "<script>alert('Неверный логин или пароль!'); window.location.href='index.php';</script>";
        exit;
    }
}

// РЕГИСТРАЦИЯ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['regEmail'])) {
    $name = trim($_POST['regName']);
    $email = trim($_POST['regEmail']);
    $password = password_hash($_POST['regPassword'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')");
        $stmt->execute([$name, $email, $password]);
        echo "<script>alert('Регистрация успешна! Теперь войдите.'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Ошибка: такой Email уже есть.'); window.location.href='index.php';</script>";
    }
}