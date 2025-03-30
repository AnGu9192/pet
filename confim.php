<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем код подтверждения
$confirmation_code = $_GET['code'];

// Проверяем код в базе данных
$sql = "SELECT * FROM users WHERE confirmation_code = '$confirmation_code'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Обновляем статус подтверждения
    $sql = "UPDATE users SET confirmed = 1 WHERE confirmation_code = '$confirmation_code'";
    if ($conn->query($sql) === TRUE) {
        echo "Подтверждение прошло успешно!";
    } else {
        echo "Ошибка при подтверждении пользователя.";
    }
} else {
    echo "Неверный код подтверждения.";
}

$conn->close();
?>
