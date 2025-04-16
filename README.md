Инструкция по использованию системы тестирования
1. Установка на любом ПК
1. Установите необходимые компоненты:
   - PHP (рекомендуется 8.1+)
   - PostgreSQL (рекомендуется 13+)
   - DBeaver (для работы с базой)
2. Импортируйте базу данных через DBeaver:
   - Откройте DBeaver и подключитесь к вашей базе данных PostgreSQL
   - Создайте базу данных, например: student_test
   - Откройте файл test.sql и выполните его через DBeaver либо через командную строку: psql -U student_test -h localhost -p 5432 -d student_test -f test.sql
3. Настройка подключения в config/db.php:
   $host = 'localhost';
   $db = 'student_test';
   $user = 'student_test';
   $pass = 'ваш_пароль';
4. Запуск приложения:
   - Откройте консоль в папке /tests
   - Выполните: php -S localhost:8080
   - Откройте в браузере: http://localhost:8080





2. Инструкция для преподавателя
Вход:
   - Логин: teacher1
   - Пароль: teacher123
Функции преподавателя:
   - Создание вопросов (один/несколько правильных или текстовый вариант ответа)
   - Создание тестов на основе вопросов
   - Генерация логинов и паролей для студентов
   - Просмотр результатов (в процентах) и подробного отчета каждого
   - Смена пароля
   - Импорт и Экспорт вопросов в формате CSV
3. Инструкция для студентов
1. Получите логин и пароль от преподавателя
2. Зайдите на http://localhost:8080
3. Пройдите тест (один/несколько правильных или текстовый вариант ответа)
4. После прохождения увидите:
   - Количество правильных ответов
   - Общий процент
   - Название теста
   - Дата
Примечание: логины и пароли студентов генерируются преподавателем автоматически
