/**
 * ============================================================================
 * DOCUMENTACIÓN DEL CÓDIGO - SISTEMA COMPARATIVO JACOBI VS GAUSS-SEIDEL
 * ============================================================================
 * 
 * Este archivo documenta la estructura y funcionalidad del código
 * implementado en el proyecto.
 * 
 * ÍNDICE:
 * 1. Arquitectura General
 * 2. Clases Principales
 * 3. Métodos y Funciones
 * 4. Flujo de Ejecución
 * 5. Ejemplos de Uso
 * 6. Notas de Implementación
 * 
 * ============================================================================
 */

// ============================================================================
// 1. ARQUITECTURA GENERAL
// ============================================================================

/*
DIAGRAMA DE COMPONENTES:

┌─────────────────────────────────────────────────────────────┐
│                   INTERFAZ WEB (HTML/CSS/JS)               │
│  - Formulario de entrada                                    │
│  - Botones de casos de prueba                               │
│  - Gráficas con Chart.js                                    │
│  - MathJax para visualización matemática                    │
└────────────────────┬────────────────────────────────────────┘
                     │ JSON/POST
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              BACKEND PHP - sistema_comparativo.php          │
│  - Recibe datos del formulario                              │
│  - Valida entrada                                            │
│  - Instancia métodos iterativos                             │
│  - Ejecuta comparación                                      │
│  - Retorna JSON con resultados                              │
└────────────────────┬────────────────────────────────────────┘
                     │ Instancia objetos
                     ▼
┌─────────────────────────────────────────────────────────────┐
│          CLASES PHP - Carpeta /clases                       │
│  - Jacobi.php (algoritmo Jacobi)                            │
│  - GaussSeidel.php (algoritmo Gauss-Seidel)                 │
│  - Comparador.php (análisis comparativo)                    │
│  - Validador.php (validaciones matemáticas)                 │
│  - AnalizadorAvanzado.php (métricas y análisis)             │
│  - CasosPrueba.php (7 casos predefinidos)                   │
└─────────────────────────────────────────────────────────────┘

FLUJO DE DATOS:

Usuario Input → HTML Form → JavaScript → POST JSON
     ↓
sistema_comparativo.php → Validador → Jacobi → Resultados
                                     → GaussSeidel
                                     → Comparador
                                     → AnalizadorAvanzado
     ↓
JSON Response → JavaScript → Chart.js → Gráficas
              → HTML → Mostrar Resultados
*/

// ============================================================================
// 2. CLASES PRINCIPALES
// ============================================================================

/*
─────────────────────────────────────────────────────────────────────────────
CLASE: Jacobi
─────────────────────────────────────────────────────────────────────────────

PROPÓSITO:
  Implementar el algoritmo iterativo de Jacobi para resolver Ax = b

PROPIEDADES PRIVADAS:
  - $matriz_A: matriz de coeficientes (nxn)
  - $vector_b: vector de términos independientes (n)
  - $tolerancia: criterio de parada (epsilon)
  - $max_iteraciones: límite de pasos iterativos
  - $x_inicial: vector inicial (por defecto ceros)
  - $solucion: resultado del método
  - $iteraciones: número de pasos realizados
  - $tiempo_ejecucion: milisegundos tomados
  - $memoria_usada: KB estimados
  - $errores: array de errores en cada iteración
  - $convergencia: booleano indicando convergencia

MÉTODOS PRINCIPALES:

  __construct($matrizA, $vectorB, $tolerancia, $maxIter, $xInicial)
    → Constructor. Inicializa el objeto con parámetros.
    → Parámetros:
      • $matrizA: matriz de coeficientes (array 2D)
      • $vectorB: vector b (array 1D)
      • $tolerancia: epsilon para parada (defecto 0.0001)
      • $maxIter: máximo de iteraciones (defecto 100)
      • $xInicial: vector inicial (defecto null → ceros)

  resolver()
    → Ejecuta el algoritmo de Jacobi
    → Retorna: booleano indicando convergencia
    → Calcula: $solucion, $iteraciones, $errores, $tiempo_ejecucion

  esDiagonalmenteDominante()
    → Verifica si |aii| > Σ|aij| para todo i
    → Retorna: booleano
    → Nota: Suficiente pero no necesaria para convergencia

  obtenerSolucion()
    → Retorna: vector solución (array)

  obtenerIteraciones()
    → Retorna: número de iteraciones (int)

  obtenerErrores()
    → Retorna: array de errores por iteración

  obtenerTiempoEjecucion()
    → Retorna: tiempo en milisegundos (float)

  obtenerMemoriaUsada()
    → Retorna: memoria estimada en KB (float)

ALGORITMO (Pseudocódigo):

  Jacobi(A, b, x0, ε, maxIter):
    x ← x0
    Para k = 1 hasta maxIter:
      x_anterior ← x
      Para i = 1 hasta n:
        suma ← 0
        Para j = 1 hasta n:
          Si j ≠ i:
            suma ← suma + A[i][j] * x_anterior[j]
        x[i] ← (b[i] - suma) / A[i][i]
      
      error ← ||x - x_anterior|| / ||x||
      Si error < ε:
        Retornar (x, k, CONVERGENCIA)
    
    Retornar (x, maxIter, NO_CONVERGENCIA)

CARACTERÍSTICAS:
  ✓ Usa valores de la iteración anterior
  ✓ Fácilmente paralelizable
  ✓ Convergencia más lenta que Gauss-Seidel
  ✓ Converge si matriz es diagonal dominante
*/

