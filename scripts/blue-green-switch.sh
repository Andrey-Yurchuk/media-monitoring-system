#!/bin/bash

# Скрипт для переключения Blue-Green deployment
# Использование: ./scripts/blue-green-switch.sh [blue|green]

set -e

NGINX_LB_CONFIG="/var/www/html/media-monitoring-system/docker/nginx/nginx-lb.conf"
NGINX_LB_CONTAINER="nginx-lb-mms"

# Функция для переключения на Blue
switch_to_blue() {
    echo "Переключаемся на Blue (порт 8080)..."
    
    # Создаем новую конфигурацию для Blue
    cat > "$NGINX_LB_CONFIG" << 'EOF'
user nginx;
worker_processes  auto;
error_log  /var/log/nginx/error.log warn;
pid        /var/run/nginx.pid;

events {
    worker_connections  1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    sendfile        on;
    keepalive_timeout  65;

    # Upstream для Blue-Green deployment
    upstream app_backend {
        # Blue активен (порт 8080)
        server host.docker.internal:8080;
        
        # Green в резерве (порт 8081)
        # server host.docker.internal:8081 backup;
    }

    server {
        listen 80;
        server_name _;
        
        # Health check endpoint - проксируем на активное окружение
        location /health {
            proxy_pass http://app_backend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # Основное приложение
        location / {
            proxy_pass http://app_backend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
            
            # Таймауты
            proxy_connect_timeout 5s;
            proxy_send_timeout 5s;
            proxy_read_timeout 5s;
        }
    }
}
EOF
    
    # Перезагружаем nginx
    docker exec "$NGINX_LB_CONTAINER" nginx -s reload
    
    echo "Переключение на Blue завершено"
}

# Функция для переключения на Green
switch_to_green() {
    echo "Переключаемся на Green (порт 8081)..."
    
    # Создаем новую конфигурацию для Green
    cat > "$NGINX_LB_CONFIG" << 'EOF'
user nginx;
worker_processes  auto;
error_log  /var/log/nginx/error.log warn;
pid        /var/run/nginx.pid;

events {
    worker_connections  1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    sendfile        on;
    keepalive_timeout  65;

    # Upstream для Blue-Green deployment
    upstream app_backend {
        # Blue в резерве (порт 8080)
        # server host.docker.internal:8080 backup;
        
        # Green активен (порт 8081)
        server host.docker.internal:8081;
    }

    server {
        listen 80;
        server_name _;
        
        # Health check endpoint - проксируем на активное окружение
        location /health {
            proxy_pass http://app_backend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # Основное приложение
        location / {
            proxy_pass http://app_backend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
            
            # Таймауты
            proxy_connect_timeout 5s;
            proxy_send_timeout 5s;
            proxy_read_timeout 5s;
        }
    }
}
EOF
    
    # Перезагружаем nginx
    docker exec "$NGINX_LB_CONTAINER" nginx -s reload
    
    echo "Переключение на Green завершено"
}

# Функция для проверки статуса
check_status() {
    echo "Текущий статус Blue-Green deployment:"
    echo "======================================"
    
    # Проверяем Blue
    if curl -f http://localhost:8080/health 2>/dev/null; then
        echo "✅ Blue (порт 8080): Работает"
    else
        echo "❌ Blue (порт 8080): Не работает"
    fi
    
    # Проверяем Green
    if curl -f http://localhost:8081/health 2>/dev/null; then
        echo "✅ Green (порт 8081): Работает"
    else
        echo "❌ Green (порт 8081): Не работает"
    fi
    
    # Проверяем Load Balancer
    if curl -f http://localhost:8082/health 2>/dev/null; then
        echo "✅ Load Balancer (порт 8082): Работает"
    else
        echo "❌ Load Balancer (порт 8082): Не работает"
    fi
}

# Основная логика
case "${1:-status}" in
    "blue")
        switch_to_blue
        ;;
    "green")
        switch_to_green
        ;;
    "status")
        check_status
        ;;
    *)
        echo "Использование: $0 [blue|green|status]"
        echo ""
        echo "Команды:"
        echo "  blue   - Переключиться на Blue (порт 8080)"
        echo "  green  - Переключиться на Green (порт 8081)"
        echo "  status - Показать текущий статус"
        exit 1
        ;;
esac
