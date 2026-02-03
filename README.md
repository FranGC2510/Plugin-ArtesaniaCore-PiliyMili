# Artesanía Core (Pili & Mili) 

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org)
[![Estado](https://img.shields.io/badge/Estado-Producción-green.svg)](https://tudominio.com)

> **Core de lógica de negocio y gestión fiscal para el eCommerce [Pili & Mili HandMade](https://piliymilidetalles.es).**

Este plugin implementa un sistema de gestión integral modular bajo arquitectura **MVC (Modelo-Vista-Controlador)** y **Clean Architecture**. Centraliza la lógica de personalización de productos, optimización del checkout, diseño frontend y control fiscal automatizado.

---

## Características Principales

### 1. Control Fiscal y de Producción (Sales Limiter)
Sistema automatizado para limitar las ventas anuales basándose en tramos fiscales o capacidad de producción artesanal.
* **Límites Dinámicos:** Configuración de límite de facturación (€) y volumen de pedidos por cada pasarela de pago.
* **Bloqueo Inteligente:** Desactiva automáticamente los métodos de pago en el checkout al superar el umbral anual.
* **Dashboard Widget:** Monitorización en tiempo real desde el escritorio de WordPress con sistema de semáforos (Verde/Rojo).

### 2. Personalización de Productos
Módulo que permite a los clientes añadir textos personalizados (frases, nombres) a sus pedidos.
* **Activación Selectiva:** Checkbox en el backend de cada producto ("Permitir Personalización").
* **Integración Completa:** El dato viaja desde la ficha de producto -> Carrito -> Checkout -> Email de pedido -> Backend.

### 3. Checkout Optimizado (B2B/B2C)
Mejora la experiencia de pago y garantiza el cumplimiento legal de facturación.
* **Campos Condicionales:** El campo NIF/DNI permanece oculto y solo se despliega (y se vuelve obligatorio) si el cliente marca "¿Deseo factura?".
* **Validación en Servidor:** Impide finalizar la compra si se solicita factura pero no se aporta el documento fiscal.

### 4. Slow Design & Stock
Adaptación de la terminología técnica de WooCommerce a la filosofía de marca.
* **Mensajería Emocional:** Reemplaza avisos técnicos como "Reserva" por mensajes de valor: *"Se fabrica bajo pedido. Producto hecho a mano con mucho amor."*

### 5. Frontend Manager
Control centralizado de la presentación visual y assets.
* **Shortcodes Propios:** `[seccion_ofertas]`, `[seccion_novedades]`.
* **Cabeceras Inteligentes:** Inyección automática de títulos y navegación en tienda y categorías.
* **Footer Personalizado:** Reemplazo total del pie de página de Storefront.
* **Etiquetas de Oferta:** Inyección de badges personalizados junto al precio.

---

## Arquitectura del Proyecto

El plugin sigue una estructura estricta de separación de responsabilidades:

```text
artesania-core/
├── assets/                  # Recursos Públicos (Compilados/Minificados)
│   ├── css/                 # Estilos (Admin y Front separados)
│   └── js/                  # Scripts (Checkout interactions)
├── src/                     # Lógica de Negocio (PHP Classes - PSR-4)
│   ├── Admin/               # Gestión del Panel y Settings
│   ├── Checkout/            # Lógica de proceso de compra
│   ├── Front/               # Controladores de vistas públicas
│   ├── Product/             # Manipulación de datos de producto
│   └── Sales/               # Motor de cálculo y límites fiscales
├── templates/               # Vistas y Fragmentos HTML (Views)
└── artesania-core.php       # Bootstrapper y Singleton principal
```
## Requisitos Técnicos

* **WordPress:** 6.0 o superior.
* **WooCommerce:** 8.0 o superior.
* **PHP:** 7.4 o superior.

---

## Instalación y Configuración

1. **Despliegue:** Clonar el repositorio en el directorio `/wp-content/plugins/`.
2. **Activación:** Activar el plugin desde el panel de administración de WordPress.
3. **Configuración de Límites:**
   * Navegar a **Ajustes > Pili & Mili Control**.
   * Establecer los topes anuales de dinero o pedidos para cada método de pago.
   * Marcar "Activar Bloqueo" para que el sistema actúe automáticamente.

---

## Créditos

**Desarrollado para Pili & Mili Detalles.**

* **Arquitectura y Desarrollo:** Fco Javier García Cañero.
* **Licencia:** Propietaria.