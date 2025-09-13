# Contribuir al proyecto Lista de Chequeo

Gracias por interesarte en contribuir. Antes de enviar un PR, por favor sigue estas indicaciones:

1. Haz fork del repositorio y crea una rama con nombre claro:
   - `feature/<descripcion-corta>`
   - `fix/<descripcion-corta>`
   - `docs/<descripcion-corta>`
2. Mantén el código conforme a PSR-12. Usa `phpcs` o tu editor para formatear.
3. Escribe tests cuando agregues funcionalidad crítica.
4. Ejecuta linters y tests localmente antes de abrir el PR:
   ```bash
   composer install
   php artisan test
   vendor/bin/phpcs --standard=PSR12 app/
   ```
5. En el PR incluye una descripción del cambio, cómo probarlo y screenshots si aplica.
6. Los commits deberían seguir convenciones claras: `feat:`, `fix:`, `docs:`, `chore:`.
