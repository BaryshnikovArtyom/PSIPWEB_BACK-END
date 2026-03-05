<?php
// Проверяем, не была ли функция создана ранее
if (!function_exists('calculateY')) {
    function calculateY($x) {
        if ($x == 0) return "Ошибка: деление на 0";
        $numerator = 3.2 + sqrt(1 + $x);
        $denominator = abs(5 * $x);
        return sin($numerator / $denominator);
    }
}

// Логика формы (оставляем как есть)
$x_input = isset($_POST['x_val']) ? (float)$_POST['x_val'] : 1;
$y_result = calculateY($x_input);
?>

<form method="POST">
    <label>Введите x: </label>
    <input type="number" step="0.01" name="x_val" value="<?php echo $x_input; ?>">
    <button type="submit">Рассчитать</button>
</form>
<p>Результат Y = <b><?php echo $y_result; ?></b></p>