FROM php:8.3-fpm

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Node.js 20 (para npm run dev)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs


    
WORKDIR /var/www

COPY . .

RUN composer install
RUN npm install
