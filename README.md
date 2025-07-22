## Media Monitoring System

REST API для мониторинга новостей с возможностью генерации HTML-отчётов

### Использование

#### 1. Клонирование репозитория и переход в каталог проекта

```bash
git clone https://github.com/Andrey-Yurchuk/media-monitoring-system.git
cd media-monitoring-system
```

#### 2. Настройка переменных окружения

Создайте файл `.env` на основе `.env.example`:

```bash
cp .env.example .env
```

Отредактируйте `.env` файл:

```env
DB_HOST=<your_host>
DB_PORT=<your_port>
DB_NAME=<your_db_name>
DB_USER=<your_user>
DB_PASSWORD=<your_password>
DB_EXTERNAL_PORT=<your_external_port>
```

#### 3. Инициализация базы данных

Запустите SQL-скрипт для создания таблиц:

```bash
# Подключитесь к PostgreSQL контейнеру
docker exec -it postgres-mms psql -U <your_user> -d <your_db_name>

# Внутри psql выполните:
\i /var/www/html/database/init.sql
\q
```

Или выполните команду напрямую:

```bash
docker exec -i postgres-mms psql -U <your_user> -d <your_db_name> < database/init.sql
```

#### 4. Запуск приложения

```bash
# Сборка контейнеров
docker-compose build

# Установка зависимостей
docker-compose run --rm php composer install

# Запуск всех контейнеров
docker-compose up -d

# Проверка статуса контейнеров
docker-compose ps
```

Приложение при локальном развертывании будет доступно по адресу: **http://localhost:8080**

### API Endpoints

#### Добавление новости

```bash
curl -X POST http://localhost:8080/api/v1/news \
  -H "Content-Type: application/json" \
  -d '{"url": "https://www.example.com"}'
```

**Ответ:**
```json
{
  "id": "687fc17a6cd264.99397957"
}
```

#### Получение списка новостей

```bash
curl http://localhost:8080/api/v1/news
```

**Ответ:**
```json
[
  {
    "id": "687fc17a6cd264.99397957",
    "url": "https://www.example.com",
    "title": "Example Domain",
    "date": "2025-07-22 16:51:06"
  }
]
```

#### Генерация отчёта

```bash
curl -X POST http://localhost:8080/api/v1/reports \
  -H "Content-Type: application/json" \
  -d '{"news_ids": ["687fc17a6cd264.99397957"]}'
```

**Ответ:**
```json
{
  "report_url": "/reports/report_687fc729eb1262.02503281.html"
}
```

Отчёт будет доступен при локальном развертывании по адресу: **http://localhost:8080/reports/report_687fc729eb1262.02503281.html**
