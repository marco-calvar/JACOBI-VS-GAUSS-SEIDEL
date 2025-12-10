<?php
/**
 * CLASE COMPARADOR - ANÁLISIS COMPARATIVO
 * ======================================
 * Realiza análisis exhaustivo entre métodos de Jacobi y Gauss-Seidel
 * 
 * PARÁMETROS COMPARADOS:
 * 1. CONVERGENCIA: ¿Ambos convergen? ¿Uno falla?
 * 2. ITERACIONES: Cantidad de iteraciones requeridas
 * 3. VELOCIDAD: Tiempo de ejecución en milisegundos
 * 4. MEMORIA: Consumo de memoria en kilobytes
 * 5. ERROR: Error relativo final
 * 6. EFICIENCIA: Score combinado de todos los factores
 * 7. RECOMENDACIONES: Cuál usar y por qué
 * 
 * SALIDA:
 * - Array asociativo con análisis detallado
 * - Matriz de puntos para ranking
 * - Recomendaciones cualitativas basadas en propiedades de matriz
 */
class Comparador {
    private $jacobi;           // Objeto Jacobi con resultados
    private $gauss_seidel;     // Objeto GaussSeidel con resultados
    private $matriz_A;         // Matriz original para análisis
    
    /**
     * CONSTRUCTOR
     * Inicializa el comparador con dos métodos ya ejecutados
     * 
     * @param Jacobi $jacobi           Instancia de Jacobi con resolver() ejecutado
     * @param GaussSeidel $gauss_seidel Instancia de GaussSeidel con resolver() ejecutado
     * @param array $matriz_A           Matriz original A para análisis de propiedades
     */
    public function __construct($jacobi, $gauss_seidel, $matriz_A) {
        $this->jacobi = $jacobi;
        $this->gauss_seidel = $gauss_seidel;
        $this->matriz_A = $matriz_A;
    }
    
    /**
     * GENERA ANÁLISIS COMPARATIVO COMPLETO
     * Extrae resultados de ambos métodos y crea análisis exhaustivo
     * 
     * ESTRUCTURA DEL ANÁLISIS RETORNADO:
     * {
     *   'convergencia': { jacobi: bool, gauss_seidel: bool },
     *   'iteraciones': { jacobi: int, gauss_seidel: int, diferencia: int },
     *   'tiempo': { jacobi: float ms, gauss_seidel: float ms, mas_rapido: string },
     *   'memoria': { jacobi: float KB, gauss_seidel: float KB, diferencia: float },
     *   'error_final': { jacobi: float, gauss_seidel: float },
     *   'eficiencia': { jacobi: score, gauss_seidel: score, mas_eficiente: string },
     *   'recomendacion': [ strings con análisis cualitativo ]
     * }
     * 
     * @return array Análisis comparativo completo
     */
    public function generarAnalisis() {
        $analisis = [];
        
        // Convergencia
        $analisis['convergencia'] = [
            'jacobi' => $this->jacobi->convergio(),
            'gauss_seidel' => $this->gauss_seidel->convergio()
        ];
        
        // Iteraciones
        $analisis['iteraciones'] = [
            'jacobi' => $this->jacobi->getIteraciones(),
            'gauss_seidel' => $this->gauss_seidel->getIteraciones(),
            'diferencia' => abs($this->jacobi->getIteraciones() - $this->gauss_seidel->getIteraciones())
        ];
        
        // Tiempo de ejecución
        $analisis['tiempo'] = [
            'jacobi' => $this->jacobi->getTiempoEjecucion(),
            'gauss_seidel' => $this->gauss_seidel->getTiempoEjecucion(),
            'mas_rapido' => $this->jacobi->getTiempoEjecucion() < $this->gauss_seidel->getTiempoEjecucion() ? 'Jacobi' : 'Gauss-Seidel'
        ];
        
        // Memoria
        $analisis['memoria'] = [
            'jacobi' => $this->jacobi->getMemoriaUsada(),
            'gauss_seidel' => $this->gauss_seidel->getMemoriaUsada(),
            'diferencia' => abs($this->jacobi->getMemoriaUsada() - $this->gauss_seidel->getMemoriaUsada())
        ];
        
        // Error final
        $errores_jacobi = $this->jacobi->getErrores();
        $errores_gs = $this->gauss_seidel->getErrores();
        
        $analisis['error_final'] = [
            'jacobi' => !empty($errores_jacobi) ? end($errores_jacobi) : 0,
            'gauss_seidel' => !empty($errores_gs) ? end($errores_gs) : 0
        ];
        
        // Eficiencia
        $analisis['eficiencia'] = $this->calcularEficiencia();
        
        // Recomendación
        $analisis['recomendacion'] = $this->generarRecomendacion();
        
        return $analisis;
    }
    
