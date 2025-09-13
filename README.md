# Lista de Chequeo

**Proyecto:** Sistema para gestión de permisos, autorizaciones, verificación diaria y programación de turnos.

## Descripción
Lista de Chequeo es una aplicación desarrollada en **Laravel** con **Filament** para administrar personal, permisos de trabajo, autorizados, verificaciones diarias y reportes. Está orientada a operaciones de seguridad y control de turnos.

## Características principales
- Gestión de usuarios y colaboradores.
- Registro y verificación diaria de permisos (`permiso_trabajos`, `permiso_trabajo_detalles`).
- Listado y verificación de autorizados asociados a permisos.
- Gestión de equipos, novedades y reportes.
- Vista en Filament para administración (panel administrativo).
- Generación de reportes (PDF) y visor integrado.

## Requisitos
- PHP 8.1+
- Composer
- MySQL / MariaDB (o otra DB SQL compatible)
- Node.js + npm
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath
- (Opcional) Imagick para procesamiento de imágenes y DomPDF si usas generación de PDFs.

## Instalación (local)
1. Clona el repositorio:
   ```bash
   git clone https://github.com/raulreyes10982/listadechequeo.git
   cd listadechequeo
   ```
2. Instala dependencias PHP:
   ```bash
   composer install --prefer-dist --no-interaction
   ```
3. Copia y configura variables de entorno:
   ```bash
   cp .env.example .env
   # o crea .env manualmente y configura DB, MAIL, etc.
   php artisan key:generate
   ```
4. Configura la base de datos en `.env` y ejecuta migraciones + seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Link de storage y assets front:
   ```bash
   php artisan storage:link
   npm install
   npm run build   # o npm run dev durante desarrollo
   ```
6. Levanta el servidor:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
7. Accede al panel de Filament (por defecto `/admin` o según configuración):
   - URL: `http://localhost:8000/admin`

## Notas de desarrollador
- El proyecto usa Filament v3 para los recursos administrativos.
- Revisa `app/Filament/Resources` para ver recursos ya implementados (Permisos, Autorizados, Verificaciones, Reportes, etc.).
- Estructura de tablas principales: `permiso_trabajos`, `permiso_trabajo_detalles`, `autorizados`, `personal`, `novedades`.

## Cómo contribuir
Lee `CONTRIBUTING.md` antes de abrir issues o PRs.

## Licencia
Este proyecto está bajo la licencia en `LICENSE.md`.

## Roadmap
Ver `ROADMAP.md` para prioridades y tareas.
