# contro-inv-eleck

Aplicación web para el **control de inventario de un almacén de productos electrónicos**, desarrollada con [Laravel](https://laravel.com).

El sistema permite gestionar productos, categorías, proveedores, movimientos de stock (entradas/salidas), alertas de bajo inventario y reportes filtrables, con soporte para roles de usuario (`Administrador` y `Empleado`).

---

## Funcionalidades principales

- **CRUD de productos** con SKU único e inmutable, categoría, proveedor, precio, cantidad inicial y stock mínimo.
- **Gestión de categorías y proveedores** con restricción de eliminación cuando tienen productos asociados.
- **Control de stock** mediante movimientos de entrada y salida que actualizan automáticamente las existencias.
- **Alertas de bajo inventario** cuando un producto alcanza su stock mínimo configurado.
- **Reportes** filtrables por fecha, producto, categoría y proveedor, con exportación a PDF/Excel.
- **Dashboard consolidado** con indicadores de inventario total, productos con bajo stock y tendencias de movimiento.
- **Roles de usuario**:
  - **Administrador**: acceso total al sistema, incluyendo gestión de usuarios, categorías, proveedores y configuración.
  - **Empleado**: registro de movimientos, consulta de productos y reportes, sin acceso a administración de usuarios ni catálogos.

---

## Tecnologías

- **Backend:** Laravel 13.x (PHP ^8.3)
- **Frontend:** Vite 8, Tailwind CSS 4, Blade
- **Fuente:** Instrument Sans (Bunny Fonts)
- **Base de datos:** MySQL/MariaDB, PostgreSQL, SQLite o SQL Server
- **Testing:** Pest PHP 4.x
- **Linting:** Laravel Pint

---

## Requisitos

- PHP >= 8.3
- Composer
- Node.js y npm
- Extensión PDO de la base de datos a utilizar

---

## Instalación rápida

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio>
cd contro-inv-eleck

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar la base de datos en el archivo .env
# DB_CONNECTION=mysql
# DB_DATABASE=contro_inv_eleck
# DB_USERNAME=root
# DB_PASSWORD=secret

# 5. Ejecutar migraciones
php artisan migrate

# 6. Compilar assets
npm run build

# 7. Iniciar servidor de desarrollo
php artisan serve
```

---

## Comandos útiles

| Comando | Descripción |
| --- | --- |
| `composer install` | Instala las dependencias de PHP. |
| `npm install` | Instala las dependencias de Node. |
| `npm run dev` | Inicia el servidor de desarrollo de Vite. |
| `npm run build` | Compila los assets para producción. |
| `php artisan serve` | Inicia el servidor de desarrollo de PHP. |
| `php artisan migrate` | Ejecuta las migraciones pendientes. |
| `php artisan test` | Ejecuta la suite de tests. |
| `vendor/bin/pint` | Ejecuta Laravel Pint para verificar el estilo de código. |
| `composer dev` | Ejecuta el servidor, Vite, queue listener y Pail en paralelo. |

---

## Testing

El proyecto utiliza [Pest PHP](https://pestphp.com). La configuración de pruebas se encuentra en `phpunit.xml`.

```bash
php artisan test
```

---

## Documentación

- Requerimientos funcionales detallados: [`Ficha-Tecnica-Requerimientos-Funcionales.md`](./Ficha-Tecnica-Requerimientos-Funcionales.md)
- Guías para agentes de IA: [`AGENTS.md`](./AGENTS.md)

---

## Licencia

Este proyecto es software de código abierto licenciado bajo la [Licencia MIT](https://opensource.org/licenses/MIT).
