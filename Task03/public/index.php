<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$dbPath = __DIR__ . '/../db/calculator.sqlite';
$pdo = new PDO("sqlite:$dbPath");
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Внутренняя логика ---

function calculateResult($nums, $ops) {
    // Умножение
    for ($i = 0; $i < count($ops); $i++) {
        if ($ops[$i] === '*') {
            $nums[$i] = $nums[$i] * $nums[$i + 1];
            array_splice($nums, $i + 1, 1);
            array_splice($ops, $i, 1);
            $i--;
        }
    }
    // Сложение и вычитание
    $result = $nums[0];
    for ($i = 0; $i < count($ops); $i++) {
        if ($ops[$i] === '+') $result += $nums[$i + 1];
        elseif ($ops[$i] === '-') $result -= $nums[$i + 1];
    }
    return $result;
}

function generateNewExpression() {
    $numbers = [rand(1, 15), rand(1, 15), rand(1, 15), rand(1, 15)];
    $operators = ['+', '-', '*'];
    $ops = [$operators[rand(0, 2)], $operators[rand(0, 2)], $operators[rand(0, 2)]];
    
    $exprStr = "{$numbers[0]}{$ops[0]}{$numbers[1]}{$ops[1]}{$numbers[2]}{$ops[2]}{$numbers[3]}";
    $val = calculateResult($numbers, $ops);
    
    return ['str' => $exprStr, 'val' => $val];
}

// --- Маршруты REST API ---
// Корневой маршрут для удобного запуска через роутер PHP/Slim.
$app->get('/', function (Request $request, Response $response) {
    $htmlPath = __DIR__ . '/index.html';
    $response->getBody()->write((string)file_get_contents($htmlPath));
    return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
});

// Поддержка прямого перехода на /index.html, если все запросы идут через Slim.
$app->get('/index.html', function (Request $request, Response $response) {
    $htmlPath = __DIR__ . '/index.html';
    $response->getBody()->write((string)file_get_contents($htmlPath));
    return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
});

// 1. GET /games - Список всех игр
$app->get('/games', function (Request $request, Response $response) use ($pdo) {
    $stmt = $pdo->query("
        SELECT
            g.id,
            g.player_name,
            g.game_date,
            g.status,
            s.expression,
            s.user_answer,
            s.correct_answer
        FROM games g
        LEFT JOIN steps s
            ON s.id = (
                SELECT id
                FROM steps
                WHERE game_id = g.id
                ORDER BY id DESC
                LIMIT 1
            )
        ORDER BY g.game_date DESC
    ");
    $games = $stmt->fetchAll();
    $response->getBody()->write(json_encode($games));
    return $response->withHeader('Content-Type', 'application/json');
});

// 2. GET /games/{id} - Данные о конкретной игре (ходах)
$app->get('/games/{id}', function (Request $request, Response $response, array $args) use ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM steps WHERE game_id = ?");
    $stmt->execute([$args['id']]);
    $steps = $stmt->fetchAll();
    $response->getBody()->write(json_encode($steps));
    return $response->withHeader('Content-Type', 'application/json');
});

// 3. POST /games - Создание игры и первого хода
$app->post('/games', function (Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    $name = $data['player_name'] ?? 'Player';

    // Создаем игру
    $stmt = $pdo->prepare("INSERT INTO games (player_name) VALUES (?)");
    $stmt->execute([$name]);
    $gameId = $pdo->lastInsertId();

    // Генерируем выражение для первого шага
    $calc = generateNewExpression();
    // В текущей схеме steps.user_answer имеет NOT NULL, поэтому сохраняем стартовое значение.
    $stmtStep = $pdo->prepare("INSERT INTO steps (game_id, expression, correct_answer, user_answer) VALUES (?, ?, ?, ?)");
    $stmtStep->execute([$gameId, $calc['str'], $calc['val'], 0]);
    $stepId = $pdo->lastInsertId();

    $response->getBody()->write(json_encode([
        'game_id' => $gameId,
        'step_id' => $stepId,
        'expression' => $calc['str']
    ]));
    return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
});

// 4. POST /step/{id} - Запись ответа для очередного хода (id — это id игры)
$app->post('/step/{id}', function (Request $request, Response $response, array $args) use ($pdo) {
    $gameId = $args['id'];
    $data = $request->getParsedBody();
    $userAnswer = (int)$data['user_answer'];

    // Находим последний шаг игры
    $stmt = $pdo->prepare("
        SELECT id, correct_answer, user_answer
        FROM steps
        WHERE game_id = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute([$gameId]);
    $step = $stmt->fetch();
    if (!$step) {
        $response->getBody()->write(json_encode(['error' => 'Шаг не найден для указанной игры']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $isCorrect = ($userAnswer === (int)$step['correct_answer']);

    // Обновляем шаг
    $upd = $pdo->prepare("UPDATE steps SET user_answer = ? WHERE id = ?");
    $upd->execute([$userAnswer, $step['id']]);

    $newStatus = $isCorrect ? 'won' : 'lost';
    $statusUpd = $pdo->prepare("UPDATE games SET status = ? WHERE id = ?");
    $statusUpd->execute([$newStatus, $gameId]);

    $response->getBody()->write(json_encode([
        'correct' => $isCorrect,
        'answer' => $step['correct_answer']
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();