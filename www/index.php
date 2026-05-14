<?php
session_start();
require_once 'UserInfo.php';

$info = UserInfo::getInfo();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Викторина - Главная</title>
    <style>
        .api-data {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            max-height: 400px;
            overflow: auto;
        }
        .error {
            color: red;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .cached-badge {
            color: green;
            font-size: 12px;
        }
        button {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background: #e74c3c;
        }
    </style>
</head>
<body>

<h2>Участие в викторине</h2>

<h3>Информация о пользователе:</h3>
<?php foreach($info as $key => $val): ?>
    <?= htmlspecialchars($key) ?>: <?= htmlspecialchars($val) ?><br>
<?php endforeach; ?>

<hr>

<?php if(isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
    <ul class="error">
        <?php foreach($_SESSION['errors'] as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['username']) && !isset($_SESSION['errors'])): ?>
    <p><strong>Данные из сессии:</strong></p>
    <ul>
        <li>Имя: <?= htmlspecialchars($_SESSION['username']) ?></li>
        <li>Возраст: <?= htmlspecialchars($_SESSION['age'] ?? 'не указан') ?></li>
        <li>Тема: <?= htmlspecialchars($_SESSION['topic'] ?? 'не указана') ?></li>
        <li>Приз: <?= htmlspecialchars($_SESSION['prize'] ?? 'нет') ?></li>
        <li>Сложность: <?= htmlspecialchars($_SESSION['difficulty'] ?? 'не выбрана') ?></li>
    </ul>
<?php else: ?>
    <?php if(!isset($_SESSION['errors'])): ?>
        <p>Данных пока нет. Заполните форму.</p>
    <?php endif; ?>
<?php endif; ?>

<?php if(isset($_COOKIE['last_username'])): ?>
    <hr>
    <p><strong>Данные из куки (последняя отправленная форма):</strong></p>
    <ul>
        <li>Имя: <?= htmlspecialchars($_COOKIE['last_username']) ?></li>
        <li>Возраст: <?= htmlspecialchars($_COOKIE['last_age'] ?? 'не указан') ?></li>
        <li>Тема: <?= htmlspecialchars($_COOKIE['last_topic'] ?? 'не указана') ?></li>
        <li>Приз: <?= htmlspecialchars($_COOKIE['last_prize'] ?? 'нет') ?></li>
        <li>Сложность: <?= htmlspecialchars($_COOKIE['last_difficulty'] ?? 'не выбрана') ?></li>
    </ul>
<?php endif; ?>

<hr>

<div id="apiBlock">
    <h3>Данные из API (вопросы для викторины):
        <?php if(isset($_SESSION['api_cached']) && $_SESSION['api_cached'] === true): ?>
            <span class="cached-badge">(из кеша)</span>
        <?php endif; ?>
    </h3>
    
    <?php if(isset($_SESSION['api_error'])): ?>
        <div class="error">
            <p>Ошибка API: <?= htmlspecialchars($_SESSION['api_error']) ?></p>
        </div>
        <?php unset($_SESSION['api_error']); ?>
    <?php elseif(isset($_SESSION['api_data']) && !empty($_SESSION['api_data'])): ?>
        <button id="refreshApiBtn">🔄 Обновить данные</button>
        <div class="api-data" id="apiContent">
            <pre><?= print_r($_SESSION['api_data'], true) ?></pre>
        </div>
    <?php else: ?>
        <p>Данных API пока нет. Отправьте форму для загрузки вопросов.</p>
    <?php endif; ?>
</div>

<hr>

<p>
    <a href="form.html">Заполнить форму</a> |
    <a href="view.php">Посмотреть все данные</a>
</p>

<script>
document.getElementById('refreshApiBtn')?.addEventListener('click', async function() {
    const apiContent = document.getElementById('apiContent');
    const btn = this;
    
    btn.textContent = '🔄 Загрузка...';
    btn.disabled = true;
    
    try {
        const response = await fetch('process.php?refresh_api=1', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        if (data.success) {
            apiContent.innerHTML = '<pre>' + JSON.stringify(data.data, null, 2) + '</pre>';
            const badge = document.querySelector('.cached-badge');
            if (badge && data.cached) {
                badge.style.display = 'inline';
            } else if (badge) {
                badge.style.display = 'none';
            }
        } else {
            apiContent.innerHTML = '<div class="error">Ошибка: ' + data.error + '</div>';
        }
    } catch (error) {
        apiContent.innerHTML = '<div class="error">Ошибка при обновлении: ' + error.message + '</div>';
    } finally {
        btn.textContent = '🔄 Обновить данные';
        btn.disabled = false;
    }
});
</script>

</body>
</html>