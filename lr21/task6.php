<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Задание 6</title>
</head>
<body>
    <h3>Предопределенные константы и переменные</h3>
    <?php
        // ПРЕДОПРЕДЕЛЕННЫЕ КОНСТАНТЫ
        
        
        echo "<b>Примеры магических констант:</b><br>";
        echo "Версия PHP (PHP_VERSION): " . PHP_VERSION . "<br>";
        echo "Полный путь к этому файлу (__FILE__): " . __FILE__ . "<br>";
        echo "Номер текущей строки кода (__LINE__): " . __LINE__ . "<br>"; 
        echo "Операционная система сервера (PHP_OS): " . PHP_OS . "<br><br>";

        // ПРЕДОПРЕДЕЛЕННЫЕ ПЕРЕМЕННЫЕ 
       
        
        echo "<b>Примеры из суперглобального массива \$_SERVER:</b><br>";
        
        // Имя скрипта, который выполняется сейчас
        echo "Текущий скрипт (\$_SERVER['PHP_SELF']): " . $_SERVER['PHP_SELF'] . "<br>";
        
        // Имя хоста (сервера)
        echo "Имя сервера (\$_SERVER['SERVER_NAME']): " . $_SERVER['SERVER_NAME'] . "<br>";
        
        // Браузер пользователя (User Agent)
        echo "Ваш браузер (\$_SERVER['HTTP_USER_AGENT']): " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
        
        // IP адрес пользователя
       
        echo "Ваш IP адрес (\$_SERVER['REMOTE_ADDR']): " . $_SERVER['REMOTE_ADDR'] . "<br>";
    ?>
    <br>
    <br>
    <a href="index.php">Вернуться в меню</a>
</body>
</html>