/*
─────────────────────────────────────────────────────────────────────────────
CLASE: GaussSeidel
─────────────────────────────────────────────────────────────────────────────

PROPÓSITO:
  Implementar el algoritmo iterativo de Gauss-Seidel para resolver Ax = b

DIFERENCIAS CON JACOBI:
  - Usa valores actualizados en la misma iteración
  - Converge típicamente más rápido
  - No es fácilmente paralelizable
  - Estructura de código muy similar a Jacobi

MÉTODOS:
  Idénticos a Jacobi (interfaz compatible)

ALGORITMO (Pseudocódigo):

  GaussSeidel(A, b, x0, ε, maxIter):
    x ← x0
    Para k = 1 hasta maxIter:
      x_anterior ← x
      Para i = 1 hasta n:
        suma1 ← 0
        suma2 ← 0
        Para j = 1 hasta i-1:           // Valores nuevos
          suma1 ← suma1 + A[i][j] * x[j]
        Para j = i+1 hasta n:           // Valores antiguos
          suma2 ← suma2 + A[i][j] * x_anterior[j]
        
        x[i] ← (b[i] - suma1 - suma2) / A[i][i]
      
      error ← ||x - x_anterior|| / ||x||
      Si error < ε:
        Retornar (x, k, CONVERGENCIA)
    
    Retornar (x, maxIter, NO_CONVERGENCIA)

VENTAJAS:
  ✓ Converge más rápido (~30-50% menos iteraciones)
  ✓ Mejor para sistemas bien acondicionados
  ✓ Menos memoria típicamente
*/

/*
─────────────────────────────────────────────────────────────────────────────
CLASE: Validador
─────────────────────────────────────────────────────────────────────────────

PROPÓSITO:
  Validar y verificar parámetros antes de resolver

MÉTODOS ESTÁTICOS:

  validarSistemaCompleto($matrizA, $vectorB, $tolerancia, $maxIter)
    → Valida todo el sistema
    → Retorna: array con ['valido' => bool, 'advertencias' => array]
    → Verificaciones:
      • Dimensión matriz válida
      • Vector b con dimensión correcta
      • Matriz no singular (det ≠ 0)
      • Sin elementos diagonales cero
      • Tolerancia y maxIter positivos

  validarDimension($n)
    → Verifica 2 ≤ n ≤ 20
    → Retorna: booleano

  verificarDiagonalDominancia($matrizA)
    → Valida |aii| > Σ|aij|
    → Retorna: booleano

ADVERTENCIAS GENERADAS:
  ⚠️ Matriz NO diagonal dominante
  ⚠️ Número de condición alto
  ⚠️ Tolerancia muy pequeña
  ⚠️ Tolerancia muy grande
*/

/*
─────────────────────────────────────────────────────────────────────────────
CLASE: Comparador
─────────────────────────────────────────────────────────────────────────────

PROPÓSITO:
  Comparar resultados de ambos métodos

MÉTODOS ESTÁTICOS:

  compararMetodos($jacobi, $gaussSeidel)
    → Compara dos objetos después de resolver
    → Retorna: array con comparativas
    → Análisis:
      • Diferencia en iteraciones
      • Diferencia en tiempo
      • Diferencia en memoria
      • Método más eficiente
      • Porcentaje de mejora

SALIDA:
  [
    'mas_rapido' => 'GaussSeidel',
    'diferencia_iteraciones' => 12,
    'mejora_porcentaje' => 35.5,
    'diferencia_tiempo' => 15.2
  ]
*/

