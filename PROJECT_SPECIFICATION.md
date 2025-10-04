# Especificación del Proyecto: Sistema de Ranking de Clínicas

## Información General del Proyecto

### Tecnologías Base
- **Framework**: Laravel v12
- **Frontend**: Vanilla JavaScript + CSS + Plantillas Blade
- **Base de Datos**: SQLite (configurada por defecto)
- **Filosofía**: Simplicidad sobre buenas prácticas complejas
- **Configuración**: Variables de entorno (.env)
- **Sin panel de administración**: Todo configurable vía .env

## Estructura de Páginas

### 1. Página Principal (Home)
- **Ruta**: `/`
- **Función**: Punto de entrada del sistema
- **Navegación**: 
  - Botón/enlace a "Ranking" (redirige a login si no hay sesión)
  - Botón/enlace a "Login"
- **Layout**: Header con título + contenido + footer con enlace a política de privacidad

### 2. Página de Login
- **Ruta**: `/login`
- **Función**: Autenticación por código de clínica
- **Campos**: Solo un input para "Código de Clínica"
- **Validación**: Verificar que el código existe en la caché
- **Redirección**: Al ranking tras login exitoso
- **Layout**: Header con botón volver + título + formulario + footer

### 3. Página de Ranking
- **Ruta**: `/ranking`
- **Función**: Mostrar tabla de posiciones
- **Protección**: Requiere autenticación (sesión activa)
- **Contenido**: Tabla con columnas:
  - Posición global
  - Código de clínica
  - Puntos acumulados (recomendaciones)
  - Variación de puestos (+/- respecto semana anterior)
- **Funcionalidad**: 
  - Filtro con JavaScript
  - Auto-scroll a la posición de la clínica logueada
- **Layout**: Header + filtro + tabla + footer

### 4. Página de Política de Privacidad
- **Ruta**: `/privacy`
- **Función**: Mostrar política de privacidad
- **Contenido**: Texto estático configurable
- **Layout**: Header + contenido + footer

## Layout Global

### Header (Todas las páginas)
- Botón de retroceso (flecha izquierda) que lleva a Home
- Título de la página actual
- **Excepción**: En Home no mostrar botón de retroceso

### Footer (Todas las páginas)
- Enlace a "Política de Privacidad" (redirige a `/privacy`)

## Sistema de Datos y Caché

### Fuente de Datos
- **Origen**: Google Spreadsheet
- **Columnas del Excel**:
  - `codigo`: Código único de la clínica
  - `email`: Email de contacto de la clínica
  - `recomendaciones`: Puntos acumulados (número)

### Sistema de Caché
- **Almacenamiento**: Base de datos (tabla `rankings`)
- **Estructura de la tabla**:
  - `id`: Primary key
  - `codigo`: Código de clínica
  - `email`: Email de la clínica
  - `recomendaciones`: Puntos actuales
  - `posicion_actual`: Posición en el ranking actual
  - `posicion_anterior`: Posición en el ranking anterior
  - `variacion`: Diferencia de posiciones (+/-)
  - `created_at`, `updated_at`: Timestamps

### Cálculo de Variaciones
- Comparar posición actual vs posición anterior
- **Ejemplo**: Si estaba en posición 1 y ahora está en posición 5: variación = -4
- **Nuevas clínicas**: variación = 0 o "NEW"
- **Clínicas que salen**: mantener en histórico pero marcar como inactivas

## Procesos Automatizados (Jobs)

### 1. Job de Actualización de Ranking
- **Frecuencia**: Semanal, miércoles a las 5:00 AM
- **Función**: 
  - Conectar a Google Spreadsheet
  - Leer datos (codigo, email, recomendaciones)
  - Calcular nuevas posiciones
  - Comparar con caché anterior
  - Calcular variaciones
  - Actualizar caché
- **Comando**: `php artisan ranking:update`
- **Configuración Cron**: `0 7 * * 3 cd /path/to/project && php artisan ranking:update`

### 2. Job de Envío de Emails
- **Frecuencia**: Semanal, después de actualizar ranking, a las 8am
- **Función**: Notificar a todas las clínicas sobre nuevo ranking
- **Comando**: `php artisan emails:send-ranking-update`

## Sistema de Emails

### Tipos de Email

#### 1. Email Inicial (Día 3 de octubre de 2025)
- **Plantilla**: `emails.ranking.first`
- **Contenido**: Bienvenida especial al sistema
- **Variables**: codigo, posicion, puntos

