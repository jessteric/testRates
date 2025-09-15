# Installation

`docker compose -f docker/compose.yml up -d --build`

`docker compose -f docker/compose.yml exec php bash -lc "cd app && composer install"`

`docker compose -f docker/compose.yml exec php bash -lc "cd app && php bin/console doctrine:database:create"`

`docker compose -f docker/compose.yml exec php bash -lc "cd app && php bin/console doctrine:migrations:diff"`

`docker compose -f docker/compose.yml exec php bash -lc "cd app && php bin/console doctrine:migrations:migrate -n"`

# Composer
`
composer require \
symfony/framework-bundle symfony/http-client symfony/validator symfony/serializer \
symfony/monolog-bundle nelmio/cors-bundle \
doctrine/orm doctrine/doctrine-bundle doctrine/doctrine-migrations-bundle`

`composer require --dev symfony/test-pack phpunit/phpunit`

# API
`GET /api/rates/last-24h?pair=EUR/BTC`

`GET /api/rates/day?pair=EUR/BTC&date=YYYY-MM-DD`

# Commands
``php bin/console app:rates:fetch`` - take rates 

# Database and Tests
Postgres - forgot update docker compose
Tests - doesnt work only test for Controller