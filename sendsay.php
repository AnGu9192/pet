<?php
// Подключение к базе данных MySQL
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "work";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];
$pet_category = $_POST['pet_category'];
$pet_name = $_POST['pet_name'];


// Подготовка SQL-запроса для добавления данных в базу
$dbd = $conn->prepare("INSERT INTO users (name, email, pet_category, pet_name) VALUES (?, ?, ?, ?)");
$dbd->bind_param("ssss", $name, $email, $pet_category, $pet_name);


if ($dbd->execute()) {
    echo "Пользователь добавлен в базу данных.<br>";

    sendSendsay($name, $email, $pet_category, $pet_name);
    send_trigger_email($email);
} else {
    echo "Ошибка добавления пользователя: " . $dbd->error;
}

$dbd->close();
$conn->close();
}

// Функция для отправки данных в Sendsay
function sendSendsay($name, $email, $pet_category, $pet_name) {
    $api_key = '18Gb7axvxJ7y4U1pdJ5lm-a4Bwekmok2vkEpOxnSmYMrw3NuS6rH65ty4TqD1kZeOsxhXt-868WQg'; 
    $url = 'https://api.sendsay.ru/v3/mail/send.json';

    $data = [
        'name' => $name,
        'email' => $email,
        'login' => 'x_1742400453806537',           // Ваш логин в Sendsay
        'fields' => [
            'pet_category' => $pet_category,
            'pet_name' => $pet_name
        ],
        'apikey' => $api_key
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header' => 'Pet',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        die('Ошибка при отправке данных в Sendsay');
    }

    echo "Данные отправлены в Sendsay.";
}

//Отправка триггерного письма
function send_trigger_email($email) {
    $url = "https://api.sendsay.ru/handler_apimail.php";
    $data = [
        'action' => 'sendtrigger',        // Отправка триггерного письма
        'email' => $email,                // Email получателя
        'listid' => 'pl78183', 
        'trigger_id' => '1' // ID триггера
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Pet',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        die('Ошибка при отправке триггерного письма');
    }
    
    echo "Триггерное письмо отправлено.<br>";
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация питомца</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #0056b3;;
        }
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-top: 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="name">Имя:</label><br>
        <input type="text" id="name" name="name"><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br><br>
        
        <label for="pet_category">Категория питомца:</label><br>
        <select id="pet_category" name="pet_category">
            <option value="cat">Кот</option>
            <option value="dog">Собака</option>
            <option value="rodent">Грызун</option>
            <option value="fish">Рыбки</option>
            <option value="other">   
            </option>
        </select><br><br>
        
        <label for="pet_name">Имя питомца:</label><br>
        <input type="text" id="pet_name" name="pet_name"><br><br>
        <button type="submit">Отправить</button>
    </form>
</body>
</html>