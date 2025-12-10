<?php
/**
 * SISTEMA_COMPARATIVO.PHP - APLICACIÓN PRINCIPAL
 * =============================================
 * Controlador central que orquesta todo el sistema
 * 
 * RESPONSABILIDADES (BACKEND):
 * 1. RECEPCIÓN: Procesar datos POST del formulario
 * 2. VALIDACIÓN: Verificar matriz, vector, parámetros
 * 3. EJECUCIÓN: Resolver con ambos métodos (Jacobi y Gauss-Seidel)
 * 4. ANÁLISIS: Comparar resultados y generar insights
 * 5. RESPUESTA: HTML con gráficas, tablas, análisis
 * 
 * FLUJO:
 * 1. Usuario ingresa matrix A, vector b, parámetros en formulario
 * 2. POST → sistema_comparativo.php (AQUÍ)
 * 3. Backend valida y ejecuta ambos métodos
 * 4. Crea análisis comparativo exhaustivo
 * 5. Renderiza página con resultados visuales
 * 
 * DEPENDENCIAS EXTERNAS (Cargan clases PHP):
 * - Jacobi.php: implementación del método iterativo Jacobi
 * - GaussSeidel.php: implementación mejorada Gauss-Seidel
 * - Comparador.php: análisis comparativo de resultados
 * - Validador.php: verificación de integridad
 * - AnalizadorAvanzado.php: análisis matemático profundo
 * - CasosPrueba.php: suite de 7 casos predefinidos
 */

// ============== CARGAR TODAS LAS CLASES ==============
require_once __DIR__ . '/clases/Jacobi.php';
require_once __DIR__ . '/clases/GaussSeidel.php';
require_once __DIR__ . '/clases/Comparador.php';
require_once __DIR__ . '/clases/Validador.php';
require_once __DIR__ . '/clases/AnalizadorAvanzado.php';
require_once __DIR__ . '/clases/CasosPrueba.php';

// ============== VARIABLES GLOBALES ==============
// Almacenan resultados de la ejecución
$resultados = null;         // Datos principales de ambos métodos
$error_msg = null;          // Mensaje de error si falla
$advertencias = [];         // Advertencias no críticas
$json_resultado = null;     // JSON con datos para gráficas JavaScript

