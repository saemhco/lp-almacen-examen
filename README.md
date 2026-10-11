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

Cada respuesta es un solo flag: un dato, sin explicación.

1. `php artisan serve` no deja el servidor en pie. Corrige la causa. Cuando http://127.0.0.1:8000/ responda, copia en la hoja el flag que muestra esa página. http://127.0.0.1:8000/api/documentation también debe abrir.
2. Después de sembrar, abre http://127.0.0.1:8000/flags/admin. La vista calcula el flag y te indica cuál copiar.
3. Abre http://127.0.0.1:8000/api/mensaje. Es un GET y responde JSON. Usa el token de un usuario administrador. El flag es el campo respuesta.
4. En Swagger o en Postman, con el token de un administrador, envía `POST /api/registro` con este cuerpo:

```json
{
  "nombres": "Tu nombre",
  "apellidos": "Tus apellidos",
  "codigo_universitario": "Tu código"
}
```

La respuesta indica el archivo y qué corregir. Cuando funcione, el flag es el código que devuelve.

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
