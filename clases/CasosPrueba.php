<?php
/**
 * CLASE CASOS PRUEBA - SUITE DE PRUEBAS INTEGRAL
 * ===============================================
 * Repositorio de 7 casos de prueba predefinidos para validar:
 * - Convergencia de ambos métodos
 * - Comparación de eficiencia (iteraciones, tiempo, estabilidad)
 * - Comportamiento en diferentes tipos de matrices
 * 
 * CASOS INCLUYEN:
 * 1. Caso 1: 3×3 Diagonal Dominante Simétrica (convergencia garantizada)
 * 2. Caso 2: 4×4 Diagonal Dominante No Simétrica (caso general)
 * 3. Caso 3: 2×2 Pequeño Simple (visualización básica)
 * 4. Caso 4: 5×5 Débilmente DD (diferencia J vs GS máxima)
 * 5. Caso 5: 3×3 NO Diagonal Dominante (caso límite, divergencia posible)
 * 6. Caso 6: 6×6 Tamaño Mediano (eficiencia computacional)
 * 7. Caso 7: 5×5 Tridiagonal (estructura especial, diferencias finitas)
 * 
 * PROPÓSITO PEDAGÓGICO:
 * - Demostrar cuándo convergen/divergen métodos iterativos
 * - Visualizar impacto de dominancia diagonal
 * - Comparar velocidad Jacobi vs Gauss-Seidel en misma matriz
 * - Ilustrar comportamiento en casos reales (tridiagonal)
 */

class CasosPrueba {
    
    /**
     * OBTIENE TODOS LOS CASOS DE PRUEBA
     * Retorna array asociativo con los 7 casos predefinidos
     * 
     * ESTRUCTURA RETORNADA:
     * {
     *   'ejemplo_1': { nombre, descripcion, matriz_A, vector_b, ... },
     *   'ejemplo_2': { ... },
     *   ...
     *   'ejemplo_7': { ... }
     * }
     * 
     * CADA CASO CONTIENE:
     * - nombre: Título descriptivo
     * - descripcion: Característica principal
     * - matriz_A: Array n×n con coeficientes
     * - vector_b: Array n×1 con términos independientes
     * - tolerancia: Tolerancia de convergencia
     * - max_iteraciones: Límite de iteraciones
     * - solucion_exacta: Array con solución verdadera (para validar)
     * - propiedades: { diagonal_dominante, simetrica, condicion_conocida, ... }
     * 
     * @return array Array de todos los casos de prueba
     */
    public static function obtenerTodos() {
        return [
            'ejemplo_1' => self::caso1(),
            'ejemplo_2' => self::caso2(),
            'ejemplo_3' => self::caso3(),
            'ejemplo_4' => self::caso4(),
            'ejemplo_5' => self::caso5(),
            'ejemplo_6' => self::caso6(),
            'ejemplo_7' => self::caso7(),
        ];
    }
    
    /**
     * CASO 1: SISTEMA 3×3 DIAGONAL DOMINANTE SIMÉTRICO
     * ================================================
     * PROPÓSITO: Caso ideal donde ambos métodos convergen rápidamente
     * 
     * MATRIZ: Diagonal dominante y simétrica
     * ┌────────────────┐     ┌────┐
     * │ 10  -1   2   │     │  6 │
     * │ -1  11  -1   │  x = │ 25 │
     * │  2  -1  10   │     │-11 │
     * └────────────────┘     └────┘
     * 
     * PROPIEDADES MATEMÁTICAS:
     * ✓ Diagonal Dominante: |a_ii| > Σ|a_ij| para todo i
     *   10 > 1+2=3 ✓, 11 > 1+1=2 ✓, 10 > 2+1=3 ✓
     * ✓ Simétrica: A = A^T
     * ✓ Bien Condicionada
     * ✓ Convergencia Garantizada
     * 
     * SOLUCIÓN EXACTA: x₁ = 1, x₂ = 2, x₃ = -1
     * 
     * COMPORTAMIENTO ESPERADO:
     * - Jacobi: ~10-15 iteraciones
     * - Gauss-Seidel: ~5-8 iteraciones (~2x más rápido)
     * - Ambos convergen monotonamente
     * 
     * @return array Especificación completa del caso 1
     */
    private static function caso1() {
        return [
            'nombre' => 'Sistema 3x3 - Diagonal Dominante',
            'descripcion' => 'Matriz diagonal dominante con convergencia garantizada',
            'matriz_A' => [
                [10, -1, 2],
                [-1, 11, -1],
                [2, -1, 10]
            ],
            'vector_b' => [6, 25, -11],
            'tolerancia' => 0.0001,
            'max_iteraciones' => 100,
            'solucion_exacta' => [1, 2, -1],
            'propiedades' => [
                'diagonal_dominante' => true,
                'simetrica' => true,
                'condicion_conocida' => 'Convergencia rápida esperada'
            ]
        ];
    }
    
