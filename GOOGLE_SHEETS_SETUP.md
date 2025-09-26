# Configuración de Google Sheets API

## Pasos para configurar la integración con Google Sheets

### 1. Instalar dependencias

La dependencia ya está instalada en el proyecto:

```bash
composer require google/apiclient
```

### 2. Configurar el archivo .env

Actualiza las siguientes variables en tu archivo `.env`:

```env
# Google Sheets API
GOOGLE_SHEETS_SPREADSHEET_ID=tu_spreadsheet_id_real
GOOGLE_SHEETS_RANGE=Sheet1!A:C
GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/private/royal-incentivos-1ef3c60cc797.json
```

### 3. Obtener el Spreadsheet ID

El ID del spreadsheet se encuentra en la URL de Google Sheets:
```
https://docs.google.com/spreadsheets/d/[SPREADSHEET_ID]/edit
```

Por ejemplo, si tu URL es:
```
https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
```

Tu SPREADSHEET_ID sería: `1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms`

### 4. Configurar permisos del spreadsheet

1. Abre tu Google Spreadsheet
2. Haz clic en "Compartir" (Share)
3. Agrega el email de la cuenta de servicio: `royal-352@royal-incentivos.iam.gserviceaccount.com`
4. Dale permisos de "Viewer" (Lector)

### 5. Formato del spreadsheet

El spreadsheet debe tener las siguientes columnas en este orden:

| A (Código) | B (Email) | C (Recomendaciones) |
|------------|-----------|---------------------|
| CLI001     | clinica001@example.com | 155 |
| CLI002     | clinica002@example.com | 148 |
| CLI003     | clinica003@example.com | 142 |

**Notas importantes:**
- La primera fila puede contener headers (se detecta automáticamente)
- Los códigos se convierten automáticamente a mayúsculas
- Los emails se validan y convierten a minúsculas
- Las recomendaciones deben ser números enteros

### 6. Probar la conexión

#### Desde la línea de comandos:
```bash
php artisan sheets:test
```

#### Desde el navegador (solo en desarrollo):
```
http://localhost:8000/test/google-sheets
```

### 7. Actualizar el ranking manualmente

```bash
php artisan ranking:update
```

## Características de la Integración

### ✅ Funcionalidades Implementadas

- **Conexión real** con Google Sheets API
- **Fallback automático** a datos simulados si hay errores
- **Validación de datos** (emails, códigos, números)
- **Manejo de headers** en el spreadsheet (detección automática)
- **Logs detallados** para debugging
- **Comandos de prueba** para verificar la conexión
- **Procesamiento robusto** de datos con validaciones

### 🔄 Flujo de Datos

1. **Lectura**: El sistema lee datos del spreadsheet usando Google Sheets API
2. **Validación**: Se validan emails, códigos y números
3. **Procesamiento**: Los datos se normalizan (mayúsculas para códigos, minúsculas para emails)
4. **Almacenamiento**: Se guardan en la base de datos local como caché
5. **Fallback**: Si hay errores, se usan datos simulados automáticamente

## Solución de problemas

### Error: "Spreadsheet not found"
- ✅ Verifica que el SPREADSHEET_ID sea correcto
- ✅ Asegúrate de que la cuenta de servicio tenga acceso al spreadsheet
- ✅ Confirma que el spreadsheet existe y es accesible

### Error: "Credentials file not found"
- ✅ Verifica que el archivo `royal-incentivos-1ef3c60cc797.json` esté en `storage/app/private/`
- ✅ Verifica que la ruta en GOOGLE_SHEETS_CREDENTIALS_PATH sea correcta
- ✅ Confirma que el archivo tiene los permisos correctos

### Error: "Permission denied"
- ✅ Asegúrate de haber compartido el spreadsheet con la cuenta de servicio
- ✅ Verifica que los permisos sean al menos de "Viewer"
- ✅ Confirma que el email de la cuenta de servicio sea correcto

### Datos vacíos
- ✅ Verifica que el rango (GOOGLE_SHEETS_RANGE) sea correcto
- ✅ Asegúrate de que haya datos en las celdas especificadas
- ✅ Verifica que el formato de los datos sea correcto
- ✅ Confirma que no hay filas completamente vacías

### Error: "Service not initialized"
- ✅ Verifica que las credenciales de Google sean válidas
- ✅ Confirma que la dependencia `google/apiclient` esté instalada
- ✅ Revisa los logs para errores de inicialización

## Logs y Debugging

Los logs de la integración se guardan en `storage/logs/laravel.log`. Busca entradas que contengan:

- `"Google Sheets"` - Para logs generales de la integración
- `"Conexión exitosa"` - Para confirmaciones de conexión
- `"Error al obtener datos"` - Para errores de lectura
- `"Usando datos simulados"` - Para confirmación de fallback

### Comandos útiles para debugging:

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar caché de configuración
php artisan config:clear

# Probar conexión
php artisan sheets:test

# Actualizar ranking con logs detallados
php artisan ranking:update -v
```

## Configuración de Producción

### Variables de entorno requeridas:

```env
# Obligatorias
GOOGLE_SHEETS_SPREADSHEET_ID=tu_id_real_aqui
GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/private/royal-incentivos-1ef3c60cc797.json

# Opcionales
GOOGLE_SHEETS_RANGE=Sheet1!A:C
```

### Consideraciones de seguridad:

- ✅ El archivo de credenciales está en `storage/app/private/` (no accesible desde web)
- ✅ Las rutas de prueba solo están disponibles en entorno local
- ✅ Los logs no exponen información sensible
- ✅ La cuenta de servicio tiene permisos mínimos (solo lectura)

## Mantenimiento

### Tareas regulares:

1. **Verificar conexión** semanalmente con `php artisan sheets:test`
2. **Revisar logs** para detectar errores recurrentes
3. **Actualizar credenciales** si es necesario
4. **Validar formato** del spreadsheet periódicamente

### Actualizaciones:

- La integración es compatible con cambios en el formato del spreadsheet
- Se puede cambiar el rango sin afectar el funcionamiento
- Los datos simulados se mantienen como fallback permanente

Esta documentación cubre todos los aspectos de la integración con Google Sheets. Para soporte adicional, revisa los logs y usa los comandos de prueba proporcionados.