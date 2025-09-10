# Creating .env file if does not exists
[ ! -f .env ] && cp .env.example .env

# Builds the image
docker compose build php --no-cache --build-arg UID=$UID

# Run the containers
docker compose up -d
docker compose exec php composer install --no-dev --optimize-autoloader --prefer-dist --classmap-authoritative
docker compose exec php composer update --no-dev --optimize-autoloader --prefer-dist --classmap-authoritative
docker compose exec php composer dump-autoload -o
