# druvle-stock

Sistema de gestión de inventario y ventas desarrollado en Laravel 12, con frontend moderno usando Vite, TailwindCSS y Vue.js.

## Características principales

- Gestión de productos, categorías, clientes y usuarios
- Registro y control de ventas
- Exportación de reportes a Excel y PDF (maatwebsite/excel, dompdf)
- Configuración de impuestos y ajustes generales
- Autenticación y roles de usuario
- Panel administrativo responsivo
- Integración con Docker y Nginx para despliegue

## Tecnologías utilizadas

- PHP 8.2+
- Laravel 12
- Vite
- TailwindCSS
- Vue.js
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

## Licencia

MIT
