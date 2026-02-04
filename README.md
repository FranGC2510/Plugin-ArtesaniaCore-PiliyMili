# Artesanía Core (Pili & Mili) 

[![WordPress](https://img.shields.io/badge/WordPress-6.4%2B-blue.svg)](https://wordpress.org)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-HPOS%20Ready-violet.svg)](https://woocommerce.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg)](https://php.net)
[![Estado](https://img.shields.io/badge/Estado-Producción-green.svg)](https://piliymilidetalles.es)

> **Core de lógica de negocio y gestión fiscal para el eCommerce [Pili & Mili Detalles](https://piliymilidetalles.es).**

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
* **Flujo Seguro:** El dato viaja validado desde la ficha de producto -> Carrito -> Checkout -> Email de pedido -> Backend.

### 3. Checkout Optimizado (B2B/B2C)
Mejora la experiencia de pago y garantiza el cumplimiento legal de facturación.
* **Campos Condicionales:** El campo NIF/DNI permanece oculto y solo se despliega si el cliente marca "¿Deseo factura?".
* **Validación Server-Side:** Impide finalizar la compra si se solicita factura pero no se aporta el documento fiscal.

### 4. Slow Design & Stock
Adaptación de la terminología técnica de WooCommerce a la filosofía de marca.
* **Mensajería Emocional:** Reemplaza avisos técnicos por mensajes configurables desde el panel (ej: *"Hecho a mano con mucho amor"*).

### 5. Frontend Manager
Control centralizado de la presentación visual.
* **Panel de Gestión:** Textos del footer y mensajes de stock editables sin tocar código.
* **Shortcodes Propios:** `[seccion_ofertas]`, `[seccion_novedades]`.
* **Cabeceras Inteligentes:** Inyección automática de títulos y navegación.

---

## Ingeniería y Rendimiento (Under the Hood)

Este plugin ha sido auditado para cumplir con los estándares más altos de desarrollo WordPress (v2.4.0):

* **HPOS Compatible:** Declaración oficial de compatibilidad con *High Performance Order Storage* de WooCommerce (tablas personalizadas).
* **Caché Inteligente (Transients):** El cálculo de ventas anuales no impacta la base de datos en cada visita. Se almacena en caché y se invalida automáticamente solo cuando entra un nuevo pedido.
* **Seguridad Robusta:** Uso estricto de `Nonces` para formularios, saneamiento de datos (`sanitize_text_field`, `wp_kses_post`) y validación de capacidades por roles.
* **Arquitectura MVC:** Separación estricta de Lógica (PHP Classes) y Diseño (Templates HTML) tanto en Frontend como en Admin.

---

## 📂 Estructura del Proyecto

```text
artesania-core/
├── assets/                  # Recursos Públicos (Versionado Dinámico)
│   ├── css/                 # Estilos (admin.css / front.css)
│   └── js/                  # Scripts (checkout.js)
├── languages/               # Archivos de traducción (.mo/.po)
├── src/                     # Lógica de Negocio (PHP Classes - PSR-4)
│   ├── Admin/               # Controladores del Panel
│   ├── Checkout/            # Lógica de proceso de compra
│   ├── Front/               # Controladores de vistas públicas
│   ├── Product/             # Manipulación de datos de producto
│   └── Sales/               # Motor de cálculo y límites fiscales
├── templates/               # Vistas (View Layer)
│   ├── admin/               # Plantillas del Panel de Control
│   └── ...                  # Plantillas del Frontend (Footer, Headers)
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
3. **Configuración:**
   * Navegar a **Ajustes > Pili & Mili Control**.
   * **Pestaña Módulos:** Activar o desactivar funcionalidades técnicas (Checkout, Frontend, etc.). *Nota: Solo accesible para Administradores.*
   * **Pestaña Textos:** Personalizar los mensajes de stock y el pie de página sin tocar código.
   * **Pestaña Fiscal:**
      * Establecer los topes anuales de facturación (€) o volumen (pedidos) por pasarela.
      * Marcar "Activar Bloqueo" para que el sistema deshabilite la pasarela automáticamente al llegar al límite.

---

## Créditos

**Desarrollado para Pili & Mili Detalles.**

* **Arquitectura y Desarrollo:** Fco Javier García Cañero.
* **Licencia:** Propietaria.