// ============== PROCESAR FORMULARIO ==============
// Si fue POST (usuario envió formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener y validar dimensión
        $n = intval($_POST['dimension']);
        
        if ($n < 2 || $n > 20) {
            throw new Exception("La dimensión debe estar entre 2 y 20");
        }
        
        // ===== CONSTRUIR MATRIZ A (n×n) =====
        $matriz_A = [];
        for ($i = 0; $i < $n; $i++) {
            $matriz_A[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                // POST contiene: a_0_0, a_0_1, ..., a_n-1_n-1
                $valor = floatval($_POST["a_{$i}_{$j}"]);
                $matriz_A[$i][$j] = $valor;
            }
        }
        
        // ===== CONSTRUIR VECTOR B (n×1) =====
        $vector_b = [];
        for ($i = 0; $i < $n; $i++) {
            // POST contiene: b_0, b_1, ..., b_n-1
            $vector_b[$i] = floatval($_POST["b_{$i}"]);
        }
        
        // ===== OBTENER PARÁMETROS NUMÉRICOS =====
        $tolerancia = floatval($_POST['tolerancia']);      // Error máximo permitido
        $max_iter = intval($_POST['max_iteraciones']);     // Límite de iteraciones
        
        // ===== VALIDACIÓN EXHAUSTIVA =====
        // Verifica: matriz cuadrada, diagonal no nula, vector compatible, parámetros válidos
        $validacion = Validador::validarSistemaCompleto($matriz_A, $vector_b, $tolerancia, $max_iter);
        $advertencias = $validacion['advertencias'];  // Guardar advertencias para mostrar
        
        // ===== VECTOR INICIAL (OPCIONAL) =====
        // Usuario puede especificar x^(0) custom o usar [0, 0, ..., 0]
        $x_inicial = null;
        if (!empty($_POST['usar_x_inicial'])) {
            $x_inicial = [];
            for ($i = 0; $i < $n; $i++) {
                $x_inicial[$i] = floatval($_POST["x_inicial_{$i}"] ?? 0);
            }
        }
        
        // ===== EJECUTAR MÉTODO JACOBI =====
        $jacobi = new Jacobi($matriz_A, $vector_b, $tolerancia, $max_iter, $x_inicial);
        $jacobi->resolver();  // Itera hasta convergencia o max_iteraciones
        
        // ===== EJECUTAR MÉTODO GAUSS-SEIDEL =====
        $gauss_seidel = new GaussSeidel($matriz_A, $vector_b, $tolerancia, $max_iter, $x_inicial);
        $gauss_seidel->resolver();  // Itera hasta convergencia o max_iteraciones
        
        // ===== ANÁLISIS COMPARATIVO =====
        // Compara: iteraciones, tiempo, memoria, error, eficiencia
        $comparador = new Comparador($jacobi, $gauss_seidel, $matriz_A);
        $analisis = $comparador->generarAnalisis();
        
        // ===== ANÁLISIS MATEMÁTICO AVANZADO =====
        // Estima: tasa lineal, radio espectral, estabilidad, residuos
        $analizador = new AnalizadorAvanzado($jacobi, $gauss_seidel, $matriz_A, $vector_b);
        $analisis_avanzado = $analizador->analizarConvergencia();
        $residuos = $analizador->calcularResiduos();  // ||Ax - b|| para ambos
        
        // ===== EMPAQUETAR RESULTADOS =====
        $resultados = [
            'jacobi' => $jacobi,                          // Objeto Jacobi con getters
            'gauss_seidel' => $gauss_seidel,              // Objeto GaussSeidel con getters
            'analisis' => $analisis,                      // Comparación cuantitativa
            'analisis_avanzado' => $analisis_avanzado,    // Análisis matemático
            'residuos' => $residuos,                      // ||Ax-b|| para verificación
            'tipo_matriz' => $comparador->getTipoMatriz(),// Propiedades de matriz
            'matriz_A' => $matriz_A,                      // Matriz original (para mostrar)
            'vector_b' => $vector_b                       // Vector original (para mostrar)
        ];
        
        // ===== GENERAR JSON PARA GRÁFICAS JAVASCRIPT =====
        // Chart.js necesita arrays de errores para graficar convergencia
        $json_resultado = json_encode([
            'errores_jacobi' => $jacobi->getErrores(),        // [e0, e1, e2, ...]
            'errores_gs' => $gauss_seidel->getErrores(),      // [e0, e1, e2, ...]
            'iter_jacobi' => $jacobi->getIteraciones(),       // Número de iteraciones
            'iter_gs' => $gauss_seidel->getIteraciones(),     // Número de iteraciones
            'tiempo_jacobi' => $jacobi->getTiempoEjecucion(), // ms
            'tiempo_gs' => $gauss_seidel->getTiempoEjecucion(), // ms
            'memoria_jacobi' => $jacobi->getMemoriaUsada(),   // KB
            'memoria_gs' => $gauss_seidel->getMemoriaUsada()  // KB
        ]);
        
    } catch (Exception $e) {
        // Si algo falla, capturar el error y mostrar al usuario
        $error_msg = $e->getMessage();
    }
}