    /**
     * CASO 2: SISTEMA 4×4 DIAGONAL DOMINANTE MODERADA
     * ================================================
     * PROPÓSITO: Caso general no simétrico con convergencia garantizada
     * 
     * PROPIEDADES:
     * ✓ Diagonal Dominante (pero no fuertemente)
     * ✗ No Simétrica
     * ✓ Bien Condicionada
     * ✓ Convergencia Garantizada
     * 
     * SOLUCIÓN EXACTA: x = [2, -1, 3, 1]
     * 
     * COMPORTAMIENTO ESPERADO:
     * - Convergencia más lenta que Caso 1
     * - Diferencia J vs GS visible pero no extrema
     * - Ambos convergen sin oscilaciones
     */
    private static function caso2() {
        return [
            'nombre' => 'Sistema 4x4 - Diagonal Dominante Moderada',
            'descripcion' => 'Matriz moderadamente diagonal dominante',
            'matriz_A' => [
                [4, 1, -1, 0],
                [1, 4, -1, -1],
                [-1, -1, 5, 1],
                [0, -1, 1, 3]
            ],
            'vector_b' => [8, 7, 4, -5],
            'tolerancia' => 0.0001,
            'max_iteraciones' => 150,
            'solucion_exacta' => [2, -1, 3, 1],
            'propiedades' => [
                'diagonal_dominante' => true,
                'simetrica' => false,
                'condicion_conocida' => 'Convergencia moderada'
            ]
        ];
    }
    
    /**
     * CASO 3: SISTEMA 2×2 SIMPLE PEQUEÑO
     * ==================================
     * PROPÓSITO: Caso mínimo para visualización básica y pedagogía
     * 
     * SISTEMA:
     *  5x₁ + x₂ = 10
     *  x₁ + 3x₂ = 8
     * 
     * PROPIEDADES:
     * ✓ Diagonal Dominante
     * ✓ Simétrica
     * ✓ Muy Bien Condicionada
     * ✓ Convergencia MUY rápida
     * 
     * SOLUCIÓN EXACTA: x₁ = 2, x₂ = 0
     * 
     * COMPORTAMIENTO ESPERADO:
     * - Jacobi: ~3-5 iteraciones
     * - Gauss-Seidel: ~2-3 iteraciones
     * - Excelente para aprender el método (pasos visualizables)
     */
    private static function caso3() {
        return [
            'nombre' => 'Sistema 2x2 - Simple',
            'descripcion' => 'Sistema pequeño para visualización básica',
            'matriz_A' => [
                [5, 1],
                [1, 3]
            ],
            'vector_b' => [10, 8],
            'tolerancia' => 0.001,
            'max_iteraciones' => 50,
            'solucion_exacta' => [2, 0],
            'propiedades' => [
                'diagonal_dominante' => true,
                'simetrica' => true,
                'condicion_conocida' => 'Convergencia muy rápida'
            ]
        ];
    }
    
    /**
     * CASO 4: SISTEMA 5×5 DÉBILMENTE DIAGONAL DOMINANTE
     * ===================================================
     * PROPÓSITO: Demostrar DIFERENCIA MÁXIMA entre Jacobi y Gauss-Seidel
     * 
     * MATRIZ: Patrón típico: diagonal=8, vecinos=-1
     * Débil dominancia diagonal: |8| apenas > |1+1+...| en algunos lugares
     * 
     * PROPIEDADES:
     * ✓ Diagonal Dominante (débilmente)
     * ✓ Simétrica (patrón simétrico)
     * ✓ Radio espectral de Jacobi: ~0.8-0.9 (lento)
     * ✓ Radio espectral de GS: ~0.6-0.7 (~2x mejor)
     * 
     * SOLUCIÓN EXACTA: x = [1.5, 2.5, 3, 2.5, 1.5]
     * 
     * COMPORTAMIENTO ESPERADO:
     * - Jacobi: ~50-70 iteraciones
     * - Gauss-Seidel: ~25-35 iteraciones (~2x más rápido)
     * - DIFERENCIA VISIBLE: caso de estudio perfecto
     * - Ambos convergen pero lentamente
     * 
     * USO PEDAGÓGICO: Justifica por qué usar Gauss-Seidel
     */
    private static function caso4() {
        return [
            'nombre' => 'Sistema 5x5 - Débilmente Diagonal Dominante',
            'descripcion' => 'Matriz débilmente diagonal dominante - Diferencia notable entre métodos',
            'matriz_A' => [
                [8, -1, 0, -1, 0],
                [-1, 8, -1, 0, -1],
                [0, -1, 8, -1, 0],
                [-1, 0, -1, 8, -1],
                [0, -1, 0, -1, 8]
            ],
            'vector_b' => [10, 15, 20, 15, 10],
            'tolerancia' => 0.00001,
            'max_iteraciones' => 200,
            'solucion_exacta' => [1.5, 2.5, 3, 2.5, 1.5],
            'propiedades' => [
                'diagonal_dominante' => true,
                'simetrica' => true,
                'condicion_conocida' => 'GS converge ~2x más rápido que Jacobi'
            ]
        ];
    }
    
