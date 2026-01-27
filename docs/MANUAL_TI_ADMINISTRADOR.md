# Manual Tecnico y de Administracion - Sistema Turnero

## Tabla de Contenidos

1. [Informacion del Sistema](#1-informacion-del-sistema)
2. [Requisitos e Instalacion](#2-requisitos-e-instalacion)
3. [Configuracion del Sistema](#3-configuracion-del-sistema)
4. [Administracion de Usuarios](#4-administracion-de-usuarios)
5. [Gestion de Servicios y Ventanillas](#5-gestion-de-servicios-y-ventanillas)
6. [Gestion de Clientes](#6-gestion-de-clientes)
7. [Reportes y Estadisticas](#7-reportes-y-estadisticas)
8. [Configuracion de Pantallas](#8-configuracion-de-pantallas)
9. [Impresion de Tickets](#9-impresion-de-tickets)
10. [Base de Datos](#10-base-de-datos)
11. [Arquitectura del Sistema](#11-arquitectura-del-sistema)
12. [Mantenimiento y Backup](#12-mantenimiento-y-backup)
13. [Solucion de Problemas](#13-solucion-de-problemas)
14. [Seguridad](#14-seguridad)
15. [API y Endpoints](#15-api-y-endpoints)

---

## 1. Informacion del Sistema

### Stack Tecnologico

| Componente | Tecnologia | Version |
|------------|------------|---------|
| Backend | Laravel | 8.75 |
| PHP | PHP | 8.1+ |
| Base de Datos | MySQL | 5.7+ |
| Frontend | Blade + Bootstrap | 4.6 |
| JavaScript | jQuery | 3.6+ |
| Graficos | Chart.js | 3.x |
| Autenticacion | Laravel Sanctum | - |
| Servidor Web | Apache (XAMPP) | - |

### Estructura de Carpetas

```
turneroapp/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Controladores de administracion
│   │   │   ├── Agent/           # Controladores de agentes
│   │   │   └── Auth/            # Autenticacion
│   │   └── Middleware/          # CheckRole, Authenticate
│   ├── Models/                  # 9 modelos Eloquent
│   └── Services/                # PrinterService
├── config/
│   └── printer.php              # Configuracion impresora
├── database/
│   ├── migrations/              # 13 migraciones
│   └── seeders/                 # Datos iniciales
├── resources/
│   └── views/
│       ├── admin/               # Vistas de admin
│       ├── agent/               # Vistas de agente
│       ├── auth/                # Login
│       ├── display/             # Pantallas publicas
│       └── queue/               # Generacion de turnos
├── routes/
│   └── web.php                  # Todas las rutas
├── storage/
│   └── framework/cache/         # Cache del sistema
└── public/                      # Assets publicos
```

---

## 2. Requisitos e Instalacion

### Requisitos del Servidor

- **Sistema Operativo**: Windows Server / Linux (Ubuntu 20.04+)
- **PHP**: 8.1 o superior
- **MySQL**: 5.7 o superior
- **Servidor Web**: Apache 2.4+ o Nginx
- **RAM**: Minimo 2GB, recomendado 4GB
- **Disco**: Minimo 10GB disponibles

### Extensiones PHP Requeridas

```
php-mbstring
php-xml
php-mysql
php-curl
php-json
php-zip
php-gd
```

### Instalacion Paso a Paso

#### 1. Clonar/Copiar el Proyecto
```bash
cd /var/www/html  # o C:\xampp\htdocs en Windows
git clone [repositorio] turneroapp
cd turneroapp
```

#### 2. Instalar Dependencias
```bash
composer install
npm install
```

#### 3. Configurar Entorno
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Editar .env
```env
APP_NAME="Sistema Turnero"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=turnero_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

#### 5. Crear Base de Datos
```sql
CREATE DATABASE turnero_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 6. Ejecutar Migraciones
```bash
php artisan migrate
php artisan db:seed  # Datos iniciales (opcional)
```

#### 7. Compilar Assets
```bash
npm run prod
```

#### 8. Permisos (Linux)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 9. Configurar Virtual Host (Apache)
```apache
<VirtualHost *:80>
    ServerName turnero.local
    DocumentRoot /var/www/html/turneroapp/public

    <Directory /var/www/html/turneroapp/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 3. Configuracion del Sistema

### Acceso al Panel de Configuracion

1. Iniciar sesion como **admin**
2. Ir a **Administracion > Configuracion** (`/admin/settings`)

### Grupos de Configuracion

#### 3.1 General
| Clave | Descripcion | Ejemplo |
|-------|-------------|---------|
| business_name | Nombre del negocio | "Clinica San Jose" |
| business_address | Direccion | "Av. Principal 123" |
| business_phone | Telefono | "01-234-5678" |

#### 3.2 Horarios
| Clave | Descripcion | Ejemplo |
|-------|-------------|---------|
| opening_time | Hora de apertura | "08:00" |
| closing_time | Hora de cierre | "17:00" |
| work_days | Dias laborales (JSON) | ["mon","tue","wed","thu","fri"] |

#### 3.3 Cola de Turnos
| Clave | Descripcion | Ejemplo |
|-------|-------------|---------|
| max_wait_time | Tiempo maximo espera (minutos) | 60 |
| absent_timeout | Tiempo para marcar ausente (minutos) | 5 |
| max_recalls | Maximo de rellamadas | 3 |
| auto_reset_daily | Reiniciar contadores diariamente | true |

#### 3.4 Pantallas (Display)
| Clave | Descripcion | Ejemplo |
|-------|-------------|---------|
| display_refresh_rate | Frecuencia de actualizacion (segundos) | 5 |
| display_show_next | Cantidad de turnos siguientes a mostrar | 5 |
| display_sound_enabled | Habilitar sonido | true |
| display_voice_enabled | Habilitar sintesis de voz | true |

#### 3.5 TV
| Clave | Descripcion | Ejemplo |
|-------|-------------|---------|
| tv_logo_url | URL o ruta del logo | "/images/logo.png" |
| tv_message | Mensaje personalizado | "Bienvenidos" |
| tv_video_url | URL del video promocional | "/videos/promo.mp4" |
| kiosk_message | Mensaje del kiosco | "Seleccione su servicio" |

#### 3.6 Sistema
| Clave | Descripcion | Ejemplo |
|-------|-------------|---------|
| demo_mode | Modo demostracion | false |

### Modificacion via Codigo

```php
use App\Models\SystemSetting;

// Obtener valor
$value = SystemSetting::getValue('business_name', 'Default');

// Establecer valor
SystemSetting::setValue('business_name', 'Nuevo Nombre');

// Limpiar cache
SystemSetting::clearCache();
```

---

## 4. Administracion de Usuarios

### Acceso
**Administracion > Usuarios** (`/admin/users`)

### Roles del Sistema

| Rol | Permisos |
|-----|----------|
| **admin** | Acceso total al sistema |
| **agent** | Panel de agente, atencion de turnos |
| **viewer** | Solo lectura (sin rutas especificas actualmente) |

### Crear Usuario

1. Ir a **Usuarios > Nuevo Usuario**
2. Completar campos:
   - Nombre completo
   - Email (unico)
   - Password (minimo 8 caracteres)
   - Rol (admin/agent/viewer)
   - Ventanilla asignada (opcional)
   - Estado (activo/inactivo)
3. Guardar

### Campos del Usuario

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| name | string | Nombre completo |
| email | string | Email unico |
| password | string | Encriptado bcrypt |
| role | enum | admin, agent, viewer |
| service_window_id | FK | Ventanilla asignada |
| is_active | boolean | Si puede acceder |
| avatar | string | Ruta de imagen |
| last_login_at | timestamp | Ultimo acceso |

### Activar/Desactivar Usuario

1. En la lista de usuarios, buscar el usuario
2. Click en el boton de estado
3. Usuario inactivo = no puede iniciar sesion

### Auditoria de Usuarios

Todos los cambios en usuarios se registran en `audit_logs`:
- Creacion
- Modificacion
- Cambio de rol
- Activacion/desactivacion

---

## 5. Gestion de Servicios y Ventanillas

### 5.1 Tipos de Servicio

**Acceso**: Administracion > Tipos de Servicio (`/admin/service-types`)

#### Crear Tipo de Servicio

| Campo | Descripcion | Ejemplo |
|-------|-------------|---------|
| name | Nombre del servicio | "Atencion General" |
| prefix | Prefijo para tickets (unico) | "A" |
| color | Color hexadecimal | "#3498db" |
| estimated_time | Tiempo estimado en minutos | 15 |
| description | Descripcion del servicio | "Consultas generales" |
| is_active | Estado activo/inactivo | true |
| requires_appointment | Requiere cita previa | false |
| daily_limit | Limite diario de turnos (0=sin limite) | 100 |
| display_order | Orden en pantallas | 1 |

#### Ejemplo de Numeracion

Con prefijo "A":
- Primer turno del dia: **A-001**
- Segundo turno: **A-002**
- ...
- Turno 999: **A-999**

Los contadores se reinician diariamente.

### 5.2 Ventanillas (Service Windows)

**Acceso**: Administracion > Ventanillas (`/admin/service-windows`)

#### Crear Ventanilla

| Campo | Descripcion | Ejemplo |
|-------|-------------|---------|
| name | Nombre descriptivo | "Ventanilla 1" |
| code | Codigo corto (unico) | "V1" |
| description | Descripcion | "Atencion general" |
| status | Estado | active/inactive/paused |
| location | Ubicacion fisica | "Primer piso" |
| display_order | Orden en pantallas | 1 |

#### Estados de Ventanilla

| Estado | Descripcion |
|--------|-------------|
| **active** | Operativa, puede recibir turnos |
| **inactive** | Cerrada, no aparece disponible |
| **paused** | Temporalmente sin atencion |

#### Asignar Servicios a Ventanilla

1. Editar la ventanilla
2. En la seccion "Servicios que atiende"
3. Marcar los tipos de servicio
4. Guardar

Una ventanilla puede atender multiples servicios.

---

## 6. Gestion de Clientes

**Acceso**: Administracion > Clientes (`/admin/clients`)

### Campos del Cliente

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| first_name | string | Nombres |
| last_name | string | Apellidos |
| document_type | enum | dni, passport, ce, ruc, other |
| document_number | string | Numero de documento (unico) |
| email | string | Correo electronico |
| phone | string | Telefono |
| birth_date | date | Fecha de nacimiento |
| gender | enum | M, F, O |
| is_elderly | boolean | Adulto mayor |
| is_pregnant | boolean | Embarazada |
| has_disability | boolean | Discapacidad |
| notes | text | Notas adicionales |

### Deteccion Automatica de Prioridad

El sistema detecta automaticamente si un cliente tiene prioridad basado en:
- `is_elderly = true`
- `is_pregnant = true`
- `has_disability = true`

Si alguno es verdadero, el turno se crea con prioridad **priority**.

### Busqueda de Clientes (API)

```
GET /api/clients/search?q={termino}
GET /api/clients/find?document={documento}
```

---

## 7. Reportes y Estadisticas

### Dashboard de Administracion

**Acceso**: Administracion > Dashboard (`/admin/`)

#### KPIs en Tiempo Real

- Total de turnos del dia
- Turnos pendientes
- Turnos en atencion
- Turnos completados
- Turnos ausentes
- Agentes activos
- Tiempo promedio de espera
- Tiempo promedio de atencion

#### Graficos

1. **Turnos por Tipo de Servicio** (barras)
2. **Turnos por Hora** (lineas)

### Reportes Detallados

**Acceso**: Administracion > Reportes (`/admin/reports`)

#### Filtros Disponibles

| Filtro | Opciones |
|--------|----------|
| Fecha inicio | Selector de fecha |
| Fecha fin | Selector de fecha |
| Tipo de servicio | Todos / especifico |
| Agente | Todos / especifico |
| Ventanilla | Todos / especifica |
| Estado | Todos / pendiente / completado / ausente / etc |

#### Metricas del Reporte

- Total de turnos
- Turnos completados (cantidad y %)
- Turnos ausentes (cantidad y %)
- Turnos cancelados
- Tiempo promedio de espera
- Tiempo maximo de espera
- Tiempo promedio de atencion
- Tiempo maximo de atencion

#### Graficos del Reporte

1. Turnos por servicio
2. Turnos completados por agente
3. Distribucion por hora
4. Distribucion por dia

#### Exportar a CSV

1. Aplicar filtros deseados
2. Click en **"Exportar CSV"**
3. Se descarga archivo con:
   - Fecha
   - Numero de turno
   - Cliente
   - Documento
   - Servicio
   - Ventanilla
   - Agente
   - Prioridad
   - Estado
   - Hora de creacion
   - Hora de llamada
   - Hora de inicio
   - Hora de fin
   - Tiempo de espera
   - Tiempo de atencion

El CSV usa UTF-8 con BOM para compatibilidad con Excel.

---

## 8. Configuracion de Pantallas

### URLs de Pantallas Publicas

| Pantalla | URL | Uso |
|----------|-----|-----|
| Display General | `/display/` | Vista general de turnos |
| TV Optimizada | `/display/tv` | Pantallas grandes, con video |
| Kiosco | `/display/kiosk` | Pantalla tactil para generar turnos |
| Por Servicio | `/display/service/{id}` | Filtrado por tipo de servicio |

### Configurar Pantalla TV

1. Ir a **Configuracion > TV**
2. Configurar:
   - **Logo**: Subir imagen o poner URL
   - **Mensaje**: Texto que aparece en pantalla
   - **Video**: URL de video promocional

### Caracteristicas de la Pantalla TV

- **Turno llamado**: Numero grande, destacado
- **Ventanilla**: Donde debe ir el cliente
- **Lista de ventanillas**: Estado de cada una
- **Proximos turnos**: 5-8 turnos siguientes
- **Video**: Area lateral con video
- **Audio**: Campana + voz sintetizada

### Desplegar en TV Fisica

1. Conectar PC/Raspberry Pi a la TV
2. Abrir navegador en modo kiosco:
   ```bash
   # Chrome kiosk mode
   chrome --kiosk http://servidor/display/tv

   # Firefox kiosk mode
   firefox --kiosk http://servidor/display/tv
   ```
3. Desactivar protector de pantalla
4. Configurar inicio automatico

### Cache de Display

El sistema cachea datos del display por **3 segundos** para optimizar multiples pantallas.

---

## 9. Impresion de Tickets

### Configuracion de Impresora

Editar `config/printer.php`:

```php
return [
    'enabled' => env('PRINTER_ENABLED', true),
    'name' => env('PRINTER_NAME', 'POS-80'),        // Windows
    'usb_port' => env('PRINTER_USB_PORT', '/dev/usb/lp0'), // Linux
];
```

O en `.env`:
```env
PRINTER_ENABLED=true
PRINTER_NAME=POS-80
PRINTER_USB_PORT=/dev/usb/lp0
```

### Impresoras Soportadas

El sistema usa protocolo **ESC/POS** compatible con:
- Impresoras termicas USB de 80mm
- Epson TM-T20, TM-T88
- Star TSP100
- Genericas ESC/POS

### Configuracion en Windows

1. Instalar driver de la impresora
2. Compartir impresora con nombre simple (ej: "POS-80")
3. Configurar `PRINTER_NAME=POS-80`

### Configuracion en Linux

1. Identificar puerto USB:
   ```bash
   ls /dev/usb/lp*
   ```
2. Dar permisos:
   ```bash
   chmod 666 /dev/usb/lp0
   ```
3. Configurar `PRINTER_USB_PORT=/dev/usb/lp0`

### Formato del Ticket

```
================================
      [NOMBRE NEGOCIO]
================================
   Fecha: DD/MM/YYYY HH:MM

   Servicio: [TIPO SERVICIO]

        *** [NUMERO] ***

   Prioridad: [PRIORIDAD]
   Ventanilla: [VENTANILLA]
   Personas antes: [N]
   Espera estimada: [X] min

   Gracias por su visita
================================
[CORTE DE PAPEL]
```

### Solucion de Problemas de Impresion

| Problema | Solucion |
|----------|----------|
| No imprime | Verificar PRINTER_ENABLED=true |
| Error de conexion | Verificar nombre/puerto de impresora |
| Caracteres erroneos | Verificar codificacion UTF-8 |
| Papel no corta | Verificar soporte de impresora |

---

## 10. Base de Datos

### Diagrama de Tablas

```
users
├── id
├── name
├── email
├── password
├── role (admin/agent/viewer)
├── is_active
├── service_window_id (FK)
└── timestamps

queues
├── id
├── ticket_number
├── client_id (FK)
├── service_type_id (FK)
├── service_window_id (FK)
├── agent_id (FK)
├── priority (emergency/priority/scheduled/normal)
├── status (pending/called/in_progress/completed/absent/transferred/cancelled)
├── is_express
├── notes
├── called_at
├── started_at
├── completed_at
├── wait_time (segundos)
├── service_time (segundos)
├── queue_date
└── timestamps + soft_deletes

service_types
├── id
├── name
├── prefix (A, B, C...)
├── color
├── estimated_time
├── is_active
├── requires_appointment
├── daily_limit
├── display_order
└── timestamps + soft_deletes

service_windows
├── id
├── name
├── code (V1, V2...)
├── status (active/inactive/paused)
├── current_agent_id (FK)
├── location
├── display_order
└── timestamps + soft_deletes

service_type_service_window (pivot)
├── service_type_id (FK)
└── service_window_id (FK)

clients
├── id
├── first_name
├── last_name
├── document_type
├── document_number
├── email
├── phone
├── birth_date
├── is_elderly
├── is_pregnant
├── has_disability
├── notes
└── timestamps + soft_deletes

daily_counters
├── id
├── date
├── service_type_id (FK)
├── last_number
└── total_generated

queue_transfers
├── id
├── queue_id (FK)
├── from_window_id (FK)
├── to_window_id (FK)
├── from_agent_id (FK)
├── to_agent_id (FK)
├── reason
└── timestamps

system_settings
├── id
├── key
├── value
├── type (string/boolean/integer/json)
├── group
├── description
└── timestamps

audit_logs
├── id
├── user_id (FK)
├── action
├── model_type
├── model_id
├── old_values (JSON)
├── new_values (JSON)
├── ip_address
├── user_agent
└── timestamps
```

### Indices Importantes

```sql
-- queues
INDEX idx_queues_date_status (queue_date, status)
INDEX idx_queues_service_status (service_type_id, status)
INDEX idx_queues_ticket (ticket_number)

-- daily_counters
UNIQUE idx_daily_counter (date, service_type_id)
```

### Consultas Utiles

```sql
-- Turnos de hoy por estado
SELECT status, COUNT(*)
FROM queues
WHERE queue_date = CURDATE()
GROUP BY status;

-- Tiempo promedio de espera por servicio
SELECT st.name, AVG(q.wait_time) as avg_wait
FROM queues q
JOIN service_types st ON q.service_type_id = st.id
WHERE q.status = 'completed'
GROUP BY st.id;

-- Agentes con mas turnos atendidos
SELECT u.name, COUNT(*) as total
FROM queues q
JOIN users u ON q.agent_id = u.id
WHERE q.status = 'completed'
  AND q.queue_date = CURDATE()
GROUP BY u.id
ORDER BY total DESC;
```

---

## 11. Arquitectura del Sistema

### Patron MVC

```
Request → Routes → Middleware → Controller → Model → View → Response
```

### Flujo de un Turno

```
1. Cliente solicita turno (QueueController@store)
   ↓
2. Sistema valida horario y limite
   ↓
3. Busca/crea cliente
   ↓
4. Detecta prioridad
   ↓
5. Genera numero (DailyCounter)
   ↓
6. Crea Queue (status=pending)
   ↓
7. Intenta imprimir (PrinterService)
   ↓
8. Retorna ticket

--- Agente ---

9. Agente llama turno (DashboardController@callNext)
   ↓
10. Queue status → called
    ↓
11. DisplayController detecta cambio
    ↓
12. TV anuncia turno

13. Agente inicia (DashboardController@startService)
    ↓
14. Queue status → in_progress

15. Agente completa (DashboardController@completeService)
    ↓
16. Queue status → completed
    ↓
17. Calcula tiempos
```

### Cache Strategy

| Dato | TTL | Razon |
|------|-----|-------|
| SystemSettings | 1 hora | Cambia poco |
| Display data | 3 segundos | Multiples TVs |
| User permissions | Session | Por usuario |

### Real-time Updates

El sistema usa **polling** (no WebSockets):
- Display: cada 5-10 segundos
- Panel agente: cada 5 segundos
- Configurable en settings

---

## 12. Mantenimiento y Backup

### Backup de Base de Datos

```bash
# Backup completo
mysqldump -u usuario -p turnero_db > backup_$(date +%Y%m%d).sql

# Backup con gzip
mysqldump -u usuario -p turnero_db | gzip > backup_$(date +%Y%m%d).sql.gz
```

### Restaurar Backup

```bash
mysql -u usuario -p turnero_db < backup_20260127.sql
```

### Limpiar Turnos Antiguos

```sql
-- Eliminar turnos de mas de 1 año (soft delete)
UPDATE queues
SET deleted_at = NOW()
WHERE queue_date < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
  AND deleted_at IS NULL;

-- Eliminar definitivamente (cuidado!)
DELETE FROM queues
WHERE deleted_at IS NOT NULL
  AND deleted_at < DATE_SUB(NOW(), INTERVAL 2 YEAR);
```

### Limpiar Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Logs del Sistema

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Limpiar logs antiguos
find storage/logs -name "*.log" -mtime +30 -delete
```

### Tareas Programadas (Cron)

Agregar al crontab:
```bash
* * * * * cd /path/to/turneroapp && php artisan schedule:run >> /dev/null 2>&1
```

---

## 13. Solucion de Problemas

### Errores Comunes

#### Error 500 - Internal Server Error
1. Revisar `storage/logs/laravel.log`
2. Verificar permisos de carpetas
3. Verificar configuracion de `.env`

#### Error de Base de Datos
1. Verificar credenciales en `.env`
2. Verificar que MySQL este corriendo
3. Ejecutar `php artisan migrate:status`

#### Pantalla en Blanco
1. Habilitar `APP_DEBUG=true` temporalmente
2. Revisar logs
3. Verificar `php artisan config:cache`

#### Login No Funciona
1. Verificar usuario activo en BD
2. Limpiar cache de sesion
3. Verificar `SESSION_DRIVER` en `.env`

#### Turnos No Se Muestran en TV
1. Verificar cache: `php artisan cache:clear`
2. Verificar que haya turnos pendientes
3. Revisar consola del navegador (F12)

### Comandos de Diagnostico

```bash
# Estado del sistema
php artisan about

# Verificar rutas
php artisan route:list

# Verificar migraciones
php artisan migrate:status

# Test de conexion DB
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar permisos
ls -la storage/
ls -la bootstrap/cache/
```

---

## 14. Seguridad

### Autenticacion

- Sesiones con token CSRF
- Passwords hasheados con bcrypt
- Logout forzado para usuarios desactivados

### Autorizacion

Middleware `CheckRole`:
```php
// Rutas admin
Route::middleware(['auth', 'role:admin'])->group(...)

// Rutas agente
Route::middleware(['auth', 'role:admin,agent'])->group(...)
```

### Protecciones Implementadas

| Amenaza | Proteccion |
|---------|------------|
| CSRF | Token en formularios |
| SQL Injection | Eloquent ORM, prepared statements |
| XSS | Blade escape automatico |
| Session Hijacking | Regeneracion de session |

### Recomendaciones

1. **HTTPS**: Siempre usar en produccion
2. **Passwords**: Exigir minimo 8 caracteres
3. **Backups**: Diarios automaticos
4. **Updates**: Mantener Laravel actualizado
5. **Logs**: Monitorear logs regularmente
6. **Acceso**: Limitar acceso a `/admin` por IP si es posible

### Auditoria

Todas las acciones criticas se registran en `audit_logs`:
- Creacion/modificacion de usuarios
- Cambios de configuracion
- Operaciones de turnos

---

## 15. API y Endpoints

### Rutas Publicas (Sin Auth)

```
GET  /queue/                    → Formulario de turno
POST /queue/                    → Crear turno
GET  /queue/{id}/ticket         → Ver ticket
GET  /queue/{id}/status         → Estado JSON
POST /queue/{id}/cancel         → Cancelar turno

GET  /api/clients/search?q=     → Buscar clientes
GET  /api/clients/find?document= → Buscar por documento

GET  /display/                  → Display general
GET  /display/tv                → Display TV
GET  /display/kiosk             → Kiosco
GET  /display/data              → Datos JSON (polling)
```

### Rutas Admin (Requiere Auth + Admin)

```
/admin/                         → Dashboard
/admin/users                    → CRUD usuarios
/admin/service-types            → CRUD servicios
/admin/service-windows          → CRUD ventanillas
/admin/clients                  → CRUD clientes
/admin/settings                 → Configuracion
/admin/reports                  → Reportes
```

### Rutas Agent (Requiere Auth + Admin/Agent)

```
GET  /agent/                    → Dashboard
POST /agent/select-window       → Seleccionar ventanilla
POST /agent/leave-window        → Abandonar ventanilla
POST /agent/call-next           → Llamar siguiente
POST /agent/call/{id}           → Llamar especifico
POST /agent/recall              → Rellamar
POST /agent/start-service       → Iniciar atencion
POST /agent/complete-service    → Completar
POST /agent/mark-absent         → Marcar ausente
POST /agent/transfer            → Transferir
POST /agent/pause-window        → Pausar
POST /agent/resume-window       → Reanudar
GET  /agent/pending-queues      → Lista pendientes (AJAX)
GET  /agent/current-status      → Estado actual (AJAX)
```

### Formato de Respuesta JSON

```json
// Display Data
{
  "current_queues": [
    {
      "id": 1,
      "ticket_number": "A-025",
      "status": "called",
      "service_type": "Atencion General",
      "service_window": "V1",
      "client_name": "Juan Perez"
    }
  ],
  "next_queues": [...],
  "windows_status": [...]
}
```

---

## Contacto y Soporte

Para soporte tecnico:
- Revisar logs en `storage/logs/laravel.log`
- Consultar este manual
- Revisar `CLAUDE.md` para comandos de desarrollo

---

*Manual Tecnico version 1.0 - Sistema Turnero*
*Ultima actualizacion: Enero 2026*
