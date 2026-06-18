# Ficha Técnica de Requerimientos Funcionales  
**Aplicación Web para Control de Inventario de Almacén de Productos Electrónicos**  
*Desarrollada en Laravel*

---

## 1. Introducción  
La presente ficha técnica detalla los requerimientos funcionales y no funcionales de una aplicación web desarrollada con el framework Laravel, cuyo propósito es optimizar la gestión del inventario de un almacén dedicado a productos electrónicos.  
El sistema permitirá registrar, controlar y monitorear en tiempo real las existencias, automatizar alertas de bajo inventario, generar reportes de movimientos y facilitar la administración de usuarios con distintos roles, reduciendo errores humanos y pérdidas económicas.

---

## 2. Alcance del sistema  
La aplicación cubrirá los siguientes procesos de negocio:

- Registro y mantenimiento del catálogo de productos, categorías y proveedores.
- Control de entradas y salidas de mercancía con actualización automática del stock.
- Alertas configurables para niveles críticos de inventario.
- Generación de reportes históricos y filtrables de movimientos (entradas/salidas).
- Gestión de usuarios con roles diferenciados (administrador y empleado).
- Interfaz web accesible desde navegadores modernos, sin requerir instalación local.

---

## 3. Requerimientos funcionales  

### 3.1 CRUD de productos  
- **Crear producto**: formulario con los campos: nombre, descripción, SKU (código único), categoría, proveedor, precio unitario, cantidad inicial y stock mínimo.
- **Leer producto**: listado paginado con búsqueda por nombre, SKU o categoría; vista detallada con toda la información y movimientos asociados.
- **Actualizar producto**: edición de cualquier campo excepto SKU (inmutable); al modificar la cantidad manualmente se debe registrar un ajuste de inventario.
- **Eliminar producto**: borrado lógico (soft delete) que preserve el historial de movimientos.

### 3.2 Gestión de categorías y proveedores  
- **Categorías**: CRUD completo (nombre, descripción, estado activo/inactivo). Un producto pertenece a una categoría; no se puede eliminar una categoría si tiene productos asociados.
- **Proveedores**: CRUD completo (nombre, contacto, teléfono, dirección, correo electrónico). Un producto se asigna a un proveedor; la eliminación se restringe si existen referencias activas.

### 3.3 Control de stock y alertas de bajo inventario  
- **Movimientos de inventario**:  
  - *Entradas* (compras, devoluciones) → incrementan el stock.  
  - *Salidas* (ventas, transferencias) → decrementan el stock.  
  - Cada movimiento guarda fecha, tipo, producto, cantidad, usuario responsable y referencia opcional (número de factura/orden).
- **Actualización automática**: el stock del producto se recalcula en tiempo real al registrar un movimiento.
- **Alertas**: cuando la cantidad de un producto alcanza o baja del nivel de “stock mínimo” configurado, el sistema genera una notificación visible en el panel del administrador y, opcionalmente, envía un correo electrónico. El listado de productos resalta en rojo aquellos con bajo inventario.

### 3.4 Generación de reportes de entradas y salidas  
- **Reporte de entradas**: filtros por rango de fechas, producto, categoría y proveedor. Incluye columnas: fecha, producto, cantidad, proveedor y usuario.
- **Reporte de salidas**: mismos filtros, columnas adicionales como cliente/destino (si aplica).
- **Exportación**: posibilidad de descargar los reportes en formatos PDF y Excel.
- **Vista consolidada**: dashboard con indicadores de inventario total valorizado, productos con bajo stock y tendencias de movimiento.

### 3.5 Roles de usuario  
- **Administrador**: acceso total al sistema. Puede gestionar productos, categorías, proveedores, movimientos, reportes y usuarios (crear, editar, desactivar, restablecer contraseñas). Configura parámetros generales (stock mínimo por defecto, notificaciones).
- **Empleado**: puede visualizar el catálogo de productos, registrar entradas y salidas, y consultar reportes. No tiene acceso a la administración de usuarios, categorías o proveedores, ni puede eliminar productos o modificar configuraciones.

---

## 4. Requerimientos no funcionales  

