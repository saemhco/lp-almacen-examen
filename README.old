# Almacén

API REST en Laravel 12 para administrar un almacén. La base de datos local es SQLite. La documentación interactiva está en `/api/documentation`.

## Flujo de la app

El catálogo se arma antes que el inventario:

1. Una **categoría** agrupa los productos (ferretería, pinturas, electricidad).
2. Un **proveedor** es quien surte esos productos.
3. Una **ubicación** dice en qué pasillo o bodega está el producto.
4. Un **ítem** pertenece a una categoría, un proveedor y una ubicación. Guarda nombre, descripción, precio y cantidad.
5. Un **movimiento** registra una entrada o una salida de un ítem: quién la hizo, la cantidad y una nota. La entrada inicial de cada ítem queda como carga de stock.
6. Un **usuario** tiene un **rol**. El rol agrupa **permisos** con el nombre `recurso.accion`, por ejemplo `items.delete` o `movements.create`.

`POST /api/login` devuelve un token de Sanctum. El resto de las rutas piden `Authorization: Bearer {token}`. El middleware `permission` deja pasar la acción solo si el rol del usuario tiene ese permiso, por ejemplo `users.view`.

Todas las respuestas JSON usan las mismas claves: `success`, `code`, `message` y `data`.

## Roles y funciones

La contraseña de los tres usuarios de prueba es `password`.

| Rol | Usuario | Función |
| --- | --- | --- |
| `admin` | `admin@example.com` | Administra el catálogo, los movimientos, los usuarios, los roles y los permisos. Tiene ver, crear, actualizar y eliminar en cada recurso. |
| `supervisor` | `supervisor@example.com` | Revisa el almacén, corrige ítems y ajusta movimientos. Ve los cinco recursos, actualiza ítems y crea o actualiza movimientos. No crea ni elimina el catálogo, y no elimina movimientos. |
| `almacenista` | `almacenista@example.com` | Consulta el almacén y registra entradas y salidas. Puede ver los cinco recursos y crear movimientos. No crea, ni edita, ni elimina el catálogo, y no modifica ni borra movimientos. |

Permisos del almacenista:

| Permiso | Qué permite |
| --- | --- |
| `items.view`, `categories.view`, `suppliers.view`, `locations.view`, `movements.view` | Listar y ver un registro |
| `movements.create` | Registrar una entrada o una salida |

El supervisor tiene esos mismos permisos y, además, `items.update` y `movements.update`.

Solo el administrador puede gestionar usuarios, roles y permisos, y también crear, editar o borrar el catálogo (categorías, proveedores y ubicaciones), crear o borrar ítems y borrar movimientos. El almacenista y el supervisor reciben `403` en esas rutas.

| Rutas solo de admin | Permisos |
| --- | --- |
| `/api/users` | `users.view`, `users.create`, `users.update`, `users.delete` |
| `/api/roles` | `roles.view`, `roles.create`, `roles.update`, `roles.delete` |
| `/api/permissions` | `permissions.view`, `permissions.create`, `permissions.update`, `permissions.delete` |
| Alta, edición y borrado de categorías, proveedores y ubicaciones | `create`, `update` y `delete` de cada uno |
| Alta y borrado de ítems, borrado de movimientos | `items.create`, `items.delete`, `movements.delete` |

## Qué creamos

| Pieza | Archivo | Para qué |
| --- | --- | --- |
| Proyecto Laravel | raíz del repo | Esqueleto de la aplicación |
| Ítem | `app/Models/Item.php` | Producto del almacén |
| Categoría, proveedor y ubicación | `app/Models/Category.php`, `Supplier.php`, `Location.php` | Datos a los que pertenece cada ítem |
| Movimiento | `app/Models/Movement.php` | Entrada o salida de stock |
| Rol y permiso | `app/Models/Role.php`, `Permission.php` | Qué puede hacer cada usuario |
| Usuario | `app/Models/User.php` | Persona que entra al sistema y tiene un rol |
| Middleware | `app/Http/Middleware/CheckPermission.php` | Compara la ruta con un permiso del rol |
| Form requests | `app/Http/Requests/` | Reglas de validación de cada alta y actualización |
| Seeders | `database/seeders/` | Cargan permisos, roles, usuarios, catálogo, ítems y movimientos |
| Controladores | `app/Http/Controllers/` | CRUD de cada recurso, más login y logout |
| Trait de respuestas | `app/Traits/ApiResponseTrait.php` | Formato JSON de éxito y error |
| Rutas API | `routes/api.php` | Endpoints bajo `/api` |
| Sanctum | `laravel/sanctum` | Tokens de acceso. Protegen las rutas junto con el middleware `permission` |
| Swagger | `app/OpenApi/Documentation.php` y los controladores | Documenta cuerpos, permisos y códigos de error |

`POST /api/login` es público. `POST /api/logout` pide token. Cada recurso tiene las mismas cinco rutas y el middleware `permission:{recurso}.{accion}`. `{id}` es el id del registro.

| Método | Rutas | Permiso |
| --- | --- | --- |
| GET | `/api/items`, `/api/categories`, `/api/suppliers`, `/api/locations`, `/api/movements`, `/api/users`, `/api/roles`, `/api/permissions` | `{recurso}.view` |
| POST | las mismas | `{recurso}.create` |
| GET | `/api/{recurso}/{id}` | `{recurso}.view` |
| PUT | `/api/{recurso}/{id}` | `{recurso}.update` |
| DELETE | `/api/{recurso}/{id}` | `{recurso}.delete` |

En un rol, `permission_ids` asigna los permisos en el alta y en la actualización. Una validación que falla responde `422` con los errores en `data`.

