<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Задание 4</title>
</head>
<body>
    <?php
        // Объявляем переменные для стиля
        $color = "blue"; // Цвет текста 
        $size = "24px";  // Размер шрифта
        $fio = "Барышников"; // ФИО разработчика

        
        
        echo "<div style='color: $color; font-size: $size;'>$fio</div>";

      
    ?>
    <br>
    <a href="index.php">Вернуться в меню</a>
</body>
</html>