<?php
/**
 * Clase Jacobi
 * Implementa el método iterativo de Jacobi para resolver sistemas de ecuaciones lineales
 * Ax = b
 */
class Jacobi {
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
            
            if (abs($this->matriz_A[$i][$i]) <= $suma) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Ejecuta el método de Jacobi
     */
    public function resolver() {
        $inicio = microtime(true);
        $memoria_inicio = memory_get_usage();
        
        $n = count($this->vector_b);
        $x_viejo = $this->x_inicial;
        $x_nuevo = array_fill(0, $n, 0.0);
        $this->convergencia = false;
        
        for ($k = 0; $k < $this->max_iteraciones; $k++) {
            // Método de Jacobi: x_i^(k+1) = (1/a_ii) * (b_i - sum(a_ij * x_j^(k)))
            for ($i = 0; $i < $n; $i++) {
                $suma = 0.0;
                for ($j = 0; $j < $n; $j++) {
                    if ($i != $j) {
                        $suma += $this->matriz_A[$i][$j] * $x_viejo[$j];
                    }
                }
                $x_nuevo[$i] = ($this->vector_b[$i] - $suma) / $this->matriz_A[$i][$i];
            }
            
            // Calcular error
            $error = $this->calcularError($x_nuevo, $x_viejo);
            $this->errores[] = $error;
            
            // Verificar convergencia
            if ($error < $this->tolerancia) {
                $this->convergencia = true;
                $this->iteraciones = $k + 1;
                $this->solucion = $x_nuevo;
                break;
            }
            
            $x_viejo = $x_nuevo;
        }
        
        // Si no convergió
        if (!$this->convergencia) {
            $this->iteraciones = $this->max_iteraciones;
            $this->solucion = $x_nuevo;
        }
        
        $this->tiempo_ejecucion = (microtime(true) - $inicio) * 1000; // en milisegundos
        $this->memoria_usada = (memory_get_usage() - $memoria_inicio) / 1024; // en KB
    }
    
    /**
     * Calcula el error relativo entre dos vectores
     */
    private function calcularError($x_nuevo, $x_viejo) {
        $n = count($x_nuevo);
        $suma_num = 0.0;
        $suma_den = 0.0;
        
        for ($i = 0; $i < $n; $i++) {
            $suma_num += pow($x_nuevo[$i] - $x_viejo[$i], 2);
            $suma_den += pow($x_nuevo[$i], 2);
        }
        
        if ($suma_den == 0) return 0;
        return sqrt($suma_num / $suma_den);
    }
    
    // Getters
    public function getSolucion() { return $this->solucion; }
    public function getIteraciones() { return $this->iteraciones; }
    public function getTiempoEjecucion() { return $this->tiempo_ejecucion; }
    public function getMemoriaUsada() { return $this->memoria_usada; }
    public function getErrores() { return $this->errores; }
    public function convergio() { return $this->convergencia; }
}
?>