# Almacén

Examen de la API de almacén. Las respuestas se buscan en el código y probando la API. La guía anterior quedó en `README.old`.

## Requisitos mínimos para levantar

Hace falta PHP 8.2 o superior, Composer y la extensión de SQLite. En la carpeta del proyecto:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

- `composer install` instala Laravel y el resto de dependencias.
- `.env` es la configuración local. `key:generate` crea `APP_KEY`.
- `touch` crea la base SQLite. En `.env`, `DB_CONNECTION=sqlite`.
- `migrate --seed` crea las tablas y carga los datos de prueba.
- `serve` levanta el servidor en `http://127.0.0.1:8000`.

El proyecto está levantado cuando estas dos direcciones responden sin error:

- http://127.0.0.1:8000/
- http://127.0.0.1:8000/api/documentation

## Preguntas

1. Levanta el proyecto. Si `php artisan serve` muestra un error, léelo y corrígelo. Queda resuelto cuando abren la página de inicio y Swagger.
2. En el modelo `User`, ¿qué método comprueba si esa persona tiene un permiso como `items.create`, y qué relación usa para llegar a su rol?
3. Después de un login correcto, ¿en qué clave del JSON viene el token? Si luego se llama a `POST /api/logout` y se reutiliza ese mismo token, ¿qué código y qué mensaje devuelve la API?
4. Un administrador intenta borrar un rol que todavía tiene usuarios. ¿Qué código HTTP y qué mensaje exacto devuelve la API?
5. ¿Qué permisos tiene el rol `supervisor` que el rol `almacenista` no tiene?

## Comandos de Laravel

| Comando | Para qué sirve |
| --- | --- |
| `php artisan serve` | Levanta el servidor local en el puerto 8000. |
| `php artisan key:generate` | Genera `APP_KEY` en `.env`. |
| `php artisan migrate` | Crea o actualiza las tablas con las migraciones pendientes. |
| `php artisan migrate:status` | Muestra qué migraciones ya corrieron. |
| `php artisan migrate:rollback` | Deshace el último grupo de migraciones. |
| `php artisan migrate:fresh --seed` | Borra las tablas, las vuelve a crear y ejecuta los seeders. Úsalo solo en local. |
| `php artisan db:seed` | Carga los datos de `DatabaseSeeder`. |
| `php artisan db:seed --class=ItemSeeder` | Ejecuta un seeder concreto. |
| `php artisan make:model Nombre -mfs` | Crea el modelo, su migración (`-m`), su factory (`-f`) y su seeder (`-s`). |
| `php artisan make:controller NombreController --api` | Crea un controlador con `index`, `store`, `show`, `update` y `destroy`. |
| `php artisan make:request StoreItemRequest` | Crea una clase para las reglas de validación. |
| `php artisan make:seeder NombreSeeder` | Crea un seeder. |
| `php artisan make:factory NombreFactory --model=Nombre` | Crea un factory para datos de prueba. |
| `php artisan route:list` | Lista las rutas registradas. |
| `php artisan tinker` | Abre una consola para probar modelos. Salir con `exit`. |
| `php artisan list` | Lista todos los comandos de Artisan. |
| `php artisan --version` | Muestra la versión de Laravel. |
