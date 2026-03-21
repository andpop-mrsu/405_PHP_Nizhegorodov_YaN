<?php

namespace Radiculitca\Calculator\Controller;

use Radiculitca\Calculator\View;
use function cli\line;
use function cli\prompt;

function startGame() {
    View\displayStartScreen();
    $operators = ['+', '-', '*'];
    $numbers = [];
    $chosenOps = [];
    
    for ($i = 0; $i < 4; $i++) {
        $numbers[] = rand(1, 25);
    }
    for ($i = 0; $i < 3; $i++) {
        $chosenOps[] = $operators[array_rand($operators)];
    }

    $expression = "{$numbers[0]} {$chosenOps[0]} {$numbers[1]} {$chosenOps[1]} {$numbers[2]} {$chosenOps[2]} {$numbers[3]}";
    
    line("Выражение: {$expression}");
    
    $correctAnswer = calculate($numbers, $chosenOps);

    $userAnswer = prompt("Ваш ответ");

    if (!is_numeric($userAnswer)) {
        line("Пожалуйста, введите числовой ответ!");
        return;
    }

    if ((int)$userAnswer === $correctAnswer) {
        line("Верно!");
    } else {
        line("'{$userAnswer}' это неправильный ответ. Правильный ответ: '{$correctAnswer}'.");
    }
}


function calculate(array $nums, array $ops): int
{
    // 1. Первый проход: обрабатываем умножение
    for ($i = 0; $i < count($ops); $i++) {
        if ($ops[$i] === '*') {
            $nums[$i] = $nums[$i] * $nums[$i + 1];
            array_splice($nums, $i + 1, 1);
            array_splice($ops, $i, 1);
            $i--;
        }
    }

    // 2. Второй проход: сложение и вычитание слева направо
    $result = $nums[0];
    for ($i = 0; $i < count($ops); $i++) {
        if ($ops[$i] === '+') {
            $result += $nums[$i + 1];
        } elseif ($ops[$i] === '-') {
            $result -= $nums[$i + 1];
        }
    }

    return $result;
}