/*
─────────────────────────────────────────────────────────────────────────────
CLASE: AnalizadorAvanzado
─────────────────────────────────────────────────────────────────────────────

PROPÓSITO:
  Análisis profundo de convergencia y rendimiento

MÉTODOS ESTÁTICOS:

  analizarConvergencia($errores)
    → Calcula tasa de convergencia lineal
    → Identifica patrón de convergencia
    → Retorna: array con análisis estadístico

  calcularTasaConvergencia($errores)
    → Calcula ratio entre errores consecutivos
    → Retorna: array de tasas

  calcularMemoria($matriz_A, $vector_b)
    → Estima uso de memoria en KB
    → Considera: matriz, vector, almacenamiento iterativo

SALIDA:
  [
    'tasa_promedio' => 0.45,
    'tipo_convergencia' => 'Lineal',
    'velocidad' => 'Moderada'
  ]
*/

/*
─────────────────────────────────────────────────────────────────────────────
CLASE: CasosPrueba
─────────────────────────────────────────────────────────────────────────────

PROPÓSITO:
  Almacenar y gestionar 7 casos de prueba predefinidos

MÉTODOS ESTÁTICOS:

  obtenerTodos()
    → Retorna: array con los 7 casos
    → Estructura de cada caso:
      [
        'nombre' => string,
        'descripcion' => string,
        'matriz_A' => array 2D,
        'vector_b' => array 1D,
        'tolerancia' => float,
        'max_iteraciones' => int
      ]

  obtenerCaso($clave)
    → Retorna: un caso específico

  obtenerDescripciones()
    → Retorna: array de nombres y descripciones

CASOS INCLUIDOS:
  1. Sistema 3x3 - Diagonal Dominante
  2. Sistema 4x4 - Diagonal Dominante Moderada
  3. Sistema 2x2 - Simple
  4. Sistema 5x5 - Débilmente Diagonal Dominante
  5. Sistema 3x3 - No Diagonal Dominante
  6. Sistema 6x6 - Tamaño Mediano
  7. Sistema Tridiagonal 5x5
*/

// ============================================================================
// 3. MÉTODOS Y FUNCIONES PRINCIPALES
// ============================================================================

/*
─────────────────────────────────────────────────────────────────────────────
FUNCIÓN: Resolución de Sistema (algoritmo iterativo genérico)
─────────────────────────────────────────────────────────────────────────────

FÓRMULA GENERAL:
  x_i^(k) = (b_i - Σ(a_ij * x_j^(k-α))) / a_ii
  
  Donde α = 0 para Jacobi (usa valores previos)
        α = 1 para índices i < j en Gauss-Seidel (usa valores nuevos)

CRITERIO DE PARADA:
  error_relativo = ||x^(k) - x^(k-1)|| / ||x^(k)|| < ε

NORMA UTILIZADA:
  Norma euclidiana: ||x|| = √(Σ x_i²)

COMPLEJIDAD:
  - Por iteración: O(n²) - dos bucles anidados
  - Típicamente: O(k·n²) donde k es número de iteraciones
  - k típicamente 10-100 para sistemas bien acondicionados
*/

/*
─────────────────────────────────────────────────────────────────────────────
FUNCIÓN: Cálculo de Error Relativo
─────────────────────────────────────────────────────────────────────────────

DEFINICIÓN:
  error = ||x_nuevo - x_anterior|| / ||x_nuevo||

IMPLEMENTACIÓN EN PHP:
  
  function calcularErrorRelativo($x_nuevo, $x_anterior) {
    // Calcular diferencia
    $diferencia = 0;
    for ($i = 0; $i < count($x_nuevo); $i++) {
      $diferencia += pow($x_nuevo[$i] - $x_anterior[$i], 2);
    }
    $norma_diferencia = sqrt($diferencia);
    
    // Calcular norma de nuevo
    $norma_nuevo = 0;
    for ($i = 0; $i < count($x_nuevo); $i++) {
      $norma_nuevo += pow($x_nuevo[$i], 2);
    }
    $norma_nuevo = sqrt($norma_nuevo);
    
    // Evitar división por cero
    if ($norma_nuevo == 0) return PHP_FLOAT_MAX;
    
    return $norma_diferencia / $norma_nuevo;
  }

INTERPRETACIÓN:
  - error < ε: Convergencia alcanzada
  - error > ε: Continuar iterando
  - Escala logarítmica para visualización
*/

