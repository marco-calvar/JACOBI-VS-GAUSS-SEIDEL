<?php
/**
 * Clase AnalizadorAvanzado
 * Realiza análisis profundos de convergencia y propiedades matriciales
 */
class AnalizadorAvanzado {
    private $jacobi;
    private $gauss_seidel;
    private $matriz_A;
    private $vector_b;
    
    public function __construct($jacobi, $gauss_seidel, $matriz_A, $vector_b = null) {
        $this->jacobi = $jacobi;
        $this->gauss_seidel = $gauss_seidel;
        $this->matriz_A = $matriz_A;
        $this->vector_b = $vector_b;
    }
    
    /**
     * Realiza análisis completo de convergencia
     */
    public function analizarConvergencia() {
        $n = count($this->matriz_A);
        
        return [
            'velocidad_convergencia' => $this->calcularVelocidad(),
            'tasa_convergencia_lineal' => $this->estimarTasaConvergencia(),
            'estabilidad' => $this->analizarEstabilidad(),
            'radio_espectral' => $this->estimarRadioEspectral(),
            'prediccion_convergencia' => $this->predecirConvergencia()
        ];
    }
    
    /**
     * Calcula la velocidad relativa de convergencia
     */
    private function calcularVelocidad() {
        $iter_j = $this->jacobi->getIteraciones();
        $iter_gs = $this->gauss_seidel->getIteraciones();
        
        if ($iter_j == 0 || $iter_gs == 0) {
            return ['relacion' => 'N/A', 'mas_rapido' => 'N/A'];
        }
        
        $relacion = $iter_gs > 0 ? round($iter_j / $iter_gs, 2) : 0;
        $mas_rapido = $iter_gs < $iter_j ? 'Gauss-Seidel' : 'Jacobi';
        
        return [
            'relacion' => $relacion . 'x',
            'mas_rapido' => $mas_rapido,
            'mejora_porcentual' => $this->calcularMejora()
        ];
    }
    
    /**
     * Calcula la mejora porcentual
     */
    private function calcularMejora() {
        $iter_j = $this->jacobi->getIteraciones();
        $iter_gs = $this->gauss_seidel->getIteraciones();
        
        if ($iter_j == 0) return 0;
        
        return round(abs($iter_j - $iter_gs) / $iter_j * 100, 2) . '%';
    }
    
    /**
     * Estima la tasa de convergencia lineal
     */
    private function estimarTasaConvergencia() {
        $errores_j = $this->jacobi->getErrores();
        $errores_gs = $this->gauss_seidel->getErrores();
        
        $tasa_j = $this->calcularTasaLineal($errores_j);
        $tasa_gs = $this->calcularTasaLineal($errores_gs);
        
        return [
            'jacobi' => round($tasa_j, 4),
            'gauss_seidel' => round($tasa_gs, 4),
            'interpretacion' => $this->interpretarTasa($tasa_j, $tasa_gs)
        ];
    }
    
    /**
     * Calcula la tasa lineal de convergencia r = e_{k+1}/e_k
     */
    private function calcularTasaLineal($errores) {
        if (count($errores) < 2) return 0;
        
        $tasas = [];
        for ($i = 1; $i < count($errores); $i++) {
            if ($errores[$i-1] > 1e-10) {
                $tasas[] = $errores[$i] / $errores[$i-1];
            }
        }
        
        if (empty($tasas)) return 0;
        
        // Promediar los últimos valores
        $ultimas = array_slice($tasas, -5);
        return array_sum($ultimas) / count($ultimas);
    }
    
    /**
     * Interpreta la tasa de convergencia
     */
    private function interpretarTasa($tasa_j, $tasa_gs) {
        $mejor = min($tasa_j, $tasa_gs) === $tasa_j ? 'Jacobi' : 'Gauss-Seidel';
        
        if ($tasa_j < 0.5 && $tasa_gs < 0.5) {
            return "Ambos métodos convergen rápidamente. {$mejor} es ligeramente mejor.";
        } elseif ($tasa_j < 0.9 && $tasa_gs < 0.9) {
            return "Convergencia moderada. {$mejor} tiene ventaja.";
        } else {
            return "Convergencia lenta. Considere cambiar parámetros o matriz.";
        }
    }
    
