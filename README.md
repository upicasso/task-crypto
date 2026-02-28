# Docker + PHP 8.1 + Xdebug 3 + MySQL 8 + Nginx 1.21 + Symfony Boilerplate 🐳

## Description

This is a basic stack for **create y running Symfony project** into Docker containers.
Here are the docker-compose built images:

- `nginx`, acting as the webserver v1.21.
- `php`, the PHP-FPM container with the 8.1 version of PHP. Xdebug is available by default.
- `mysql` which is the MySQL database container with a **MySQL 8.0** image.

## Directory strcuture

```
Project (/var/www/)
├── docker
│   ├── mysql
│   ├── nginx
│   └── php
└── app
    └── .. all symfony content
```

## Installation
### 0: Before install

Create .env file inside ***docker*** folder and put your own values to variables defined here

```
########################################################################
# DOCKER
########################################################################
# Set Docker Compose project name
COMPOSE_PROJECT_NAME=crypto-investments
CONTAINER_PREFIX_NAME=app-crypto

########################################################################
# NGINX
########################################################################
NGINX_SERVER_NAME=crypto.test

########################################################################
# MySQL
########################################################################
MYSQL_ROOT_PASSWORD=secret
MYSQL_DATABASE=app_docker
MYSQL_USER=symfony
MYSQL_PASSWORD=symfony
```

Also you need you need to create .env file inside ***app*** folder with sample content

```
# In all environments, the following files are loaded if they exist,
# the latter taking precedence over the former:
#
#  * .env                contains default values for the environment variables needed by the app
#  * .env.local          uncommitted file with local overrides
#  * .env.$APP_ENV       committed environment-specific defaults
#  * .env.$APP_ENV.local uncommitted environment-specific overrides
#
# Real environment variables win over .env files.
#
# DO NOT DEFINE PRODUCTION SECRETS IN THIS FILE NOR IN ANY OTHER COMMITTED FILES.
# https://symfony.com/doc/current/configuration/secrets.html
#
# Run "composer dump-env prod" to compile .env files for production use (requires symfony/flex >=1.2).
# https://symfony.com/doc/current/best_practices.html#use-environment-variables-for-infrastructure-configuration

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=
APP_SHARE_DIR=var/share
###< symfony/framework-bundle ###

###> symfony/routing ###
# Configure how to generate URLs in non-HTTP contexts, such as CLI commands.
# See https://symfony.com/doc/current/routing.html#generating-urls-in-commands
DEFAULT_URI=http://localhost
###< symfony/routing ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
DATABASE_URL="mysql://symfony:symfony@app-crypto-mysql:3306/app_docker?serverVersion=8.0.32&charset=utf8mb4"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
#DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
###< doctrine/doctrine-bundle ###

```

### 🚀 1: Quick Start 

1. Clone this repository.
2. (Optional) Customize your local domain by editing the variable `NGINX_SERVER_NAME` in [`docker/.env`](docker/.env#L11) **before** running the setup.
3. From the project root, run****:****
   ```
   chmod +x setup.sh
   ./setup.sh
   ```
   This script will:
   - Build and start all containers.
   - If `/app` is empty, it will create a new Symfony project automatically.
   - Set correct permissions for the `var` directory.

---

### 2: Setting up Symfony application

Go inside PHP container by command
```
docker exec -it {PHP_CONTAINER_NAME} bash
```

Run migrations:
```
php bin/console doctrine:migration:migrate
php bin/console doctrine:migration:migrate --env=test
```

Load fixtures:
```
php bin/console doctrine:fixtures:load
php bin/console doctrine:fixtures:load --env=test
```

Run tests:
```
php bin/phpunit
```

## Xdebug

Available by default. Configure `/docker/php/xdebug.ini`

- In case you want to deactivate it:

```
xdebug.mode=off
```

- In case you need debug only requests with IDE KEY: PHPSTORM from frontend in your browser:

```
xdebug.start_with_request = no
xdebug.idekey=PHPSTORM
```

- In case you need debug any request to an api (by default):

```
xdebug.start_with_request = yes
```