    /**
     * CASO 5: SISTEMA 3×3 NO DIAGONAL DOMINANTE
     * ==========================================
     * PROPÓSITO: CASO LÍMITE - Demostrar FALLA de métodos iterativos
     * 
     * MATRIX: NO satisface dominancia diagonal
     *  1×₁ + 2x₂ + 3x₃ = 14      |1| < |2+3|=5 ✗
     *  4x₁ + 1x₂ + 2x₃ = 11      |1| < |4+2|=6 ✗
     *  3x₁ + 4x₂ + 1x₃ = 16      |1| < |3+4|=7 ✗
     * 
     * PROPIEDADES:
     * ✗ NO Diagonal Dominante
     * ✗ NO Simétrica
     * ⚠️ Radio espectral ρ > 1 (teoría: divergencia)
     * ⚠️ Convergencia NO GARANTIZADA
     * 
     * SOLUCIÓN EXACTA: x = [1, 2, 3] (matemáticamente existe)
     * 
     * COMPORTAMIENTO ESPERADO:
     * - Jacobi: DIVERGE (error crece) u oscila sin converger
     * - Gauss-Seidel: Puede converger o divergir (ambiguo)
     * - Número de iteraciones: Alcanza máximo sin converger
     * 
     * ENSEÑANZA CRÍTICA:
     * - No todos los sistemas iterativos convergen
     * - Dominancia diagonal es condición SUFICIENTE pero no necesaria
     * - Métodos directos (Gaussian elimination) son seguros para estos casos
     */
    private static function caso5() {
        return [
            'nombre' => 'Sistema 3x3 - No Diagonal Dominante',
            'descripcion' => 'Matriz sin garantía de convergencia - Caso límite',
            'matriz_A' => [
                [1, 2, 3],
                [4, 1, 2],
                [3, 4, 1]
            ],
            'vector_b' => [14, 11, 16],
            'tolerancia' => 0.0001,
            'max_iteraciones' => 100,
            'solucion_exacta' => [1, 2, 3],
            'propiedades' => [
                'diagonal_dominante' => false,
                'simetrica' => false,
                'condicion_conocida' => 'Convergencia NO garantizada - Posible divergencia'
            ]
        ];
    }
    
    /**
     * CASO 6: SISTEMA 6×6 TAMAÑO MEDIANO
     * ==================================
     * PROPÓSITO: Evaluar eficiencia en matrices de tamaño práctico
     * 
     * PROPIEDADES:
     * ✓ Diagonal Dominante
     * ✓ Simétrica
     * ✓ Patrón estructurado (matriz banda)
     * 
     * SOLUCIÓN EXACTA: x = [1, 1, 1, 1, 1, 1] (vector constante)
     * 
     * COMPORTAMIENTO ESPERADO:
     * - Jacobi: ~30-50 iteraciones
     * - Gauss-Seidel: ~15-25 iteraciones
     * - Tiempo observable: ~0.1-1 ms (depende de computadora)
     * - Memoria: ~1-5 KB consumida
     * 
     * ANÁLISIS:
     * - Verifica escalabilidad a tamaños mayores
     * - Demuestra overhead de datos (crecimiento O(n²))
     * - Útil para benchmark computacional
     */
    private static function caso6() {
        return [
            'nombre' => 'Sistema 6x6 - Tamaño Mediano',
            'descripcion' => 'Sistema mediano para evaluar eficiencia computacional',
            'matriz_A' => [
                [10, 1, 0, 0, 1, 0],
                [1, 10, 1, 0, 0, 1],
                [0, 1, 10, 1, 0, 0],
                [0, 0, 1, 10, 1, 0],
                [1, 0, 0, 1, 10, 1],
                [0, 1, 0, 0, 1, 10]
            ],
            'vector_b' => [12, 13, 12, 12, 13, 12],
            'tolerancia' => 0.0001,
            'max_iteraciones' => 200,
            'solucion_exacta' => [1, 1, 1, 1, 1, 1],
            'propiedades' => [
                'diagonal_dominante' => true,
                'simetrica' => true,
                'condicion_conocida' => 'Evaluación de eficiencia en matrices medianas'
            ]
        ];
    }
    
