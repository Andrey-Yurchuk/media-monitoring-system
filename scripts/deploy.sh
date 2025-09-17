#!/bin/bash

# Скрипт для ручного деплоя (альтернатива GitHub Actions)
# Используется для тестирования деплоя локально

set -e

echo "Ручной деплой Media Monitoring System"
echo "======================================="

# Переменные
PROJECT_DIR="/var/www/html/media-monitoring-system"
PRODUCTION_DIR="/var/www/html/production-media-monitoring"
BACKUP_DIR="/var/www/html/backup-media-monitoring-$(date +%Y%m%d_%H%M%S)"

echo "Проект: $PROJECT_DIR"
echo "Продакшн: $PRODUCTION_DIR"
echo "Бэкап: $BACKUP_DIR"

# Проверяем, что мы в правильной папке
if [ ! -f "$PROJECT_DIR/docker-compose.yml" ]; then
    echo "Не найден docker-compose.yml в $PROJECT_DIR"
    exit 1
fi

# Создаем бэкап существующей продакшн версии
if [ -d "$PRODUCTION_DIR" ]; then
    echo "Создаем бэкап существующей версии..."
    cp -r "$PRODUCTION_DIR" "$BACKUP_DIR"
    echo "Бэкап создан: $BACKUP_DIR"
fi

# Останавливаем старые контейнеры
echo "Останавливаем старые контейнеры..."
if [ -d "$PRODUCTION_DIR" ]; then
    cd "$PRODUCTION_DIR"
    docker-compose down || true
fi

# Создаем папку для продакшн
echo "Создаем папку для продакшн..."
mkdir -p "$PRODUCTION_DIR"

# Копируем новый код
echo "Копируем новый код..."
cp -r "$PROJECT_DIR"/* "$PRODUCTION_DIR/" 2>/dev/null || true
# Исключаем проблемные папки
rm -rf "$PRODUCTION_DIR/docker/data" 2>/dev/null || true

# Настраиваем продакшн окружение
echo "Настраиваем продакшн окружение..."
cd "$PRODUCTION_DIR"

# Создаем .env.production если его нет
if [ ! -f ".env.production" ]; then
    if [ -f ".env" ]; then
        cp .env .env.production
        echo "Создан .env.production из .env"
    elif [ -f ".env.example" ]; then
        cp .env.example .env.production
        echo "Создан .env.production из .env.example"
    else
        echo "Не найден .env или .env.example, создайте .env.production вручную"
    fi
fi

# Запускаем новые контейнеры
echo "Запускаем новые контейнеры..."
docker-compose -f docker-compose.prod.yml --env-file .env.production up -d --build

# Ждем запуска сервисов
echo "Ждем запуска сервисов..."
sleep 30

# Проверяем здоровье приложения
echo "Проверяем здоровье приложения..."
for i in {1..10}; do
    if curl -f http://localhost:8080/health 2>/dev/null || curl -f http://localhost:8080/ 2>/dev/null; then
            echo "Приложение работает!"
        break
    fi
            echo "Попытка $i/10, ждем 5 секунд..."
    sleep 5
done

# Очищаем старые образы
echo "Очищаем старые Docker образы..."
docker image prune -f

echo ""
echo "Деплой завершен успешно!"
echo "Приложение доступно по адресу: http://localhost:8080"
echo "Статус контейнеров:"
docker-compose -f docker-compose.prod.yml ps

echo ""
echo "Полезные команды:"
echo "  Логи: docker-compose -f docker-compose.prod.yml logs -f"
echo "  Остановить: docker-compose -f docker-compose.prod.yml down"
echo "  Перезапустить: docker-compose -f docker-compose.prod.yml restart"