// ===== OBTENER CASOS DE PRUEBA DISPONIBLES =====
// Para el selector de casos predefinidos (7 casos)
$casos_prueba = CasosPrueba::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Comparativo - Jacobi vs Gauss-Seidel</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 50%, #154360 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 50%, #154360 100%);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid rgba(255,255,255,0.1);
        }
        
        header h1 {
            font-size: 2.2em;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        header p {
            font-size: 0.95em;
            opacity: 0.9;
        }
        
        .header-back {
            background: rgba(255,255,255,0.15);
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
            font-size: 0.95em;
        }
        
        .header-back:hover {
            background: rgba(255,255,255,0.25);
            transform: translateX(-3px);
        }
        
        .content {
            padding: 40px;
        }
        
        .casos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .caso-btn {
            background: linear-gradient(135deg, #1a5276 0%, #0a3d62 100%);
            border: 2px solid #0a3d62;
            padding: 25px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 140px;
            width: 100%;
            box-sizing: border-box;
        }
        
        .caso-contenido {
            width: 100%;
            text-align: center;
        }
        
        .caso-btn:hover {
            background: linear-gradient(135deg, #154360 0%, #0a2f4a 100%);
            border-color: #b3e5fc;
            box-shadow: 0 15px 35px rgba(10, 61, 98, 0.4);
            transform: translateY(-4px);
        }
        
        .caso-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(10, 61, 98, 0.2);
        }
        
        .caso-btn h4 {
            color: #e3f2fd;
            margin: 0 0 10px 0;
            font-size: 1.1em;
            font-weight: 600;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            line-height: 1.5;
            width: 100%;
            box-sizing: border-box;
            letter-spacing: 0.3px;
        }
        
        .caso-btn p {
            color: #b3e5fc;
            font-size: 0.85em;
            margin: 0;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .caso-dimension {
            display: inline-block;
            background: #e3f2fd;
            color: #0a3d62;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 2px solid #e0e0e0;
        }
        
        .form-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.4em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input[type="number"], input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
        }
        
        input[type="number"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #0a3d62;
            box-shadow: 0 0 0 3px rgba(10, 61, 98, 0.1);
        }
        
        .matriz-container {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            justify-content: center;
            align-items: flex-start;
        }
        
        .matriz-seccion {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
        
        .matriz-seccion label {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .matriz-fila {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .matriz-fila input {
            width: 60px;
            padding: 10px;
            text-align: center;
            font-family: 'Courier New', monospace;
        }
        
        button {
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(10, 61, 98, 0.35);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .error-msg {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #f44336;
        }
        
        .advertencias-box {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        
        .resultados-container {
            display: none;
            animation: slideIn 0.5s ease;
        }
        
        .resultados-container.activo {
            display: block;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .resultado-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
        }
        
        .resultado-card h3 {
            color: #0a3d62;
            margin-bottom: 20px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .resultado-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .metrica {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #0a3d62;
        }
        
        .metrica-label {
            font-weight: bold;
            color: #666;
            font-size: 0.9em;
            display: block;
        }
        
        .metrica-valor {
            font-size: 1.4em;
            color: #333;
            font-weight: bold;
            margin-top: 8px;
            font-family: 'Courier New', monospace;
        }
        
        .comparativa {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .metodo-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
        }
        
        .metodo-card h4 {
            font-size: 1.2em;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .metodo-card.jacobi h4 {
            color: #3498db;
            border-bottom-color: #3498db;
        }
        
        .metodo-card.gauss h4 {
            color: #2ecc71;
            border-bottom-color: #2ecc71;
        }
        
        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px solid #e0e0e0;
        }
        
        .chart-container h3 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            flex-wrap: wrap;
        }
        
        .tab {
            background: none;
            border: none;
            padding: 15px 25px;
            font-size: 1em;
            cursor: pointer;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            width: auto;
        }
        
        .tab:hover {
            color: #0a3d62;
        }
        
        .tab.activo {
            color: #0a3d62;
            border-bottom-color: #0a3d62;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.activo {
            display: block;
        }
        
        .matriz-display {
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            margin: 15px 0;
            font-size: 1.1em;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .comparativa {
                grid-template-columns: 1fr;
            }
            
            header {
                flex-direction: column;
                gap: 15px;
            }
            
            .casos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>Sistema Comparativo</h1>
                <p>Jacobi vs Gauss-Seidel</p>
            </div>
            <a href="bienvenida.php" class="header-back">Volver a Inicio</a>
        </header>
        
        <div class="content">
            <?php if ($error_msg): ?>
                <div class="error-msg"><?= htmlspecialchars($error_msg) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($advertencias)): ?>
                <div class="advertencias-box">
                    <strong>Advertencias:</strong><br>
                    <?php foreach ($advertencias as $adv): ?>
                        <?= htmlspecialchars($adv) ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$resultados): ?>
                <!-- FORMULARIO -->
                <div class="form-section">
                    <h2>Casos de Prueba Rápidos</h2>
                    <div class="casos-grid">
                        <?php foreach ($casos_prueba as $clave => $caso): ?>
                        <button type="button" class="caso-btn" onclick="cargarCaso('<?= $clave ?>')">
                            <div class="caso-contenido">
                                <h4><?= htmlspecialchars($caso['nombre']) ?></h4>
                                <p><?= htmlspecialchars($caso['descripcion']) ?></p>
                            </div>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Ingresa tus Datos</h2>
                    
                    <form method="POST" id="mainForm">
                        <div class="form-group">
                            <label>Dimensión (2-20):</label>
                            <input type="number" name="dimension" id="dimension" min="2" max="20" value="3" required onchange="generarMatriz()">
                        </div>
                        
                        <div id="matrizContainer"></div>
                        
                        <div class="form-group">
                            <label>Tolerancia (ε):</label>
                            <input type="number" name="tolerancia" step="0.0001" value="0.0001" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Máximo de iteraciones:</label>
                            <input type="number" name="max_iteraciones" min="1" value="100" required>
                        </div>
                        
                        <button type="submit">Ejecutar Comparación</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- RESULTADOS -->
                <div class="resultados-container activo">
                    <div style="margin-bottom: 30px;">
                        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" style="display: inline-block; background: #0a3d62; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s;">Volver al Formulario</a>
                    </div>
                    
                    <!-- Matrices -->
                    <div class="resultado-card">
                        <h3>Sistema Ingresado</h3>
                        <div class="matriz-display">
                            <p><strong>Matriz A:</strong></p>
                            $$\begin{pmatrix}
                            <?php
                            for ($i = 0; $i < count($resultados['matriz_A']); $i++) {
                                for ($j = 0; $j < count($resultados['matriz_A'][$i]); $j++) {
                                    echo number_format($resultados['matriz_A'][$i][$j], 2);
                                    if ($j < count($resultados['matriz_A'][$i]) - 1) echo " & ";
                                }
                                if ($i < count($resultados['matriz_A']) - 1) echo " \\\\ ";
                            }
                            ?>
                            \end{pmatrix}$$
                            
                            <p style="margin-top: 20px;"><strong>Vector b:</strong></p>
                            $$\begin{pmatrix}
                            <?php
                            for ($i = 0; $i < count($resultados['vector_b']); $i++) {
                                echo number_format($resultados['vector_b'][$i], 2);
                                if ($i < count($resultados['vector_b']) - 1) echo " \\\\ ";
                            }
                            ?>
                            \end{pmatrix}$$
                        </div>
                    </div>
                    
                    <!-- Comparativa de resultados -->
                    <div class="resultado-card">
                        <h3>Resultados Comparativos</h3>
                        
                        <div class="comparativa">
                            <!-- Jacobi -->
                            <div class="metodo-card jacobi">
                                <h4>Método de Jacobi</h4>
                                <div class="resultado-grid">
                                    <div class="metrica">
                                        <span class="metrica-label">Convergencia</span>
                                        <span class="metrica-valor">
                                            <?= $resultados['jacobi']->convergio() ? 'SI' : 'NO' ?>
                                        </span>
                                    </div>
                                    <div class="metrica">
                                        <span class="metrica-label">Iteraciones</span>
                                        <span class="metrica-valor"><?= $resultados['jacobi']->getIteraciones() ?></span>
                                    </div>
                                    <div class="metrica">
                                        <span class="metrica-label">Tiempo (ms)</span>
                                        <span class="metrica-valor"><?= number_format($resultados['jacobi']->getTiempoEjecucion(), 4) ?></span>
                                    </div>
                                    <div class="metrica">
                                        <span class="metrica-label">Memoria (KB)</span>
                                        <span class="metrica-valor"><?= number_format($resultados['jacobi']->getMemoriaUsada(), 2) ?></span>
                                    </div>
                                </div>
                                
                                <p style="margin-top: 15px; color: #666;"><strong>Solución:</strong></p>
                                $$\mathbf{x} = \begin{pmatrix}
                                <?php
                                $sol = $resultados['jacobi']->getSolucion();
                                for ($i = 0; $i < count($sol); $i++) {
                                    echo number_format($sol[$i], 6);
                                    if ($i < count($sol) - 1) echo " \\\\ ";
                                }
                                ?>
                                \end{pmatrix}$$
                            </div>
                            
                            <!-- Gauss-Seidel -->
                            <div class="metodo-card gauss">
                                <h4>Método de Gauss-Seidel</h4>
                                <div class="resultado-grid">
                                    <div class="metrica">
                                        <span class="metrica-label">Convergencia</span>
                                        <span class="metrica-valor">
                                            <?= $resultados['gauss_seidel']->convergio() ? 'SI' : 'NO' ?>
                                        </span>
                                    </div>
                                    <div class="metrica">
                                        <span class="metrica-label">Iteraciones</span>
                                        <span class="metrica-valor"><?= $resultados['gauss_seidel']->getIteraciones() ?></span>
                                    </div>
                                    <div class="metrica">
                                        <span class="metrica-label">Tiempo (ms)</span>
                                        <span class="metrica-valor"><?= number_format($resultados['gauss_seidel']->getTiempoEjecucion(), 4) ?></span>
                                    </div>
                                    <div class="metrica">
                                        <span class="metrica-label">Memoria (KB)</span>
                                        <span class="metrica-valor"><?= number_format($resultados['gauss_seidel']->getMemoriaUsada(), 2) ?></span>
                                    </div>
                                </div>
                                
                                <p style="margin-top: 15px; color: #666;"><strong>Solución:</strong></p>
                                $$\mathbf{x} = \begin{pmatrix}
                                <?php
                                $sol = $resultados['gauss_seidel']->getSolucion();
                                for ($i = 0; $i < count($sol); $i++) {
                                    echo number_format($sol[$i], 6);
                                    if ($i < count($sol) - 1) echo " \\\\ ";
                                }
                                ?>
                                \end{pmatrix}$$
                            </div>
                        </div>
                    </div>
                    
                    <!-- Análisis de Eficiencia Computacional -->
                    <div class="resultado-card">
                        <h3>Análisis de Eficiencia Computacional</h3>
                        <div class="resultado-grid">
                            <div class="metrica">
                                <span class="metrica-label">Método Jacobi - Tiempo</span>
                                <span class="metrica-valor"><?= number_format($resultados['jacobi']->getTiempoEjecucion(), 4) ?> ms</span>
                            </div>
                            <div class="metrica">
                                <span class="metrica-label">Método Jacobi - Iteraciones</span>
                                <span class="metrica-valor"><?= $resultados['jacobi']->getIteraciones() ?></span>
                            </div>
                            <div class="metrica">
                                <span class="metrica-label">Método GS - Tiempo</span>
                                <span class="metrica-valor"><?= number_format($resultados['gauss_seidel']->getTiempoEjecucion(), 4) ?> ms</span>
                            </div>
                            <div class="metrica">
                                <span class="metrica-label">Método GS - Iteraciones</span>
                                <span class="metrica-valor"><?= $resultados['gauss_seidel']->getIteraciones() ?></span>
                            </div>
                        </div>
                        <p style="margin-top: 20px; color: #555; font-size: 0.95em;">
                            <?php 
                            $tiempo_jacobi = $resultados['jacobi']->getTiempoEjecucion();
                            $tiempo_gs = $resultados['gauss_seidel']->getTiempoEjecucion();
                            $diferencia = abs($tiempo_jacobi - $tiempo_gs);
                            $mas_rapido = $tiempo_jacobi < $tiempo_gs ? 'Jacobi es más rápido' : 'Gauss-Seidel es más rápido';
                            echo "Diferencia de tiempo: " . number_format($diferencia, 4) . " ms. $mas_rapido";
                            ?>
                        </p>
                    </div>
                    
                    <!-- Comparación Destacada de Iteraciones -->
                    <div class="resultado-card" style="background: linear-gradient(135deg, #0a3d62 0%, #1a5276 100%); border: 2px solid #64b5f6;">
                        <h3 style="color: #e3f2fd; border-bottom: 3px solid #64b5f6; padding-bottom: 15px;">Comparación de Iteraciones</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-top: 20px;">
                            <!-- Jacobi Iteraciones -->
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 8px; border-left: 4px solid #4dd0e1; text-align: center; backdrop-filter: blur(10px);">
                                <h4 style="color: #e3f2fd; margin-bottom: 15px; font-size: 1.1em;">Método de Jacobi</h4>
                                <div style="background: rgba(100, 181, 246, 0.2); padding: 25px; border-radius: 8px; margin-bottom: 15px;">
                                    <p style="color: #b3e5fc; font-size: 0.9em; margin-bottom: 8px;">Iteraciones Totales</p>
                                    <div style="font-size: 2.5em; font-weight: bold; color: #e3f2fd;">
                                        <?= $resultados['jacobi']->getIteraciones() ?>
                                    </div>
                                </div>
                                <p style="color: #b3e5fc; font-size: 0.9em;">
                                    Número de ciclos necesarios para alcanzar la tolerancia
                                </p>
                            </div>
                            
                            <!-- Gauss-Seidel Iteraciones -->
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 8px; border-left: 4px solid #81c784; text-align: center; backdrop-filter: blur(10px);">
                                <h4 style="color: #e3f2fd; margin-bottom: 15px; font-size: 1.1em;">Método de Gauss-Seidel</h4>
                                <div style="background: rgba(129, 199, 132, 0.2); padding: 25px; border-radius: 8px; margin-bottom: 15px;">
                                    <p style="color: #b3e5fc; font-size: 0.9em; margin-bottom: 8px;">Iteraciones Totales</p>
                                    <div style="font-size: 2.5em; font-weight: bold; color: #e3f2fd;">
                                        <?= $resultados['gauss_seidel']->getIteraciones() ?>
                                    </div>
                                </div>
                                <p style="color: #b3e5fc; font-size: 0.9em;">
                                    Número de ciclos necesarios para alcanzar la tolerancia
                                </p>
                            </div>
                        </div>
                        
                        <!-- Análisis de Diferencia de Iteraciones -->
                        <div style="background: rgba(255, 255, 255, 0.08); padding: 20px; border-radius: 8px; margin-top: 20px; border-top: 3px solid #64b5f6; backdrop-filter: blur(10px);">
                            <?php 
                            $iter_jacobi = $resultados['jacobi']->getIteraciones();
                            $iter_gs = $resultados['gauss_seidel']->getIteraciones();
                            $diff_iter = abs($iter_jacobi - $iter_gs);
                            $porcentaje = $iter_jacobi > 0 ? round(($diff_iter / $iter_jacobi) * 100, 1) : 0;
                            $mas_rapido_iter = $iter_jacobi < $iter_gs ? 'Jacobi es más rápido' : 'Gauss-Seidel es más rápido';
                            $ganancia = $iter_jacobi > 0 ? ($iter_jacobi - $iter_gs) : 0;
                            ?>
                            <p style="color: #e3f2fd; font-weight: bold; margin-bottom: 10px;">Análisis Comparativo:</p>
                            <ul style="color: #b3e5fc; list-style-position: inside; line-height: 2;">
                                <li><strong>Diferencia:</strong> <?= $diff_iter ?> iteraciones (<?= $porcentaje ?>%)</li>
                                <li><strong>Eficiencia:</strong> <?= $mas_rapido_iter ?> convergiendo en menos ciclos</li>
                                <?php if ($ganancia > 0): ?>
                                    <li><strong>Ahorro con GS:</strong> <?= $ganancia ?> iteraciones menos que Jacobi</li>
                                <?php elseif ($ganancia < 0): ?>
                                    <li><strong>Ahorro con Jacobi:</strong> <?= abs($ganancia) ?> iteraciones menos que Gauss-Seidel</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Estudio de Requisitos de Memoria -->
                    <div class="resultado-card">
                        <h3>Estudio de Requisitos de Memoria</h3>
                        <div class="resultado-grid">
                            <div class="metrica">
                                <span class="metrica-label">Memoria - Jacobi</span>
                                <span class="metrica-valor"><?= number_format($resultados['jacobi']->getMemoriaUsada(), 2) ?> KB</span>
                            </div>
                            <div class="metrica">
                                <span class="metrica-label">Memoria - Gauss-Seidel</span>
                                <span class="metrica-valor"><?= number_format($resultados['gauss_seidel']->getMemoriaUsada(), 2) ?> KB</span>
                            </div>
                            <div class="metrica">
                                <span class="metrica-label">Dimensión del Sistema</span>
                                <span class="metrica-valor"><?= count($resultados['matriz_A']) ?>x<?= count($resultados['matriz_A']) ?></span>
                            </div>
                            <div class="metrica">
                                <span class="metrica-label">Tipo de Matriz</span>
                                <span class="metrica-valor"><?= ucfirst($resultados['tipo_matriz']) ?></span>
                            </div>
                        </div>
                        <p style="margin-top: 20px; color: #555; font-size: 0.95em;">
                            <?php 
                            $mem_jacobi = $resultados['jacobi']->getMemoriaUsada();
                            $mem_gs = $resultados['gauss_seidel']->getMemoriaUsada();
                            $diff_mem = abs($mem_jacobi - $mem_gs);
                            echo "Diferencia de memoria: " . number_format($diff_mem, 2) . " KB. ";
                            echo ($mem_jacobi < $mem_gs ? "Jacobi usa menos memoria." : "Gauss-Seidel usa menos memoria.");
                            ?>
                        </p>
                    </div>
                    
                    <!-- Análisis de Convergencia -->
                    <div class="resultado-card">
                        <h3>Análisis de Convergencia para Diferentes Matrices</h3>
                        <div style="background: white; padding: 20px; border-radius: 8px;">
                            <?php 
                            $ambos_convergen = $resultados['jacobi']->convergio() && $resultados['gauss_seidel']->convergio();
                            if ($ambos_convergen): 
                            ?>
                                <p style="color: #2ecc71; font-weight: bold; margin-bottom: 15px;">Estado: Ambos métodos convergen</p>
                            <?php else: ?>
                                <p style="color: #f44336; font-weight: bold; margin-bottom: 15px;">Estado: Al menos un método no convergió</p>
                            <?php endif; ?>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <div style="background: #e8f5e9; padding: 15px; border-radius: 8px;">
                                    <strong style="color: #2ecc71;">Jacobi</strong>
                                    <p style="margin-top: 8px; color: #333;">
                                        Iteraciones: <?= $resultados['jacobi']->getIteraciones() ?><br>
                                        Tiempo: <?= number_format($resultados['jacobi']->getTiempoEjecucion(), 4) ?> ms<br>
                                        Convergió: <?= $resultados['jacobi']->convergio() ? 'SÍ' : 'NO' ?>
                                    </p>
                                </div>
                                <div style="background: #e3f2fd; padding: 15px; border-radius: 8px;">
                                    <strong style="color: #3498db;">Gauss-Seidel</strong>
                                    <p style="margin-top: 8px; color: #333;">
                                        Iteraciones: <?= $resultados['gauss_seidel']->getIteraciones() ?><br>
                                        Tiempo: <?= number_format($resultados['gauss_seidel']->getTiempoEjecucion(), 4) ?> ms<br>
                                        Convergió: <?= $resultados['gauss_seidel']->convergio() ? 'SÍ' : 'NO' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <?php if ($ambos_convergen): ?>
                            <div style="padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid #0a3d62;">
                                <strong>Análisis Comparativo:</strong>
                                <p style="margin-top: 8px; color: #555; line-height: 1.6;">
                                    <?php 
                                    $iter_j = $resultados['jacobi']->getIteraciones();
                                    $iter_g = $resultados['gauss_seidel']->getIteraciones();
                                    $tiempo_j = $resultados['jacobi']->getTiempoEjecucion();
                                    $tiempo_g = $resultados['gauss_seidel']->getTiempoEjecucion();
                                    
                                    if ($iter_j > 0 && $iter_g > 0) {
                                        $mejora_iter = abs($iter_j - $iter_g) / max($iter_j, $iter_g) * 100;
                                        echo "Mejora en iteraciones: " . number_format($mejora_iter, 1) . "%<br>";
                                    }
                                    
                                    if ($tiempo_j > 0 && $tiempo_g > 0) {
                                        $mejora_tiempo = abs($tiempo_j - $tiempo_g) / max($tiempo_j, $tiempo_g) * 100;
                                        echo "Mejora en tiempo: " . number_format($mejora_tiempo, 1) . "%<br>";
                                    }
                                    
                                    echo ($iter_j < $iter_g) ? "Jacobi converge más rápido en iteraciones." : "Gauss-Seidel converge más rápido en iteraciones.";
                                    ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Gráficas -->
                    <div class="chart-container">
                        <h3>Visualización Comparativa de Errores</h3>
                        <p style="margin-bottom: 15px; color: #666; font-size: 0.95em;">Evolución del error en escala logarítmica a lo largo de las iteraciones</p>
                        <canvas id="errorChart"></canvas>
                    </div>
                    
                    <div class="chart-container">
                        <h3>Comparación de Métricas</h3>
                        <canvas id="metricsChart"></canvas>
                    </div>
                    
                    <!-- Recomendaciones de Uso por Tipo de Matriz -->
                    <div class="resultado-card">
                        <h3>Recomendaciones de Uso por Tipo de Matriz</h3>
                        <div style="background: white; padding: 20px; border-radius: 8px;">
                            <?php 
                            $recomendaciones = $resultados['analisis']['recomendacion'];
                            foreach ($recomendaciones as $index => $rec): 
                            ?>
                            <div style="padding: 15px; margin: 10px 0; background: linear-gradient(135deg, #e8f4f8 0%, #d1e7f0 100%); border-radius: 8px; border-left: 4px solid #0a3d62;">
                                <p style="color: #333; line-height: 1.6;">
                                    <strong style="color: #0a3d62;">Recomendación <?= $index + 1 ?>:</strong><br>
                                    <?= htmlspecialchars($rec) ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="margin-top: 20px; padding: 20px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                            <strong style="color: #856404;">Resumen de Decisión:</strong>
                            <p style="margin-top: 10px; color: #666; line-height: 1.6;">
                                <?php 
                                if ($resultados['jacobi']->convergio() && $resultados['gauss_seidel']->convergio()) {
                                    $iter_j = $resultados['jacobi']->getIteraciones();
                                    $iter_g = $resultados['gauss_seidel']->getIteraciones();
                                    if ($iter_j < $iter_g) {
                                        echo "Para este sistema: Jacobi requiere menos iteraciones (" . $iter_j . " vs " . $iter_g . "). Considere Jacobi si necesita paralelización.";
                                    } else {
                                        echo "Para este sistema: Gauss-Seidel requiere menos iteraciones (" . $iter_g . " vs " . $iter_j . "). Es la mejor opción para máquinas secuenciales.";
                                    }
                                } elseif ($resultados['jacobi']->convergio()) {
                                    echo "Solo Jacobi convergió. Use Jacobi obligatoriamente.";
                                } elseif ($resultados['gauss_seidel']->convergio()) {
                                    echo "Solo Gauss-Seidel convergió. Use Gauss-Seidel obligatoriamente.";
                                } else {
                                    echo "Ningún método convergió. Verifique los parámetros o la matriz.";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <script>
                const data = <?= $json_resultado ?>;
                
                // Gráfica de errores
                const ctxError = document.getElementById('errorChart').getContext('2d');
                const maxIter = Math.max(data.errores_jacobi.length, data.errores_gs.length);
                
                new Chart(ctxError, {
                    type: 'line',
                    data: {
                        labels: Array.from({length: maxIter}, (_, i) => i + 1),
                        datasets: [{
                            label: 'Jacobi',
                            data: data.errores_jacobi,
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Gauss-Seidel',
                            data: data.errores_gs,
                            borderColor: '#2ecc71',
                            backgroundColor: 'rgba(46, 204, 113, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true, position: 'top' }
                        },
                        scales: {
                            y: {
                                type: 'logarithmic',
                                title: { display: true, text: 'Error (escala log)' }
                            }
                        }
                    }
                });
                
                // Gráfica de métricas
                const ctxMetrics = document.getElementById('metricsChart').getContext('2d');
                new Chart(ctxMetrics, {
                    type: 'bar',
                    data: {
                        labels: ['Iteraciones', 'Tiempo (ms)', 'Memoria (KB)'],
                        datasets: [{
                            label: 'Jacobi',
                            data: [data.iter_jacobi, data.tiempo_jacobi, data.memoria_jacobi],
                            backgroundColor: 'rgba(52, 152, 219, 0.6)',
                            borderColor: '#3498db',
                            borderWidth: 2
                        }, {
                            label: 'Gauss-Seidel',
                            data: [data.iter_gs, data.tiempo_gs, data.memoria_gs],
                            backgroundColor: 'rgba(46, 204, 113, 0.6)',
                            borderColor: '#2ecc71',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true, position: 'top' }
                        }
                    }
                });
                
                // MathJax render
                MathJax.typesetPromise().catch(err => console.log(err));
                </script>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        const casosPrueba = <?= json_encode($casos_prueba) ?>;
        
        function generarMatriz() {
            const n = parseInt(document.getElementById('dimension').value);
            const container = document.getElementById('matrizContainer');
            
            let html = '<div class="form-group"><label>Sistema de Ecuaciones: Ax = b</label></div>';
            html += '<div class="matriz-container">';
            
            // Matriz A
            html += '<div class="matriz-seccion">';
            html += '<label>Matriz A</label>';
            for (let i = 0; i < n; i++) {
                html += '<div class="matriz-fila">';
                for (let j = 0; j < n; j++) {
                    html += `<input type="number" name="a_${i}_${j}" step="any" required>`;
                }
                html += '</div>';
            }
            html += '</div>';
            
            // Vector b
            html += '<div class="matriz-seccion">';
            html += '<label>Vector b</label>';
            for (let i = 0; i < n; i++) {
                html += '<div class="matriz-fila">';
                html += `<input type="number" name="b_${i}" step="any" required>`;
                html += '</div>';
            }
            html += '</div>';
            
            html += '</div>';
            
            container.innerHTML = html;
        }
        
        function cargarCaso(clave) {
            const caso = casosPrueba[clave];
            if (!caso) return;
            
            document.getElementById('dimension').value = caso.matriz_A.length;
            generarMatriz();
            
            setTimeout(() => {
                const inputsA = document.querySelectorAll('input[name^="a_"]');
                let idx = 0;
                for (let i = 0; i < caso.matriz_A.length; i++) {
                    for (let j = 0; j < caso.matriz_A[i].length; j++) {
                        if (inputsA[idx]) inputsA[idx].value = caso.matriz_A[i][j];
                        idx++;
                    }
                }
                
                const inputsB = document.querySelectorAll('input[name^="b_"]');
                for (let i = 0; i < caso.vector_b.length; i++) {
                    if (inputsB[i]) inputsB[i].value = caso.vector_b[i];
                }
                
                document.querySelector('input[name="tolerancia"]').value = caso.tolerancia;
                document.querySelector('input[name="max_iteraciones"]').value = caso.max_iteraciones;
                
                document.getElementById('mainForm').scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
        
        generarMatriz();
    </script>
</body>
</html>
