<?php
/**
 * Clase GaussSeidel
 * Implementa el método iterativo de Gauss-Seidel para resolver sistemas de ecuaciones lineales
 * Ax = b
 * 
 * DIFERENCIA CON JACOBI:
 * En cada iteración, Gauss-Seidel usa los valores YA ACTUALIZADOS en la misma iteración,
 * mientras que Jacobi usa todos los valores de la iteración anterior.
 * 
 * Esto hace que Gauss-Seidel generalmente converja más rápido (aproximadamente 2x),
 * pero no puede paralelizarse fácilmente.
 */
class GaussSeidel {
    // Entrada
    private $matriz_A;
    private $vector_b;
    private $tolerancia;
    private $max_iteraciones;
    private $x_inicial;
    
    // Resultados
    private $solucion;
    private $iteraciones;
    private $tiempo_ejecucion;
    private $memoria_usada;
    private $errores;
    private $convergencia;
    
    /**
     * Constructor
     * 
     * @param array $matriz_A Matriz de coeficientes (n x n)
     * @param array $vector_b Vector de términos independientes (n x 1)
     * @param float $tolerancia Error máximo aceptable
     * @param int $max_iter Número máximo de iteraciones
     * @param array $x_inicial Vector inicial (opcional, por defecto ceros)
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
     * Verifica si la matriz es diagonalmente dominante
     * 
     * Una matriz A es diagonalmente dominante si:
     * |a_ii| > Σ|a_ij| para todo i (donde j ≠ i)
     * 
     * Si es diagonalmente dominante, la convergencia está garantizada.
     * 
     * @return bool True si es diagonalmente dominante
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
     * Ejecuta el método de Gauss-Seidel
     * 
     * Fórmula: x_i^(k+1) = (1/a_ii) * [b_i - Σ(j<i) a_ij*x_j^(k+1) - Σ(j>i) a_ij*x_j^(k)]
     * 
     * La diferencia clave con Jacobi es que usa valores ya actualizados (x_j^(k+1))
     * para j < i en la misma iteración.
     */
    public function resolver() {
        // Medir tiempo y memoria
        $inicio = microtime(true);
        $memoria_inicio = memory_get_usage();
        
        $n = count($this->vector_b);
        $x_viejo = $this->x_inicial;
        $x_nuevo = $x_viejo; // Trabajamos sobre el mismo vector
        $this->convergencia = false;
        
        // Iterar hasta convergencia o máximo de iteraciones
        for ($k = 0; $k < $this->max_iteraciones; $k++) {
            $x_temp = $x_viejo; // Guardar para calcular el error
            
            // MÉTODO DE GAUSS-SEIDEL
            // Para cada ecuación i
            for ($i = 0; $i < $n; $i++) {
                $suma = 0.0;
                
                // Sumar todos los términos a_ij * x_j (excepto cuando j = i)
                for ($j = 0; $j < $n; $j++) {
                    if ($i != $j) {
                        // CLAVE: Usa x_nuevo[j], que puede contener valores ya actualizados
                        // en esta misma iteración (cuando j < i)
                        $suma += $this->matriz_A[$i][$j] * $x_nuevo[$j];
                    }
                }
                
                // Calcular nuevo valor de x_i
                // x_i = (b_i - suma) / a_ii
                $x_nuevo[$i] = ($this->vector_b[$i] - $suma) / $this->matriz_A[$i][$i];
            }
            
            // Calcular error relativo
            $error = $this->calcularError($x_nuevo, $x_temp);
            $this->errores[] = $error;
            
            // Verificar convergencia
            if ($error < $this->tolerancia) {
                $this->convergencia = true;
                $this->iteraciones = $k + 1;
                $this->solucion = $x_nuevo;
                break;
            }
            
            // Preparar para siguiente iteración
            $x_viejo = $x_nuevo;
        }
        
        // Si no convergió en max_iteraciones
        if (!$this->convergencia) {
            $this->iteraciones = $this->max_iteraciones;
            $this->solucion = $x_nuevo;
        }
        
        // Calcular métricas finales
        $this->tiempo_ejecucion = (microtime(true) - $inicio) * 1000; // en milisegundos
        $this->memoria_usada = (memory_get_usage() - $memoria_inicio) / 1024; // en KB
    }
    
    /**
     * Calcula el error relativo entre dos vectores
     * 
     * Error = ||x_nuevo - x_viejo|| / ||x_nuevo||
     * 
     * Donde ||·|| es la norma euclidiana (norma L2)
     * 
     * @param array $x_nuevo Vector en iteración k+1
     * @param array $x_viejo Vector en iteración k
     * @return float Error relativo
     */
    private function calcularError($x_nuevo, $x_viejo) {
        $n = count($x_nuevo);
        $suma_numerador = 0.0;
        $suma_denominador = 0.0;
        
        // Calcular ||x_nuevo - x_viejo||²
        for ($i = 0; $i < $n; $i++) {
            $diferencia = $x_nuevo[$i] - $x_viejo[$i];
            $suma_numerador += $diferencia * $diferencia;
        }
        
        // Calcular ||x_nuevo||²
        for ($i = 0; $i < $n; $i++) {
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
     * Calcula el error absoluto (norma del residuo)
     * 
     * Residuo = ||Ax - b||
     * 
     * Útil para verificar qué tan cerca está la solución del sistema original.
     * 
     * @return float Error absoluto
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
            
            // (Ax)_i - b_i
            $diferencia = $suma - $this->vector_b[$i];
            $residuo += $diferencia * $diferencia;
        }
        
        return sqrt($residuo);
    }
    
    /**
     * Verifica la solución multiplicando Ax y comparando con b
     * 
     * @return array Vector Ax
     */
    public function verificarSolucion() {
        if ($this->solucion === null) {
            return null;
        }
        
        $n = count($this->solucion);
        $Ax = [];
        
        for ($i = 0; $i < $n; $i++) {
            $Ax[$i] = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $Ax[$i] += $this->matriz_A[$i][$j] * $this->solucion[$j];
            }
        }
        
        return $Ax;
    }
    
    // ========================================
    // GETTERS
    // ========================================
    
    /**
     * Obtiene el vector solución
     * @return array Vector solución x
     */
    public function getSolucion() {
        return $this->solucion;
    }
    
    /**
     * Obtiene el número de iteraciones realizadas
     * @return int Número de iteraciones
     */
    public function getIteraciones() {
        return $this->iteraciones;
    }
    
    /**
     * Obtiene el tiempo de ejecución en milisegundos
     * @return float Tiempo en ms
     */
    public function getTiempoEjecucion() {
        return $this->tiempo_ejecucion;
    }
    
    /**
     * Obtiene la memoria utilizada en kilobytes
     * @return float Memoria en KB
     */
    public function getMemoriaUsada() {
        return $this->memoria_usada;
    }
    
    /**
     * Obtiene el historial de errores por iteración
     * @return array Array de errores
     */
    public function getErrores() {
        return $this->errores;
    }
    
    /**
     * Verifica si el método convergió
     * @return bool True si convergió
     */
    public function convergio() {
        return $this->convergencia;
    }
    
    /**
     * Obtiene información completa del método
     * @return array Array asociativo con toda la información
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