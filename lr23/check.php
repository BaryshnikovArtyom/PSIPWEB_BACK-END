<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Тестируем расширенный список настроек:</h3>";

$configs = [
    ['port' => '3306', 'pass' => 'root'],       
    ['port' => '3306', 'pass' => ''],           
    ['port' => '3306', 'pass' => '1234567890'], // Твой пароль из прошлого кода
    ['port' => '8889', 'pass' => 'root'],       // Стандарт MAMP
    ['port' => '3307', 'pass' => 'root'],       
];

foreach ($configs as $c) {
    try {
        $dsn = "mysql:host=127.0.0.1;port={$c['port']};dbname=amatto_db;charset=utf8mb4";
        $test_pdo = new PDO($dsn, 'root', $c['pass']);
        echo "<b style='color:green'>УСПЕХ! Порт: {$c['port']}, Пароль: '{$c['pass']}'</b><br>";
        echo "Скопируй эти данные в db.php!<br><hr>";
    } catch (PDOException $e) {
        echo "Порт {$c['port']} + пароль '{$c['pass']}' -> <span style='color:red'>Ошибка: " . $e->getMessage() . "</span><br>";
    }
}
?>