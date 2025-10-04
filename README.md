# Sistema de Ranking de Clínicas Royal Canin

Sistema web desarrollado en Laravel v12 para gestionar y mostrar el ranking de clínicas basado en recomendaciones de productos Royal Canin.

## 🚀 Características Principales

- **4 páginas principales**: Home, Login, Ranking, Política de Privacidad
- **Autenticación simple**: Por código de clínica
- **Ranking dinámico**: Con cálculo de variaciones semanales
- **Filtro JavaScript**: Búsqueda en tiempo real
- **Auto-scroll**: A la posición de la clínica logueada
- **Sistema de emails**: 3 tipos de plantillas (inicial, semanal, final)
- **Integración Google Sheets**: Actualización automática de datos
- **Comandos automatizados**: Para actualización y envío de emails

## 📋 Requisitos

- PHP 8.2+
- Laravel 12
- SQLite (configurado por defecto)
- Node.js y npm (para assets)

## 🛠️ Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [url-del-repositorio]
   cd excel-rank
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   npm install
   ```

3. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Ejecutar migraciones**
   ```bash
   php artisan migrate
   ```

5. **Crear datos de prueba** (opcional)
   ```bash
   php artisan db:seed --class=RankingSeeder
   ```

6. **Compilar assets**
   ```bash
   npm run build
   ```

7. **Iniciar servidor**
   ```bash
   php artisan serve
   ```

## ⚙️ Configuración

### Variables de Entorno Principales

```env
# Configuración básica
APP_NAME="Ranking de Clínicas"
APP_URL=http://localhost:8000

# Base de datos (SQLite por defecto)
DB_CONNECTION=sqlite

# Sesiones
SESSION_LIFETIME=60

# Email
MAIL_FROM_ADDRESS=info@agenciamarsway.com
MAIL_FROM_NAME="Ranking Royal Canin"

# Google Sheets API
GOOGLE_SHEETS_SPREADSHEET_ID=your_spreadsheet_id
GOOGLE_SHEETS_RANGE=Sheet1!A:C
GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/google-credentials.json

# Configuración de Ranking
RANKING_UPDATE_DAY=wednesday
RANKING_UPDATE_HOUR=07:00
RANKING_START_DATE=2025-01-03
RANKING_END_DATE=2025-12-12
RANKING_FIRST_EMAIL_DATE=2025-01-03
```

## 🎯 Uso del Sistema

### Para Usuarios (Clínicas)

1. **Acceder al sistema**: Ir a la URL principal
2. **Iniciar sesión**: Usar el código de clínica proporcionado
3. **Ver ranking**: Consultar posición y puntos
4. **Filtrar**: Buscar clínicas específicas
5. **Auto-scroll**: El sistema automáticamente muestra tu posición

### Códigos de Prueba

Con los datos de prueba, puedes usar estos códigos:
- `CLI001` - `CLI010`

## 🤖 Comandos Automatizados

### Actualizar Ranking

```bash
# Actualización normal (solo miércoles)
php artisan ranking:update

# Forzar actualización
php artisan ranking:update --force
```

### Enviar Emails

```bash
# Envío normal (solo miércoles)
php artisan emails:send-ranking-update

# Forzar envío
php artisan emails:send-ranking-update --force

# Email de prueba
php artisan emails:send-ranking-update --test=email@test.com
```

## 📅 Configuración de Cron

Para automatización completa, agregar al crontab:

```bash
# Actualizar ranking los miércoles a las 7:00 AM
0 7 * * 3 cd /path/to/project && php artisan ranking:update

# Enviar emails los miércoles a las 8:00 AM
0 8 * * 3 cd /path/to/project && php artisan emails:send-ranking-update
```

## 📊 Estructura de Datos

### Tabla Rankings

- `codigo`: Código único de la clínica
- `email`: Email de contacto
- `recomendaciones`: Puntos acumulados
- `posicion_actual`: Posición en el ranking actual
- `posicion_anterior`: Posición en el ranking anterior
- `variacion`: Diferencia de posiciones (+/-)
- `activo`: Estado de la clínica

### Google Sheets Format

El spreadsheet debe tener 3 columnas:
- **Columna A**: Código de clínica
- **Columna B**: Email
- **Columna C**: Recomendaciones (número)

## 🎨 Personalización

### Estilos

El sistema usa Tailwind CSS. Los estilos están en:
- `resources/css/app.css`
- Clases inline en las vistas Blade

### Plantillas de Email

Las plantillas están en `resources/views/emails/ranking/`:
- `first.blade.php`: Email inicial
- `weekly.blade.php`: Email semanal
- `final.blade.php`: Email final

## 🔧 Desarrollo

### Estructura del Proyecto

```
app/
├── Http/Controllers/     # Controladores
├── Models/              # Modelos Eloquent
├── Services/            # Lógica de negocio
├── Console/Commands/    # Comandos Artisan
└── Http/Middleware/     # Middleware personalizado

resources/
├── views/
│   ├── layouts/         # Layout principal
│   ├── pages/          # Páginas del sistema
│   └── emails/         # Plantillas de email
├── css/                # Estilos CSS
└── js/                 # JavaScript
```

### Servicios Principales

- **GoogleSheetsService**: Integración con Google Sheets
- **RankingService**: Lógica del ranking
- **EmailService**: Gestión de emails

## 🐛 Troubleshooting

### Problemas Comunes

1. **Error de permisos en SQLite**
   ```bash
   chmod 664 database/database.sqlite
   chmod 775 database/
   ```

2. **Assets no cargan**
   ```bash
   npm run build
   php artisan config:clear
   ```

3. **Sesiones no funcionan**
   ```bash
   php artisan session:table
   php artisan migrate
   ```

### Logs

Los logs del sistema están en:
- `storage/logs/laravel.log`
- Logs de comandos incluyen timestamps y detalles

## 📝 Notas de Desarrollo

- **Filosofía**: Simplicidad sobre buenas prácticas complejas
- **Sin administración**: Todo configurable vía .env
- **Datos simulados**: GoogleSheetsService usa datos de prueba por defecto
- **Emails en log**: Por defecto los emails se guardan en log

## 🚀 Próximos Pasos

1. **Configurar Google Sheets API real**
2. **Configurar servidor de email**
3. **Personalizar plantillas de email**
4. **Configurar cron jobs en producción**
5. **Añadir SSL en producción**

## 📞 Soporte

Para consultas técnicas o configuración, contactar con el equipo de desarrollo.

---

**Desarrollado para Royal Canin España**  
*Sistema de Ranking de Clínicas 2025*
