<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1'; 
$port = '3307';      // Твой порт из Workbench
$db   = 'amatto_db';
$user = 'root';      // В 99% случаев это root
$pass = 'root';      // В MAMP на Windows это root или пусто ''

echo "Попытка подключения к $host:$port... <br>";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "<h1 style='color:green'>УСПЕХ! База данных подключена!</h1>";
} catch (PDOException $e) {
    echo "<h1 style='color:red'>ОШИБКА ПОДКЛЮЧЕНИЯ:</h1>";
    echo $e->getMessage();
}
?>