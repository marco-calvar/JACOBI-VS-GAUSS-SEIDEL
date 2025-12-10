# Sistema Comparativo: Jacobi vs Gauss-Seidel

**Comparación exhaustiva de dos métodos iterativos para resolver sistemas de ecuaciones lineales**

Proyecto de Métodos Numéricos I | UMSA | 2025

---

## 🚀 Inicio Rápido (3 Pasos)

### 1. Verificar Requisitos
Necesitas **PHP 7.4+** instalado:
```bash
php -v
```

Si no tienes PHP:
- **Windows**: Descarga desde [php.net](https://www.php.net/downloads)
- **macOS**: `brew install php`
- **Linux**: `sudo apt-get install php`

### 2. Clonar o Descargar el Proyecto
```bash
git clone https://github.com/marco-calvar/JACOBI-VS-GAUSS-SEIDEL.git
cd JACOBI-VS-GAUSS-SEIDEL
```

### 3. Ejecutar el Sistema

#### Opción A: Windows (Recomendado)
**Doble clic en:** `SETUP.bat`

O en PowerShell:
```powershell
.\SETUP.ps1
```

#### Opción B: Linux / macOS
```bash
chmod +x SETUP.sh
./SETUP.sh
```

#### Opción C: Comando Manual
```bash
php -S localhost:8000
```

---

## 📱 Acceso a la Aplicación

Una vez que el servidor esté corriendo, abre en tu navegador:

```
http://localhost:8000
```

---

## 📋 Estructura del Proyecto

```
proyecto/
├── index.php                      # Página de inicio
├── bienvenida.php                # Introducción y selector de casos
├── sistema_comparativo.php        # Aplicación principal (análisis)
│
├── clases/                        # Backend - Lógica matemática
│   ├── Jacobi.php               # Implementación del método Jacobi
│   ├── GaussSeidel.php          # Implementación de Gauss-Seidel
│   ├── Validador.php            # Validación de integridad
│   ├── Comparador.php           # Análisis comparativo
│   ├── AnalizadorAvanzado.php   # Análisis matemático profundo
│   └── CasosPrueba.php          # 7 casos predefinidos
│
├── css/
│   └── estilos.css              # Estilos (tema azul marino)
│
├── js/
│   └── script.js                # Lógica del cliente
│
├── docs/                         # Documentación
│   ├── INSTALACION.txt
│   ├── README.md
│   └── CASOS_PRUEBA.txt
│
└── SETUP.*                       # Scripts de instalación
    ├── SETUP.bat                # Para Windows CMD
    ├── SETUP.ps1                # Para Windows PowerShell
    └── SETUP.sh                 # Para Linux/macOS
```

---

## 🔧 Configuración Avanzada

### Con Servidor Web Local (XAMPP/WAMP)

**Windows:**
1. Instala [XAMPP](https://www.apachefriends.org/)
2. Extrae el proyecto en: `C:\xampp\htdocs\jacobi-gauss-seidel`
3. Inicia Apache en XAMPP Control Panel
4. Accede a: `http://localhost/jacobi-gauss-seidel`

**Linux:**
```bash
sudo cp -r . /var/www/html/jacobi-gauss-seidel
sudo systemctl restart apache2
# Accede a: http://localhost/jacobi-gauss-seidel
```

### Con Docker (Opcional)

```bash
# Crear imagen
docker build -t jacobi-gauss-seidel .

# Ejecutar contenedor
docker run -p 8000:8000 jacobi-gauss-seidel
```

---

## 📚 ¿Qué Hace el Sistema?

### Métodos Implementados

#### 🔹 Jacobi
- Formula iterativa: **x_i^(k+1) = (b_i - Σ(j≠i) a_ij*x_j^(k)) / a_ii**
- **Característica:** Usa valores anteriores (x^(k))
- **Ventaja:** Altamente paralelizable
- **Desventaja:** Convergencia más lenta

#### 🔹 Gauss-Seidel
- Formula iterativa: **x_i^(k+1) = (b_i - Σ(j<i) a_ij*x_j^(k+1) - Σ(j>i) a_ij*x_j^(k)) / a_ii**
- **Característica:** Usa valores nuevos (x^(k+1)) cuando están disponibles
- **Ventaja:** Convergencia ~2x más rápida
- **Desventaja:** Secuencial (no paralelizable)

### Análisis Realizados

✅ **Convergencia:** ¿Ambos convergen? ¿Uno falla?
✅ **Iteraciones:** Cantidad necesaria para cada método
✅ **Velocidad:** Tiempo de ejecución en ms
✅ **Memoria:** Consumo en KB
✅ **Estabilidad:** ¿Monótona? ¿Oscilante?
✅ **Radio Espectral:** Estimación de velocidad de convergencia
✅ **Residuos:** Verificación ||Ax - b||
✅ **Recomendaciones:** Cuál usar y por qué

---

## 🧪 7 Casos de Prueba Predefinidos

El sistema incluye 7 casos pedagógicos para aprender y validar:

| Caso | Dimensión | Propósito | Resultado Esperado |
|------|-----------|----------|-------------------|
| **Caso 1** | 3×3 | Diagonal dominante ideal | Convergencia rápida en ambos |
| **Caso 2** | 4×4 | Sistema general moderado | GS converge ~2x más rápido |
| **Caso 3** | 2×2 | Visualización simple | Muy pocas iteraciones |
| **Caso 4** | 5×5 | Diferencia J vs GS máxima | EJEMPLO PEDAGÓGICO |
| **Caso 5** | 3×3 | NO diagonal dominante | Divergencia (enseñanza) |
| **Caso 6** | 6×6 | Tamaño mediano | Evaluación de eficiencia |
| **Caso 7** | 5×5 | Tridiagonal (diferencias finitas) | Estructura especial |

---

## 📊 Flujo de Uso

```
1. Abre http://localhost:8000
   ↓
2. Lee presentación (index.php)
   ↓
3. Elige una opción en bienvenida.php
   ├─ Seleccionar caso predefinido
   └─ Ingresar sistema manual
   ↓
4. Sistema ejecuta:
   ├─ Validación
   ├─ Resuelve con Jacobi
   ├─ Resuelve con Gauss-Seidel
   └─ Genera análisis comparativo
   ↓
5. Visualiza resultados:
   ├─ Gráficas de convergencia
   ├─ Tablas comparativas
   ├─ Análisis matemático
   └─ Recomendaciones
```

---

## 🔍 Características Técnicas

### Frontend
- **HTML5** con estructura semántica
- **CSS3** con tema azul marino profesional
- **JavaScript** ES6+ para interactividad
- **Chart.js 3.9** para gráficas de convergencia
- **MathJax 3.2** para renderizado de matrices

### Backend
- **PHP 7.4+** con programación orientada a objetos
- **6 clases** con responsabilidades específicas
- **Validación exhaustiva** de entrada
- **Análisis matemático avanzado**
- **Sin dependencias externas** (puro PHP)

### Matemática
- Implementación exacta de fórmulas iterativas
- Criterios de convergencia (dominancia diagonal)
- Estimación de radio espectral
- Cálculo de residuos
- Análisis de estabilidad numérica

---

## 🛠️ Solución de Problemas

### "PHP no encontrado" / "php: command not found"

**Solución:**
1. Instala PHP (ver sección Inicio Rápido)
2. Agrega PHP al PATH:
   - **Windows:** Busca "Variables de entorno" → Editar PATH
   - **Linux/Mac:** `echo $PATH` y verifica ruta PHP

### "Puerto 8000 en uso"

**Solución - Usar otro puerto:**
```bash
php -S localhost:3000   # Usa puerto 3000
# Luego accede a: http://localhost:3000
```

### "Permisos denegados en SETUP.sh"

**Solución:**
```bash
chmod +x SETUP.sh
./SETUP.sh
```

### "Error: División por cero en Jacobi.php"

**Causa:** Diagonal de matriz contiene ceros
**Solución:** Usa un caso predefinido o ingresa matriz con diagonal ≠ 0

---

## 📖 Documentación Adicional

- **[INSTALACION.txt](docs/INSTALACION.txt)** - Instalación detallada
- **[REPORTE_TECNICO.txt](docs/REPORTE_TECNICO.txt)** - Análisis matemático
- **[MANUAL_USUARIO.txt](docs/MANUAL_USUARIO.txt)** - Guía de uso
- **[CASOS_PRUEBA.txt](docs/CASOS_PRUEBA.txt)** - Descripción de casos

---

## 👥 Autores

**Proyecto:** Sistema Comparativo Jacobi vs Gauss-Seidel
**Materia:** Métodos Numéricos I
**Universidad:** Universidad Mayor de San Andrés (UMSA)
**Carrera:** Informática

**Autores:**
- Quiroz Coila Ariadne Checcid
- Rocha Rivero Jose Leonardo
- Laura Rios Lizbeth Fabiola
- Callisaya Vargas Marco Ronaldo

---

## 📝 Licencia

Proyecto académico de código abierto
Libre para uso educativo y modificación

---

## 🔗 Enlaces Útiles

- **GitHub:** https://github.com/marco-calvar/JACOBI-VS-GAUSS-SEIDEL
- **PHP Manual:** https://www.php.net/manual/
- **Chart.js:** https://www.chartjs.org/
- **MathJax:** https://www.mathjax.org/

---

## ✨ Características Destacadas

✅ Interfaz moderna y responsiva
✅ 2 métodos iterativos completamente implementados
✅ 7 casos de prueba predefinidos
✅ Análisis comparativo exhaustivo
✅ Gráficas interactivas de convergencia
✅ Visualización de matrices con MathJax
✅ Validación exhaustiva de entrada
✅ Análisis matemático avanzado (radio espectral)
✅ Documentación inline en código fuente
✅ Scripts de instalación automatizados

---

## 🚀 Próximos Pasos

¿Primera vez aquí? Sigue estos pasos:

1. **Lee:** Intro en index.php
2. **Prueba:** Caso 1 en bienvenida.php (diagonal dominante)
3. **Compara:** Caso 4 (ve la diferencia entre métodos)
4. **Experimenta:** Ingresa tu propio sistema
5. **Aprende:** Lee la documentación detallada

¡Disfruta explorando los métodos iterativos! 🎓
