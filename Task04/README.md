<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Лабораторная работа №4: Веб-приложение "Калькулятор" (Laravel)

Веб-приложение, реализованное на фреймворке Laravel, представляющее собой игру "Калькулятор" с сохранением данных в базе SQLite.

Приложение является развитием предыдущей лабораторной работы (REST API + SPA), перенесённым на полноценный MVC-фреймворк.

---

## 📝 Описание

Пользователь решает математические выражения, а результаты сохраняются в базе данных.

Приложение реализовано с использованием возможностей Laravel:

- маршрутизация
- контроллеры
- работа с базой данных
- миграции

---

## 🎮 Функциональность

- Создание новой игры
- Генерация математических выражений
- Проверка ответа пользователя
- Сохранение результатов
- Просмотр истории игр

---

## ⚙️ Используемые технологии

- PHP 8+
- Laravel
- SQLite
- Blade (шаблоны Laravel)
- JavaScript (при необходимости)

---

## 🚀 Установка

### ⚡ Быстрая установка (Linux)

```bash id="f3q9da"
make install
```

Команда автоматически выполняет:

- установку зависимостей (`composer install`)
- создание файла `.env`
- генерацию ключа приложения
- настройку базы данных
- выполнение миграций

---

### 🔧 Ручная установка

```bash id="1v7wme"
composer install
cp .env.example .env
php artisan key:generate
```

Создать файл базы данных:

```bash id="x9jvrs"
touch database/database.sqlite
```

Выполнить миграции:

```bash id="z0j3c1"
php artisan migrate
```

---

## ▶️ Запуск приложения

```bash id="5qqg8f"
php artisan serve
```

После запуска открыть в браузере:

```id="9d8m1s"
http://localhost:8000/
```

---

## 🗄️ База данных

Используется SQLite:

```id="2b8wq7"
database/database.sqlite
```

Структура создаётся через миграции Laravel.

---

## 🧩 Архитектура

Приложение построено по архитектуре MVC:

- **Model** — работа с данными
- **View** — Blade-шаблоны
- **Controller** — обработка логики

---

## ▶️ Использование

1. Открыть приложение в браузере
2. Ввести имя игрока
3. Решить выражение
4. Получить результат
5. Просмотреть историю игр

---

## ⚠️ Требования

- PHP 8+
- Composer
- SQLite

---

## 👨‍💻 Автор

Нижегородов Ярослав