    /**
     * CALCULA EFICIENCIA RELATIVA
     * Otorga puntos basados en desempeño en 3 criterios
     * 
     * CRITERIOS (1 punto cada uno):
     * 1. ITERACIONES: Menor cantidad gana punto
     * 2. TIEMPO: Menor ejecución gana punto
     * 3. CONVERGENCIA: Converger gana punto
     * 
     * SCORE TOTAL:
     * - 3 puntos: Mejor en todo
     * - 2 puntos: Mejor en 2/3 criterios
     * - 1 punto: Mejor en 1/3 criterios
     * - 0 puntos: Peor en todo
     * 
     * @return array { jacobi: int 0-3, gauss_seidel: int 0-3, mas_eficiente: string }
     */
    private function calcularEficiencia() {
        $iter_j = $this->jacobi->getIteraciones();
        $iter_gs = $this->gauss_seidel->getIteraciones();
        $tiempo_j = $this->jacobi->getTiempoEjecucion();
        $tiempo_gs = $this->gauss_seidel->getTiempoEjecucion();
        
        // Menor número de iteraciones y menor tiempo es más eficiente
        $score_jacobi = 0;
        $score_gs = 0;
        
        if ($iter_j < $iter_gs) $score_jacobi++;
        else if ($iter_gs < $iter_j) $score_gs++;
        
        if ($tiempo_j < $tiempo_gs) $score_jacobi++;
        else if ($tiempo_gs < $tiempo_j) $score_gs++;
        
        if ($this->jacobi->convergio()) $score_jacobi++;
        if ($this->gauss_seidel->convergio()) $score_gs++;
        
        return [
            'jacobi' => $score_jacobi,
            'gauss_seidel' => $score_gs,
            'mas_eficiente' => $score_jacobi > $score_gs ? 'Jacobi' : ($score_gs > $score_jacobi ? 'Gauss-Seidel' : 'Similar')
        ];
    }
    
