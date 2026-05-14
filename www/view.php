<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Все данные викторины</title>
</head>
<body>

<h2>Все сохранённые данные:</h2>

<ul>
    <?php
    if (file_exists("data.txt")) {
        // Читаем файл построчно
        $lines = file("data.txt", FILE_IGNORE_NEW_LINES);
        
        foreach ($lines as $line) {
            // Разделяем строку на части (разделитель - точка с запятой)
            $data = explode(";", $line);
            
            $username = $data[0] ?? "Не указано";
            $age = $data[1] ?? "Не указано";
            $topic = $data[2] ?? "Не указано";
            $prize = $data[3] ?? "Не указано";
            $difficulty = $data[4] ?? "Не указано";
            
            echo "<li>$username (возраст: $age, тема: $topic, приз: $prize, сложность: $difficulty)</li>";
        }
    } else {
        echo "<li>Данных пока нет. Заполните форму.</li>";
    }
    ?>
</ul>

<p>
    <a href="index.php">На главную</a> |
    <a href="form.html">Заполнить форму</a>
</p>

</body>
</html>