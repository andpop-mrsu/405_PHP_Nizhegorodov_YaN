<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблиц для игры
     */
    public function up(): void
    {
        // Таблица 1: Общая информация об игре
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('player_name');
            $table->timestamps(); // Создаст поля created_at и updated_at автоматически
        });

        // Таблица 2: Ходы внутри игры
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            // Внешний ключ, связывающий шаг с игрой
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->string('expression');
            $table->integer('correct_answer');
            $table->integer('user_answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Удаление таблиц при откате миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
        Schema::dropIfExists('games');
    }
};
