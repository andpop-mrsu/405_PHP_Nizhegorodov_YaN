<?php
$dbPath = __DIR__ . '/calculator.sqlite';
$pdo = new PDO("sqlite:$dbPath");

// Таблица игр
$pdo->exec("CREATE TABLE IF NOT EXISTS games (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    player_name TEXT NOT NULL,
    game_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TEXT DEFAULT 'active' -- active, won, lost
)");

// Таблица шагов внутри игры
$pdo->exec("CREATE TABLE IF NOT EXISTS steps (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    game_id INTEGER,
    expression TEXT NOT NULL,
    correct_answer INTEGER NOT NULL,
    user_answer INTEGER NOT NULL,
    FOREIGN KEY (game_id) REFERENCES games(id)
)");