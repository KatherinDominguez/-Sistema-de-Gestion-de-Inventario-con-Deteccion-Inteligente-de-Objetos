# 📦 Sistema de Gestión de Inventario con Detección Inteligente de Objetos

Sistema web desarrollado en Laravel que utiliza inteligencia artificial para detectar y clasificar objetos en imágenes, permitiendo una gestión automatizada del inventario mediante visión por computadora, control por voz y gestos.

## 🎯 Características Principales

- **🔍 Detección Automática de Objetos**: Identifica múltiples objetos en una imagen usando Python + OpenCV
- **🎤 Control por Voz**: Comandos de voz en español para control manos libres
- **🖐️ Control por Gestos**: Interacción mediante gestos de mano con MediaPipe
- **📊 Dashboard Analítico**: Visualización de estadísticas con gráficos interactivos (Chart.js)
- **📈 Reportes y Exportación**: Genera reportes en CSV del inventario
- **🎨 Detección por Color y Forma**: Clasifica objetos según características visuales
- **🔄 Procesamiento en Tiempo Real**: Análisis de imágenes y videos

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 12.0.1** - Framework PHP
- **PHP 8.2.12** - Lenguaje de programación
- **MySQL** - Base de datos
- **Python 3.x** - Scripts de detección de objetos

### Frontend
- **HTML5 / CSS3** - Estructura y diseño
- **JavaScript** - Interactividad
- **Chart.js 3.9.1** - Gráficos y visualizaciones
- **MediaPipe** - Detección de gestos de mano

### Librerías Python
- **OpenCV** - Procesamiento de imágenes
- **NumPy** - Operaciones numéricas

## 📋 Requisitos Previos

- PHP >= 8.2
- Composer
- Python >= 3.8
- Node.js y npm
- MySQL
- Extensiones PHP: `pdo`, `mbstring`, `tokenizer`, `json`, `openssl`

## 📦 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/proyecto-inventario.git
cd proyecto-inventario
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Configurar el archivo `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 4. Ejecutar migraciones

```bash
php artisan migrate
```

### 5. Crear enlace simbólico para almacenamiento

```bash
php artisan storage:link
```

### 6. Instalar dependencias de Python

```bash
pip install opencv-python numpy
```

### 7. Iniciar el servidor

```bash
php artisan serve
```

El sistema estará disponible en `http://127.0.0.1:8000`

## 🚀 Uso del Sistema

### Registro y Gestión de Objetos

1. **Registrar un nuevo objeto**:
   - Ir a "Gestión de Objetos"
   - Completar el formulario con nombre, forma y color
   - Guardar

```php
// Ejemplo de modelo Objeto
$objeto = new Objeto();
$objeto->nombre = 'Botella de Agua';
$objeto->forma = 'cilindrica';
$objeto->color = 'azul';
$objeto->save();
```

### Detección de Objetos

1. **Subir una imagen**:
   - Hacer clic en "Subir archivo"
   - Seleccionar imagen o video

2. **Identificar objetos**:
   - Elegir modo: "Detectar TODOS" o "Detectar específico"
   - Hacer clic en "🔍 Identificar"
   - El sistema procesará la imagen y mostrará resultados

3. **Guardar en inventario**:
   - Revisar los resultados de detección
   - Hacer clic en "✅ Guardar todos los resultados en inventario"

### Control por Voz 🎤

Comandos disponibles:

```javascript
// Comandos reconocidos
"subir archivo"           // Abre el selector de archivos
"identificar [nombre]"    // Identifica un objeto específico
"reiniciar"               // Reinicia el proceso
"abrir inventario"        // Navega al inventario
"abrir objetos"           // Navega a gestión de objetos
"abrir reportes"          // Navega a reportes
```

**Uso del control por voz:**

```javascript
// El sistema usa Web Speech API
const recognition = new webkitSpeechRecognition();
recognition.lang = 'es-ES';

// Procesa el comando
fetch("/voz/procesar", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken
    },
    body: JSON.stringify({ texto: comandoVoz })
});
```

### Control por Gestos 🖐️

Gestos reconocidos con MediaPipe:

| Gesto | Acción |
|-------|--------|
| 🖐️ Mano abierta (5 dedos) | Subir archivo |
| ✊ Puño cerrado (0 dedos) | Mostrar opciones de identificación |
| ☝️ 1 dedo | Reiniciar |
| ✌️ 2 dedos | Ir a Objetos |
| 🤟 3 dedos | Ir a Inventario |
| 🖖 4 dedos | Ir a Reportes |

