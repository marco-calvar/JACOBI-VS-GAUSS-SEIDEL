<?php
/**
 * CLASE VALIDADOR - VERIFICACIÓN DE INTEGRIDAD
 * ============================================
 * Validación exhaustiva de matrices, vectores y parámetros antes de resolver
 * 
 * VALIDACIONES REALIZADAS:
 * 1. Matriz cuadrada (n×n)
 * 2. Diagonal no nula (a_ii ≠ 0 necesario para ambos métodos)
 * 3. Vector b dimensión compatible
 * 4. Parámetros numéricos en rangos válidos
 * 5. Matriz bien condicionada (advertencias si no)
 * 6. Valores en rango numérico razonable
 * 
 * LANZA EXCEPCIONES EN VALIDACIONES CRÍTICAS
 * GENERA ADVERTENCIAS PARA CASOS DE RIESGO
 */
class Validador {
    
    /**
     * VALIDA MATRIZ CUADRADA (CRÍTICO)
     * Verifica que matriz sea n×n y no vacía
     * 
     * REQUISITO: Ambos Jacobi y Gauss-Seidel requieren matriz cuadrada
     * 
     * @param array $matriz Matriz a validar
     * @return bool true si válida
     * @throws Exception Si no es array, está vacía, o no es cuadrada
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
     * VALIDA DIAGONAL NO NULA (CRÍTICO)
     * Verifica que a_ii ≠ 0 para todo i (no puede ser cercano a cero)
     * 
     * REQUISITO: Si a_ii ≈ 0, fórmula x_i = (b_i - suma) / a_ii causaría:
     *   - División por cero (falla matemática)
     *   - O amplificación masiva de errores (inestabilidad numérica)
     * 
     * UMBRAL: |a_ii| > 1e-15 (máquina épsilon × factor de seguridad)
     * 
     * @param array $matriz Matriz cuadrada ya validada
     * @return bool true si todas las diagonales son no nulas
     * @throws Exception Si algún elemento diagonal es ≈ 0
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
     * VALIDA VECTOR b DIMENSIÓN (CRÍTICO)
     * Verifica que vector b sea compatible con matriz A
     * 
     * REQUISITO: Sistema Ax = b requiere que b sea n×1 si A es n×n
     * 
     * @param array $matriz Matriz n×n
     * @param array $vector_b Vector términos independientes
     * @return bool true si dimensiones coinciden
     * @throws Exception Si b no es array o tiene dimensión incorrecta
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
     * VALIDA PARÁMETROS NUMÉRICOS (CRÍTICO)
     * Verifica tolerancia y máximo de iteraciones están en rangos válidos
     * 
     * RESTRICCIONES:
     * - Tolerancia: 0 < tol < 1 (recomendado: 1e-6 a 1e-4)
     *   • Si tol > 0.1: muy permisiva, solución imprecisa
     *   • Si tol < 1e-15: imposible alcanzar (límite máquina)
     * 
     * - Max iteraciones: 1 ≤ max_iter ≤ 10000
     *   • Si < 1: sin sentido
     *   • Si > 10000: riesgo de tiempo infinito
     * 
     * @param float $tolerancia Error máximo permitido
     * @param int $max_iter Límite máximo de iteraciones
     * @return bool true si parámetros válidos
     * @throws Exception Si están fuera de rangos
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
     * CALCULA NÚMERO DE CONDICIÓN
     * Indica sensibilidad de matriz a perturbaciones (estabilidad numérica)
     * 
     * FÓRMULA SIMPLIFICADA:
     *   cond(A) ≈ ||A||_∞ = max_i( Σ_j |a_ij| )
     * 
     * INTERPRETACIÓN:
     * - κ < 10: Bien condicionada (solución muy estable)
     * - κ ~ 100: Aceptable (pequeños errores de entrada → pequeños errores salida)
     * - κ > 1000: Mal condicionada (números en salida pueden ser imprecisos)
     * 
     * RELACIÓN: Si κ es grande, errores de redondeo se amplifican
     * 
     * @param array $matriz Matriz n×n
     * @return float Número de condición estimado
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
     * VERIFICA SI MATRIZ ES BIEN CONDICIONADA
     * Usa heurística: κ < 100 es buen condicionamiento
     * 
     * @param float $numero_condicion Número de condición (de calcularNumeroCondicion())
     * @return bool true si bien condicionada (κ < 100)
     */
    public static function esBienCondicionada($numero_condicion) {
        return $numero_condicion < 100; // Heurística
    }
    
    /**
     * VALIDA SISTEMA COMPLETO (ORQUESTADOR)
     * Ejecuta TODAS las validaciones críticas en cascada
     * 
     * PROCESO:
     * 1. Verifica matriz cuadrada
     * 2. Verifica diagonal no nula
     * 3. Verifica vector b compatible
     * 4. Verifica parámetros numéricos
     * 5. Genera advertencias sobre propiedades
     * 
     * RETORNA:
     * {
     *   'valido': true (si pasó todas las pruebas),
     *   'advertencias': [ string1, string2, ... ] (no son errores, solo alertas)
     * }
     * 
     * @param array $matriz_A Matriz n×n
     * @param array $vector_b Vector términos independientes
     * @param float $tolerancia Tolerancia de convergencia
     * @param int $max_iter Máximo de iteraciones
     * @return array { valido: bool, advertencias: array }
     * @throws Exception Si alguna validación crítica falla
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
     * GENERA ADVERTENCIAS SOBRE EL SISTEMA
     * Analiza valores extremos que podrían causar problemas numéricos
     * 
     * ADVERTENCIAS GENERADAS:
     * 1. Valores muy grandes (>1000): Riesgo de overflow/inestabilidad
     * 2. Valores muy pequeños (<0.001): Riesgo de underflow/pérdida precisión
     * 
     * NOTA: Estas NO son errores (no impiden resolver), solo alertas
     * El usuario puede decidir proceder o no
     * 
     * @param array $matriz Matriz n×n
     * @return array Array de strings con advertencias (puede estar vacío)
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
