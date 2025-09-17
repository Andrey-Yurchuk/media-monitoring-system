#!/bin/bash

# Скрипт для настройки GitHub Actions Self-hosted Runner
# Запускать от имени обычного пользователя (не root)

set -e

echo "Настройка GitHub Actions Self-hosted Runner"
echo "=============================================="

# Проверяем, что мы не root
if [ "$EUID" -eq 0 ]; then
    echo "Не запускайте этот скрипт от имени root!"
    echo "Запустите от имени обычного пользователя: ./scripts/setup-runner.sh"
    exit 1
fi

# Создаем папку для runner
RUNNER_DIR="$HOME/actions-runner"
echo "Создаем папку для runner: $RUNNER_DIR"

if [ -d "$RUNNER_DIR" ]; then
    echo "Папка $RUNNER_DIR уже существует"
    read -p "Удалить существующую папку? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm -rf "$RUNNER_DIR"
    else
        echo "Отменено"
        exit 1
    fi
fi

mkdir -p "$RUNNER_DIR"
cd "$RUNNER_DIR"

# Скачиваем последнюю версию runner
echo "Скачиваем GitHub Actions Runner..."
LATEST_VERSION=$(curl -s https://api.github.com/repos/actions/runner/releases/latest | grep -o '"tag_name": "[^"]*' | grep -o '[^"]*$')
echo "Версия: $LATEST_VERSION"

curl -o "actions-runner-linux-x64-${LATEST_VERSION}.tar.gz" -L "https://github.com/actions/runner/releases/download/${LATEST_VERSION}/actions-runner-linux-x64-${LATEST_VERSION}.tar.gz"

# Распаковываем
echo "Распаковываем runner..."
tar xzf "./actions-runner-linux-x64-${LATEST_VERSION}.tar.gz"

# Удаляем архив
rm "./actions-runner-linux-x64-${LATEST_VERSION}.tar.gz"

echo ""
echo "Runner скачан и распакован!"
echo ""
echo "Следующие шаги:"
echo "1. Перейдите в GitHub репозиторий"
echo "2. Settings → Actions → Runners"
echo "3. Нажмите 'New self-hosted runner'"
echo "4. Выберите Linux x64"
echo "5. Скопируйте команду конфигурации"
echo "6. Запустите её в папке: $RUNNER_DIR"
echo ""
echo "Пример команды конфигурации:"
echo "./config.sh --url https://github.com/USERNAME/REPO --token TOKEN"
echo ""
echo "После конфигурации запустите:"
echo "sudo ./svc.sh install"
echo "sudo ./svc.sh start"
echo ""
echo "Готово! Runner будет автоматически выполнять деплои при push в main ветку."
