<?php
/**
 * CLASE ANALIZADOR AVANZADO - ANÁLISIS PROFUNDO DE CONVERGENCIA
 * =============================================================
 * Realiza análisis matemático avanzado de propiedades de convergencia
 * 
 * ANÁLISIS REALIZADOS:
 * 1. VELOCIDAD DE CONVERGENCIA: Relación Jacobi/Gauss-Seidel
 * 2. TASA LINEAL: r = e_{k+1}/e_k (cuánto mejora cada iteración)
 * 3. RADIO ESPECTRAL: ρ (mayor autovalor de matriz iteración)
 * 4. ESTABILIDAD: ¿Monotona? ¿Oscilante? ¿Decrece siempre?
 * 5. RESIDUOS: ||Ax - b|| para verificar solución real
 * 
 * TEORÍA MATEMÁTICA:
 * - Convergencia garantizada si ρ < 1
 * - Velocidad: ρ más pequeño = más rápido
 * - Tasa lineal: aproxima log(ρ) para grandes iteraciones
 * - Estimación: Usa historial de errores (método de la potencia implícito)
 */
class AnalizadorAvanzado {
    private $jacobi;           // Instancia Jacobi con resultados
    private $gauss_seidel;     // Instancia GaussSeidel con resultados
    private $matriz_A;         // Matriz original
    private $vector_b;         // Vector b (opcional, para residuos)
    
    /**
     * CONSTRUCTOR
     * 
     * @param Jacobi $jacobi           Instancia con resolver() ya ejecutado
     * @param GaussSeidel $gauss_seidel Instancia con resolver() ya ejecutado
     * @param array $matriz_A           Matriz original
     * @param array|null $vector_b      Vector b (opcional, para cálculo de residuos)
     */
    public function __construct($jacobi, $gauss_seidel, $matriz_A, $vector_b = null) {
        $this->jacobi = $jacobi;
        $this->gauss_seidel = $gauss_seidel;
        $this->matriz_A = $matriz_A;
        $this->vector_b = $vector_b;
    }
    
    /**
     * ANÁLISIS COMPLETO DE CONVERGENCIA (ORQUESTADOR)
     * Ejecuta todos los análisis y retorna en estructura unificada
     * 
     * @return array {
     *   'velocidad_convergencia': { relacion, mas_rapido, mejora_porcentual },
     *   'tasa_convergencia_lineal': { jacobi, gauss_seidel, interpretacion },
     *   'estabilidad': { jacobi: {...}, gauss_seidel: {...} },
     *   'radio_espectral': { jacobi, gauss_seidel, garantiza_convergencia },
     *   'prediccion_convergencia': [ predicciones textuales ]
     * }
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
     * CALCULA VELOCIDAD RELATIVA DE CONVERGENCIA
     * Compara iteraciones: cuántas veces más rápido es uno vs otro
     * 
     * EJEMPLO:
     * - Jacobi: 100 iteraciones
     * - Gauss-Seidel: 50 iteraciones
     * - Relación: 100/50 = 2.0x (GS es 2x más rápido)
     * 
     * @return array {
     *   'relacion': string "2.0x",
     *   'mas_rapido': string "Gauss-Seidel" o "Jacobi",
     *   'mejora_porcentual': string "50%"
     * }
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
     * CALCULA MEJORA PORCENTUAL
     * Porcentaje de iteraciones ahorradas por método más rápido
     * 
     * FÓRMULA: (Δiters / iteraciones_más_lento) × 100%
     * 
     * @return string Mejora porcentual con símbolo %, ej "50%"
     */
    private function calcularMejora() {
        $iter_j = $this->jacobi->getIteraciones();
        $iter_gs = $this->gauss_seidel->getIteraciones();
        
        if ($iter_j == 0) return 0;
        
        return round(abs($iter_j - $iter_gs) / $iter_j * 100, 2) . '%';
    }
    
