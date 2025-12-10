<?php
/**
 * Clase Validador
 * Realiza validación exhaustiva de matrices y parámetros
 */
class Validador {
    
    /**
     * Valida que la matriz sea cuadrada
     */
    public static function validarMatrizCuadrada($matriz) {
        if (!is_array($matriz) || empty($matriz)) {
            throw new Exception("La matriz debe ser un array no vacío");
        }
        
        $n = count($matriz);
        for ($i = 0; $i < $n; $i++) {
            if (!is_array($matriz[$i]) || count($matriz[$i]) !== $n) {
                throw new Exception("La matriz debe ser cuadrada (n x n)");
            }
        }
        
        return true;
    }
    
    /**
     * Valida que la diagonal no contenga ceros
     */
    public static function validarDiagonalNoNula($matriz) {
        $n = count($matriz);
        for ($i = 0; $i < $n; $i++) {
            if (abs($matriz[$i][$i]) < 1e-15) {
                throw new Exception("El elemento diagonal A[{$i}][{$i}] no puede ser cero o muy cercano a cero");
            }
        }
        return true;
    }
    
    /**
     * Valida que el vector b tenga la dimensión correcta
     */
    public static function validarVectorB($matriz, $vector_b) {
        if (!is_array($vector_b)) {
            throw new Exception("El vector b debe ser un array");
        }
        
        if (count($vector_b) !== count($matriz)) {
            throw new Exception("La dimensión del vector b no coincide con la matriz");
        }
        
        return true;
    }
    
    /**
     * Valida parámetros numéricos
     */
    public static function validarParametros($tolerancia, $max_iter) {
        if ($tolerancia <= 0 || $tolerancia >= 1) {
            throw new Exception("La tolerancia debe estar entre 0 y 1 (recomendado: 0.0001 a 0.1)");
        }
        
        if (!is_int($max_iter) || $max_iter < 1 || $max_iter > 10000) {
            throw new Exception("El máximo de iteraciones debe estar entre 1 y 10000");
        }
        
        return true;
    }
    
    /**
     * Calcula el número de condición de la matriz (sensibilidad)
     */
    public static function calcularNumeroCondicion($matriz) {
        // Simplificación: usar la norma infinito
        $norma_inf = 0;
        $n = count($matriz);
        
        for ($i = 0; $i < $n; $i++) {
            $suma = 0;
            for ($j = 0; $j < $n; $j++) {
                $suma += abs($matriz[$i][$j]);
            }
            $norma_inf = max($norma_inf, $suma);
        }
        
        return $norma_inf;
    }
    
    /**
     * Verifica si la matriz es bien condicionada
     */
    public static function esBienCondicionada($numero_condicion) {
        return $numero_condicion < 100; // Heurística
    }
    
    /**
     * Valida un sistema completo
     */
    public static function validarSistemaCompleto($matriz_A, $vector_b, $tolerancia, $max_iter) {
        self::validarMatrizCuadrada($matriz_A);
        self::validarDiagonalNoNula($matriz_A);
        self::validarVectorB($matriz_A, $vector_b);
        self::validarParametros($tolerancia, $max_iter);
        
        return [
            'valido' => true,
            'advertencias' => self::generarAdvertencias($matriz_A)
        ];
    }
    
    /**
     * Genera advertencias sobre el sistema
     */
    private static function generarAdvertencias($matriz) {
        $advertencias = [];
        $n = count($matriz);
        
        // Verificar valores muy grandes
        $max_valor = 0;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $max_valor = max($max_valor, abs($matriz[$i][$j]));
            }
        }
        
        if ($max_valor > 1000) {
            $advertencias[] = "⚠️ La matriz contiene valores muy grandes. Esto puede causar inestabilidad numérica.";
        }
        
        // Verificar valores muy pequeños
        $min_valor = PHP_FLOAT_MAX;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($matriz[$i][$j] !== 0) {
                    $min_valor = min($min_valor, abs($matriz[$i][$j]));
                }
            }
        }
        
        if ($min_valor < 0.001 && $min_valor > 0) {
            $advertencias[] = "⚠️ La matriz contiene valores muy pequeños. Esto puede afectar la precisión.";
        }
        
        return $advertencias;
    }
}
?>
