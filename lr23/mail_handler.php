<?php
// Подключаем файлы библиотеки
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        // --- НАСТРОЙКИ SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'saominamisa@gmail.com'; // Твой Gmail
        $mail->Password   = 'nuhg uyzl zcqp egmt'; // 16 букв из Шага 1
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // --- ДАННЫЕ ИЗ ФОРМЫ ---
        $type = $_POST['form_type'] ?? 'Заявка';
        $name = strip_tags($_POST['user_name'] ?? 'Гость');
        $phone = strip_tags($_POST['user_phone'] ?? 'Нет телефона');

        // --- ОТПРАВИТЕЛЬ И ПОЛУЧАТЕЛЬ ---
        $mail->setFrom('saominamisa@gmail.com', 'Amatto Site');
        $mail->addAddress('kmay3123@gmail.com');

        // --- ТЕКСТ ПИСЬМА ---
        $mail->isHTML(true);
        $mail->Subject = "Amatto Kitchens: $type";
        $mail->Body    = "
            <div style='border:10px solid #f8f9fa; padding: 20px;'>
                <h2 style='color: #a04d00;'>Новая заявка с сайта!</h2>
                <p><b>Тип:</b> $type</p>
                <p><b>Имя:</b> $name</p>
                <p><b>Телефон:</b> $phone</p>
                <hr>
                <p style='font-size:10px;'>Дата: " . date('d.m.Y H:i') . "</p>
            </div>";

        $mail->send();
        header("Location: index.php?mail=success");

    } catch (Exception $e) {
        echo "Ошибка отправки: {$mail->ErrorInfo}";
    }
}