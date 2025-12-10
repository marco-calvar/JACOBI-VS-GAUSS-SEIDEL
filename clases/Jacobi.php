<?php
/**
 * CLASE JACOBI - MÉTODO ITERATIVO CLÁSICO
 * ========================================
 * Resuelve sistemas de ecuaciones lineales Ax = b usando el método de Jacobi.
 * 
 * TEORÍA:
 * - Descompone A = D - L - U (D diagonal, L/U triangulares)
 * - Fórmula: x^(k) = D^(-1)(b + (L+U)x^(k-1))
 * - Componente: x_i^(k) = (b_i - Σ(j≠i) a_ij*x_j^(k-1)) / a_ii
 * - PUNTO CLAVE: Usa valores VIEJOS (iteración anterior)
 * 
 * CARACTERÍSTICAS:
 * - Converge si matriz es diagonal dominante
 * - Paralelizable (cálculos independientes)
 * - Más lento que Gauss-Seidel (~2x)
 * - Estable numéricamente
 * 
 * COMPLEJIDAD:
 * - Tiempo por iteración: O(n²)
 * - Iteraciones necesarias: O(log(1/ε)/log(1/ρ))
 * - Total: O(n² × iteraciones)
 */
class Jacobi {
    private $matriz_A;           // Matriz de coeficientes n×n
    private $vector_b;           // Vector términos independientes
    private $tolerancia;         // Criterio parada (típico 1e-6)
    private $max_iteraciones;    // Límite máximo de iteraciones
    private $x_inicial;          // Aproximación inicial [0, 0, ..., 0]
    
    // Resultados y métrica
    private $solucion;           // Vector solución x
    private $iteraciones;        // Cantidad iteraciones realizadas
    private $tiempo_ejecucion;   // Tiempo en segundos
    private $memoria_usada;      // Memoria consumida
    private $errores;            // Error en cada iteración (para gráficas)
    private $convergencia;       // ¿Convergio o no?
    
    /**
     * CONSTRUCTOR
     * Inicializa el método de Jacobi con parámetros de control
     * 
     * @param array $matriz_A         Matriz de coeficientes n×n de números reales
     * @param array $vector_b         Vector de términos independientes (lado derecho)
     * @param float $tolerancia       Error máximo permitido (default 0.0001)
     * @param int $max_iter           Límite máximo de iteraciones (default 100)
     * @param array|null $x_inicial   Aproximación inicial (default: vector ceros)
     * 
     * NOTA: Si x_inicial es null, se inicia con vector cero
     */
    public function __construct($matriz_A, $vector_b, $tolerancia = 0.0001, $max_iter = 100, $x_inicial = null) {
        $this->matriz_A = $matriz_A;
        $this->vector_b = $vector_b;
        $this->tolerancia = $tolerancia;
        $this->max_iteraciones = $max_iter;
        $this->errores = [];
        
        // Si no hay vector inicial, usar ceros
        if ($x_inicial === null) {
            $n = count($vector_b);
            $this->x_inicial = array_fill(0, $n, 0.0);
        } else {
            $this->x_inicial = $x_inicial;
        }
    }
    
    /**
     * VERIFICA DOMINANCIA DIAGONAL ESTRICTA
     * Condición suficiente (no necesaria) para garantizar convergencia
     * 
     * CRITERIO: Para cada fila i:
     *    |a_ii| > Σ(j≠i) |a_ij|
     * 
     * En palabras: El elemento diagonal debe ser MAYOR (no igual) que la suma
     * de valores absolutos de otros elementos en la fila.
     * 
     * @return bool true si TODAS las filas satisfacen dominancia diagonal
     * 
     * EJEMPLO con matriz 3×3:
     *   Fila 0: |a_00| > |a_01| + |a_02|
     *   Fila 1: |a_11| > |a_10| + |a_12|
     *   Fila 2: |a_22| > |a_20| + |a_21|
     */
    public function esDiagonalmenteDominante() {
        $n = count($this->matriz_A);
        
        // Verificar cada fila
        for ($i = 0; $i < $n; $i++) {
            $suma = 0;  // Suma de valores absolutos fuera diagonal
            
            for ($j = 0; $j < $n; $j++) {
                if ($i != $j) {
                    $suma += abs($this->matriz_A[$i][$j]);
                }
            }
            
            // Si |a_ii| <= suma, NO es diagonalmente dominante
            if (abs($this->matriz_A[$i][$i]) <= $suma) {
                return false;  // Falla en fila i
            }
        }
        return true;  // Todas las filas son diagonalmente dominantes
    }
    
