<?php

namespace Radiculitca\Calculator\Generator;

function generateExpression() {
    $numbers = [rand(1, 20), rand(1, 20), rand(1, 20), rand(1, 20)];
    $operators = ['+', '-', '*'];
    $ops = [
        $operators[array_rand($operators)],
        $operators[array_rand($operators)],
        $operators[array_rand($operators)]
    ];

    // Формируем строку для отображения пользователю
    $expression = "{$numbers[0]}{$ops[0]}{$numbers[1]}{$ops[1]}{$numbers[2]}{$ops[2]}{$numbers[3]}";
    
    $answer = calculate($numbers, $ops); 

    return [
        'expression' => $expression,
        'answer' => $answer
    ];
}


function calculate($nums, $ops) {
    // 1. Первый проход: обрабатываем умножение
    for ($i = 0; $i < count($ops); $i++) {
        if ($ops[$i] === '*') {
            $nums[$i] = $nums[$i] * $nums[$i + 1];
            array_splice($nums, $i + 1, 1);
            array_splice($ops, $i, 1);
            $i--;
        }
    }

    // 2. Второй проход: сложение и вычитание
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