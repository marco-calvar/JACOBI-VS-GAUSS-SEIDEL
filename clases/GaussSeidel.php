<?php
/**
 * CLASE GAUSS-SEIDEL - VARIANTE MEJORADA DE JACOBI
 * =================================================
 * Resuelve sistemas de ecuaciones lineales Ax = b usando el método de Gauss-Seidel.
 * 
 * TEORÍA:
 * - Descompone A = D - L - U (D diagonal, L triangular inferior, U triangular superior)
 * - Fórmula: (D-L)x^(k) = Ux^(k-1) + b
 *   → x^(k) = (D-L)^(-1)(Ux^(k-1) + b)
 * - Componente: x_i^(k) = (b_i - Σ(j<i) a_ij*x_j^(k) - Σ(j>i) a_ij*x_j^(k-1)) / a_ii
 * - PUNTO CLAVE: Usa valores NUEVOS para j<i, valores VIEJOS para j>i
 * 
 * DIFERENCIA CRÍTICA CON JACOBI:
 * ┌─────────────────────────────────────────────────────────┐
 * │ JACOBI:        usa x_j^(k-1) para TODOS los j≠i        │
 * │ GAUSS-SEIDEL:  usa x_j^(k) para j<i, x_j^(k-1) para j>i│
 * └─────────────────────────────────────────────────────────┘
 * 
 * CARACTERÍSTICAS:
 * - Converge ~2x más rápido que Jacobi (mismo sistema)
 * - Converge si matriz es diagonal dominante
 * - NO PARALELIZABLE (dependencias de datos)
 * - Mejor para sistemas pequelos/medianos
 * - Memoria: O(n²) (almacena solo x actual)
 * 
 * COMPLEJIDAD:
 * - Tiempo por iteración: O(n²)
 * - Iteraciones: típicamente 50-70% de Jacobi
 * - Total: ~0.5x el tiempo de Jacobi
 */
class GaussSeidel {
    // Entrada
    private $matriz_A;           // Matriz de coeficientes n×n
    private $vector_b;           // Vector términos independientes
    private $tolerancia;         // Criterio parada (típico 1e-6)
    private $max_iteraciones;    // Límite máximo de iteraciones
    private $x_inicial;          // Aproximación inicial [0, 0, ..., 0]
    
    // Resultados
    private $solucion;           // Vector solución x
    private $iteraciones;        // Cantidad iteraciones realizadas
    private $tiempo_ejecucion;   // Tiempo en milisegundos
    private $memoria_usada;      // Memoria en kilobytes
    private $errores;            // Error en cada iteración (para gráficas)
    private $convergencia;       // ¿Convergio o no?
    
