#!/bin/bash

# Скрипт для Blue-Green deployment
# Использование: ./scripts/blue-green-deploy.sh

set -e

PROJECT_DIR="/var/www/html/media-monitoring-system"
BLUE_COMPOSE="docker-compose.blue.yml"
GREEN_COMPOSE="docker-compose.green.yml"
LB_COMPOSE="docker-compose.lb.yml"

echo "Blue-Green Deployment"
echo "===================="

# Определяем текущее активное окружение
if curl -f http://localhost:8080/health 2>/dev/null; then
    CURRENT_ENV="blue"
    NEW_ENV="green"
    NEW_COMPOSE="$GREEN_COMPOSE"
    NEW_PORT="8081"
    echo "Текущее активное окружение: Blue"
    echo "Деплоим в: Green"
else
    CURRENT_ENV="green"
    NEW_ENV="blue"
    NEW_COMPOSE="$BLUE_COMPOSE"
    NEW_PORT="8080"
    echo "Текущее активное окружение: Green"
    echo "Деплоим в: Blue"
fi

echo ""

# Шаг 1: Запускаем Load Balancer (если не запущен)
echo "1. Проверяем Load Balancer..."
if ! docker ps --format "table {{.Names}}" | grep -q "nginx-lb-mms"; then
    echo "Запускаем Load Balancer..."
    cd "$PROJECT_DIR"
    docker-compose -f "$LB_COMPOSE" up -d
    sleep 5
else
    echo "Load Balancer уже запущен"
fi

# Шаг 2: Останавливаем старое окружение (если запущено)
echo ""
echo "2. Останавливаем старое окружение..."
if [ "$CURRENT_ENV" = "blue" ]; then
    docker-compose -f "$BLUE_COMPOSE" down 2>/dev/null || echo "Blue окружение не было запущено"
else
    docker-compose -f "$GREEN_COMPOSE" down 2>/dev/null || echo "Green окружение не было запущено"
fi

# Шаг 3: Запускаем новое окружение
echo ""
echo "3. Запускаем новое окружение ($NEW_ENV)..."
cd "$PROJECT_DIR"
docker-compose -f "$NEW_COMPOSE" up -d --build

# Шаг 4: Ждем запуска сервисов
echo ""
echo "4. Ждем запуска сервисов..."
sleep 30

# Шаг 5: Проверяем health check нового окружения
echo ""
echo "5. Проверяем health check нового окружения..."
for i in {1..10}; do
    if curl -f "http://localhost:$NEW_PORT/health" 2>/dev/null; then
        echo "✅ Новое окружение ($NEW_ENV) работает!"
        break
    fi
    echo "Попытка $i/10, ждем 5 секунд..."
    sleep 5
done

# Шаг 6: Переключаем Load Balancer на новое окружение
echo ""
echo "6. Переключаем Load Balancer на новое окружение..."
./scripts/blue-green-switch.sh "$NEW_ENV"

# Шаг 7: Финальная проверка
echo ""
echo "7. Финальная проверка..."
sleep 5
if curl -f http://localhost:8082/health 2>/dev/null; then
    echo "✅ Деплой завершен успешно!"
    echo "Приложение доступно по адресу: http://localhost:8082"
else
    echo "❌ Ошибка! Приложение недоступно"
    exit 1
fi

echo ""
echo "Статус окружений:"
./scripts/blue-green-switch.sh status