    /**
     * EJECUTA ALGORITMO DE JACOBI
     * Itera hasta convergencia o límite máximo
     * 
     * FÓRMULA POR ITERACIÓN:
     *   x_i^(k+1) = (b_i - Σ(j≠i) a_ij * x_j^(k)) / a_ii
     * 
     * PASOS:
     * 1. Inicializa x^(0) (proporcionado o vector cero)
     * 2. Para cada iteración k = 0, 1, 2, ...
     *    a) Calcula x_i^(k+1) usando valores VIEJOS x^(k)
     *    b) Calcula error relativo Euclidiano
     *    c) Si error < tolerancia: CONVERGIO
     *    d) Sino: continúa a iteración k+1
     * 
     * CONVERGENCIA: error = sqrt(Σ(x_new[i]-x_old[i])² / Σ(x_new[i]²))
     * 
     * TAMBIÉN REGISTRA:
     * - Tiempo de ejecución (ms)
     * - Memoria consumida (KB)
     * - Historial de errores (para gráficas)
     * 
     * @return void (resultados en propiedades internas)
     */
    public function resolver() {
        $inicio = microtime(true);
        $memoria_inicio = memory_get_usage();
        
        $n = count($this->vector_b);
        $x_viejo = $this->x_inicial;  // x^(k)
        $x_nuevo = array_fill(0, $n, 0.0);  // x^(k+1)
        $this->convergencia = false;
        
        // LOOP PRINCIPAL DE ITERACIONES
        for ($k = 0; $k < $this->max_iteraciones; $k++) {
            // ===== CÁLCULO DE JACOBI =====
            // Para cada ecuación i:
            for ($i = 0; $i < $n; $i++) {
                $suma = 0.0;  // Σ(j≠i) a_ij * x_j^(k)
                
                // Sumar contribución de todas variables excepto i
                for ($j = 0; $j < $n; $j++) {
                    if ($i != $j) {
                        $suma += $this->matriz_A[$i][$j] * $x_viejo[$j];  // Usa x VIEJO
                    }
                }
                
                // Fórmula: (b_i - suma) / a_ii
                $x_nuevo[$i] = ($this->vector_b[$i] - $suma) / $this->matriz_A[$i][$i];
            }
            
            // ===== VERIFICACIÓN DE CONVERGENCIA =====
            $error = $this->calcularError($x_nuevo, $x_viejo);
            $this->errores[] = $error;  // Guardar para gráficas
            
            // Si error suficientemente pequeño
            if ($error < $this->tolerancia) {
                $this->convergencia = true;
                $this->iteraciones = $k + 1;
                $this->solucion = $x_nuevo;
                break;  // SALIR - convergio
            }
            
            // Preparar siguiente iteración
            $x_viejo = $x_nuevo;  // x^(k) = x^(k+1)
        }
        
        // Si alcanzó max_iteraciones sin converger
        if (!$this->convergencia) {
            $this->iteraciones = $this->max_iteraciones;
            $this->solucion = $x_nuevo;  // Usar última aproximación
        }
        
        // Calcular métricas de performance
        $this->tiempo_ejecucion = (microtime(true) - $inicio) * 1000;  // convertir a ms
        $this->memoria_usada = (memory_get_usage() - $memoria_inicio) / 1024;  // convertir a KB
    }
    
    /**
     * CALCULA ERROR RELATIVO EUCLIDIANO
     * Métrica para determinar convergencia
     * 
     * FÓRMULA:
     *   error = sqrt(Σ(i=0 a n-1) (x_new[i] - x_old[i])²) / sqrt(Σ(i=0 a n-1) x_new[i]²)
     *   
     *   Numerador: Norma L2 del cambio (Euclidiana de diferencia)
     *   Denominador: Norma L2 del vector nuevo
     * 
     * INTERPRETACIÓN:
     * - error ≈ 0  → solución prácticamente no cambió → convergencia
     * - error > tolerancia → debe seguir iterando
     * 
     * CASOS ESPECIALES:
     * - Si x_nuevo es vector cero: retorna 0 (evita división por 0)
     * - Si x_nuevo = x_viejo: retorna 0
     * 
     * @param array $x_nuevo Vector solución en iteración k+1
     * @param array $x_viejo Vector solución en iteración k
     * @return float Error relativo (número entre 0 y 1 típicamente)
     */
    private function calcularError($x_nuevo, $x_viejo) {
        $n = count($x_nuevo);
        $suma_num = 0.0;  // Σ(x_new[i] - x_old[i])²
        $suma_den = 0.0;  // Σ(x_new[i])²
        
        // Calcular ambas sumas simultáneamente
        for ($i = 0; $i < $n; $i++) {
            $suma_num += pow($x_nuevo[$i] - $x_viejo[$i], 2);  // Diferencia al cuadrado
            $suma_den += pow($x_nuevo[$i], 2);  // Componente nuevo al cuadrado
        }
        
        // Prevenir división por cero (caso: x_nuevo ≈ [0,0,...,0])
        if ($suma_den == 0) return 0;
        
        // Retornar raíz de cociente
        return sqrt($suma_num / $suma_den);
    }
    
    // ============ MÉTODOS DE ACCESO (GETTERS) ============
    // Retornan resultados de la última ejecución de resolver()
    
    /** @return array Vector solución x que satisface Ax ≈ b */
    public function getSolucion() { return $this->solucion; }
    
    /** @return int Número de iteraciones ejecutadas */
    public function getIteraciones() { return $this->iteraciones; }
    
    /** @return float Tiempo de ejecución en milisegundos */
    public function getTiempoEjecucion() { return $this->tiempo_ejecucion; }
    
    /** @return float Memoria consumida en kilobytes */
    public function getMemoriaUsada() { return $this->memoria_usada; }
    
    /** @return array Historial de errores por iteración (para gráficas) */
    public function getErrores() { return $this->errores; }
    
    /** @return bool true si convergio dentro tolerancia, false si alcanzó max_iteraciones */
    public function convergio() { return $this->convergencia; }
}
?>