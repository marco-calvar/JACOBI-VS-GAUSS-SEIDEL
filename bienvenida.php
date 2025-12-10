<?php
/**
 * bienvenida.php
 * Página de presentación inicial del sistema
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Comparativo - Jacobi vs Gauss-Seidel</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .bienvenida-container {
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 50%, #154360 100%);
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            max-width: 1200px;
            width: 100%;
            overflow: hidden;
        }
        
        .bienvenida-header {
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 50%, #154360 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
        }
        
        .bienvenida-header h1 {
            font-size: 3em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            color: #ffffff;
        }
        
        .bienvenida-header p {
            font-size: 1.3em;
            opacity: 1;
            margin-bottom: 10px;
            color: #e3f2fd;
        }
        
        .bienvenida-header .subtitulo {
            font-size: 1.1em;
            opacity: 1;
            font-style: italic;
            color: #b3e5fc;
        }
        
        .bienvenida-content {
            padding: 60px 40px;
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 50%, #154360 100%);
            color: #ffffff;
        }
        
        .contenido-principal {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .metodo-presentacion {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 12px;
            border-left: 4px solid;
            backdrop-filter: blur(10px);
            color: #ffffff;
        }
        
        .metodo-presentacion.jacobi {
            border-left-color: #0a3d62;
        }
        
        .metodo-presentacion.gauss {
            border-left-color: #2ecc71;
        }
        
        .metodo-presentacion h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #ffffff;
        }
        
        .metodo-presentacion.jacobi h2 {
            color: #e3f2fd;
        }
        
        .metodo-presentacion.gauss h2 {
            color: #b3e5fc;
        }
        
        .formula {
            background: rgba(227, 242, 253, 0.15);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.95em;
            overflow-x: auto;
            border: 1px solid rgba(227, 242, 253, 0.3);
            color: #e3f2fd;
        }
        
        .caracteristicas {
            list-style: none;
            margin: 20px 0;
        }
        
        .caracteristicas li {
            padding: 12px 0;
            padding-left: 30px;
            position: relative;
            color: #e3f2fd;
            line-height: 1.6;
        }
        
        .caracteristicas li::before {
            content: "●";
            position: absolute;
            left: 0;
            font-weight: bold;
            font-size: 1.2em;
            color: #4dd0e1;
        }
        
        .ventajas {
            background: rgba(76, 208, 225, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 3px solid #4dd0e1;
        }
        
        .ventajas h4 {
            color: #4dd0e1;
            margin-bottom: 10px;
        }
        
        .desventajas {
            background: rgba(229, 57, 53, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 3px solid #ef5350;
        }
        
        .desventajas h4 {
            color: #ef5350;
            margin-bottom: 10px;
        }
        
        .comparativa-tabla {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        
        .comparativa-tabla th {
            background: #1a5276;
            color: #e3f2fd;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }
        
        .comparativa-tabla td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(227, 242, 253, 0.2);
            color: #e3f2fd;
        }
        
        .comparativa-tabla tr:hover {
            background: rgba(100, 181, 246, 0.15);
        }
        
        .comparativa-tabla tr:nth-child(even) {
            background: rgba(100, 181, 246, 0.08);
        }
        
        .botones-accion {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 50px;
        }
        
        .boton {
            padding: 20px 40px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .boton-principal {
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 100%);
            color: white;
            grid-column: 1 / -1;
        }
        
        .boton-principal:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(10, 61, 98, 0.4);
        }
        
        .boton-destacado {
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 100%);
            color: white;
            padding: 30px 80px;
            font-size: 1.6em;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            box-shadow: 0 10px 30px rgba(10, 61, 98, 0.3);
            margin: 30px 0;
        }
        
        .boton-destacado:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(10, 61, 98, 0.5);
        }
        
        .boton-secundario {
            background: #f0f0f0;
            color: #0a3d62;
            border: 2px solid #0a3d62;
        }
        
        .boton-secundario:hover {
            background: #0a3d62;
            color: white;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }
        
        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            border-color: #0a3d62;
            box-shadow: 0 10px 25px rgba(10, 61, 98, 0.15);
            transform: translateY(-5px);
        }
        
        .feature-card .icon {
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        
        .feature-card h3 {
            color: #0a3d62;
            margin-bottom: 10px;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
            font-size: 0.95em;
        }
        
        .diferencia-visual {
            margin-top: 30px;
            padding: 20px;
            background: #e8f4f8;
            border-radius: 10px;
            border: 2px solid #0a3d62;
        }
        
        .diferencia-visual h4 {
            color: #0a3d62;
            margin-bottom: 15px;
        }
        
        .diferencia-visual p {
            margin: 10px 0;
            line-height: 1.8;
        }
        
        .paso {
            display: flex;
            gap: 15px;
            margin: 15px 0;
        }
        
        .paso-numero {
            background: #64b5f6;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .paso-contenido {
            flex: 1;
        }
        
        .paso-contenido strong {
            color: #e3f2fd;
        }
        
        .paso-contenido p {
            color: #b3e5fc;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .contenido-principal {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .bienvenida-header h1 {
                font-size: 2em;
            }
            
            .bienvenida-header {
                padding: 40px 20px;
            }
            
            .bienvenida-content {
                padding: 30px 20px;
            }
            
            .botones-accion {
                grid-template-columns: 1fr;
            }
            
            .boton-principal {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    <div class="bienvenida-container">
        <!-- Header -->
        <div class="bienvenida-header">
            <h1>Sistema Comparativo</h1>
            <p>Jacobi vs Gauss-Seidel</p>
            <p class="subtitulo">Análisis exhaustivo de métodos iterativos para resolver sistemas lineales</p>
        </div>
        
        <!-- Botón Destacado al Sistema -->
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 40px; text-align: center; border-bottom: 3px solid #0a3d62;">
            <h2 style="color: #0a3d62; margin-bottom: 20px; font-size: 2em;">¿Listo para Comparar?</h2>
            <a href="sistema_comparativo.php" class="boton-destacado">Acceder al Sistema Comparativo →</a>
            <p style="color: #b3e5fc; margin-top: 15px; font-size: 1.1em;">Utiliza tus propias matrices o prueba con nuestros casos predefinidos</p>
        </div>
        
        <!-- Contenido -->
        <div class="bienvenida-content">
            
            <!-- Características principales -->
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon">1</div>
                    <h3>Comparación Lado a Lado</h3>
                    <p>Compara ambos métodos simultáneamente en un mismo sistema</p>
                </div>
                <div class="feature-card">
                    <div class="icon">2</div>
                    <h3>Visualización de Errores</h3>
                    <p>Gráficas interactivas que muestran la convergencia</p>
                </div>
                <div class="feature-card">
                    <div class="icon">3</div>
                    <h3>Análisis de Eficiencia</h3>
                    <p>Medición de tiempo, memoria e iteraciones</p>
                </div>
                <div class="feature-card">
                    <div class="icon">4</div>
                    <h3>Recomendaciones</h3>
                    <p>Sugerencias automáticas según el tipo de matriz</p>
                </div>
                <div class="feature-card">
                    <div class="icon">5</div>
                    <h3>Casos de Prueba</h3>
                    <p>7 ejemplos predefinidos listos para usar</p>
                </div>
                <div class="feature-card">
                    <div class="icon">6</div>
                    <h3>Validación Exhaustiva</h3>
                    <p>Verificación completa de datos y advertencias</p>
                </div>
            </div>
            
            <!-- Presentación de métodos -->
            <h2 style="text-align: center; margin: 50px 0 40px; color: #ffffff; font-size: 2em;">
                ¿Cuál es la diferencia entre ambos métodos?
            </h2>
            
            <div class="contenido-principal">
                <!-- Jacobi -->
                <div class="metodo-presentacion jacobi">
                    <h2>Método de Jacobi</h2>
                    
                    <div class="formula">
                        x<sub>i</sub><sup>(k+1)</sup> = (1/a<sub>ii</sub>) * [b<sub>i</sub> - Σ(j≠i) a<sub>ij</sub> * x<sub>j</sub><sup>(k)</sup>]
                    </div>
                    
                    <p style="color: #e3f2fd; margin: 15px 0;">
                        <strong>Característica clave:</strong> Utiliza TODOS los valores de la iteración anterior
                    </p>
                    
                    <ul class="caracteristicas">
                        <li>Actualiza los valores simultáneamente</li>
                        <li>No usa valores recién calculados en la misma iteración</li>
                        <li>Fácil de paralelizar</li>
                        <li>Convergencia más lenta en sistemas bien condicionados</li>
                    </ul>
                    
                    <div class="ventajas">
                        <h4>Ventajas</h4>
                        <ul class="caracteristicas" style="margin: 10px 0;">
                            <li>Paralelizable en múltiples procesadores</li>
                            <li>Óptimo para computación en GPU</li>
                            <li>Independencia entre cálculos</li>
                        </ul>
                    </div>
                    
                    <div class="desventajas">
                        <h4>Desventajas</h4>
                        <ul class="caracteristicas" style="margin: 10px 0;">
                            <li>Convergencia más lenta que GS</li>
                            <li>Requiere más iteraciones</li>
                            <li>Más uso de memoria en sistemas grandes</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Gauss-Seidel -->
                <div class="metodo-presentacion gauss">
                    <h2>Método de Gauss-Seidel</h2>
                    
                    <div class="formula">
                        x<sub>i</sub><sup>(k+1)</sup> = (1/a<sub>ii</sub>) * [b<sub>i</sub> - Σ(j<i) a<sub>ij</sub> * x<sub>j</sub><sup>(k+1)</sup> - Σ(j>i) a<sub>ij</sub> * x<sub>j</sub><sup>(k)</sup>]
                    </div>
                    
                    <p style="color: #e3f2fd; margin: 15px 0;">
                        <strong>Característica clave:</strong> Utiliza valores YA ACTUALIZADOS en la misma iteración
                    </p>
                    
                    <ul class="caracteristicas">
                        <li>Aprovecha valores más recientes (actualizados)</li>
                        <li>Usa valores de la iteración k+1 cuando están disponibles</li>
                        <li>Difícil de paralelizar</li>
                        <li>Convergencia ~2x más rápida que Jacobi</li>
                    </ul>
                    
                    <div class="ventajas">
                        <h4>Ventajas</h4>
                        <ul class="caracteristicas" style="margin: 10px 0;">
                            <li>Convergencia 2x más rápida que Jacobi</li>
                            <li>Menos iteraciones necesarias</li>
                            <li>Mejor para sistemas medianos</li>
                        </ul>
                    </div>
                    
                    <div class="desventajas">
                        <h4>Desventajas</h4>
                        <ul class="caracteristicas" style="margin: 10px 0;">
                            <li>No es fácilmente paralelizable</li>
                            <li>Dependencia secuencial entre cálculos</li>
                            <li>Lento en arquitecturas paralelas</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Tabla comparativa -->
            <h2 style="text-align: center; margin: 50px 0 30px; color: #333; font-size: 1.8em;">
                Comparación Resumida
            </h2>
            
            <table class="comparativa-tabla">
                <thead>
                    <tr>
                        <th>Característica</th>
                        <th style="text-align: center;">Jacobi</th>
                        <th style="text-align: center;">Gauss-Seidel</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Velocidad de Convergencia</strong></td>
                        <td>Lenta</td>
                        <td>Rápida (~2x)</td>
                    </tr>
                    <tr>
                        <td><strong>Iteraciones Necesarias</strong></td>
                        <td>Mayor cantidad</td>
                        <td>Menor cantidad</td>
                    </tr>
                    <tr>
                        <td><strong>Paralelización</strong></td>
                        <td>Fácil</td>
                        <td>Difícil</td>
                    </tr>
                    <tr>
                        <td><strong>GPU/CUDA</strong></td>
                        <td>Excelente</td>
                        <td>No recomendado</td>
                    </tr>
                    <tr>
                        <td><strong>Mejor para</strong></td>
                        <td>Computación paralela</td>
                        <td>Sistemas generales</td>
                    </tr>
                    <tr>
                        <td><strong>Dependencias</strong></td>
                        <td>Ninguna</td>
                        <td>Secuencial</td>
                    </tr>
                    <tr>
                        <td><strong>Uso de Memoria</strong></td>
                        <td>Estándar</td>
                        <td>Similar a Jacobi</td>
                    </tr>
                    <tr>
                        <td><strong>Precisión Final</strong></td>
                        <td>Igual</td>
                        <td>Igual</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Sección de Demostración Efectiva -->
            <h2 style="text-align: center; margin: 50px 0 30px; color: #333; font-size: 1.8em;">
                Demostración Interactiva del Sistema
            </h2>
            
            <div style="background: linear-gradient(135deg, #f0f7ff 0%, #e3f2fd 100%); padding: 30px; border-radius: 10px; border-left: 5px solid #0a3d62; margin-bottom: 40px;">
                <p style="color: #e3f2fd; font-size: 1.1em; margin-bottom: 20px; line-height: 1.7;">
                    Nuestro sistema comparativo te permite ver en <strong>tiempo real</strong> cómo cada método resuelve un sistema de ecuaciones lineales:
                </p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 25px;">
                    <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #0a3d62;">
                        <h4 style="color: #0a3d62; margin-bottom: 12px;">Visualización en Tiempo Real</h4>
                        <ul style="color: #333; list-style-position: inside; line-height: 1.8;">
                            <li><strong>Matrices formateadas:</strong> A y b en formato matemático</li>
                            <li><strong>Gráficas interactivas:</strong> Error y convergencia por iteración</li>
                            <li><strong>Comparación lado a lado:</strong> Ver ambos métodos simultáneamente</li>
                            <li><strong>Resultados finales:</strong> Soluciones aproximadas y sus diferencias</li>
                        </ul>
                    </div>
                    
                    <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #1a5276;">
                        <h4 style="color: #1a5276; margin-bottom: 12px;">Análisis de Desempeño</h4>
                    <ul style="color: #333; list-style-position: inside; line-height: 1.8;">
                            <li><strong>Iteraciones:</strong> Cuántos ciclos necesita cada método</li>
                            <li><strong>Tiempo de ejecución:</strong> Duración exacta en milisegundos</li>
                            <li><strong>Memoria utilizada:</strong> Recursos consumidos por cada algoritmo</li>
                            <li><strong>Eficiencia:</strong> Cuál converge más rápido y con menos recursos</li>
                        </ul>
                    </div>
                </div>
                
                <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #2ecc71;">
                    <h4 style="color: #0a3d62; margin-bottom: 12px;">Validación y Recomendaciones Inteligentes</h4>
                    <p style="color: #555; line-height: 1.7; margin-bottom: 12px;">
                        El sistema verifica automáticamente:
                    </p>
                    <ul style="color: #555; list-style-position: inside; line-height: 1.8;">
                        <li>Que la matriz cumpla condiciones de convergencia</li>
                        <li>Cuál método será más eficiente según el tipo de sistema</li>
                        <li>Posibles problemas de estabilidad numérica</li>
                        <li>Recomendaciones de cuál método usar para tu caso específico</li>
                    </ul>
                </div>
            </div>
            
            <!-- Cómo Interpretar los Resultados -->
            <h2 style="text-align: center; margin: 50px 0 30px; color: #ffffff; font-size: 1.8em;">
                Cómo Interpretar los Resultados
            </h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 40px;">
                <div style="background: rgba(156, 39, 176, 0.15); padding: 25px; border-radius: 10px; border-left: 5px solid #ce93d8; backdrop-filter: blur(10px);">
                    <h3 style="color: #e3f2fd; margin-bottom: 15px;">Iteraciones</h3>
                    <p style="color: #e3f2fd; line-height: 1.7; margin-bottom: 12px;">
                        <strong>Qué es:</strong> El número de ciclos que necesita cada método para llegar a una solución con la precisión requerida.
                    </p>
                    <p style="color: #e3f2fd; line-height: 1.7;">
                        <strong>Por qué importa:</strong> Menos iteraciones = convergencia más rápida. Si un método necesita 50 iteraciones y otro 10, el segundo es más eficiente.
                    </p>
                </div>
                
                <div style="background: rgba(76, 175, 80, 0.15); padding: 25px; border-radius: 10px; border-left: 5px solid #81c784; backdrop-filter: blur(10px);">
                    <h3 style="color: #e3f2fd; margin-bottom: 15px;">Tiempo de Ejecución</h3>
                    <p style="color: #b3e5fc; line-height: 1.7; margin-bottom: 15px;">
                        <strong>Qué es:</strong> Cuántos milisegundos tarda el método en calcular la solución.
                    </p>
                    <p style="color: #b3e5fc; line-height: 1.7;">
                        <strong>Por qué importa:</strong> Mide la velocidad real en tu sistema. Menos tiempo = computación más eficiente.
                    </p>
                </div>
                
                <div style="background: rgba(33, 150, 243, 0.15); padding: 25px; border-radius: 10px; border-left: 5px solid #64b5f6; backdrop-filter: blur(10px);">
                    <h3 style="color: #e3f2fd; margin-bottom: 15px;">Memoria Utilizada</h3>
                    <p style="color: #b3e5fc; line-height: 1.7; margin-bottom: 15px;">
                        <strong>Qué es:</strong> Kilobytes de memoria RAM consumidos por cada algoritmo.
                    </p>
                    <p style="color: #b3e5fc; line-height: 1.7;">
                        <strong>Por qué importa:</strong> Importante en sistemas con recursos limitados. Menos memoria = mejor eficiencia de recursos.
                    </p>
                </div>
                
                <div style="background: rgba(255, 152, 0, 0.15); padding: 25px; border-radius: 10px; border-left: 5px solid #ffb74d; backdrop-filter: blur(10px);">
                    <h3 style="color: #e3f2fd; margin-bottom: 15px;">Gráficas de Convergencia</h3>
                    <p style="color: #b3e5fc; line-height: 1.7; margin-bottom: 15px;">
                        <strong>Qué es:</strong> Visualización del error (diferencia con la solución exacta) en cada iteración.
                    </p>
                    <p style="color: #b3e5fc; line-height: 1.7;">
                        <strong>Por qué importa:</strong> Muestra visualmente cuál método converge más rápido. La curva más pronunciada = convergencia más rápida.
                    </p>
                </div>
            </div>
            
            <!-- Condiciones de Convergencia -->
            <h2 style="text-align: center; margin: 50px 0 30px; color: #ffffff; font-size: 1.8em;">
                Condiciones para que Converjan
            </h2>
            
            <div style="background: rgba(100, 181, 246, 0.15); padding: 30px; border-radius: 10px; border-left: 4px solid #64b5f6; backdrop-filter: blur(10px);">
                <h3 style="color: #e3f2fd; margin-bottom: 20px;">Suficiente: Dominancia Diagonal Estricta</h3>
                
                <div class="formula" style="margin-bottom: 20px;">
                    |a<sub>ii</sub>| > Σ(j≠i) |a<sub>ij</sub>|  para todo i
                </div>
                
                <p style="color: #b3e5fc; margin-bottom: 15px;">
                    Si la matriz cumple esta condición, ambos métodos (Jacobi y Gauss-Seidel) <strong>garantizan convergencia</strong>.
                </p>
                
                <div class="paso">
                    <div class="paso-numero">1</div>
                    <div class="paso-contenido">
                        <strong>El elemento diagonal debe ser mayor en valor absoluto</strong>
                        <p>|a₁₁| debe ser > suma de otros elementos en fila 1</p>
                    </div>
                </div>
                
                <div class="paso">
                    <div class="paso-numero">2</div>
                    <div class="paso-contenido">
                        <strong>Esto debe cumplirse para TODAS las filas</strong>
                        <p>Cada |a<sub>ii</sub>| debe dominar a los otros elementos de su fila</p>
                    </div>
                </div>
                
                <div class="paso">
                    <div class="paso-numero">3</div>
                    <div class="paso-contenido">
                        <strong>Resultado: Convergencia garantizada</strong>
                        <p>Ambos métodos encontrarán la solución correcta</p>
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="botones-accion">
                <a href="sistema_comparativo.php" class="boton boton-principal">
                    Ir al Sistema Comparativo
                </a>
            </div>
            
        </div>
    </div>
</body>
</html>
