<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['refresh_api']) && $_GET['refresh_api'] == 1) {
    header('Content-Type: application/json');
    require_once 'ApiClient.php';
    
    $api = new ApiClient();
    $url = 'https://opentdb.com/api.php?amount=5';
    
    $cacheFile = 'api_cache.json';
    $cacheTtl = 300;
    
    if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTtl) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        echo json_encode([
            'success' => true,
            'data' => $cached,
            'cached' => true
        ]);
        exit();
    }
    
    $apiData = $api->request($url);
    
    if (isset($apiData['error'])) {
        echo json_encode([
            'success' => false,
            'error' => $apiData['error']
        ]);
        exit();
    }
    
    file_put_contents($cacheFile, json_encode($apiData, JSON_UNESCAPED_UNICODE));
    
    echo json_encode([
        'success' => true,
        'data' => $apiData,
        'cached' => false
    ]);
    exit();
}

session_start();

$username = trim(htmlspecialchars($_POST['username'] ?? ''));
$age = htmlspecialchars($_POST['age'] ?? '');
$topic = htmlspecialchars($_POST['topic'] ?? '');
$prize = isset($_POST['prize']) ? "Да" : "Нет";
$difficulty = htmlspecialchars($_POST['difficulty'] ?? '');

$errors = [];

if (empty($username)) {
    $errors[] = "Имя не может быть пустым";
}

if (empty($age)) {
    $errors[] = "Возраст не может быть пустым";
} elseif (!is_numeric($age)) {
    $errors[] = "Возраст должен быть числом";
} elseif ($age < 1 || $age > 120) {
    $errors[] = "Возраст должен быть от 1 до 120 лет";
}

if (empty($topic)) {
    $errors[] = "Выберите тему викторины";
}

if (empty($difficulty)) {
    $errors[] = "Выберите сложность вопросов";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: index.php");
    exit();
}

$_SESSION['username'] = $username;
$_SESSION['age'] = $age;
$_SESSION['topic'] = $topic;
$_SESSION['prize'] = $prize;
$_SESSION['difficulty'] = $difficulty;

$line = $username . ";" . $age . ";" . $topic . ";" . $prize . ";" . $difficulty . "\n";
file_put_contents("data.txt", $line, FILE_APPEND);

setcookie("last_username", $username, time() + 86400, "/");
setcookie("last_age", $age, time() + 86400, "/");
setcookie("last_topic", $topic, time() + 86400, "/");
setcookie("last_prize", $prize, time() + 86400, "/");
setcookie("last_difficulty", $difficulty, time() + 86400, "/");
setcookie("last_submission", date('Y-m-d H:i:s'), time() + 3600, "/");

require_once 'ApiClient.php';
$api = new ApiClient();
$url = 'https://opentdb.com/api.php?amount=5';

$cacheFile = 'api_cache.json';
$cacheTtl = 300;

if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTtl) {
    $cached = json_decode(file_get_contents($cacheFile), true);
    $_SESSION['api_data'] = $cached;
    $_SESSION['api_cached'] = true;
} else {
    $apiData = $api->request($url);
    if (isset($apiData['error'])) {
        $_SESSION['api_error'] = $apiData['error'];
        $_SESSION['api_data'] = null;
    } else {
        file_put_contents($cacheFile, json_encode($apiData, JSON_UNESCAPED_UNICODE));
        $_SESSION['api_data'] = $apiData;
        $_SESSION['api_cached'] = false;
    }
}

unset($_SESSION['errors']);

header("Location: index.php");
exit();
?>