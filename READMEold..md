# Territorios LSC Bucaramanga – Laravel (Mobile-first + PDF plantilla)
## Objetivo
Sistema en Laravel para:
- CRUD de Ciudades / Barrios / Territorios
- Registro de Personas (con aprobación Admin)
- Asignación y completado de territorios
- Recordatorios automáticos 6pm Bogotá
- Exportación PDF SOLO del “REGISTRO DE ASIGNACIÓN DE TERRITORIO” con el formato exacto de la plantilla enviada.

---

## Stack
- Laravel 10/11 + MySQL
- Auth: Breeze
- Roles: Spatie Laravel Permission
- PDF: barryvdh/laravel-dompdf
- UI: TailwindCSS (mobile-first)
- Opcional: Inertia+Vue o Blade puro (pero UI debe ser mobile)

---

## Roles
- admin
- capitan
- publicador
**Nota:** No usar “visitadores”, solo “Publicadores”.

---

# 1) Catálogo (CRUD)

## Cities
- id, name, slug, is_active

## Neighborhoods
- id, city_id, name, slug, is_active
- evitar duplicados por city_id + name

## Territories
- id
- code (P1, F1, etc) único por neighborhood
- city_id
- neighborhood_id
- status active/inactive
- last_completed_at (nullable)
- notes (nullable)

---

# 2) Personas (Registro + mapa)
Tabla: persons
- id
- full_name
- address (text)
- map_url (nullable)   // SOLO EN PERSONAS
- territory_id
- status active/inactive
- inactive_reason_note (nullable, solo admin)
- created_by_user_id
- approved_at (nullable)
- approved_by_user_id (nullable)

Reglas:
- Publicador crea → queda pendiente (approved_at null)
- Admin aprueba
- Admin puede editar todo, y marcar inactivo + nota

UI:
- Botón “Abrir mapa” si map_url existe

---

# 3) Asignación de territorios (histórico)
Tabla: territory_assignments
- id
- territory_id
- assigned_to_user_id
- assigned_by_user_id
- assigned_at
- completed_at (nullable)

Reglas:
- Capitán puede autoasignarse
- Admin puede asignar a cualquiera (incluido él mismo). => “admin puede asignar territorios personales”
- Completar: set completed_at y actualizar territories.last_completed_at

---

# 4) Warning 2 meses
Si territory.last_completed_at existe y han pasado < 2 meses:
- mostrar warning visual al asignar (para evitar repetir muy seguido)

---

# 5) Recordatorios automáticos
- Condición: assignment sin completed_at y assigned_at <= (hoy - 1 día)
- Enviar recordatorio diario hasta completar
- Hora: 6:00 PM America/Bogota
- Scheduler: ejecutar hourly; el comando decide si ya es 18:00 Bogotá

---

# 6) PDF (ÚNICO) – “REGISTRO DE ASIGNACIÓN DE TERRITORIO”
### IMPORTANTÍSIMO
NO generar otros formatos. El PDF debe ser como la imagen enviada.

PDF:
- Orientación: VERTICAL (portrait)
- Título centrado: “REGISTRO DE ASIGNACIÓN DE TERRITORIO”
- Campo: “Año de servicio: ____” (dinámico, default año actual)
- Tabla con líneas/bordes estilo formulario

Estructura de tabla:
Columnas fijas a la izquierda:
1) “Núm. de terr.” => territories.code
2) “Última fecha en que se completó” => territories.last_completed_at (dd/mm/yyyy)

Luego 4 bloques horizontales repetidos (como plantilla):
Cada bloque:
- Header: “Asignado a”
- Subcolumnas:
  - “Fecha en que se asignó”
  - “Fecha en que se completó”

Datos:
- Cada bloque representa 1 asignación histórica (en orden reciente primero o cronológico según sea más parecido a plantilla; preferir cronológico como control manual).
- En el bloque se imprime:
  - Nombre del asignado (assigned_to_user.name)
  - assigned_at (dd/mm/yy)
  - completed_at (dd/mm/yy) o vacío

Si hay más de 4 asignaciones por territorio:
- Crear segunda página con el mismo encabezado y continuar los bloques.

Endpoint:
- /export/assignments/{year}.pdf
Genera un PDF con TODOS los territorios (agrupados por ciudad/barrio si aplica) y sus asignaciones.

---

# 7) UI/UX Mobile-first (PRIORIDAD ALTA)
El sistema se usará casi siempre desde celular. Diseñar para:
- Pantallas 360px–430px (Android típico)
- Uso con una mano
- Inputs grandes, botones grandes, sin tablas anchas

Requerimientos UI:
- Navegación inferior (bottom nav) o menú muy simple:
  - Territorios
  - Asignaciones
  - Personas
  - Pendientes (solo admin)
  - Perfil

Patrones:
- Listados tipo “cards” (NO tablas)
- Filtros en “bottom sheet” (modal inferior) para:
  - Ciudad
  - Barrio
  - Estado (activo/pendiente/completado)
- Acciones principales sticky abajo:
  - “Asignarme”
  - “Marcar completado”
  - “Crear persona”

Flujos mobile:
1) Territorios (lista)
   - Card: Código, Barrio, estado, última fecha completado
   - CTA: Asignar / Ver
2) Detalle territorio
   - Ver historial (cards por asignación)
   - Botón grande “Marcar completado”
3) Personas
   - Card: nombre, dirección, territorio
   - Botón “Abrir mapa” si existe
4) Admin pendientes
   - Lista de personas pendientes
   - Botones grandes: Aprobar / Editar

Performance:
- Evitar páginas pesadas
- Paginación o “Load more”
- Formularios rápidos, validación clara

Accesibilidad:
- Contraste alto
- Touch targets ≥ 44px
- Feedback visual al guardar (toast)

---

## Entregable esperado
- Laravel funcionando con roles
- CRUD catálogo completo
- Registro y aprobación de personas
- Asignaciones + completado + warning 2 meses
- Scheduler recordatorios 6pm Bogotá
- PDF EXACTO tipo plantilla (único PDF requerido)
- UI mobile-first (listas en cards + CTAs grandes + filtros tipo bottom sheet)
