<?php
/**
 * Clase Comparador
 * Realiza el análisis comparativo entre los métodos de Jacobi y Gauss-Seidel
 */
class Comparador {
    private $jacobi;
    private $gauss_seidel;
    private $matriz_A;
    
    public function __construct($jacobi, $gauss_seidel, $matriz_A) {
        $this->jacobi = $jacobi;
        $this->gauss_seidel = $gauss_seidel;
        $this->matriz_A = $matriz_A;
    }
    
    /**
     * Genera análisis comparativo completo
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
     * Calcula la eficiencia de cada método
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
     * Genera recomendaciones basadas en el análisis
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
     * Obtiene el tipo de matriz
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