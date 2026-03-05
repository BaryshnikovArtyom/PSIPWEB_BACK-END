<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контрольная работа - Вариант 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Вариант 1</h1>

    <!-- ЗАДАНИЕ 1 (Подключение через include) -->
    <div class="task-card">
        <h3>Задание 1: Использование include</h3>
        <p>Ниже подключены файлы с Заданием 2 и 3:</p>
        <div style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 10px;">
            <?php include 'task2.php'; ?>
        </div>
        <div style="border: 1px dashed #ccc; padding: 10px;">
            <?php include 'task3.php'; ?>
        </div>
    </div>

    <!-- ЗАДАНИЕ 4 (Цикл while) -->
    <div class="task-card">
        <?php
        echo "<h3>Задание 4: Цикл while</h3>";
        $n = 1; 
        $limit = $n + 5; 
        $name = "Барышников Артём"; 

        echo "Вывод имени $limit раз:<br>";
        $i = 1;
        while ($i <= $limit) {
            echo "$i. $name <br>";
            $i++;
        }
        ?>
    </div>

    <!-- ЗАДАНИЕ 5 (Минимальный в массиве) -->
    <div class="task-card">
        <?php
        echo "<h3>Задание 5: Массивы</h3>";
        $arr = [12, 45, 3, 78, 10]; // 5 целых элементов
        echo "Массив: [" . implode(", ", $arr) . "]<br>";

        $min = $arr[0];
        for ($i = 1; $i < count($arr); $i++) {
            if ($arr[$i] < $min) {
                $min = $arr[$i];
            }
        }
        echo "Минимальный элемент: <b>$min</b>";
        ?>
    </div>

    <!-- ЗАДАНИЕ 6 (Строки) -->
    <div class="task-card">
        <?php
        echo "<h3>Задание 6: Строки</h3>";
        $s1 = "Я люблю Беларусь";
        $s2 = "Я учусь в Политехническом колледже";
        $n = 1; // номер варианта

        // 1. Длина S1
        echo "1. Длина строки S1: " . mb_strlen($s1, 'UTF-8') . " симв.<br>";

        // 2. n-ый символ и его код
        $char = mb_substr($s1, $n - 1, 1, 'UTF-8');
        echo "2. $n-й символ в S1: '<b>$char</b>'. Код Unicode (mb_ord): " . mb_ord($char, 'UTF-8') . "<br>";

        // 3. Замена "у" на "*" в S2
        $s2_new = str_replace("у", "*", $s2);
        echo "3. Измененная строка S2: $s2_new";
        ?>
    </div>





</div>




</body>
</html>