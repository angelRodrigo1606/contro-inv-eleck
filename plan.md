# Plan de Implementación — Control de Inventario Electrónico

> Guía de construcción del proyecto `contro-inv-eleck` basada en la *Ficha Técnica de Requerimientos Funcionales* y en las convenciones del proyecto (Laravel 13.x, Pest, Tailwind CSS 4, Vite).

---

## 1. Objetivo

Construir una aplicación web en Laravel para controlar el inventario de un almacén de productos electrónicos, cubriendo:

- CRUD de productos, categorías y proveedores.
- Control de entradas/salidas de stock con recálculo automático.
- Alertas de bajo inventario.
- Reportes filtrables con exportación PDF/Excel/CSV.
- Roles de usuario: **Administrador** y **Empleado**.

---

## 2. Stack y convenciones

- **Backend:** Laravel 13.x, PHP 8.3+.
- **Frontend:** Tailwind CSS 4, Vite, Blade.
- **Base de datos:** SQLite para desarrollo/tests, MySQL/PostgreSQL para producción.
- **Tests:** Pest PHP 4.
- **Estilo:** Laravel Pint (PSR-12 + Laravel).
- **Namespaces:** PSR-4 (`App\`, `Database\Factories\`, `Database\Seeders\`, `Tests\`).
- **Idioma del código:** inglés (clases, variables, comentarios).
- **Idioma de la interfaz:** español (el dominio y los usuarios finales hablan español).

---

## 3. Modelos del dominio

### 3.1 `User`
- Ya existe. Modificar tabla para agregar campo `role` (`enum`: `administrador`, `empleado`).
- Un usuario puede ser responsable de muchos movimientos.

### 3.2 `Category`
| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | bigIncrements | PK |
| `name` | string(100) | único, indexado |
| `description` | text | nullable |
| `is_active` | boolean | default true |
| `timestamps` | - | |
| `softDeletes` | - | |

- Relación: una categoría tiene muchos `Product`.
- No se puede eliminar si tiene productos asociados.

### 3.3 `Supplier`
| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | bigIncrements | PK |
| `name` | string(150) | indexado |
| `contact_name` | string(150) | nullable |
| `phone` | string(50) | nullable |
| `address` | text | nullable |
| `email` | string(100) | nullable |
| `is_active` | boolean | default true |
| `timestamps` | - | |
| `softDeletes` | - | |

- Relación: un proveedor tiene muchos `Product`.
- No se puede eliminar si tiene productos asociados.

### 3.4 `Product`
| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | bigIncrements | PK |
| `name` | string(150) | indexado |
| `description` | text | nullable |
| `sku` | string(100) | único, inmutable |
| `category_id` | foreignId | constrained categories |
| `supplier_id` | foreignId | constrained suppliers |
| `price` | decimal(12,2) | >= 0 |
| `quantity` | integer | >= 0, stock actual |
| `min_stock` | integer | >= 0, default configurable |
| `timestamps` | - | |
| `softDeletes` | - | |

- Relación: pertenece a `Category` y `Supplier`.
- Relación: tiene muchos `StockMovement`.
- El SKU no se edita.
- La cantidad solo se modifica vía movimientos o ajustes (registrados explícitamente).
- Scope/attribute para detectar `is_low_stock` (`quantity <= min_stock`).

### 3.5 `StockMovement`
| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | bigIncrements | PK |
| `product_id` | foreignId | constrained products |
| `user_id` | foreignId | constrained users |
| `type` | enum | `entry` (entrada) / `exit` (salida) / `adjustment` (ajuste) |
| `quantity` | integer | > 0 para `entry`/`exit`; positivo o negativo para `adjustment` |
| `reference` | string(255) | nullable (factura, orden, cliente/destino) |
| `notes` | text | nullable |
| `created_at` / `updated_at` | timestamps | |

- Relación: pertenece a `Product` y `User`.
- Al crear un movimiento:
  - `entry` → `product.quantity += quantity`.
  - `exit` → validar stock suficiente, luego `product.quantity -= quantity`.
  - `adjustment` → `product.quantity += quantity` (positivo o negativo).
- Disparar verificación de alerta de bajo stock tras movimientos que reduzcan stock.
- **Validación:** en los Form Requests, `quantity` debe ser `required|integer|min:1` para `entry` y `exit`, y `required|integer|not_in:0` para `adjustment` (puede ser negativo, pero no cero).

### 3.6 `LowStockAlert`
| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | bigIncrements | PK |
| `product_id` | foreignId | constrained products |
| `resolved_at` | timestamp | nullable |
| `timestamps` | - | |

- Se crea (o reabre) cuando un producto baja a o por debajo de su `min_stock`.
- Se resuelve automáticamente cuando el stock supera el mínimo.
- Mostrar en dashboard de administrador.

---

## 4. Migraciones recomendadas (orden)

1. `create_categories_table`
2. `create_suppliers_table`
3. `create_products_table`
4. `create_stock_movements_table`
5. `create_low_stock_alerts_table`
6. `add_role_to_users_table` *(modifica la tabla existente de Laravel)*

Todas las tablas nuevas usan `softDeletes` salvo `stock_movements` y `low_stock_alerts`, que son tablas históricas/auditables.

---

## 5. Controladores y recursos

### 5.1 Controladores de recursos

| Controlador | Rutas | Acceso |
|-------------|-------|--------|
| `CategoryController` | resource `categories` | Admin |
| `SupplierController` | resource `suppliers` | Admin |
| `ProductController` | resource `products` | Admin (full), Employee (index/show) |
| `StockMovementController` | index, create/store | Admin + Employee (create/store) |
| `ReportController` | entries, exits, dashboard | Admin + Employee (solo lectura) |
| `UserController` | resource `users` | Admin |
| `DashboardController` | index | Autenticados |
| `LowStockAlertController` | index, resolve | Admin |

### 5.2 Acciones específicas

- `ProductController@adjust` — formulario y acción para ajustar stock manualmente (registra movimiento tipo `adjustment`).
- `ReportController@exportPdf` / `@exportExcel` / `@exportCsv` — exportaciones de reportes.
- `LowStockAlertController@index` / `@resolve` — listar y marcar alertas como resueltas.

---

## 6. Rutas (`routes/web.php`)

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Productos (lectura para empleados)
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/adjust', [ProductController::class, 'adjust'])
         ->name('products.adjust');

    // Movimientos
    Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store']);

    // Reportes
    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('reports/entries', [ReportController::class, 'entries'])->name('reports.entries');
    Route::get('reports/exits', [ReportController::class, 'exits'])->name('reports.exits');
    Route::get('reports/entries/export/{format}', [ReportController::class, 'exportEntries'])->name('reports.entries.export');
    Route::get('reports/exits/export/{format}', [ReportController::class, 'exportExits'])->name('reports.exits.export');

    // Solo administradores
    Route::middleware('role:administrador')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('users', UserController::class);
        Route::get('low-stock-alerts', [LowStockAlertController::class, 'index'])->name('low-stock-alerts.index');
        Route::patch('low-stock-alerts/{alert}/resolve', [LowStockAlertController::class, 'resolve'])
             ->name('low-stock-alerts.resolve');
    });
});
```

Rutas de autenticación de Laravel Breeze/Jetstream o implementación propia.

---

## 7. Autenticación y autorización

### 7.1 Opciones

- **Opción A (recomendada):** instalar Laravel Breeze con Blade + Tailwind.
- **Opción B:** sistema de autenticación propio con controladores manuales (login, registro, recuperación de contraseña).

La ficha técnica menciona Jetstream/Breeze. Se recomienda Breeze por ser más ligero y alineado con Tailwind.

### 7.2 Roles

- Middleware `role` personalizado.
- Gates/Policies para acciones puntuales (ej. `delete`, `manage-users`).
- `User::isAdmin()` y `User::isEmployee()` helpers.
- Implementar en la **Fase 0** para que las rutas protegidas con `role:administrador` funcionen desde el inicio.

---

## 8. Vistas (Blade + Tailwind)

### 8.1 Layout base

- `resources/views/layouts/app.blade.php` con navegación lateral, header y toast de notificaciones.
- Incluir `@vite`, `@fonts` y Alpine.js para interactividad (selects con búsqueda, modales, toasts).

### 8.2 Vistas por módulo

| Vista | Descripción |
|-------|-------------|
| `dashboard` | Indicadores: stock total valorizado, productos con bajo stock, movimientos recientes, alertas. |
| `products.index` | Tabla paginada, búsqueda, filtros, resaltado de bajo stock. |
| `products.create/edit` | Formulario con selects de categoría/proveedor, validación. |
| `products.show` | Detalle del producto + historial de movimientos. |
| `categories.*` | CRUD de categorías. |
| `suppliers.*` | CRUD de proveedores. |
| `stock-movements.create` | Formulario rápido para entrada/salida con búsqueda de producto. |
| `stock-movements.index` | Historial de movimientos con filtros. |
| `reports.entries/exits` | Tablas filtrables + botones de exportación. |
| `users.*` | Gestión de usuarios (admin). |
| `low-stock-alerts.index` | Listado de alertas pendientes (admin). |

---

## 9. Lógica de negocio clave

### 9.1 Regla de movimientos

Centralizar en `StockMovement` modelo o en un Service (`StockMovementService`):

```php
public function register(Product $product, User $user, string $type, int $quantity, ?string $reference = null, ?string $notes = null): StockMovement
{
    // Validar stock suficiente para salidas
    // Actualizar product.quantity
    // Crear movimiento dentro de DB::transaction()
    // Verificar alertas de bajo stock
}
```

### 9.2 Creación de producto

- Al crear un producto con `quantity > 0`, registrar automáticamente un `StockMovement` de tipo `entry` con referencia "Inventario inicial".

### 9.3 Ajuste manual de stock

- Diferencia entre cantidad anterior y nueva como movimiento tipo `adjustment`.
- Ejemplo: producto tenía 100, se ajusta a 90 → `adjustment` de -10.
- La cantidad resultante no puede ser negativa.

### 9.4 Alertas de bajo stock

- Listener o hook `saved`/`created` en `StockMovement`.
- Si `product.quantity <= product.min_stock` → crear alerta (si no existe activa).
- Si stock sube por encima del mínimo → resolver alerta activa.
- Resaltar filas en rojo en el listado de productos cuando `is_low_stock`.

### 9.5 Notificaciones por correo

- Implementar con Notification de Laravel.
- Enviar a administradores cuando se genere una alerta nueva.
- Encolar con `QUEUE_CONNECTION` en producción; en desarrollo puede usarse `sync`.
- Configurar queue driver en `.env` y en la Fase 4 o en el despliegue.

---

## 10. Reportes y exportaciones

### 10.1 Filtros comunes

- Rango de fechas (`from`, `to`).
- Producto (`product_id`).
- Categoría (`category_id`).
- Proveedor (`supplier_id`).

### 10.2 Reportes de entradas y salidas

- Query base sobre `stock_movements` filtrando por `type`.
- Joins con `products`, `categories`, `suppliers`, `users`.
- Columnas:
  - Entradas: fecha, producto, categoría, cantidad, proveedor, usuario, referencia.
  - Salidas: fecha, producto, categoría, cantidad, usuario, referencia, cliente/destino.

### 10.3 Exportación

- **PDF:** paquete `barryvdh/laravel-dompdf` o `spatie/laravel-pdf`.
- **Excel:** paquete `maatwebsite/excel` **solo si confirma soporte oficial para Laravel 13**; de lo contrario usar exportación **CSV nativa** de Laravel (`Symfony\Component\HttpFoundation\StreamedResponse`) o generar un archivo Excel con formato CSV.
- **CSV nativo:** alternativa robusta que no requiere dependencias adicionales.
- Crear clases `Export` o `PdfView` por tipo de reporte.

### 10.4 Dashboard

- Stock total valorizado: `sum(quantity * price)`.
- Productos con bajo stock: `Product::lowStock()->count()`.
- Movimientos recientes: últimos N movimientos.
- Gráfico de tendencias (opcional): entradas/salidas por día/semana.

---

## 11. Seeders y datos de prueba

### 11.1 Factories

- `CategoryFactory`, `SupplierFactory`, `ProductFactory`, `StockMovementFactory`, `UserFactory` (existente, agregar role).

### 11.2 DatabaseSeeder

- Crear:
  - 1 usuario administrador (`admin@example.com` / `password`).
  - 1 usuario empleado (`employee@example.com` / `password`).
  - 5–10 categorías.
  - 10–20 proveedores.
  - 50+ productos con stock variado.
  - Movimientos históricos variados.

---

## 12. Tests con Pest

### 12.1 Estructura

```
tests/
  Feature/
    AuthTest.php
    CategoryTest.php
    SupplierTest.php
    ProductTest.php
    StockMovementTest.php
    ReportTest.php
    DashboardTest.php
    UserTest.php
    LowStockAlertTest.php
  Unit/
    ProductUnitTest.php
    StockMovementUnitTest.php
    LowStockAlertUnitTest.php
```

### 12.2 Tests esenciales

- **Categorías:** CRUD, imposibilidad de eliminar con productos.
- **Proveedores:** CRUD, imposibilidad de eliminar con productos.
- **Productos:**
  - Creación genera movimiento inicial.
  - SKU único e inmutable.
  - Actualización manual genera ajuste.
  - Soft delete preserva movimientos.
  - Búsqueda/filtros.
- **Movimientos:**
  - Entrada incrementa stock.
  - Salida decrementa stock.
  - Salida con stock insuficiente falla.
  - Ajuste actualiza stock correctamente.
  - `quantity` cero o negativo para `entry`/`exit` es rechazado.
- **Alertas:** se generan al bajar del mínimo y se resuelven al reponer.
- **Roles:** empleado no accede a módulos administrativos; admin accede a todo.
- **Reportes:** filtros devuelven datos correctos; exportaciones responden 200.
- **Dashboard:** indicadores se calculan correctamente.

### 12.3 Configuración

- Habilitar `RefreshDatabase` en `Pest.php` o en cada test feature.
- Usar SQLite `:memory:` (ya configurado en `phpunit.xml`).

---

## 13. Plan de implementación por fases

### Fase 0: Preparación y roles
- [ ] Instalar Laravel Breeze (u opción de auth).
- [ ] Configurar Tailwind y layout base.
- [ ] Configurar `.env` y base de datos.
- [ ] Ejecutar migraciones base.
- [ ] **Agregar campo `role` a `users` y crear middleware/policies de roles.**
- [ ] **Crear seeders de usuarios admin y empleado.**
- [ ] Configurar Pint y asegurar tests corriendo.

### Fase 1: Dominio base
- [ ] Migración y modelo `Category`.
- [ ] Migración y modelo `Supplier`.
- [ ] CRUD de categorías (admin) — rutas ya protegidas por `role:administrador`.
- [ ] CRUD de proveedores (admin).
- [ ] Tests de categorías y proveedores.

### Fase 2: Productos
- [ ] Migración y modelo `Product`.
- [ ] Factory y Seeder de productos.
- [ ] CRUD de productos con validaciones.
- [ ] Ajuste manual de stock.
- [ ] Tests de productos.

### Fase 3: Movimientos de stock
- [ ] Migración y modelo `StockMovement`.
- [ ] Servicio `StockMovementService`.
- [ ] Formulario de entrada/salida.
- [ ] Reglas de negocio (stock insuficiente, recálculo).
- [ ] Tests de movimientos.

### Fase 4: Alertas y colas
- [ ] Migración y modelo `LowStockAlert`.
- [ ] Listener/observer para crear/resolver alertas.
- [ ] Notificación por correo (opcional).
- [ ] **Configurar queue driver (`sync` en desarrollo, `database`/`redis` en producción).**
- [ ] Vista de alertas en dashboard.
- [ ] Tests de alertas.

### Fase 5: Reportes
- [ ] Controlador `ReportController`.
- [ ] Filtros y vistas de entradas/salidas.
- [ ] Exportación PDF, Excel (si es compatible) y CSV.
- [ ] Dashboard consolidado.
- [ ] Tests de reportes.

### Fase 6: Usuarios
- [ ] CRUD de usuarios (admin).
- [ ] Restablecimiento de contraseñas (admin).
- [ ] Tests de autorización y gestión de usuarios.

### Fase 7: Pulido
- [ ] UI responsive, toasts, selects con búsqueda.
- [ ] Logs de actividad crítica (login, movimientos, cambios en productos).
- [ ] Revisión de seguridad (CSRF, validaciones, autorización).
- [ ] Ejecutar Pint.
- [ ] Suite de tests completa en verde.

### Fase 8: Despliegue
- [ ] `npm run build`.
- [ ] Configurar producción (`APP_ENV=production`, `APP_DEBUG=false`, HTTPS).
- [ ] Configurar queue worker y supervisor/systemd si aplica.
- [ ] Migraciones con `--force`.
- [ ] Documentar credenciales iniciales y uso.

---

## 14. Consideraciones de seguridad

- Hash de contraseñas con bcrypt (ya en `User`).
- CSRF en todos los formularios web.
- Validación en servidor con Form Requests.
- Autorización con Gates/Policies, no solo middleware.
- Uso de Eloquent para prevenir SQL Injection.
- Sanitización de salida en Blade (`{{ }}` escapa por defecto).
- HTTPS obligatorio en producción.
- Logs de eventos críticos (login, cambios en productos, movimientos).

---

## 15. Dependencias adicionales sugeridas

| Paquete | Propósito | Notas |
|---------|-----------|-------|
| `laravel/breeze` | Autenticación + layouts base. | Recomendado. |
| `barryvdh/laravel-dompdf` | Exportación PDF. | Verificar compatibilidad con L13. |
| `maatwebsite/excel` | Exportación Excel. | **Verificar soporte oficial para Laravel 13 antes de instalar.** |

> **Alternativa sin dependencias externas:** usar respuestas CSV nativas de Laravel para exportaciones tipo Excel.

---

## 16. Criterios de aceptación

- [ ] Administrador puede gestionar productos, categorías, proveedores, usuarios y configuraciones.
- [ ] Empleado puede registrar entradas/salidas y ver productos/reportes.
- [ ] El stock se actualiza automáticamente al registrar movimientos.
- [ ] No se permite eliminar categorías/proveedores con productos asociados.
- [ ] Los productos con bajo stock se resaltan y generan alertas.
- [ ] Los reportes se filtran por fecha, producto, categoría y proveedor.
- [ ] Los reportes se exportan a PDF y, como mínimo, a CSV (Excel si el paquete es compatible).
- [ ] El dashboard muestra indicadores consolidados.
- [ ] Todos los tests de Pest pasan.
- [ ] El código pasa Laravel Pint sin errores.

---

## 17. Notas finales

- Mantener el código en inglés y la interfaz en español.
- Priorizar la simplicidad: no agregar abstracciones innecesarias.
- Cada funcionalidad nueva debe ir acompañada de su test correspondiente.
- Ejecutar `vendor/bin/pint` y `php artisan test` antes de finalizar cada fase.