#### 2. Email Semanal Estándar
- **Plantilla**: `emails.ranking.weekly`
- **Contenido**: Notificación de actualización semanal
- **Variables**: codigo, posicion, puntos, variacion

#### 3. Email Final (12 de Diciembre)
- **Plantilla**: `emails.ranking.final`
- **Contenido**: Cierre del período de ranking
- **Variables**: codigo, posicion_final, puntos_totales

### Configuración de Fechas (Variables .env)
```env
RANKING_START_DATE=2025-01-03
RANKING_END_DATE=2025-12-12
RANKING_FIRST_EMAIL_DATE=2025-01-03
```

## Autenticación Simplificada

### Flujo de Login
1. Usuario ingresa código de clínica
2. Sistema verifica código en caché actual
3. Si existe: crear sesión con código de clínica
4. Redirigir a ranking
5. Si no existe: mostrar error

### Gestión de Sesión
- **Variable de sesión**: `clinic_code`
- **Middleware**: Verificar sesión para acceso a ranking
- **Duración**: Configurable en .env (SESSION_LIFETIME) por defecto 1 hora.

## Configuración .env Específica del Proyecto

```env
# Configuración del Proyecto
APP_NAME="Ranking de Clínicas"

# Google Sheets API
GOOGLE_SHEETS_SPREADSHEET_ID=your_spreadsheet_id
GOOGLE_SHEETS_RANGE=Sheet1!A:C
GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/google-credentials.json

# Configuración de Ranking
RANKING_UPDATE_DAY=wednesday
RANKING_UPDATE_HOUR=07:00
RANKING_START_DATE=2025-01-03
RANKING_END_DATE=2025-12-12

# Configuración de Emails
MAIL_FROM_ADDRESS=info@agenciamarsway.com
MAIL_FROM_NAME="Ranking Royal Canin"
```

## Estructura de Archivos del Proyecto

app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── RankingController.php
│   │   └── PrivacyController.php
│   └── Middleware/
│       └── CheckClinicAuth.php
├── Models/
│   └── Ranking.php
├── Console/
│   └── Commands/
│       ├── UpdateRankingCommand.php
│       └── SendRankingEmailsCommand.php
└── Services/
│   ├── GoogleSheetsService.php
│   ├── RankingService.php
│   └── EmailService.php
│
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── pages/
│   │   ├── home.blade.php
│   │   ├── login.blade.php
│   │   ├── ranking.blade.php
│   │   └── privacy.blade.php
│   └── emails/
│       └── ranking/
│           ├── first.blade.php
│           ├── weekly.blade.php
│           └── final.blade.php
│── css/
│   └── app.css
└── js/
│   └── app.js
│
database/
└── migrations/
└── create_rankings_table.php


## Funcionalidades JavaScript

### Filtro de Ranking
- **Función**: Localizar registro de tabla por código de clínica y scrollar a la fila correspondiente
- **Implementación**: Vanilla JS
- **Ubicación**: resources/js/app.js
- **Características**:
  - Búsqueda en tiempo real
  - Filtro case-insensitive
  - Scroll a position

### Auto-scroll a Clínica
- **Función**: Scroll automático a la fila de la clínica logueada
- **Trigger**: Al cargar la página de ranking
- **Implementación**: JavaScript vanilla con smooth scroll

## Consideraciones de Desarrollo

### Prioridades
1. **Simplicidad**: Código claro y directo
2. **Funcionalidad**: Que funcione correctamente
3. **Mantenibilidad**: Fácil de modificar
4. **Buenas prácticas**: Solo las esenciales

### Validaciones Mínimas
- Verificar código de clínica existe
- Validar formato de email (básico)
- Verificar conexión a Google Sheets
- Manejo básico de errores

### Seguridad Básica
- Sanitización de inputs
- Protección CSRF (Laravel por defecto)
- Validación de sesiones
- Rate limiting en login

## Cronograma de Implementación Sugerido

1. **Fase 1**: Estructura básica y rutas
2. **Fase 2**: Modelos y migraciones
3. **Fase 3**: Controladores y vistas básicas
4. **Fase 4**: Sistema de autenticación simple
5. **Fase 5**: Integración con Google Sheets
6. **Fase 6**: Jobs y comandos
7. **Fase 7**: Sistema de emails
8. **Fase 8**: JavaScript y CSS
9. **Fase 9**: Testing y ajustes finales

Este documento servirá como guía completa para el desarrollo del proyecto, manteniendo la simplicidad como prioridad mientras se respetan las funcionalidades requeridas.