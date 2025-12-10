<?php
/**
 * CasosPrueba.php
 * Suite completa de casos de prueba para validar el sistema
 */

class CasosPrueba {
    
    /**
     * Retorna todos los casos de prueba disponibles
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
     * CASO 1: Sistema 3x3 - Diagonal Dominante (Convergencia Garantizada)
     * 
     * Sistema:
     *  10x₁ - x₂ + 2x₃ = 6
     *  -x₁ + 11x₂ - x₃ = 25
     *  2x₁ - x₂ + 10x₃ = -11
     * 
     * Solución exacta: x₁ = 1, x₂ = 2, x₃ = -1
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
     * CASO 2: Sistema 4x4 - Diagonal Dominante Moderada
     * 
     * Sistema bien condicionado con convergencia garantizada
     * Solución exacta: x₁ = 2, x₂ = -1, x₃ = 3, x₄ = 1
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
     * CASO 3: Sistema 2x2 - Simple y Pequeño
     * 
     * Sistema:
     *  5x₁ + x₂ = 10
     *  x₁ + 3x₂ = 8
     * 
     * Solución exacta: x₁ = 2, x₂ = 0
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
     * CASO 4: Sistema 5x5 - Matriz Débilmente Diagonal Dominante
     * 
     * Desafío: Convergencia más lenta, demuestra diferencia entre Jacobi y Gauss-Seidel
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
     * CASO 5: Sistema 3x3 - No Diagonal Dominante
     * 
     * Desafío: No hay garantía de convergencia, demuestra límites de métodos iterativos
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
     * CASO 6: Sistema 6x6 - Mayor Tamaño, Diagonal Dominante
     * 
     * Desafío: Evalúa eficiencia en matrices más grandes
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
     * CASO 7: Sistema Tridiagonal 5x5
     * 
     * Desafío: Matriz con patrón especial - Muy común en diferencias finitas
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
     * Obtiene un caso de prueba específico
     */
    public static function obtenerCaso($nombre) {
        $todos = self::obtenerTodos();
        return $todos[$nombre] ?? null;
    }
    
    /**
     * Obtiene descripciones de todos los casos para mostrar en UI
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