    /**
     * ESTIMA TASA DE CONVERGENCIA LINEAL
     * Calcula r = e_{k+1}/e_k (razón de reducción del error)
     * 
     * TEORÍA:
     * - Convergencia lineal: e_k ≈ r^k × e_0 (r es la tasa)
     * - r < 0.5: Muy bueno (error reduce a mitad cada iteración)
     * - 0.5 < r < 0.9: Aceptable
     * - r > 0.9: Lento (error reduce poco)
     * - r ≈ 1: Casi sin convergencia
     * 
     * CÁLCULO: Promedian las últimas 5 tasas (evita perturbaciones iniciales)
     * 
     * @return array {
     *   'jacobi': float tasa 0-1,
     *   'gauss_seidel': float tasa 0-1,
     *   'interpretacion': string análisis cualitativo
     * }
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
     * CALCULA TASA LINEAL r = e_{k+1}/e_k
     * Implementación del cálculo de razón entre errores consecutivos
     * 
     * PSEUDOCÓDIGO:
     * Para k=1 hasta len(errores)-1:
     *   r_k = e_{k+1} / e_k
     * Retornar promedio(últimas 5 r_k)
     * 
     * PROTECCIÓN:
     * - Si e_{k-1} < 1e-10: salta (evita división por muy pequeño)
     * - Si menos de 2 errores: retorna 0
     * 
     * @param array $errores Array de errores por iteración
     * @return float Tasa promedio (tipicamente 0.0 a 1.0)
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
     * INTERPRETA TASA DE CONVERGENCIA
     * Convierte número de tasa en análisis cualitativo
     * 
     * CLASIFICACIÓN:
     * - r < 0.5: "rápidamente" (error se reduce a mitad)
     * - 0.5 ≤ r < 0.9: "moderadamente" (mejora visible por iteración)
     * - r ≥ 0.9: "lentamente" (casi sin cambio)
     * 
     * @param float $tasa_j Tasa lineal de Jacobi
     * @param float $tasa_gs Tasa lineal de Gauss-Seidel
     * @return string Interpretación textual
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
     * ANALIZA ESTABILIDAD DEL PROCESO ITERATIVO
     * Examina patrón de convergencia (monotona vs oscilante)
     * 
     * MÉTRICAS:
     * 1. MONOTONO: ¿Error siempre disminuye? (±1% de tolerancia)
     * 2. OSCILACIONES: ¿Cuántos cambios de signo en derivada?
     * 3. ESTABLE: ¿Error_final < Error_inicial?
     * 
     * INTERPRETACIÓN:
     * - Monotono = estable
     * - Oscilante = posible convergencia lenta
     * - No estable = falla
     * 
     * @return array {
     *   'jacobi': { monotono: bool, oscilaciones: int, estable: bool },
     *   'gauss_seidel': { monotono: bool, oscilaciones: int, estable: bool }
     * }
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
     * VERIFICA SI CONVERGENCIA ES MONÓTONA
     * Chequea que cada error ≤ error anterior (con 1% de tolerancia)
     * 
     * @param array $errores Array de errores
     * @return bool true si convergencia monótona (error no aumenta)
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
     * CUENTA OSCILACIONES EN ERROR
     * Detecta cambios de signo en la derivada numérica
     * 
     * Una oscilación = cambio de dirección (e_{k-1} < e_k > e_{k+1})
     * Indica convergencia no monótona
     * 
     * @param array $errores Array de errores
     * @return int Número de oscilaciones detectadas
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
     * VERIFICA ESTABILIDAD DEL PROCESO
     * Compara error inicial vs error final
     * 
     * Proceso estable: error_final < error_inicial (convergencia real)
     * 
     * @param array $errores Array de errores
     * @return bool true si error final < error inicial
     */
    private function esEstable($errores) {
        if (empty($errores)) return false;
        
        $inicio = $errores[0];
        $fin = $errores[count($errores) - 1];
        
        return $fin < $inicio;
    }
    
    /**
     * ESTIMA RADIO ESPECTRAL
     * Aproxima ρ (mayor valor propio de matriz iteración)
     * 
     * TEORÍA:
     * - Convergencia garantizada sii ρ < 1
     * - Velocidad ~ log(ρ): ρ más pequeño = converge más rápido
     * - Para iteración k: ||e_k|| ≈ ρ^k × ||e_0||
     * 
     * ESTIMACIÓN:
     * - Usa tasa lineal como aproximación a ρ
     * - Método: ratio de errores en régimen estable
     * 
     * @return array {
     *   'jacobi': float radio espectral estimado,
     *   'gauss_seidel': float radio espectral estimado,
     *   'garantiza_convergencia': bool (true sii ambos < 1)
     * }
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
     * PREDICE CONVERGENCIA BASADO EN PROPIEDADES
     * Interpreta resultados en términos matemáticos
     * 
     * PREDICCIONES INCLUYEN:
     * 1. Dominancia diagonal (garantía teórica)
     * 2. Velocidad estimada (rápida/moderada/lenta)
     * 3. Comportamiento observado
     * 
     * @return array Array de predicciones textuales
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
     * CALCULA RESIDUOS DE AMBAS SOLUCIONES
     * Verifica: ||Ax - b|| para cada método
     * 
     * REQUISITO: vector_b debe estar disponible
     * 
     * INTERPRETACIÓN:
     * - residuo ≈ 0: solución es exacta
     * - residuo > 0.01: solución tiene error notable
     * - Compara con tolerancia usada
     * 
     * @return array {
     *   'jacobi': float residuo ||Ax-b||,
     *   'gauss_seidel': float residuo ||Ax-b||
     * } o array vacío si vector_b no disponible
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
     * CALCULA RESIDUO PARA UNA SOLUCIÓN
     * Implementación de ||Ax - b||₂ (norma Euclidiana)
     * 
     * FÓRMULA:
     * residuo = sqrt(Σ((Ax)_i - b_i)²)
     * donde (Ax)_i = Σ_j a_ij × x_j
     * 
     * @param array $solucion Vector solución x
     * @return float|null Norma L2 del residuo (o null si inputs inválidos)
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
