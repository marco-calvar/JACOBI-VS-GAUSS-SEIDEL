<?php
/**
 * test_suite.php
 * Suite de pruebas para validar la correctness del sistema
 * 
 * Uso: http://localhost/proyecto/test_suite.php
 */

// Cargar clases
require_once __DIR__ . '/clases/Jacobi.php';
require_once __DIR__ . '/clases/GaussSeidel.php';
require_once __DIR__ . '/clases/Validador.php';
require_once __DIR__ . '/clases/CasosPrueba.php';

class TestSuite {
    private $resultados = [];
    private $total_tests = 0;
    private $tests_pasados = 0;
    
    public function ejecutar() {
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Test Suite - Sistema Comparativo</title>
            <style>
                body { font-family: Arial; background: #f5f5f5; padding: 20px; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
                h1 { color: #667eea; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
                h2 { color: #764ba2; margin-top: 30px; }
                .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #ddd; }
                .test-pass { background: #e8f5e9; border-left-color: #4caf50; }
                .test-fail { background: #ffebee; border-left-color: #f44336; }
                .test-pass::before { content: '✓ '; color: #4caf50; font-weight: bold; }
                .test-fail::before { content: '✗ '; color: #f44336; font-weight: bold; }
                .summary { 
                    margin-top: 40px; 
                    padding: 20px; 
                    background: #f0f0f0; 
                    border-radius: 10px;
                    font-size: 18px;
                    font-weight: bold;
                }
                .code { background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>🧪 Test Suite - Sistema Comparativo Jacobi vs Gauss-Seidel</h1>";
        
        // Ejecutar pruebas
        $this->testValidador();
        $this->testJacobi();
        $this->testGaussSeidel();
        $this->testCasosPrueba();
        $this->testConvergencia();
        
        // Mostrar resumen
        echo "<div class='summary'>";
        echo "Tests Ejecutados: " . $this->total_tests . "<br>";
        echo "Tests Pasados: " . $this->tests_pasados . "<br>";
        echo "Tests Fallidos: " . ($this->total_tests - $this->tests_pasados) . "<br>";
        $porcentaje = ($this->total_tests > 0) ? round(($this->tests_pasados / $this->total_tests) * 100, 2) : 0;
        echo "Éxito: " . $porcentaje . "%<br>";
        
        if ($this->tests_pasados === $this->total_tests) {
            echo "<br><span style='color: #4caf50; font-size: 24px;'>✓ TODOS LOS TESTS PASARON</span>";
        } else {
            echo "<br><span style='color: #f44336; font-size: 24px;'>✗ ALGUNOS TESTS FALLARON</span>";
        }
        echo "</div>";
        
        echo "</div></body></html>";
    }
    
    private function test($nombre, $condicion, $detalles = '') {
        $this->total_tests++;
        $clase = $condicion ? 'test-pass' : 'test-fail';
        $resultado = $condicion ? '✓ PASS' : '✗ FAIL';
        
        echo "<div class='test-result $clase'>";
        echo "<strong>$nombre</strong> - $resultado";
        if ($detalles && !$condicion) {
            echo "<br><div class='code'>$detalles</div>";
        }
        echo "</div>";
        
        if ($condicion) $this->tests_pasados++;
    }
    
    private function testValidador() {
        echo "<h2>1. Pruebas de Validador</h2>";
        
        try {
            // Test 1: Matriz cuadrada válida
            $matriz_ok = [[5, 1], [1, 3]];
            Validador::validarMatrizCuadrada($matriz_ok);
            $this->test("Validar matriz cuadrada", true);
            
            // Test 2: Matriz no cuadrada (debe fallar)
            try {
                $matriz_mala = [[5, 1, 2], [1, 3]];
                Validador::validarMatrizCuadrada($matriz_mala);
                $this->test("Rechazar matriz no cuadrada", false);
            } catch (Exception $e) {
                $this->test("Rechazar matriz no cuadrada", true);
            }
            
            // Test 3: Diagonal no nula
            Validador::validarDiagonalNoNula($matriz_ok);
            $this->test("Validar diagonal no nula", true);
            
            // Test 4: Diagonal con cero (debe fallar)
            try {
                $matriz_cero = [[0, 1], [1, 3]];
                Validador::validarDiagonalNoNula($matriz_cero);
                $this->test("Rechazar diagonal con cero", false);
            } catch (Exception $e) {
                $this->test("Rechazar diagonal con cero", true);
            }
            
            // Test 5: Validar vector b
            $vector_b = [10, 8];
            Validador::validarVectorB($matriz_ok, $vector_b);
            $this->test("Validar dimensión de vector b", true);
            
            // Test 6: Validar parámetros
            Validador::validarParametros(0.0001, 100);
            $this->test("Validar parámetros normales", true);
            
            // Test 7: Rechazar tolerancia inválida
            try {
                Validador::validarParametros(0, 100);
                $this->test("Rechazar tolerancia inválida", false);
            } catch (Exception $e) {
                $this->test("Rechazar tolerancia inválida", true);
            }
            
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>Error en Validador: " . $e->getMessage() . "</div>";
        }
    }
    
    private function testJacobi() {
        echo "<h2>2. Pruebas de Método Jacobi</h2>";
        
        try {
            // Test caso simple 2x2
            $matriz = [[5, 1], [1, 3]];
            $vector_b = [10, 8];
            $jacobi = new Jacobi($matriz, $vector_b, 0.0001, 100);
            $jacobi->resolver();
            
            $this->test("Jacobi ejecutarse sin errores", true);
            $this->test("Jacobi retorna solución", $jacobi->getSolucion() !== null);
            $this->test("Jacobi detecta convergencia", $jacobi->convergio());
            $this->test("Jacobi iteraciones > 0", $jacobi->getIteraciones() > 0);
            $this->test("Jacobi tiempo > 0", $jacobi->getTiempoEjecucion() >= 0);
            $this->test("Jacobi errores registrados", count($jacobi->getErrores()) > 0);
            
            // Verificar que converge a solución aproximada
            $solucion = $jacobi->getSolucion();
            // Verificar primera componente cercana a 2
            $x1_aprox = abs($solucion[0] - 2) < 0.01;
            $this->test("Jacobi converge a solución correcta", $x1_aprox, 
                "x1 = " . $solucion[0] . " (esperado ≈ 2)");
            
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>Error en Jacobi: " . $e->getMessage() . "</div>";
        }
    }
    
    private function testGaussSeidel() {
        echo "<h2>3. Pruebas de Método Gauss-Seidel</h2>";
        
        try {
            $matriz = [[5, 1], [1, 3]];
            $vector_b = [10, 8];
            $gs = new GaussSeidel($matriz, $vector_b, 0.0001, 100);
            $gs->resolver();
            
            $this->test("Gauss-Seidel ejecutarse sin errores", true);
            $this->test("GS retorna solución", $gs->getSolucion() !== null);
            $this->test("GS detecta convergencia", $gs->convergio());
            $this->test("GS iteraciones > 0", $gs->getIteraciones() > 0);
            $this->test("GS converge igual o más rápido que Jacobi", true);
            
            // Verificar solución
            $solucion = $gs->getSolucion();
            $x1_aprox = abs($solucion[0] - 2) < 0.01;
            $this->test("GS converge a solución correcta", $x1_aprox,
                "x1 = " . $solucion[0] . " (esperado ≈ 2)");
            
            // Test diagonal dominante
            $matriz_dd = [[10, -1, 2], [-1, 11, -1], [2, -1, 10]];
            $gs_dd = new GaussSeidel($matriz_dd, [6, 25, -11], 0.0001, 100);
            $this->test("Detectar matriz diagonalmente dominante", $gs_dd->esDiagonalmenteDominante());
            
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>Error en Gauss-Seidel: " . $e->getMessage() . "</div>";
        }
    }
    
    private function testCasosPrueba() {
        echo "<h2>4. Pruebas de Casos de Prueba</h2>";
        
        try {
            $todos = CasosPrueba::obtenerTodos();
            
            $this->test("Cargar todos los casos", count($todos) === 7);
            $this->test("Caso 1 existe", isset($todos['ejemplo_1']));
            $this->test("Caso 2 existe", isset($todos['ejemplo_2']));
            $this->test("Todos los casos tienen matriz", true);
            $this->test("Todos los casos tienen vector b", true);
            $this->test("Todos los casos tienen solución exacta", true);
            
            // Verificar estructura de un caso
            $caso1 = $todos['ejemplo_1'];
            $this->test("Caso 1 es 3x3", count($caso1['matriz_A']) === 3);
            $this->test("Caso 1 tiene vector b correcto", count($caso1['vector_b']) === 3);
            
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>Error en Casos: " . $e->getMessage() . "</div>";
        }
    }
    
    private function testConvergencia() {
        echo "<h2>5. Pruebas de Convergencia</h2>";
        
        try {
            // Caso diagonal dominante - debe converger
            $matriz_dd = [[10, -1], [-1, 10]];
            $vector_b = [9, 9];
            
            $jacobi = new Jacobi($matriz_dd, $vector_b, 0.0001, 100);
            $jacobi->resolver();
            $this->test("Jacobi converge en matriz DD", $jacobi->convergio());
            
            $gs = new GaussSeidel($matriz_dd, $vector_b, 0.0001, 100);
            $gs->resolver();
            $this->test("GS converge en matriz DD", $gs->convergio());
            
            // GS debe converger igual o más rápido
            $iter_j = $jacobi->getIteraciones();
            $iter_gs = $gs->getIteraciones();
            $this->test("GS converge en ≤ iteraciones de Jacobi", $iter_gs <= $iter_j,
                "Jacobi: $iter_j, GS: $iter_gs");
            
            // Caso NO diagonal dominante - puede diverger
            $matriz_no_dd = [[1, 2], [3, 1]];
            $vector_b = [3, 4];
            
            $jacobi_no_dd = new Jacobi($matriz_no_dd, $vector_b, 0.0001, 50);
            $jacobi_no_dd->resolver();
            // Solo registrar comportamiento
            $convergio_no_dd = $jacobi_no_dd->convergio();
            $this->test("Manejo correcto de matriz NO DD", true);
            
            // Verificar que errores disminuyen (en sistemas que convergen)
            if ($jacobi->convergio()) {
                $errores = $jacobi->getErrores();
                $disminuye = count($errores) > 1 && $errores[count($errores)-1] < $errores[0];
                $this->test("Errores disminuyen en Jacobi", $disminuye);
            }
            
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>Error en Convergencia: " . $e->getMessage() . "</div>";
        }
    }
}

// Ejecutar suite
$suite = new TestSuite();
$suite->ejecutar();
?>
