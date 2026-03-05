<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Задание 5</title>
</head>
<body>
    <h3>Работа с константами и типами данных</h3>
    <?php
        // 1. Создание константы NUM_E
        define("NUM_E", 2.71828);

        // Вывод значения константы
        echo "Число e равно " . NUM_E . "<br><br>";

        // 2. Присваиваем переменной значение константы
        $num_e1 = NUM_E;


        echo "Исходный тип переменной \$num_e1: <b>" . gettype($num_e1) . "</b><br>";
        echo "Значение: $num_e1 <br><hr>";

        // --- Преобразование в СТРОКУ ---
        $num_e1 = (string)$num_e1; 
        echo "Тип после преобразования в строку: <b>" . gettype($num_e1) . "</b><br>";
        echo "Значение: $num_e1 <br><hr>";

        // --- Преобразование в ЦЕЛОЕ ---
        $num_e1 = (int)$num_e1;
        echo "Тип после преобразования в целое: <b>" . gettype($num_e1) . "</b><br>";
        echo "Значение: $num_e1 <br><hr>";

        // --- Преобразование в БУЛЕВО ---
        $num_e1 = (bool)$num_e1; 
        echo "Тип после преобразования в булево: <b>" . gettype($num_e1) . "</b><br>";
       
        echo "Значение (через echo): $num_e1 <br>"; 
        echo "Значение (через var_export): " . var_export($num_e1, true) . "<br>";
    ?>
    <br>
    <a href="index.php">Вернуться в меню</a>
</body>
</html>