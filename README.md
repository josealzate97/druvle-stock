# druvle-stock

Sistema de gestión de inventario y ventas desarrollado en Laravel 12, con frontend moderno usando Vite, Blade, Bootstrap y Alpine Js.

## Características principales

- Gestión de productos, categorías, clientes y usuarios
- Registro y control de ventas
- Exportación de reportes a Excel y PDF (maatwebsite/excel, dompdf)
- Configuración de impuestos y ajustes generales
- Autenticación y roles de usuario
- Sistema de notificaciones internas (manuales y automáticas)
- Procesamiento asíncrono con colas (database queue)
- Panel administrativo responsivo
- Integración con Docker y Nginx para despliegue

## Tecnologías utilizadas

- PHP 8.2+
- Laravel 12
- Vite
- Bootstrap
- Alpine Js
- Blade
- Docker
- Nginx

## Instalación rápida

```bash
git clone <este-repo>
cd druvle-stock
cp .env.example .env # (si tienes un archivo de ejemplo)
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```

## Uso

Accede a http://localhost (o el puerto configurado) para usar el sistema.

## Colas (Queues)

Este proyecto usa colas de Laravel con `QUEUE_CONNECTION=database`.

### Tablas de cola

- `jobs`: trabajos pendientes/en ejecución.
- `failed_jobs`: trabajos fallidos.
- `job_batches`: lotes (solo aplica cuando se usan batches).

### Comandos útiles

```bash
# Ejecutar worker
php artisan queue:work --tries=3

# Ver trabajos fallidos
php artisan queue:failed

# Reintentar un trabajo fallido por ID
php artisan queue:retry <id>

# Reintentar todos los fallidos
php artisan queue:retry all

# Limpiar lista de fallidos
php artisan queue:flush
```

Si usas Docker, ejecuta estos comandos dentro del contenedor PHP:

```bash
docker exec -it <php-container> php artisan queue:work --tries=3
```

## Notificaciones

El sistema de notificaciones contempla:

- Notificaciones manuales desde `/settings`.
- Notificaciones automáticas por eventos de negocio:
  - `stock_low` (stock bajo)
  - `refund_created` (devoluciones)
- Campana en el navbar con lectura individual y masiva.

### Tablas de notificaciones

- `notifications`: contenido principal de la notificación.
- `user_notifications`: entrega y estado por usuario (`read_at`, etc.).
- `notifications_preferences`: preferencias por usuario y tipo.

### Flujo de alto nivel

1. Se dispara un evento de negocio (`SaleCompleted`, `RefundProcessed`, `LowStockDetected`).
2. Un listener procesa la lógica de notificación.
3. Los listeners de envío se ejecutan en cola (`ShouldQueue`).
4. Se guarda en `notifications` y se distribuye en `user_notifications`.

## Monitoreo de colas

Laravel base no trae UI visual de colas por defecto en modo database.

Opciones recomendadas:

- CLI (incluida): `queue:failed`, `queue:retry`, logs.
- Laravel Horizon (UI, recomendado si migras a Redis).
- Laravel Telescope (observabilidad de eventos/jobs en desarrollo).

## Webhooks

Actualmente no hay webhooks externos implementados para notificaciones.
El canal actual es interno (in-app + base de datos).

## Licencia

MIT