/*
─────────────────────────────────────────────────────────────────────────────
FUNCIÓN: Verificación de Diagonal Dominancia
─────────────────────────────────────────────────────────────────────────────

DEFINICIÓN:
  Matriz A es diagonal dominante si:
  |a_ii| > Σ(j≠i) |a_ij| para todo i

PSEUDOCÓDIGO:
  
  function esDiagonalmenteDominante(A):
    n ← filas(A)
    Para i = 0 hasta n-1:
      diagonal ← |A[i][i]|
      suma ← 0
      Para j = 0 hasta n-1:
        Si j ≠ i:
          suma ← suma + |A[i][j]|
      Si diagonal ≤ suma:
        Retornar FALSO
    Retornar VERDADERO

SIGNIFICADO:
  ✓ Garantiza convergencia de ambos métodos
  ✗ No es necesaria (solo suficiente)
  ⚠️ Sin ella, convergencia no garantizada
*/

// ============================================================================
// 4. FLUJO DE EJECUCIÓN
// ============================================================================

/*
FLUJO PRINCIPAL (sistema_comparativo.php):

1. PÁGINA CARGADA (GET)
   → Mostrar HTML con formulario
   → Mostrar 7 botones de casos de prueba
   → JavaScript lista

2. USUARIO SELECCIONA CASO
   → JavaScript llama cargarCaso('caso_key')
   → AJAX POST a sistema_comparativo.php con 'accion' = 'cargar_caso'
   → PHP retorna JSON con matriz y parámetros
   → JavaScript completa formulario

3. USUARIO INGRESA DATOS MANUALES
   → Completa campos en formulario
   → Valida dimensión (regenera matriz si cambia)

4. USUARIO HACE CLIC EN "COMPARAR"
   → JavaScript POST con formulario completo
   → PHP recibe datos

5. PROCESAMIENTO EN BACKEND:
   
   a) VALIDACIÓN
      → Validador::validarSistemaCompleto()
      → Si hay errores: retornar JSON con error
      → Si hay advertencias: guardarlas en array
   
   b) CREACIÓN DE OBJETOS
      → $jacobi = new Jacobi($A, $b, $tol, $maxIter)
      → $gs = new GaussSeidel($A, $b, $tol, $maxIter)
   
   c) RESOLUCIÓN
      → $jacobi->resolver()
      → $gs->resolver()
      → Ambos calculan solución e iteraciones
   
   d) ANÁLISIS
      → $comparador->compararMetodos($jacobi, $gs)
      → $analizador->analizarConvergencia(...)
   
   e) GENERACIÓN DE JSON
      → Crear array con todos los resultados
      → json_encode() y retornar

6. RECEPCIÓN EN FRONTEND
   → JavaScript recibe JSON
   → Parsea datos
   → Crea gráficas con Chart.js
   → Muestra resultados en HTML
   → Renderiza MathJax para matrices

7. VISUALIZACIÓN
   → Usuario ve solución, iteraciones, gráficas
   → Puede hacer clic en otro caso
   → Puede ingresar nuevos datos
   → Ciclo se repite
*/

/*
DIAGRAMA DE ESTADOS:

     ┌──────────────┐
     │  INICIO      │
     └──────┬───────┘
            │
     ┌──────▼────────────┐
     │  Mostrar Página   │
     │  (GET - Inicial)  │
     └──────┬────────────┘
            │
     ┌──────┼──────────────────┐
     │      │                  │
  ┌──▼──┐  ┌▼──────────────┐  │
  │ Caso│  │ Datos Manuales│  │
  │Prueba  │                │  │
  └──┬──┘  └──┬───────────┬─┘  │
     │        │           │    │
     └────┬───┘    ┌──────▼──┐ │
          │        │ Validar │ │
     ┌────▼──────┐ └────┬────┘ │
     │ Formulario│      │      │
     │ Completo │      │      │
     └────┬──────┘     Si     │
          │        hay error  │
     ┌────▼──────────────┐   │
     │ Clic "Comparar"   │   │
     └────┬──────────────┘   │
          │                   │
     ┌────▼──────────────────┴──┐
     │  Resolver Jacobi         │
     │  Resolver Gauss-Seidel   │
     │  Comparar y Analizar     │
     └────┬──────────────────────┘
          │
     ┌────▼──────────────┐
     │ Retornar JSON     │
     │ con resultados    │
     └────┬──────────────┘
          │
     ┌────▼──────────────┐
     │ Mostrar Resultados│
     │ Gráficas, Tablas  │
     └────┬──────────────┘
          │
     ┌────▼──────────────┐
     │ Usuario decide:   │
     │ - Otro caso       │
     │ - Nuevos datos    │
     │ - Salir           │
     └───────────────────┘
*/