## 📚 Uso de la Librería MiParseador

La librería `MiParseador` permite interpretar comandos de voz de manera estructurada:

### Ejemplo Básico

```php
<?php

use App\Librerias\MiParseador;

// Crear una instancia del parser
$parser = new MiParseador();

// Definir comandos
$parser->agregarComando('subir', function () {
    // Lógica para subir archivo
    session(['comando_voz' => 'subir']);
});

$parser->agregarComando('identificar', function ($nombre, $color = null) {
    // Lógica para identificar objeto
    session([
        'comando_voz' => 'identificar',
        'nombre' => $nombre,
        'color' => $color
    ]);
});

// Parsear y ejecutar comando
$texto = "identificar botella azul";
$parser->parsearYejecutar($texto);
```

### Uso Avanzado con ParserService

```php
<?php

namespace App\Services;

use App\Librerias\MiParseador;
use App\Models\Objeto;

class ParserService
{
    private $parser;

    public function __construct()
    {
        $this->parser = new MiParseador();
        $this->definirComandos();
    }

    private function definirComandos()
    {
        // Comando con parámetros opcionales
        $this->parser->agregarComando('identificar', function ($nombre, $color = null) {
            $nombre = strtolower($nombre);
            
            // Si no se especifica color, buscarlo en BD
            if (!$color) {
                $objeto = Objeto::whereRaw('LOWER(nombre) = ?', [$nombre])->first();
                if ($objeto) {
                    $color = strtolower($objeto->color);
                }
            }

            session([
                'comando_voz' => 'identificar',
                'nombre' => $nombre,
                'color' => $color
            ]);
        });

        // Comando de redirección
        $this->parser->agregarComando('abrir', function ($destino) {
            $rutas = [
                'inventario' => route('inventario'),
                'reportes' => route('reportes.index'),
                'objetos' => route('objetos.index')
            ];

            $ruta = $rutas[strtolower($destino)] ?? null;

            if ($ruta) {
                session(['comando_voz' => 'redirigir', 'url' => $ruta]);
            }
        });
    }

    public function interpretar($texto)
    {
        try {
            $texto = strtolower($texto);
            $this->parser->parsearYejecutar($texto);
        } catch (\Exception $e) {
            \Log::error('Parser error: ' . $e->getMessage());
            session(['comando_voz' => 'ninguno']);
        }
    }
}
```

### API de MiParseador

#### Métodos Principales

```php
// Crear parser
$parser = new MiParseador();

// Agregar comando
$parser->agregarComando(string $comando, callable $funcion);

// Parsear texto y obtener palabras
$palabras = $parser->parsear(string $texto): array;

// Parsear y ejecutar comando
$parser->parsearYejecutar(string $texto);

// Validar si el texto es válido
$esValido = $parser->esValido(string $texto): bool;

// Obtener lista de comandos disponibles
$comandos = $parser->tablaDeComandos(): array;
```

## 🏗️ Arquitectura del Proyecto

```
paginaWeb/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── IdentificacionController.php
│   │       ├── InventarioController.php
│   │       ├── ObjetoController.php
│   │       └── VozController.php
│   ├── Models/
│   │   ├── Objeto.php
│   │   ├── Deteccion.php
│   │   └── User.php
│   ├── Services/
│   │   └── ParserService.php
│   ├── Librerias/
│   │   └── MiParseador.php
│   └── Python/
│       └── detectar_objetos.py
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── principal.blade.php
│       ├── inicio.blade.php
│       ├── dashboard.blade.php
│       └── inventario.blade.php
├── routes/
│   └── web.php
└── public/
    ├── css/
    └── storage/
```

### Patrón de Diseño: Service Layer

El proyecto implementa el patrón **Service Layer** para mantener los controladores ligeros:

```php
// Controlador delgado
class VozController extends Controller
{
    public function procesar(Request $request, ParserService $parser)
    {
        $texto = $request->input('texto');
        $parser->interpretar($texto);
        
        return response()->json([
            'accion' => session('comando_voz')
        ]);
    }
}

// Service con lógica de negocio
class ParserService
{
    private $parser;
    
    public function interpretar($texto)
    {
        // Lógica compleja de interpretación
        $this->parser->parsearYejecutar($texto);
    }
}
```