    /**
     * Analiza la estabilidad del proceso iterativo
     */
    private function analizarEstabilidad() {
        $errores_j = $this->jacobi->getErrores();
        $errores_gs = $this->gauss_seidel->getErrores();
        
        return [
            'jacobi' => [
                'monotono' => $this->esMonotono($errores_j),
                'oscilaciones' => $this->contarOscilaciones($errores_j),
                'estable' => $this->esEstable($errores_j)
            ],
            'gauss_seidel' => [
                'monotono' => $this->esMonotono($errores_gs),
                'oscilaciones' => $this->contarOscilaciones($errores_gs),
                'estable' => $this->esEstable($errores_gs)
            ]
        ];
    }
    
    /**
     * Verifica si la convergencia es monótona
     */
    private function esMonotono($errores) {
        if (count($errores) < 2) return true;
        
        for ($i = 1; $i < count($errores); $i++) {
            if ($errores[$i] > $errores[$i-1] * 1.01) { // 1% de tolerancia
                return false;
            }
        }
        return true;
    }
    
    /**
     * Cuenta las oscilaciones en el error
     */
    private function contarOscilaciones($errores) {
        if (count($errores) < 2) return 0;
        
        $oscilaciones = 0;
        for ($i = 1; $i < count($errores) - 1; $i++) {
            $derivada1 = $errores[$i] - $errores[$i-1];
            $derivada2 = $errores[$i+1] - $errores[$i];
            
            if ($derivada1 * $derivada2 < 0) {
                $oscilaciones++;
            }
        }
        return $oscilaciones;
    }
    
    /**
     * Verifica si el proceso es estable (errores disminuyen)
     */
    private function esEstable($errores) {
        if (empty($errores)) return false;
        
        $inicio = $errores[0];
        $fin = $errores[count($errores) - 1];
        
        return $fin < $inicio;
    }
    
    /**
     * Estima el radio espectral de las matrices de iteración
     */
    private function estimarRadioEspectral() {
        // Aproximación: usar la tasa de convergencia
        $tasa_j = $this->calcularTasaLineal($this->jacobi->getErrores());
        $tasa_gs = $this->calcularTasaLineal($this->gauss_seidel->getErrores());
        
        return [
            'jacobi' => round($tasa_j, 4),
            'gauss_seidel' => round($tasa_gs, 4),
            'garantiza_convergencia' => $tasa_j < 1 && $tasa_gs < 1
        ];
    }
    
    /**
     * Predice convergencia basado en propiedades
     */
    private function predecirConvergencia() {
        $es_diag_dom = $this->jacobi->esDiagonalmenteDominante();
        $iteraciones_j = $this->jacobi->getIteraciones();
        $iteraciones_gs = $this->gauss_seidel->getIteraciones();
        
        $predicciones = [];
        
        if ($es_diag_dom) {
            $predicciones[] = "✓ Matriz diagonalmente dominante → Convergencia garantizada";
        } else {
            $predicciones[] = "⚠️ Matriz no diagonalmente dominante → Convergencia NO garantizada";
        }
        
        if ($iteraciones_j < 20) {
            $predicciones[] = "✓ Jacobi converge rápidamente";
        } elseif ($iteraciones_j < 100) {
            $predicciones[] = "✓ Jacobi converge moderadamente";
        } else {
            $predicciones[] = "⚠️ Jacobi converge lentamente";
        }
        
        if ($iteraciones_gs < 20) {
            $predicciones[] = "✓ Gauss-Seidel converge rápidamente";
        } elseif ($iteraciones_gs < 100) {
            $predicciones[] = "✓ Gauss-Seidel converge moderadamente";
        } else {
            $predicciones[] = "⚠️ Gauss-Seidel converge lentamente";
        }
        
        return $predicciones;
    }
    
    /**
     * Calcula el residuo de la solución
     */
    public function calcularResiduos() {
        $residuos = [];
        
        if ($this->vector_b) {
            $residuos['jacobi'] = $this->calcularResiduo($this->jacobi->getSolucion());
            $residuos['gauss_seidel'] = $this->calcularResiduo($this->gauss_seidel->getSolucion());
        }
        
        return $residuos;
    }
    
    /**
     * Calcula ||Ax - b|| para una solución
     */
    private function calcularResiduo($solucion) {
        if (!$solucion || !$this->vector_b) return null;
        
        $n = count($solucion);
        $residuo_cuad = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $suma = 0;
            for ($j = 0; $j < $n; $j++) {
                $suma += $this->matriz_A[$i][$j] * $solucion[$j];
            }
            
            $diff = $suma - $this->vector_b[$i];
            $residuo_cuad += $diff * $diff;
        }
        
        return sqrt($residuo_cuad);
    }
}
?>
