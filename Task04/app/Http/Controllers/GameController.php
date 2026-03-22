<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Математическая логика: расчет выражения с учетом приоритета
     */
    private function calculateResult($nums, $ops)
    {
        // Сначала выполняем умножение
        for ($i = 0; $i < count($ops); $i++) {
            if ($ops[$i] === '*') {
                $nums[$i] = $nums[$i] * $nums[$i + 1];
                array_splice($nums, $i + 1, 1);
                array_splice($ops, $i, 1);
                $i--;
            }
        }
        // Затем сложение и вычитание
        $result = $nums[0];
        for ($i = 0; $i < count($ops); $i++) {
            if ($ops[$i] === '+') $result += $nums[$i + 1];
            elseif ($ops[$i] === '-') $result -= $nums[$i + 1];
        }
        return $result;
    }

    /**
     * Генерация нового выражения
     */
    private function generateExpression()
    {
        $numbers = [rand(1, 15), rand(1, 15), rand(1, 15), rand(1, 15)];
        $operators = ['+', '-', '*'];
        $ops = [$operators[rand(0, 2)], $operators[rand(0, 2)], $operators[rand(0, 2)]];
        
        $str = "{$numbers[0]}{$ops[0]}{$numbers[1]}{$ops[1]}{$numbers[2]}{$ops[2]}{$numbers[3]}";
        $val = $this->calculateResult($numbers, $ops);
        
        return ['str' => $str, 'val' => $val];
    }

    /**
     * GET /games - Список всех игр и их ходов
     */
    public function index()
    {
        $games = DB::table('games')
            ->leftJoin('steps', 'games.id', '=', 'steps.game_id')
            ->select('games.player_name', 'games.created_at', 'steps.expression', 'steps.user_answer', 'steps.correct_answer')
            ->orderBy('games.created_at', 'desc')
            ->get();

        return response()->json($games);
    }

    /**
     * POST /games - Создание игры и первого шага
     */
    public function store(Request $request)
    {
        $playerName = $request->input('player_name', 'Аноним');

        // 1. Создаем игру
        $gameId = DB::table('games')->insertGetId([
            'player_name' => $playerName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Генерируем пример
        $calc = $this->generateExpression();

        // 3. Сохраняем шаг (ход)
        $stepId = DB::table('steps')->insertGetId([
            'game_id' => $gameId,
            'expression' => $calc['str'],
            'correct_answer' => $calc['val'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'game_id' => $gameId,
            'step_id' => $stepId,
            'expression' => $calc['str']
        ], 201);
    }

    /**
     * POST /step/{id} - Проверка ответа
     */
    public function step(Request $request, $id)
    {
        $userAnswer = (int)$request->input('user_answer');

        // Ищем шаг в базе
        $step = DB::table('steps')->where('id', $id)->first();

        if (!$step) {
            return response()->json(['error' => 'Шаг не найден'], 404);
        }

        $isCorrect = ($userAnswer === (int)$step->correct_answer);

        // Обновляем ответ пользователя
        DB::table('steps')->where('id', $id)->update([
            'user_answer' => $userAnswer,
            'updated_at' => now(),
        ]);

        return response()->json([
            'correct' => $isCorrect,
            'answer' => $step->correct_answer
        ]);
    }
}