// ============================================================================
// 5. EJEMPLOS DE USO
// ============================================================================

/*
EJEMPLO 1: Resolver sistema simple con Jacobi

  <?php
  require_once 'clases/Jacobi.php';
  
  // Sistema: 4x - y = 5
  //          2x + 3y = 8
  
  $A = [
    [4, -1],
    [2,  3]
  ];
  $b = [5, 8];
  
  // Crear objeto Jacobi
  $jacobi = new Jacobi($A, $b, 0.0001, 100);
  
  // Resolver
  $convergencia = $jacobi->resolver();
  
  // Obtener resultados
  if ($convergencia) {
    echo "Solución: ";
    print_r($jacobi->obtenerSolucion());
    echo "Iteraciones: " . $jacobi->obtenerIteraciones();
  } else {
    echo "No convergió";
  }
  ?>

EJEMPLO 2: Comparar ambos métodos

  <?php
  require_once 'clases/Jacobi.php';
  require_once 'clases/GaussSeidel.php';
  require_once 'clases/Comparador.php';
  
  $jacobi = new Jacobi($A, $b, 0.0001, 100);
  $gs = new GaussSeidel($A, $b, 0.0001, 100);
  
  $jacobi->resolver();
  $gs->resolver();
  
  $comparacion = Comparador::compararMetodos($jacobi, $gs);
  
  echo "Método más rápido: " . $comparacion['mas_rapido'];
  echo "Mejora: " . $comparacion['mejora_porcentaje'] . "%";
  ?>

EJEMPLO 3: Validar antes de resolver

  <?php
  require_once 'clases/Validador.php';
  
  $validacion = Validador::validarSistemaCompleto(
    $matriz_A, $vector_b, 0.0001, 100
  );
  
  if (!$validacion['valido']) {
    echo "Errores: ";
    print_r($validacion['errores']);
  }
  
  if (!empty($validacion['advertencias'])) {
    echo "Advertencias: ";
    print_r($validacion['advertencias']);
  }
  ?>
*/

// ============================================================================
// 6. NOTAS DE IMPLEMENTACIÓN
// ============================================================================