### 4.1 Seguridad y autenticación  
- Implementación del sistema de autenticación nativo de Laravel (Jetstream o Breeze) con hash de contraseñas mediante bcrypt.
- Control de acceso basado en roles (middleware de autorización) para proteger rutas y funcionalidades.
- Protección contra vulnerabilidades web: CSRF, XSS, SQL Injection (uso de Eloquent ORM y validación de entradas).
- Todas las conexiones se harán bajo HTTPS en producción.
- Registro de actividad (logs) de eventos críticos: inicio de sesión, cambios en productos, movimientos de inventario.

### 4.2 Escalabilidad  
- Arquitectura MVC que separa lógica de negocio, permitiendo ampliaciones futuras (módulos de facturación, integración con e-commerce).
- Base de datos relacional (MySQL/PostgreSQL) con índices adecuados en columnas de búsqueda frecuente (SKU, nombre, fechas).
- Posibilidad de implementar caché (Redis) para consultas recurrentes y optimización de reportes.
- Diseño modular que soporta el crecimiento del catálogo (>100,000 productos) sin degradación significativa del rendimiento.

### 4.3 Usabilidad  
- Interfaz web responsive, desarrollada con Bootstrap o Tailwind CSS, compatible con dispositivos móviles y de escritorio.
- Navegación intuitiva con menú lateral agrupado por funciones.
- Validación de formularios en cliente y servidor con mensajes de error claros.
- Retroalimentación inmediata mediante notificaciones toast o modales tras cada acción (guardado exitoso, error).
- Flujo de trabajo simple para registrar movimientos, minimizando clics y tiempo de capacitación (uso de selectores con búsqueda, autocompletado de productos).

---

## 5. Casos de uso principales  

### Caso 1: Registro de un nuevo producto por el administrador  
1. El administrador inicia sesión y accede al módulo “Productos”.  
2. Hace clic en “Nuevo producto”.  
3. Rellena el formulario con nombre, SKU, categoría, proveedor, precio, cantidad inicial y stock mínimo.  
4. Guarda. El sistema valida los datos y crea el producto con la cantidad indicada, registrando automáticamente un movimiento inicial de tipo “entrada”.  
5. El producto aparece en el listado con el stock actualizado.

### Caso 2: Empleado registra una entrada de mercancía (compra)  
1. El empleado ingresa al sistema y va a la sección “Movimientos > Registrar entrada”.  
2. Selecciona el producto mediante un campo de búsqueda, ingresa la cantidad recibida y, opcionalmente, un número de factura.  
3. Confirma la operación. El sistema incrementa el stock del producto y almacena el movimiento con la fecha y el usuario responsable.  
4. El empleado puede verificar el nuevo stock en el detalle del producto.

### Caso 3: Alerta de bajo inventario  
1. Durante la jornada, el sistema monitorea el stock de cada producto.  
2. Al registrar una salida que hace que la cantidad de un producto (ej. condensadores) baje de 50 unidades (stock mínimo configurado), el sistema marca el producto en rojo en el listado y envía una notificación visible en el dashboard del administrador.  
3. El administrador recibe también un correo electrónico con la alerta y procede a realizar un pedido de reposición.

### Caso 4: Generación de reporte mensual de salidas por el administrador  
1. El administrador accede a “Reportes > Salidas”.  
2. Define el rango de fechas del mes anterior y, si lo desea, filtra por una categoría específica (ej. “Componentes pasivos”).  
3. Pulsa “Generar”. El sistema muestra una tabla con el resumen de salidas: producto, cantidad, fecha, empleado que registró.  
4. El administrador exporta el reporte a PDF para archivarlo o compartirlo con la gerencia.

---

## 6. Conclusión  
La implementación de esta aplicación web en Laravel aporta un control automatizado y centralizado del inventario del almacén de productos electrónicos, eliminando los riesgos del manejo manual y las hojas de cálculo dispersas.  
El sistema garantiza integridad de los datos, visibilidad en tiempo real del stock, alertas oportunas para evitar rupturas y generación de informes que facilitan la toma de decisiones. Su arquitectura escalable y segura permite adaptarse al crecimiento del negocio, mientras que la interfaz amigable reduce la curva de aprendizaje y mejora la productividad del personal. En conjunto, la solución se traduce en ahorro de tiempo, reducción de pérdidas y una gestión eficiente que respalda la operación diaria del almacén.