Para probar en Swagger: `POST /api/login` con `admin@example.com` y `password`, copiar `data.token` y pegarlo en **Authorize**. El login es la única ruta pública.

## Laravel

### Crear el proyecto y dejarlo listo

```bash
composer create-project laravel/laravel almacen
cd almacen
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

- `composer create-project` descarga Laravel y sus dependencias.
- `.env` guarda la configuración local (no se sube a GitHub).
- `key:generate` crea `APP_KEY`, necesaria para cifrar sesiones y cookies.
- `touch` crea el archivo de SQLite. En `.env`, `DB_CONNECTION=sqlite`.
- `migrate` ejecuta las migraciones y crea las tablas.
- `serve` levanta el servidor en `http://127.0.0.1:8000`.

### Instalar Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

`vendor:publish` copia la migración de `personal_access_tokens`. `migrate` crea esa tabla.

### Crear modelo, migración, factory, seeder y controlador

```bash
php artisan make:model Item -mfs
php artisan make:controller ItemController --api
php artisan make:seeder ItemSeeder
```

En `make:model`, las banderas son:

- `-m` migración
- `-f` factory
- `-s` seeder

`--api` genera los métodos `index`, `store`, `show`, `update` y `destroy`, sin las vistas de un controlador web.

El trait `ApiResponseTrait` se escribió a mano en `app/Traits/`. Artisan no trae un comando para traits.

Las reglas de validación viven en un Form Request, no en el controlador:

```bash
php artisan make:request StoreItemRequest
```

`StoreItemRequest` y `UpdateItemRequest` extienden `ApiFormRequest`, que devuelve el JSON de error `422`.

### Migraciones

```bash
php artisan make:migration create_items_table
php artisan migrate
php artisan migrate:status
php artisan migrate:rollback
php artisan migrate:fresh
php artisan migrate:fresh --seed
```

- `migrate` aplica las migraciones pendientes.
- `migrate:status` muestra cuáles ya corrieron.
- `rollback` deshace el último lote.
- `migrate:fresh` borra todas las tablas y las vuelve a crear. En clase se usa en local, nunca sobre datos que haya que conservar.
- `--seed` ejecuta los seeders justo después.

### Seeders y factories

```bash
php artisan make:factory ItemFactory --model=Item
php artisan make:seeder ItemSeeder
php artisan db:seed
php artisan db:seed --class=ItemSeeder
```

- `db:seed` corre `DatabaseSeeder`: permisos, roles, usuario admin, categorías, proveedores, ubicaciones, ítems y movimientos. `RoleSeeder` también crea a los usuarios almacenista y supervisor.
- `--class` corre un seeder concreto.
- Dentro del seeder, `Item::factory()->count(10)->create()` usa el factory para insertar 10 ítems falsos.

### Ver rutas y probar en consola

```bash
php artisan route:list
php artisan tinker
```

En Tinker, ejemplos de lo que ya existe en el modelo:

```php
Item::all();
Item::find(1);
Item::factory()->create();
```

Salir de Tinker: `exit`.

### Comandos que conviene recordar

```bash
php artisan list
php artisan --version
php artisan make:model Nombre -mfs
php artisan make:controller NombreController --api
php artisan migrate:fresh --seed
php artisan serve
```

## Git y GitHub

### Empezar el repositorio

```bash
git init
git status
git add .
git commit -m "Agrega modelo, migracion, factory, seeder, controlador y rutas de items"
```

- `git init` convierte la carpeta en un repositorio.
- `git status` muestra qué cambió y qué no está incluido.
- `git add .` prepara todos los cambios. `.env`, `vendor/` y `node_modules/` quedan fuera por `.gitignore`.
- `git commit` guarda una versión. El mensaje dice qué se creó en la clase: modelo, migración, factory, seeder, controlador y rutas.

### Ver el historial y deshacer lo que aún no se commiteó

```bash
git log --oneline
git diff
git restore archivo.php
git restore --staged archivo.php
```

- `git log` muestra los commits.
- `git diff` muestra los cambios sin commitear.
- `git restore` descarta cambios de un archivo que todavía no está en stage.
- `git restore --staged` lo saca del stage y lo deja como cambio local.

### Ramas

```bash
git branch
git switch -c feature/items
git switch main
git merge feature/items
```

- `git branch` lista las ramas.
- `git switch -c` crea una rama y se cambia a ella.
- `git merge` integra esa rama en la actual.

### Subir el proyecto a GitHub

Crear el repositorio vacío en GitHub (sin README, para no pisar este) y conectarlo:

```bash
git remote add origin git@github.com:USUARIO/almacen.git
git branch -M main
git push -u origin main
```

- `remote add` guarda la dirección del repositorio remoto con el nombre `origin`.
- `branch -M main` nombra la rama principal `main`.
- `push -u` sube los commits y deja `main` siguiendo a `origin/main`. Las siguientes veces basta con `git push`.

### Bajar cambios y clonar

```bash
git pull
git clone git@github.com:USUARIO/almacen.git
```

- `git pull` trae y fusiona lo que haya en GitHub.
- `git clone` copia un repositorio completo a una carpeta nueva.

### Secuencia de cada cambio

```bash
git status
git add .
git commit -m "Agrega modelo, migracion, factory, seeder, controlador y rutas de items"
git push
```

## Dejar el proyecto corriendo desde cero

En una máquina nueva, después de clonar:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Swagger queda en `http://127.0.0.1:8000/api/documentation`. Para llamar a la API, primero `POST /api/login` y después enviar el token en `Authorization: Bearer {token}`.
