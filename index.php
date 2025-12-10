<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métodos Iterativos - Jacobi vs Gauss-Seidel</title>
    <style>
        /* ==================== ESTILOS GLOBALES ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* PALETA DE COLORES - Tema Azul Marino Profesional */
        /* Colores: Azul muy oscuro (#0a3d62), Azul marino (#1a5276), Azul medio (#154360) */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a3d62 0%, #1a5276 50%, #154360 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .hero-container {
            max-width: 900px;
            width: 100%;
            text-align: center;
            color: white;
        }
        
        h1 {
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }
        
        .subtitle {
            font-size: 1.4em;
            opacity: 0.95;
            margin-bottom: 50px;
            font-weight: 300;
            line-height: 1.6;
        }
        
        .diferencias {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 60px 0;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }
        
        .metodo-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 30px;
            border-radius: 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .metodo-card h3 {
            font-size: 1.6em;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .metodo-card ul {
            list-style: none;
            text-align: left;
        }
        
        .metodo-card li {
            padding: 10px 0;
            font-size: 0.95em;
            line-height: 1.5;
            opacity: 0.95;
        }
        
        .metodo-card li:before {
            content: '→ ';
            margin-right: 10px;
            font-weight: bold;
        }
        
        .cta-container {
            margin-top: 60px;
        }
        
        .cta-button {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 20px 60px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.2em;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            border: none;
            cursor: pointer;
            letter-spacing: 0.5px;
        }
        
        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
        }
        
        .cta-button:active {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5em;
            }
            
            .subtitle {
                font-size: 1.1em;
            }
            
            .diferencias {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 25px;
            }
            
            .cta-button {
                padding: 16px 40px;
                font-size: 1.1em;
                width: 100%;
            }
        }
        
        .credits-section {
            margin-top: 80px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .credits-title {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 25px;
            color: #e3f2fd;
            text-align: center;
        }
        
        .credits-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            text-align: center;
        }
        
        .credits-group {
            text-align: center;
        }
        
        .credits-group h4 {
            color: #b3e5fc;
            font-size: 0.95em;
            margin-bottom: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .credits-names {
            font-size: 0.9em;
            line-height: 1.8;
            color: #e3f2fd;
            margin-bottom: 20px;
        }
        
        .credits-names .author {
            display: block;
            margin: 6px 0;
        }
        
        .credits-university {
            text-align: center;
        }
        
        .credits-university h4 {
            color: #b3e5fc;
            font-size: 0.95em;
            margin-bottom: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .university-info {
            font-size: 0.9em;
            line-height: 1.8;
            color: #e3f2fd;
        }
        
        .university-info .info-line {
            display: block;
            margin: 6px 0;
        }
    </style>
</head>
<body>
    <!-- ==================== SECCIÓN PRINCIPAL / HERO ==================== -->
    <!-- Página de bienvenida con presentación de los dos métodos -->
    <!-- Incluye: Título, subtítulo, comparación rápida, enlace a sistema completo -->
    
    <div class="hero-container">
        <h1>Métodos Iterativos</h1>
        <p class="subtitle">Comparación detallada entre Jacobi y Gauss-Seidel para resolver sistemas de ecuaciones lineales</p>
        
        <!-- ==================== TARJETAS COMPARATIVAS ==================== -->
        <!-- Muestra características principales de cada método lado a lado -->
        <div class="diferencias">
            <div class="metodo-card">
                <h3>Método de Jacobi</h3>
                <ul>
                    <li>Actualizaciones simultáneas de variables</li>
                    <li>Altamente paralelizable</li>
                    <li>Convergencia más lenta</li>
                    <li>Independencia de dependencias</li>
                    <li>Ideal para computación paralela</li>
                </ul>
            </div>
            
            <div class="metodo-card">
                <h3>Método de Gauss-Seidel</h3>
                <ul>
                    <li>Actualizaciones secuenciales de variables</li>
                    <li>No es paralelizable</li>
                    <li>Convergencia más rápida</li>
                    <li>Usa valores más actualizados</li>
                    <li>Mejor para sistemas generales</li>
                </ul>
            </div>
        </div>
        
        <!-- ==================== BOTÓN DE LLAMADA A ACCIÓN ==================== -->
        <!-- Enlace para acceder al sistema completo de comparación -->
        <div class="cta-container">
            <a href="bienvenida.php" class="cta-button">Explorar el Sistema Completo</a>
        </div>
        
        <!-- ==================== SECCIÓN DE CRÉDITOS ==================== -->
        <!-- Información de autores y contexto académico del proyecto -->
        <div class="credits-section">
            <div class="credits-title">Autoría y Contexto Académico</div>
            
            <div class="credits-content">
                <div class="credits-group">
                    <h4>Autores del Proyecto</h4>
                    <div class="credits-names">
                        <span class="author">Quiroz Coila Ariadne Checcid</span>
                        <span class="author">Rocha Rivero Jose Leonardo</span>
                        <span class="author">Laura Rios Lizbeth Fabiola</span>
                        <span class="author">Callisaya Vargas Marco Ronaldo</span>
                    </div>
                </div>
                
                <div class="credits-university">
                    <h4>Información Académica</h4>
                    <div class="university-info">
                        <span class="info-line"><strong>Materia:</strong> Métodos Numéricos I</span>
                        <span class="info-line"><strong>Tema:</strong> Sistema Comparativo Jacobi vs Gauss-Seidel</span>
                        <span class="info-line"><strong>Carrera:</strong> Informática</span>
                        <span class="info-line"><strong>Universidad:</strong> Universidad Mayor de San Andrés (UMSA)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
