<?php
require_once 'db.php';

$sql = "
CREATE TABLE IF NOT EXISTS quiz_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    topic VARCHAR(100) NOT NULL,
    prize TINYINT(1) DEFAULT 0,
    difficulty VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
    echo "Таблица 'quiz_participants' успешно создана!";
} catch (PDOException $e) {
    echo "Ошибка создания таблицы: " . $e->getMessage();
}
?>