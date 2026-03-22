<?php
$dbPath = __DIR__ . '/calculator.sqlite';
$pdo = new PDO("sqlite:$dbPath");

// Таблица для хранения истории игр
$pdo->exec("CREATE TABLE IF NOT EXISTS games (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    player_name TEXT NOT NULL,
    game_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expression TEXT NOT NULL,
    correct_answer INTEGER NOT NULL,
    user_answer INTEGER NOT NULL,
    is_winner BOOLEAN NOT NULL
)");

echo "База данных успешно создана по адресу: $dbPath";