## 📊 Base de Datos

### Modelo Entidad-Relación

```
┌──────────────┐         ┌──────────────┐
│   usuarios   │         │   objetos    │
├──────────────┤         ├──────────────┤
│ id           │         │ id           │
│ nombre       │         │ nombre       │
│ email        │         │ forma        │
│ password     │         │ color        │
└──────────────┘         └──────────────┘
                                │
                                │ 1:N
                                │
                         ┌──────────────┐
                         │ detecciones  │
                         ├──────────────┤
                         │ id           │
                         │ objeto_id    │◄──
                         │ archivo      │
                         │ cantidad     │
                         │ resultado    │
                         │ created_at   │
                         └──────────────┘
```

### Migraciones Clave

```php
// Tabla objetos
Schema::create('objetos', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('forma');
    $table->string('color');
    $table->timestamps();
});

// Tabla detecciones
Schema::create('detecciones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('objeto_id')->constrained('objetos');
    $table->string('archivo');
    $table->integer('cantidad_detectada');
    $table->text('resultado')->nullable();
    $table->timestamps();
});
```

## 🔧 Configuración del Script Python

El script `detectar_objetos.py` debe aceptar 3 parámetros:

```python
# detectar_objetos.py
import sys
import cv2
import numpy as np

def detectar_objetos(ruta_imagen, forma, color):
    # Cargar imagen
    imagen = cv2.imread(ruta_imagen)
    
    # Lógica de detección
    # ...
    
    # Retornar resultado
    print(f"Detectado {cantidad} objetos de forma {forma} y color {color}")

if __name__ == "__main__":
    ruta = sys.argv[1]
    forma = sys.argv[2]
    color = sys.argv[3]
    
    detectar_objetos(ruta, forma, color)
```

## 🎨 Personalización del Dashboard

### Agregar Nuevos Gráficos

```javascript
// En dashboard.blade.php
const ctx = document.getElementById('miGrafico').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Tendencia de Detecciones',
            data: @json($datos),
            borderColor: '#007bff',
            tension: 0.4
        }]
    }
});
```

## 🐛 Solución de Problemas Comunes

### Error: "RouteNotFoundException"

```bash
php artisan route:clear
php artisan cache:clear
```

### Error: "Class MiParseador not found"

```bash
composer dump-autoload
```

### Python no ejecuta el script

Verifica la ruta de Python:

```bash
where python  # Windows
which python  # Linux/Mac
```

Actualiza en el controlador si es necesario:

```php
$comando = "python3 " . escapeshellcmd($script);  // Para Linux/Mac
```

### Los gráficos no se muestran

Verifica que Chart.js esté cargado antes de ejecutar el código:

```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tu código de gráficos aquí
});
</script>
```

## 📸 Capturas de Pantalla

### Panel Principal
![Panel Principal](docs/screenshots/panel-principal.png)

### Dashboard de Análisis
![Dashboard](docs/screenshots/dashboard.png)

### Detección de Objetos
![Detección](docs/screenshots/deteccion.png)

### Inventario
![Inventario](docs/screenshots/inventario.png)

## 🚀 Despliegue en Producción

### 1. Optimizar para producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 2. Configurar variables de entorno

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

### 3. Configurar permisos

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 📝 Mejoras Futuras

- [ ] Implementar autenticación con JWT
- [ ] Soporte para más formatos de imagen (TIFF, BMP)
- [ ] Detección en tiempo real con cámara web
- [ ] Notificaciones cuando el stock está bajo
- [ ] API REST para integraciones externas
- [ ] Aplicación móvil (Flutter/React Native)
- [ ] Soporte multiidioma
- [ ] Machine Learning para mejorar precisión

## 👥 Autores

- **Tu Nombre** - *Desarrollo Full Stack* - [GitHub](https://github.com/tu-usuario)

## 📄 Licencia

Este proyecto es parte de un trabajo académico para la materia de Métodos y Técnicas de Programación.

## 🙏 Agradecimientos

- OpenCV por las herramientas de visión por computadora
- MediaPipe por la detección de gestos
- Laravel por el framework robusto
- Chart.js por las visualizaciones

---

⭐ **¿Te gustó el proyecto? Dale una estrella en GitHub!** ⭐
