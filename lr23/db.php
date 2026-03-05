<?php
// Проверка: включен ли модуль PDO на сервере
if (!class_exists('PDO')) {
    die("Ошибка: На вашем сервере MAMP отключено расширение PDO. Пожалуйста, включите его в настройках PHP или в файле php.ini (раскомментируйте pdo_mysql).");
}

$host = '127.0.0.1';
$port = '3306';
$db   = 'amatto_db';
$user = 'root';
$pass = '1234567890'; // Твой проверенный пароль
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    
    // Подключаемся
    $pdo = new PDO($dsn, $user, $pass);
    
    // Устанавливаем настройки (используем строки вместо констант для надежности)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Ошибка подключения к базе: " . $e->getMessage());
}
?>