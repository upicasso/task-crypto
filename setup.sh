#!/bin/bash

set -e


echo "🔧 Build and start containers"
docker compose -f docker/docker-compose.yml build
docker compose -f docker/docker-compose.yml up -d

if [ -z "$(ls -A ./app 2>/dev/null)" ]; then
  echo "📦 Create new symfony application ./app..."
  docker compose -f docker/docker-compose.yml exec php bash -c "cd /var/www/symfony && symfony new ."
else
  echo "✅ Application is installed."
fi

if [ -d "./app/var" ]; then
  echo "🔑 Setting up permissions  en ./app/var..."
  sudo chmod -R 777 ./app/var
fi

echo "🎉 Setup complete. Check ./docker/.env"