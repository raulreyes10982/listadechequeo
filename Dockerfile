# Usar la imagen oficial de FrankenPHP con PHP 8.2
FROM dunglas/frankenphp:php8.2-bookworm

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

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node (solo si usas Vite)
RUN npm install && npm run build

# Ajustar permisos requeridos por Laravel
RUN mkdir -p storage \
    && mkdir -p bootstrap/cache \
    && chmod -R 777 storage \
    && chmod -R 777 bootstrap/cache

# Exponer el puerto del servidor
EXPOSE 80

# Iniciar Laravel usando FrankenPHP (Octane)
CMD ["php", "artisan", "octane:start", "--server=frankenphp",]()
