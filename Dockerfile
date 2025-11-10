# Imagen base con PHP 8.2 + FrankenPHP
FROM dunglas/frankenphp:php8.2-bookworm

# Instalar NODEJS (necesario para Vite)
RUN apt-get update && apt-get install -y curl \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar extensiones necesarias para Laravel + Filament
RUN install-php-extensions \
    intl \
    zip \
    gd \
    pdo \
    pdo_mysql \
    mbstring \
    fileinfo \
    bcmath \
    exif \
    xml \
    curl \
    opcache

# Crear carpeta de la aplicación
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Instalar Composer (desde imagen oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias JS y construir Vite
RUN npm install && npm run build

# Ajustar permisos requeridos por Laravel
RUN chmod -R 777 storage bootstrap/cache

# Exponer el puerto del servidor
EXPOSE 80

# Iniciar Laravel con FrankenPHP sin Octane
CMD ["frankenphp", "run", "--port=80", "--worker", "public/index.php"]
