#!/bin/bash

# Быстрый старт для настройки деплоя
# Запускать от имени обычного пользователя

set -e

echo "Быстрый старт настройки деплоя"
echo "================================="

# Проверяем Docker
if ! command -v docker &> /dev/null; then
    echo "Docker не установлен. Установите Docker и Docker Compose"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose не установлен. Установите Docker Compose"
    exit 1
fi

echo "Docker и Docker Compose установлены"

# Делаем скрипты исполняемыми
chmod +x scripts/*.sh
echo "Скрипты сделаны исполняемыми"

# Создаем .env файл если его нет
if [ ! -f ".env" ]; then
    if [ -f "env.example" ]; then
        cp env.example .env
        echo "Создан .env файл из env.example"
    fi
fi

# Тестируем локальный запуск
echo "Тестируем локальный запуск..."
docker-compose up -d --build

echo "Ждем запуска сервисов..."
sleep 30

# Проверяем health check
if curl -f http://localhost:8080/health 2>/dev/null; then
    echo "Локальное приложение работает!"
    echo "Доступно по адресу: http://localhost:8080"
else
    echo "Health check не прошел, но приложение может работать"
    echo "Попробуйте: http://localhost:8080"
fi

echo ""
echo "Следующие шаги:"
echo "1. Настройте GitHub Actions Runner: ./scripts/setup-runner.sh"
echo "2. Или протестируйте ручной деплой: ./scripts/deploy.sh"
echo ""
echo "Подробная инструкция: DEPLOYMENT.md"

# Останавливаем тестовые контейнеры
read -p "Остановить тестовые контейнеры? (Y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Nn]$ ]]; then
    docker-compose down
    echo "Тестовые контейнеры остановлены"
fi
