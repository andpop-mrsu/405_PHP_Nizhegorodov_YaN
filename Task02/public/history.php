<?php
$dbPath = __DIR__ . '/../db/calculator.sqlite';

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Запрашиваем все игры, сортируем по дате (новые сверху)
    $query = "SELECT * FROM games ORDER BY game_date DESC";
    $games = $pdo->query($query)->fetchAll();
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>История игр — Калькулятор</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .win { color: green; font-weight: bold; }
        .lose { color: red; }
    </style>
</head>
<body>
    <h1>История всех игр</h1>
    
    <a href="index.php">← Вернуться к игре</a>

    <?php if (count($games) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Дата и время</th>
                    <th>Игрок</th>
                    <th>Выражение</th>
                    <th>Ваш ответ</th>
                    <th>Правильный ответ</th>
                    <th>Результат</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= htmlspecialchars($game['game_date']) ?></td>
                        <td><?= htmlspecialchars($game['player_name']) ?></td>
                        <td><?= htmlspecialchars($game['expression']) ?></td>
                        <td><?= htmlspecialchars($game['user_answer']) ?></td>
                        <td><?= htmlspecialchars($game['correct_answer']) ?></td>
                        <td>
                            <?php if ($game['is_winner']): ?>
                                <span class="win">Победа</span>
                            <?php else: ?>
                                <span class="lose">Проигрыш</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Игр пока не было. Будьте первым!</p>
    <?php endif; ?>

</body>
</html>