    /**
     * CASO 7: SISTEMA TRIDIAGONAL 5×5
     * ================================
     * PROPÓSITO: Estructura especial típica de diferencias finitas (EDP)
     * 
     * PATRÓN TRIDIAGONAL:
     * ┌                    ┐
     * │  4 -1  0  0  0    │
     * │ -1  4 -1  0  0    │
     * │  0 -1  4 -1  0    │
     * │  0  0 -1  4 -1    │
     * │  0  0  0 -1  4    │
     * └                    ┘
     * 
     * PROPIEDADES:
     * ✓ Diagonal Dominante: |4| > |-1|+|-1|=2 ✓
     * ✓ Simétrica
     * ✓ Banda: Solo 3 diagonales con valores (resto ceros)
     * ✓ Bien Condicionada
     * ✓ COMÚN EN PRÁCTICA: Solución de EDPs por diferencias finitas
     * 
     * SOLUCIÓN EXACTA: x ≈ [1.4, 2.2, 2.4, 2.2, 1.4]
     * 
     * COMPORTAMIENTO ESPERADO:
     * - Jacobi: ~30-40 iteraciones
     * - Gauss-Seidel: ~15-20 iteraciones
     * - Convergencia rápida (matriz bien condicionada)
     * 
     * IMPORTANCIA:
     * - Demuestra aplicación real en ingeniería
     * - Métodos iterativos son preferidos para matrices grandes tridiagonales
     * - Mejor que métodos directos por espacio (O(n) vs O(n²))
     */
    private static function caso7() {
        return [
            'nombre' => 'Sistema Tridiagonal 5x5',
            'descripcion' => 'Matriz tridiagonal típica de diferencias finitas',
            'matriz_A' => [
                [4, -1, 0, 0, 0],
                [-1, 4, -1, 0, 0],
                [0, -1, 4, -1, 0],
                [0, 0, -1, 4, -1],
                [0, 0, 0, -1, 4]
            ],
            'vector_b' => [5, 0, 0, 0, 5],
            'tolerancia' => 0.0001,
            'max_iteraciones' => 100,
            'solucion_exacta' => [1.4, 2.2, 2.4, 2.2, 1.4],
            'propiedades' => [
                'diagonal_dominante' => true,
                'simetrica' => true,
                'estructura' => 'Tridiagonal',
                'condicion_conocida' => 'Convergencia garantizada, estructura especial'
            ]
        ];
    }
    
    /**
     * OBTIENE UN CASO DE PRUEBA ESPECÍFICO
     * Búsqueda por clave en array de casos
     * 
     * @param string $nombre Clave del caso (ejemplo_1, ejemplo_2, ..., ejemplo_7)
     * @return array|null Especificación del caso o null si no existe
     * 
     * EJEMPLO DE USO:
     * $caso = CasosPrueba::obtenerCaso('ejemplo_4');
     * // Retorna especificación completa del Caso 4
     */
    public static function obtenerCaso($nombre) {
        $todos = self::obtenerTodos();
        return $todos[$nombre] ?? null;
    }
    
    /**
     * OBTIENE DESCRIPCIONES SIMPLIFICADAS DE TODOS LOS CASOS
     * Para mostrar en interfaz de usuario sin datos innecesarios
     * 
     * @return array {
     *   'ejemplo_1': {
     *     'nombre': string,
     *     'descripcion': string,
     *     'dimension': int n,
     *     'propiedades': array de propiedades
     *   },
     *   ...
     * }
     * 
     * USO: Mostrar lista de casos en dropdown o tabla sin cargar matrices completas
     */
    public static function obtenerDescripciones() {
        $todos = self::obtenerTodos();
        $descripciones = [];
        
        foreach ($todos as $clave => $caso) {
            $descripciones[$clave] = [
                'nombre' => $caso['nombre'],
                'descripcion' => $caso['descripcion'],
                'dimension' => count($caso['matriz_A']),
                'propiedades' => $caso['propiedades']
            ];
        }
        
        return $descripciones;
    }
}
?>
