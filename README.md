# 🌍 Gestión de Territorios LSC

Un moderno e integral sistema desarrollado en **Laravel 11** diseñado específicamente para la administración, asignación y seguimiento de territorios e individuos (personas sordas), con un enfoque absoluto en la usabilidad móvil (**Mobile-First**) y automatizaciones inteligentes.

---

## ✨ Características Principales

### 📱 Diseño Mobile-First & Premium UI/UX
Toda la interfaz ha sido construida desde cero utilizando **Tailwind CSS** enfocándose en el uso con una sola mano en dispositivos móviles (Pantallas 360px–430px).
*   **Listados tipo "Cards":** Adiós a las tablas horizontales inmanejables en el celular. Toda la información se presenta en hermosas tarjetas de actividad.
*   **Navegación Táctil:** Formularios rápidos, botones de acción grandes (Touch targets ≥ 44px) diseñados para no fallar el 'tap', y feedback visual inmediato (toasts).
*   **Aesthetic & Dark Mode:** Una paleta de colores pulcra, fondos de degradados suaves, iconos modernos en los inputs, y compatibilidad nativa con Modo Oscuro (Dark Mode).

### 👥 Sistema de Roles Completo (ACL)
Integración robusta impulsada por `spatie/laravel-permission` con 3 niveles jerárquicos:
1.  **Administrador:** Control total. Puede crear/editar ciudades, barrios, territorios, aprobar registros de personas, y ver todo el histórico global.
2.  **Capitán:** Puede auto-asignarse territorios, visualizar su progreso, enviar registros de nuevas personas (sujetas a aprobación), liberar asignaciones y gestionar su propio flujo de trabajo.
3.  **Publicador:** *(En desarrollo/estructurado como rol base)* Orientado a la lectura y registro básico.

### 🗺️ Gestión de Territorios & Personas
*   **Catálogo Escalable:** Estructura organizada de *Ciudades > Barrios > Territorios*. 
*   **Prevención de Fatiga:** Indicadores visuales y advertencias si se intenta asignar un territorio que fue completado hace menos de **2 meses**.
*   **Registro de Personas Sordas:** Los capitanes pueden registrar nuevas personas ubicadas en un territorio específico incluyendo una URL de mapa (`map_url`), pero quedan en estado "Pendiente de Aprobación" hasta que un Administrador dé el visto bueno (garantizando control de calidad y privacidad de los datos).

### 🤖 "Auto-Pilot" y Automatizaciones Inteligentes
El sistema trabaja por ti en segundo plano sin impactar el rendimiento:
*   **Middleware de Autocompletado (Cron-less):** Hemos desarrollado un middleware inteligente que no requiere configuración de servidor. Al navegar por la app, el sistema verifica subrepticiamente (apoyado en la Memoria Caché de Laravel para no saturar) si alguna asignación tiene **más de 6 horas de antigüedad**. De ser así, la marca automáticamente como **Completada**.
*   **Libertad total de despliegue:** Al no requerir programar *Cron Jobs* en Linux, este sistema puede alojarse en casi cualquier hosting barato o compartido sin dolores de cabeza.

### 🖨️ Reportes y PDFs 
Exportación en 1 clic de **"REGISTROS DE ASIGNACIÓN DE TERRITORIO"**.
Utilizando `dompdf`, el sistema genera automáticamente un documento PDF vertical con el formato tabular clásico exacto, organizando los territorios y listando hasta los últimos 4 historiales continuos de a quién se le asignó y cuándo, permitiendo una perfecta impresión en papel.

---

## 🛠️ Stack Tecnológico

Este es un proyecto **Full-Stack** optimizado:

*   **Backend:** PHP 8.2+ y Laravel 11.
*   **Autenticación:** Laravel Breeze (Flujo de registro, login, reset de contraseñas y verificación de emails, **100% traducido al español**).
*   **Frontend y Motor de UI:** Blade UI Components + Tailwind CSS v3.
*   **Manejo de Activos:** Vite (Compilación ultra rápida de HMR y optimización de producción).
*   **Base de datos:** MySQL 8+ / MariaDB.

---

## 🚀 Guía de Instalación y Despliegue

La guía completa paso a paso para instalar este sistema en tu servidor local (Laragon, XAMPP) o subirlo a un Servidor Web / Hosting de producción se encuentra en el archivo:

👉 **[INSTRUCTIONS.md](INSTRUCTIONS.md)**

Allí aprenderás cómo configurar la base de datos, compilar los estilos de producción y asegurar el despliegue para que sea a prueba de hackers sin tocar la consola más de lo estrictamente necesario.

---

## 🤝 Contribuciones (Open Source)
Este sistema nació con el deseo de modernizar y facilitar la logística de la recolección, administración y seguimiento local de territorios. ¡Si eres desarrollador y tienes ideas para mejorarlo, siéntete libre de hacer un Fork y enviar un Pull Request!

*Diseñado y desarrollado con pasión para mejorar la eficiencia organizacional. 🚀*