    /**
     * GENERA RECOMENDACIONES CUALITATIVAS
     * Analiza propiedades de matriz e interpreta resultados
     * 
     * ANÁLISIS REALIZA:
     * 1. Verificación de dominancia diagonal (garantía de convergencia)
     * 2. Comparación de velocidad de convergencia
     * 3. Evaluación de cuál usar según propiedades
     * 4. Consideraciones de paralelización
     * 
     * RECOMENDACIONES TÍPICAS:
     * - "Gauss-Seidel convergió X% más rápido"
     * - "La matriz es diagonalmente dominante"
     * - "Para matrices grandes, Jacobi es paralelizable"
     * 
     * @return array Array de recomendaciones en forma de strings legibles
     */
    private function generarRecomendacion() {
        $recomendaciones = [];
        
        // Verificar diagonal dominante
        $es_diag_dom = $this->jacobi->esDiagonalmenteDominante();
        
        if (!$es_diag_dom) {
            $recomendaciones[] = "⚠️ La matriz NO es diagonalmente dominante. La convergencia no está garantizada.";
        } else {
            $recomendaciones[] = "✓ La matriz es diagonalmente dominante. Ambos métodos deberían converger.";
        }
        
        // Comparar convergencia
        if ($this->jacobi->convergio() && $this->gauss_seidel->convergio()) {
            $iter_j = $this->jacobi->getIteraciones();
            $iter_gs = $this->gauss_seidel->getIteraciones();
            
            if ($iter_gs < $iter_j) {
                $mejora = round((($iter_j - $iter_gs) / $iter_j) * 100, 2);
                $recomendaciones[] = "✓ Gauss-Seidel convergió {$mejora}% más rápido que Jacobi.";
                $recomendaciones[] = "Recomendación: Usar Gauss-Seidel para este tipo de matriz.";
            } else if ($iter_j < $iter_gs) {
                $mejora = round((($iter_gs - $iter_j) / $iter_gs) * 100, 2);
                $recomendaciones[] = "✓ Jacobi convergió {$mejora}% más rápido que Gauss-Seidel (caso raro).";
                $recomendaciones[] = "Recomendación: Usar Jacobi para este tipo de matriz.";
            } else {
                $recomendaciones[] = "✓ Ambos métodos convergieron en el mismo número de iteraciones.";
            }
        } else if (!$this->jacobi->convergio() && !$this->gauss_seidel->convergio()) {
            $recomendaciones[] = "❌ Ningún método convergió. Considere aumentar el máximo de iteraciones o verificar la matriz.";
        } else if ($this->gauss_seidel->convergio() && !$this->jacobi->convergio()) {
            $recomendaciones[] = "✓ Solo Gauss-Seidel convergió.";
            $recomendaciones[] = "Recomendación: Usar Gauss-Seidel para este sistema.";
        } else {
            $recomendaciones[] = "✓ Solo Jacobi convergió (caso inusual).";
            $recomendaciones[] = "Recomendación: Usar Jacobi para este sistema.";
        }
        
        // Análisis de paralelización
        $n = count($this->matriz_A);
        if ($n > 10) {
            $recomendaciones[] = "Para matrices grandes (n>10), Jacobi puede paralelizarse más fácilmente.";
        }
        
        return $recomendaciones;
    }
    
    /**
     * OBTIENE TIPO Y PROPIEDADES DE MATRIZ
     * Realiza análisis estructural de la matriz para categorización
     * 
     * VERIFICA:
     * 1. Dominancia diagonal (criterio de convergencia)
     * 2. Simetría (propiedad algebraica importante)
     * 3. Tamaño: Pequeña (n≤5), Mediana (5<n≤20), Grande (n>20)
     * 
     * RETORNA:
     * String con propiedades separadas por comas
     * Ejemplo: "Diagonalmente Dominante, Simétrica, Mediana (5<n≤20)"
     * 
     * INTERPRETACIÓN:
     * - Diag. Dominante → Convergencia garantizada
     * - Simétrica → Puede usar métodos especializados (Cholesky)
     * - Tamaño → Influye en paralelización y overhead
     * 
     * @return string Descripción textual de propiedades de matriz
     */
    public function getTipoMatriz() {
        $es_diag_dom = $this->jacobi->esDiagonalmenteDominante();
        $n = count($this->matriz_A);
        
        $tipos = [];
        
        if ($es_diag_dom) {
            $tipos[] = "Diagonalmente Dominante";
        } else {
            $tipos[] = "No Diagonalmente Dominante";
        }
        
        // Verificar si es simétrica
        $es_simetrica = true;
        for ($i = 0; $i < $n && $es_simetrica; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                if (abs($this->matriz_A[$i][$j] - $this->matriz_A[$j][$i]) > 0.0001) {
                    $es_simetrica = false;
                    break;
                }
            }
        }
        
        if ($es_simetrica) {
            $tipos[] = "Simétrica";
        }
        
        // Tamaño
        if ($n <= 5) {
            $tipos[] = "Pequeña (n≤5)";
        } else if ($n <= 20) {
            $tipos[] = "Mediana (5<n≤20)";
        } else {
            $tipos[] = "Grande (n>20)";
        }
        
        return implode(", ", $tipos);
    }
}
?>