/*
─────────────────────────────────────────────────────────────────────────────
CONSIDERACIONES NUMÉRICAS
─────────────────────────────────────────────────────────────────────────────

PRECISIÓN EN PHP:
  - Punto flotante de doble precisión (64 bits)
  - Aproximadamente 14 dígitos decimales significativos
  - Redondeo automático en operaciones
  - Cuidado con valores muy pequeños (underflow)

TOLERANCIA RECOMENDADA:
  - Muy grande: ε > 0.01 → Resultados imprecisos
  - Normal: ε = 0.0001 a 0.001 → Recomendado
  - Pequeña: ε = 0.00001 a 0.000001 → Más iteraciones
  - Muy pequeña: ε < 1e-8 → Posible divergencia numérica

MÁXIMO ITERACIONES:
  - Mínimo: 100 para pequeños sistemas
  - Recomendado: 1000
  - Para sistemas grandes (n > 10): 2000
  - Evita bucles infinitos en casos de no convergencia

─────────────────────────────────────────────────────────────────────────────
ESTABILIDAD NUMÉRICA
─────────────────────────────────────────────────────────────────────────────

DIAGONAL DOMINANCIA:
  - Aumenta estabilidad significativamente
  - Garantiza convergencia teóricamente
  - En práctica, también converge sin ella frecuentemente
  - Pero: resultados pueden ser impredecibles

NÚMERO DE CONDICIÓN:
  - No se calcula explícitamente en este código
  - Matrices mal acondicionadas: precisión baja
  - κ(A) >> 1 → Pequeños cambios en b causan grandes cambios en x

REDONDEO:
  - En cada iteración se acumulan errores de redondeo
  - Después de muchas iteraciones: error total puede crecer
  - Por eso: usar tolerancia razonable

─────────────────────────────────────────────────────────────────────────────
PARALELIZACIÓN
─────────────────────────────────────────────────────────────────────────────

JACOBI:
  ✓ Fácilmente paralelizable
  ✓ Cada x_i puede calcularse independientemente
  ✓ No requiere sincronización entre cálculos
  ✓ Ideal para GPU/múltiples procesadores

GAUSS-SEIDEL:
  ✗ Difícil de paralelizar
  ✗ Requiere x_1^(k) antes de calcular x_2^(k)
  ✗ Dependencia de datos secuencial
  ✓ Aunque existen variantes (Red-Black GS)

─────────────────────────────────────────────────────────────────────────────
OPTIMIZACIONES POSIBLES
─────────────────────────────────────────────────────────────────────────────

1. SOR (Successive Over-Relaxation)
   - Gauss-Seidel con factor de relajación ω
   - Puede acelerar convergencia significativamente
   - Requiere selección óptima de ω

2. Preacondicionamiento
   - Modificar matriz para mejorar número de condición
   - Resolver M^(-1)Ax = M^(-1)b
   - Requiere computación de M^(-1)

3. Almacenamiento sparse
   - Para matrices grandes con muchos ceros
   - Guardados en formato CSR (Compressed Sparse Row)
   - Reduce memoria y tiempo

4. Métodos de Krylov
   - Conjugate Gradient, GMRES
   - Mejores para sistemas grandes
   - Fuera del alcance de este proyecto

─────────────────────────────────────────────────────────────────────────────
BUGS Y EDGE CASES
─────────────────────────────────────────────────────────────────────────────

CASAS ESPECIALES MANEJADAS:

1. Matriz singular (det = 0)
   → Error: "Sistema singular o casi singular"
   → No se ejecuta resolución

2. Elemento diagonal cero
   → Error: "Elemento diagonal cero"
   → Imposible calcular x_i

3. Vector b = 0
   → Válido: solución es x = 0
   → Converge en 1 iteración

4. Matriz identidad
   → Solución: x = b
   → Converge en 1 iteración

5. Tolerancia ≤ 0
   → Error: "Tolerancia inválida"
   → Validada en Validador

6. maxIter = 0
   → Retorna sin iteraciones
   → Error implícito

7. Nan o Infinity en entrada
   → Se propagan en cálculos
   → Resultado: NaN
   → Difícil detectar en PHP

─────────────────────────────────────────────────────────────────────────────
CAMBIOS Y MEJORAS FUTURAS
─────────────────────────────────────────────────────────────────────────────

CORTO PLAZO:
  □ Validación de NaN en entrada
  □ Número de condición calculado
  □ Exportar resultados a CSV/PDF
  □ Historial de sesiones

MEDIANO PLAZO:
  □ Base de datos para almacenar resultados
  □ Autenticación de usuarios
  □ Análisis estadístico avanzado
  □ Benchmarking automático

LARGO PLAZO:
  □ Implementar SOR (Successive Over-Relaxation)
  □ Conjugate Gradient
  □ GMRES
  □ Métodos para sistemas no lineales
  □ Paralelización con WebWorkers
  □ Versión en C/C++ para velocidad

*/

// ============================================================================
// FIN DE DOCUMENTACIÓN
// ============================================================================

/*
REFERENCIAS:

[1] Burden, R. L., & Faires, J. D. (2010). Numerical Analysis (9th ed.).
    Cengage Learning.

[2] Quarteroni, A., Sacco, R., & Saleri, F. (2010). Numerical Mathematics
    (2nd ed.). Springer-Verlag.

[3] Golub, G. H., & Van Loan, C. F. (1996). Matrix Computations
    (3rd ed.). Johns Hopkins University Press.

[4] Demmel, J. W. (1997). Applied Numerical Linear Algebra.
    Society for Industrial and Applied Mathematics.

[5] Boyd, S. (2023). Iterative Methods for Solving Linear Systems.
    Lecture Notes, Stanford University.

ÚLTIMA ACTUALIZACIÓN: Diciembre 10, 2025
VERSIÓN: 1.0
ESTADO: Documentación Completa
*/