    /**
     * CONSTRUCTOR
     * Inicializa el método de Gauss-Seidel con parámetros de control
     * 
     * @param array $matriz_A         Matriz de coeficientes n×n
     * @param array $vector_b         Vector de términos independientes
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
     * Condición suficiente para garantizar convergencia (igual que Jacobi)
     * 
     * CRITERIO: Para cada fila i:
     *    |a_ii| > Σ(j≠i) |a_ij|
     * 
     * @return bool true si TODAS las filas satisfacen dominancia diagonal
     * 
     * NOTA: Gauss-Seidel puede converger incluso sin dominancia diagonal
     * en algunos casos donde Jacobi no converge
     */
    public function esDiagonalmenteDominante() {
        $n = count($this->matriz_A);
        
        for ($i = 0; $i < $n; $i++) {
            $suma = 0;
            for ($j = 0; $j < $n; $j++) {
                if ($i != $j) {
                    $suma += abs($this->matriz_A[$i][$j]);
                }
            }
            
            // Si el elemento diagonal no es estrictamente mayor, no es DD
            if (abs($this->matriz_A[$i][$i]) <= $suma) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * EJECUTA ALGORITMO DE GAUSS-SEIDEL
     * Itera hasta convergencia o límite máximo
     * 
     * FÓRMULA POR ITERACIÓN:
     *   x_i^(k+1) = (b_i - Σ(j<i) a_ij*x_j^(k+1) - Σ(j>i) a_ij*x_j^(k)) / a_ii
     * 
     * DIFERENCIA CLAVE CON JACOBI:
     * ─────────────────────────────
     * Para cada i, usamos:
     *   - x_j^(k+1) cuando j < i  ← Valores NUEVOS (ya calculados)
     *   - x_j^(k) cuando j > i    ← Valores VIEJOS (de iteración anterior)
     * 
     * Esto causa que:
     * ✓ Converja ~2x más rápido
     * ✗ Sea secuencial, no paralelizable
     * 
     * PASOS:
     * 1. Inicializa x = x_inicial (típicamente ceros)
     * 2. Para cada iteración k = 0, 1, 2, ...
     *    a) Para cada ecuación i:
     *       - Suma los términos con j<i usando x_nuevo (valores actualizados)
     *       - Suma los términos con j>i usando x_nuevo (que contiene x_viejo de j>i)
     *       - Calcula x_nuevo[i] = (b_i - suma) / a_ii
     *    b) Calcula error relativo
     *    c) Si error < tolerancia: CONVERGIO
     * 
     * @return void (resultados en propiedades internas)
     */
    public function resolver() {
        // Medir tiempo y memoria
        $inicio = microtime(true);
        $memoria_inicio = memory_get_usage();
        
        $n = count($this->vector_b);
        $x_viejo = $this->x_inicial;  // x^(k)
        $x_nuevo = $x_viejo;  // Trabajamos sobre el mismo vector (reutilizando memoria)
        $this->convergencia = false;
        
        // LOOP PRINCIPAL DE ITERACIONES
        for ($k = 0; $k < $this->max_iteraciones; $k++) {
            $x_temp = $x_viejo;  // Guardar para calcular el error
            
            // ===== CÁLCULO DE GAUSS-SEIDEL =====
            // Para cada ecuación i
            for ($i = 0; $i < $n; $i++) {
                $suma = 0.0;  // Σ a_ij * x_j (excepto j=i)
                
                // Sumar todos los términos a_ij * x_j (excepto cuando j = i)
                for ($j = 0; $j < $n; $j++) {
                    if ($i != $j) {
                        // CLAVE: Usa x_nuevo[j], que puede contener:
                        // - Si j < i: valor x_j^(k+1) ya actualizado (NUEVO)
                        // - Si j > i: valor x_j^(k) de iteración anterior (VIEJO)
                        $suma += $this->matriz_A[$i][$j] * $x_nuevo[$j];
                    }
                }
                
                // Calcular nuevo valor de x_i
                // x_i^(k+1) = (b_i - suma) / a_ii
                $x_nuevo[$i] = ($this->vector_b[$i] - $suma) / $this->matriz_A[$i][$i];
            }
            
            // ===== VERIFICACIÓN DE CONVERGENCIA =====
            $error = $this->calcularError($x_nuevo, $x_temp);
            $this->errores[] = $error;  // Guardar para gráficas
            
            // Si error suficientemente pequeño
            if ($error < $this->tolerancia) {
                $this->convergencia = true;
                $this->iteraciones = $k + 1;
                $this->solucion = $x_nuevo;
                break;  // SALIR - convergio
            }
            
            // Preparar para siguiente iteración
            $x_viejo = $x_nuevo;
        }
        
        // Si no convergió en max_iteraciones
        if (!$this->convergencia) {
            $this->iteraciones = $this->max_iteraciones;
            $this->solucion = $x_nuevo;  // Usar última aproximación
        }
        
        // Calcular métricas finales
        $this->tiempo_ejecucion = (microtime(true) - $inicio) * 1000;  // convertir a ms
        $this->memoria_usada = (memory_get_usage() - $memoria_inicio) / 1024;  // convertir a KB
    }
    
    /**
     * CALCULA ERROR RELATIVO EUCLIDIANO
     * Métrica para determinar convergencia
     * 
     * FÓRMULA:
     *   error = ||x_nuevo - x_viejo||₂ / ||x_nuevo||₂
     *   = sqrt(Σ(x_new[i] - x_old[i])²) / sqrt(Σ(x_new[i]²))
     * 
     * INTERPRETACIÓN:
     * - error ≈ 0  → solución convergio
     * - error > tolerancia → debe seguir iterando
     * 
     * @param array $x_nuevo Vector en iteración k+1
     * @param array $x_viejo Vector en iteración k
     * @return float Error relativo
     */
    private function calcularError($x_nuevo, $x_viejo) {
        $n = count($x_nuevo);
        $suma_numerador = 0.0;    // ||x_nuevo - x_viejo||²
        $suma_denominador = 0.0;  // ||x_nuevo||²
        
        // Calcular ambas normas simultaneamente
        for ($i = 0; $i < $n; $i++) {
            $diferencia = $x_nuevo[$i] - $x_viejo[$i];
            $suma_numerador += $diferencia * $diferencia;
            $suma_denominador += $x_nuevo[$i] * $x_nuevo[$i];
        }
        
        // Evitar división por cero
        if ($suma_denominador == 0) {
            return 0;
        }
        
        // Retornar error relativo
        return sqrt($suma_numerador / $suma_denominador);
    }
    
    /**
     * CALCULA ERROR ABSOLUTO (RESIDUO)
     * Verifica qué tan cerca está la solución del sistema original
     * 
     * FÓRMULA:
     *   residuo = ||Ax - b||₂ = sqrt(Σ(A[i]*x - b[i])²)
     * 
     * INTERPRETACIÓN:
     * - residuo ≈ 0  → solución satisface bien Ax ≈ b
     * - residuo > 0.01 → solución tiene error significativo
     * 
     * NOTA: Complementa el error relativo. Verifica solución real vs ideal.
     * 
     * @return float Error absoluto (norma del residuo)
     */
    public function calcularErrorAbsoluto() {
        if ($this->solucion === null) {
            return null;
        }
        
        $n = count($this->solucion);
        $residuo = 0.0;
        
        // Calcular Ax
        for ($i = 0; $i < $n; $i++) {
            $suma = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $suma += $this->matriz_A[$i][$j] * $this->solucion[$j];
            }
            
            // (Ax - b)_i al cuadrado
            $diferencia = $suma - $this->vector_b[$i];
            $residuo += $diferencia * $diferencia;
        }
        
        return sqrt($residuo);
    }
    
    /**
     * VERIFICA SOLUCIÓN MULTIPLICANDO Ax
     * Compara resultado Ax contra vector b original
     * 
     * PROCEDIMIENTO:
     * - Para cada fila i, calcula Ax[i] = Σ a_ij * x_j
     * - Retorna vector Ax (debe ser ≈ vector b si solución es correcta)
     * 
     * ÚTIL PARA:
     * - Visualizar residuos fila por fila
     * - Verificar calidad de solución
     * 
     * @return array Vector Ax (o null si no hay solución)
     */
    public function verificarSolucion() {
        if ($this->solucion === null) {
            return null;
        }
        
        $n = count($this->solucion);
        $Ax = [];
        
        // Calcular A * x
        for ($i = 0; $i < $n; $i++) {
            $Ax[$i] = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $Ax[$i] += $this->matriz_A[$i][$j] * $this->solucion[$j];
            }
        }
        
        return $Ax;
    }
    
    // =======================================
    // MÉTODOS DE ACCESO (GETTERS)
    // =======================================
    // Retornan información de la última ejecución
    
    /** @return array Vector solución x que satisface Ax ≈ b */
    public function getSolucion() {
        return $this->solucion;
    }
    
    /** @return int Número total de iteraciones ejecutadas */
    public function getIteraciones() {
        return $this->iteraciones;
    }
    
    /** @return float Tiempo de ejecución en milisegundos */
    public function getTiempoEjecucion() {
        return $this->tiempo_ejecucion;
    }
    
    /** @return float Memoria consumida en kilobytes */
    public function getMemoriaUsada() {
        return $this->memoria_usada;
    }
    
    /** @return array Historial de errores por iteración (para gráficas) */
    public function getErrores() {
        return $this->errores;
    }
    
    /** @return bool true si convergio dentro tolerancia, false si alcanzó max_iteraciones */
    public function convergio() {
        return $this->convergencia;
    }
    
    /**
     * OBTIENE INFORMACIÓN COMPLETA
     * Retorna todos los resultados en un array asociativo
     * 
     * @return array Array con claves:
     *   - 'metodo': nombre del algoritmo
     *   - 'convergencia': bool si convergio
     *   - 'iteraciones': número de iteraciones
     *   - 'tiempo_ms': tiempo en milisegundos
     *   - 'memoria_kb': memoria en kilobytes
     *   - 'error_final': último error calculado
     *   - 'solucion': vector solución
     *   - 'historial_errores': array de errores por iteración
     */
    public function getInfo() {
        return [
            'metodo' => 'Gauss-Seidel',
            'convergencia' => $this->convergencia,
            'iteraciones' => $this->iteraciones,
            'tiempo_ms' => $this->tiempo_ejecucion,
            'memoria_kb' => $this->memoria_usada,
            'error_final' => !empty($this->errores) ? end($this->errores) : null,
            'solucion' => $this->solucion,
            'historial_errores' => $this->errores
        ];
    }
}
?>