# Examen

Las respuestas se encuentran resolviendo cada pregunta. Cada respuesta es un solo flag: un dato, sin explicación.

## Pregunta 1

Clona el repositorio https://github.com/saemhco/lp-almacen-examen. Luego, `php artisan serve` no deja el servidor en pie. Corrige la causa. Cuando http://127.0.0.1:8000/ responda, copia en la hoja el flag que muestra esa página.

```
FLAG{...}
```

## Pregunta 2

Después de sembrar (correr los seeders), abre http://127.0.0.1:8000/flags/admin. La vista calcula el flag y te indica cuál copiar.

```
FLAG{cantidad}
```

## Pregunta 3

Abre http://127.0.0.1:8000/api/mensaje. Es un GET y responde JSON. Usa el token de un usuario almacenista. El flag es `data.respuesta`.

```json
{
  "success": true,
  "code": 200,
  "message": "El flag es data.respuesta.",
  "data": {
    "respuesta": "FLAG{...}"
  }
}
```

## Pregunta 4

En Swagger (http://127.0.0.1:8000/api/documentation) o en Postman, con el token de un administrador, envía `POST /api/registro` con este cuerpo:

```json
{
  "nombres": "Tu nombre",
  "apellidos": "Tus apellidos",
  "codigo_universitario": "Tu código"
}
```

La respuesta indica el archivo y qué corregir. Cuando funcione, el flag es `data.mensaje`.

```json
{
  "success": true,
  "code": 200,
  "message": "La respuesta está en data.mensaje.",
  "data": {
    "mensaje": "FLAG{...}"
  }
}
```

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

# Requisitos mínimos para levantar

PHP 8.2 o superior y Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env`. Luego:

```bash
php artisan migrate --seed
php artisan serve
```

- http://127.0.0.1:8000/
- http://127.0.0.1:8000/api/documentation

# Comandos de Laravel

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
