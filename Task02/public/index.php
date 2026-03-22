<?php
require_once __DIR__ . '/../src/Generator.php';
$dbPath = __DIR__ . '/../db/calculator.sqlite';
$pdo = new PDO("sqlite:$dbPath");

$message = "";
$showForm = true;




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_answer'])) {
    $userName = $_POST['player_name'] ?: 'Аноним';
    $userAnswer = (int)$_POST['user_answer'];
    $correctAnswer = (int)$_POST['correct_answer'];
    $expression = $_POST['expression'];

    $isWinner = ($userAnswer === $correctAnswer);
    $message = $isWinner ? "Правильно!" : "Ошибка! Правильный ответ: $correctAnswer";

    $stmt = $pdo->prepare("INSERT INTO games (player_name, expression, correct_answer, user_answer, is_winner) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userName, $expression, $correctAnswer, $userAnswer, (int)$isWinner]);
    
    $showForm = false;
}

$game = Radiculitca\Calculator\Generator\generateExpression();
?>


<!DOCTYPE html>
<html lang="ru">
<head><title>Калькулятор</title></head>
<body>
    <h1>Игра "Калькулятор"</h1>

    <?php if ($message): ?>
        <p><strong><?= $message ?></strong></p>
        <a href="/">Играть еще раз</a>
    <?php endif; ?>

    <?php if ($showForm): ?>
        <form method="POST">
            <p>Игрок: <input type="text" name="player_name" required></p>
            <p>Вычислите: <?= $game['expression'] ?></p>
            
            <input type="hidden" name="expression" value="<?= $game['expression'] ?>">
            <input type="hidden" name="correct_answer" value="<?= $game['answer'] ?>">

            <input type="number" name="user_answer" required autofocus>
            <button type="submit">Ответить</button>
        </form>
    <?php endif; ?>

    <hr>
    <a href="history.php">Посмотреть историю игр</a>
</body>
</html>