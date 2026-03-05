<?php
// Устанавливаем часовой пояс (например, для Кишинева/Москвы)
date_default_timezone_set('Europe/Chisinau');

// --- ЛОГИКА ДЛЯ ЗАДАНИЯ 1 (Работа с файлами) ---

$filename = "textfile.txt"; 
$dir_path = "."; 

// Если файла нет, создадим его для примера
if (!file_exists($filename)) {
    $fp = fopen($filename, "w");
    fwrite($fp, "Это тестовая строка в файле.");
    fclose($fp);
}

// Обработка формы записи в файл (Пример 4)
if (isset($_POST['textblock'])) {
    $f = fopen($filename, "w"); // "w" - перезапись, "a" - добавление в конец
    fwrite($f, $_POST["textblock"]);
    fclose($f);
}




function getDayRus($dateInput = null) {

    $timestamp = $dateInput ? strtotime($dateInput) : time();
    
 
    $weekday = date('D', $timestamp);
    
   
    if ($weekday == 'Mon') { $weekday = "Понедельник"; }
    elseif ($weekday == 'Tue') { $weekday = "Вторник"; }
    elseif ($weekday == 'Wed') { $weekday = "Среда"; }
    elseif ($weekday == 'Thu') { $weekday = "Четверг"; }
    elseif ($weekday == 'Fri') { $weekday = "Пятница"; }
    elseif ($weekday == 'Sat') { $weekday = "Суббота"; }
    elseif ($weekday == 'Sun') { $weekday = "Воскресенье"; }
    
    return $weekday;
}


$show_today_message = false;
if (isset($_POST['check_date'])) {
    $show_today_message = true;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Лабораторная работа PHP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 20px; padding-bottom: 50px; }
        .task-card { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h2 { color: #333; border-bottom: 2px solid #ffc107; padding-bottom: 10px; margin-bottom: 20px; }
        .result-box { background: #e9ecef; padding: 10px; border-left: 5px solid #0d6efd; margin-top: 10px; font-family: monospace; }
    </style>
</head>
<body>

<div class="container">
    <div class="text-center mb-5">
        <h1>Практическая работа PHP</h1>
        <a href="index.html" class="btn btn-outline-secondary">← Вернуться на Главную</a>
    </div>

    <div class="task-card">
        <h2>Задание №1: Работа с файлами и каталогами</h2>
        

        <h4>1. Свойства файла (<?= $filename ?>)</h4>
        <div class="result-box">
            <?php
            echo "Последнее редактирование: " . date("r", filemtime($filename)) . "<br>";
            echo "Последний доступ: " . date("r", fileatime($filename)) . "<br>";
            echo "Размер файла: " . filesize($filename) . " байт";
            ?>
        </div>
        <hr>

        <h4>2. Содержимое текущей папки</h4>
        <div class="result-box">
            <?php
 
            $folder = opendir($dir_path);
  
            while (($entry = readdir($folder)) !== false) {
   
                if($entry != "." && $entry != "..") {
                    echo $entry . "<br>";
                }
            }
     
            closedir($folder);
            ?>
        </div>
        <hr>


        <h4>3-4. Чтение и Запись в файл</h4>
        <p>Текущее содержимое файла <b><?= $filename ?></b>:</p>
        <div class="result-box">
            <?php
            $f = fopen($filename, "r");

            if(filesize($filename) > 0) {
      
                while(!feof($f)) {
                    echo fgets($f) . "<br>";
                }
            } else {
                echo "Файл пуст";
            }
            fclose($f);
            ?>
        </div>

        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Введите новый текст для записи в файл:</label>
                <textarea name="textblock" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Записать в файл</button>
        </form>
    </div>


    <div class="task-card">
        <h2>Задание №2: Дата, время и календарь</h2>


        <h4>1. Текущая дата и время</h4>
        <div class="result-box">
            <?php
    
            echo date('d. m. Y') . "<br>";
  
            echo date('H:i') . "<br>";
     
            echo date('l'); 
            ?>
        </div>
        <hr>

    
        <h4>2. Проверка функции дня недели (рус)</h4>
        <p>Тест функции <code>getDayRus()</code> для сегодняшней даты:</p>
        <div class="result-box">
            <?php echo getDayRus();  ?>
        </div>
        <hr>


        <h4>3. Интерактивная форма</h4>
        <div class="text-center p-4 border rounded bg-light">
            <p class="fs-4 fw-bold">
                <?= date('j. m. Y. H:i') ?>
            </p>
            
            <form method="POST">
                <button type="submit" name="check_date" class="btn btn-secondary btn-lg shadow">
                    Что за день СЕГОДНЯ?
                </button>
            </form>

            <?php if ($show_today_message): ?>
                <div class="alert alert-success mt-3" role="alert">
                    "Сегодня <b><?= getDayRus() ?></b>"
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>