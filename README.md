# Sistema Comparativo - Jacobi vs Gauss-Seidel

> **Herramienta educativa e interactiva para comparar métodos iterativos de resolución de sistemas lineales**

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Uso](#uso)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Documentación](#documentación)
- [Tecnologías](#tecnologías)
- [Casos de Prueba](#casos-de-prueba)
- [Equipo](#equipo)

---

## 📖 Descripción

Este proyecto implementa un **sistema interactivo de comparación** entre dos métodos iterativos fundamentales:

- **Método de Jacobi**: Método iterativo clásico usando valores anteriores
- **Método de Gauss-Seidel**: Mejora de Jacobi usando valores actualizados

### Objetivo Pedagógico

Proporcionar una herramienta visual e interactiva para:
- Entender diferencias entre métodos iterativos
- Analizar convergencia numérica
- Comparar rendimiento (iteraciones, tiempo, memoria)
- Observar comportamiento en diferentes tipos de sistemas

---

## ✨ Características

### 🎯 Funcionalidades Principales

- **Entrada de Datos Flexible**
  - Formulario personalizado para matrices hasta 20x20
  - 7 casos de prueba predefinidos
  - Parámetros configurables (tolerancia, máx iteraciones)

- **Cálculos Simultáneos**
  - Ejecución de ambos métodos
  - Análisis comparativo automático
  - Validación matemática completa

- **Visualización de Resultados**
  - Gráficas de convergencia (escala logarítmica)
  - Comparación de métricas (barras)
  - Solución detallada del sistema

- **Análisis Avanzado**
  - Historial de errores por iteración
  - Estadísticas de rendimiento
  - Verificación de diagonal dominancia
  - Advertencias automáticas

### 🎨 Interfaz

- Diseño moderno con tema azul marino
- Interfaz responsiva (mobile-friendly)
- Gráficas interactivas con Chart.js
- MathJax para visualización matemática

---

## 📦 Requisitos

### Requisitos Mínimos

| Componente | Versión |
|-----------|---------|
| **PHP** | 7.4+ |
| **Servidor Web** | Apache 2.4+ |
| **Navegador** | Chrome, Firefox, Edge, Safari (moderno) |

### Requisitos Opcionales

- Git (para clonar repositorio)
- XAMPP / WAMP / LAMP (para desarrollo local)
- Composer (no necesario para esta versión)

### Navegadores Soportados

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Edge 90+
- ✅ Safari 14+
- ✅ Opera 76+

---

## 🚀 Instalación

### Opción 1: Instalación Local (XAMPP)

```bash
# 1. Descargar e instalar XAMPP
# Ir a: https://www.apachefriends.org/

# 2. Copiar proyecto a htdocs
cp -r proyecto C:\xampp\htdocs\
# O para Linux/Mac:
cp -r proyecto /opt/lampp/htdocs/

# 3. Iniciar Apache en XAMPP Control Panel

# 4. Acceder en navegador
# http://localhost/proyecto
```

### Opción 2: Desde Git

```bash
# Clonar repositorio
cd C:\xampp\htdocs
git clone https://github.com/usuario/proyecto.git
cd proyecto

# Iniciar Apache
# Acceder a http://localhost/proyecto
```

### Opción 3: Servidor Remoto

```bash
# Conectar por SSH
ssh usuario@example.com

# Navegar a directorio web
cd /var/www/html

# Clonar proyecto
git clone https://github.com/usuario/proyecto.git

# Configurar permisos
chmod -R 755 proyecto/

# Acceder en navegador
# http://example.com/proyecto
```

### Verificación

```bash
# Verificar PHP
php -v

# Verificar sintaxis de archivos
php -l clases/*.php

# Verificar que Apache está corriendo
# (Debería cargar la página en navegador sin errores)
```

---

## 💻 Uso

### Forma Rápida: Usar Casos de Prueba

1. Abrir aplicación en navegador
2. Hacer clic en cualquiera de los 7 botones de casos predefinidos
3. Hacer clic en "Comparar Métodos"
4. Observar resultados y gráficas

### Forma Manual: Ingreso Personalizado

1. **Ingresar Dimensión**: 2-20
2. **Completar Matriz A**: Coeficientes del sistema
3. **Ingresiar Vector b**: Términos independientes
4. **Configurar Parámetros**:
   - Tolerancia: ε (generalmente 0.0001)
   - Máximo iteraciones: (generalmente 1000)
5. **Hacer clic en "Comparar Métodos"**
6. **Analizar resultados**

### Interpretación de Resultados

**Solución del Sistema**
- Vector x que satisface Ax = b
- Redondeado a 6 decimales

**Detalles de Jacobi**
- Iteraciones necesarias
- Error final alcanzado
- Vector solución

**Detalles de Gauss-Seidel**
- Mismo formato que Jacobi
- Típicamente menos iteraciones

**Gráficas**
- **Líneas**: Convergencia del error (escala log)
- **Barras**: Comparación de métricas (Iter, Tiempo, Memoria)

---

## 📁 Estructura del Proyecto

```
proyecto/
│
├── index.php                    # Página de inicio con créditos
├── sistema_comparativo.php      # Aplicación principal
├── procesar.php                 # (Opcional) Procesamiento alternativo
│
├── clases/                      # Clases PHP OOP
│   ├── Jacobi.php              # Implementación método Jacobi
│   ├── GaussSeidel.php          # Implementación método Gauss-Seidel
│   ├── Comparador.php           # Análisis comparativo
│   ├── Validador.php            # Validaciones matemáticas
│   ├── AnalizadorAvanzado.php   # Análisis de métricas
│   └── CasosPrueba.php          # 7 casos de prueba
│
├── css/
│   └── estilos.css              # Estilos CSS3 (tema azul marino)
│
├── js/
│   └── script.js                # JavaScript interactivo
│
├── docs/                        # Documentación LaTeX
│   ├── INSTALACION.tex          # Guía de instalación
│   ├── REPORTE_TECNICO.tex      # Reporte técnico detallado
│   ├── MANUAL_USUARIO.tex       # Manual de usuario completo
│   ├── CASOS_PRUEBA.tex         # Documentación de casos
│   └── [archivos .pdf compilados]
│
└── README.md                    # Este archivo
```

---

## 📚 Documentación

### Documentos Disponibles

| Documento | Contenido |
|-----------|----------|
| **INSTALACION.tex** | Instrucciones paso a paso de instalación |
| **REPORTE_TECNICO.tex** | Fundamentos matemáticos e implementación |
| **MANUAL_USUARIO.tex** | Guía completa de uso de la aplicación |
| **CASOS_PRUEBA.tex** | Descripción detallada de los 7 casos |
| **EVIDENCIA_REQUISITOS.md** | Cumplimiento de requisitos del proyecto |

### Compilar LaTeX a PDF

```bash
# Windows (con MikTeX instalado)
pdflatex INSTALACION.tex
pdflatex REPORTE_TECNICO.tex
pdflatex MANUAL_USUARIO.tex
pdflatex CASOS_PRUEBA.tex

# Linux/Mac
pdflatex INSTALACION.tex
# o usar online: https://www.overleaf.com/
```

---

## 🛠️ Tecnologías

### Backend

| Tecnología | Versión | Propósito |
|-----------|---------|----------|
| **PHP** | 7.4+ | Lógica de algoritmos |
| **OOP** | - | Arquitectura de clases |
| **JSON** | - | Comunicación cliente-servidor |

### Frontend

| Tecnología | Versión | Propósito |
|-----------|---------|----------|
| **HTML5** | - | Estructura semántica |
| **CSS3** | - | Estilos y diseño responsivo |
| **JavaScript** | ES6+ | Interactividad dinámica |
| **Chart.js** | 3.x | Gráficas interactivas |
| **MathJax** | 3.x | Visualización matemática |

### Herramientas

- Git - Control de versiones
- LaTeX - Documentación
- VS Code - Editor recomendado

---

## 🧪 Casos de Prueba

### Los 7 Casos Incluidos

1. **Sistema 3x3 - Diagonal Dominante**
   - Descripción: Caso simple y estable
   - Iteraciones: 10-15
   - Uso: Verificación inicial

2. **Sistema 4x4 - Diagonal Dominante Moderada**
   - Descripción: Convergencia moderadamente lenta
   - Iteraciones: 25-35
   - Uso: Comparación efectiva

3. **Sistema 2x2 - Simple**
   - Descripción: Sistema mínimo
   - Iteraciones: 5-8
   - Uso: Prueba rápida

4. **Sistema 5x5 - Débilmente Diagonal Dominante**
   - Descripción: Convergencia lenta
   - Iteraciones: 80-120
   - Uso: Diferencias notables

5. **Sistema 3x3 - No Diagonal Dominante**
   - Descripción: Caso límite
   - Iteraciones: Variable
   - Uso: Educativo

6. **Sistema 6x6 - Tamaño Mediano**
   - Descripción: Evaluación de rendimiento
   - Iteraciones: 50-100
   - Uso: Comparación realista

7. **Sistema Tridiagonal 5x5**
   - Descripción: Aplicación real (diferencias finitas)
   - Iteraciones: 40-60
   - Uso: Caso práctico

---

## 🔍 Validaciones

### Validaciones Implementadas

- ✅ Dimensión: 2 ≤ n ≤ 20
- ✅ Matriz no singular: det(A) ≠ 0
- ✅ Valores numéricos válidos
- ✅ Tolerancia positiva: ε > 0
- ✅ Diagonal dominancia (aviso)

### Advertencias Generadas

- ⚠️ Matriz NO diagonal dominante
- ⚠️ Tolerancia muy pequeña
- ⚠️ Tolerancia muy grande
- ⚠️ Máximo iteraciones alcanzado

---

## 📊 Características Matemáticas

### Métodos Implementados

**Método de Jacobi**
```
x_i^(k) = (b_i - Σ(a_ij * x_j^(k-1))) / a_ii
```

**Método de Gauss-Seidel**
```
x_i^(k) = (b_i - Σ(a_ij * x_j^(k)) - Σ(a_ij * x_j^(k-1))) / a_ii
```

### Criterios de Parada

1. Error relativo: ||x^(k) - x^(k-1)|| / ||x^(k)|| < ε
2. Máximo iteraciones: k > maxIter

### Análisis de Convergencia

- Escala logarítmica para mejor visualización
- Comparación lado a lado
- Historial completo de errores

---

## 🎓 Equipo

**Autores del Proyecto**:
- Quiroz [Apellido]
- Rocha [Apellido]
- Laura [Apellido]
- Callisaya [Apellido]

**Materia**: Métodos Numéricos I
**Carrera**: Ingeniería Informática
**Universidad**: UMSA
**Fecha**: Diciembre 2025

---

## 📝 Licencia

Proyecto educativo - UMSA 2025

---

## 🤝 Contribuciones

Este es un proyecto educativo. Para reportar problemas:

1. Verificar documentación
2. Revisar casos de prueba
3. Contactar al equipo de desarrollo

---

## 📞 Soporte

### Recursos

- 📖 Documentación: Ver carpeta `/docs`
- 🧪 Casos de prueba: Botones en página principal
- ❓ FAQ: Ver MANUAL_USUARIO.tex

### Solución de Problemas

**Gráficas no aparecen**
- Limpiar caché: Ctrl+F5
- Verificar conexión internet

**Cálculos lentos**
- Aumentar tolerancia
- Reducir máximo iteraciones

**Resultados incorrectos**
- Verificar matriz válida
- Comprobar valores ingresados

---

## ✅ Checklist de Funcionalidades

- [x] Implementación Jacobi
- [x] Implementación Gauss-Seidel
- [x] Validaciones completas
- [x] 7 casos de prueba
- [x] Gráficas interactivas
- [x] Análisis comparativo
- [x] Interfaz moderna
- [x] Documentación completa
- [x] Manual de usuario
- [x] Reporte técnico
- [x] Créditos de autores

---

## 🚀 Roadmap Futuro (Opcional)

- [ ] Base de datos para almacenar resultados
- [ ] Exportar resultados a PDF/Excel
- [ ] Versión móvil mejorada
- [ ] Métodos adicionales (SOR, Conjugado Gradiente)
- [ ] Análisis de número de condición
- [ ] Paralelización en cliente

---

**Última actualización**: Diciembre 10, 2025

**Estado**: ✅ Completado y Documentado
