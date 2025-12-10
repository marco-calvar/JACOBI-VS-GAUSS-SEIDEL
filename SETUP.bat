@echo off
REM ===============================================
REM SETUP.BAT - SCRIPT INSTALACIÓN WINDOWS CMD
REM Sistema Comparativo Jacobi vs Gauss-Seidel
REM
REM Uso: Doble clic en SETUP.bat o: cmd /c SETUP.bat
REM ===============================================

setlocal enabledelayedexpansion
chcp 65001 >nul

cls
echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║  INSTALACIÓN AUTOMATIZADA - SISTEMA COMPARATIVO           ║
echo ║  Jacobi vs Gauss-Seidel - Métodos Numéricos I             ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo.

REM PASO 1: Verificar PHP
echo PASO 1: Verificación del Entorno
echo ─────────────────────────────────────────────────────────────
echo.
echo Verificando PHP... 
php -v >nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo [✓] PHP encontrado
    for /f "tokens=*" %%A in ('php -v ^| findstr /R "PHP"') do echo     %%A
) else (
    echo [✗] ERROR: PHP no está instalado o no está en PATH
    echo Descárgalo desde: https://www.php.net/downloads
    pause
    exit /b 1
)

REM PASO 2: Verificar Git (opcional)
echo.
echo Verificando Git (opcional)... 
git --version >nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo [✓] Git encontrado
    git --version
) else (
    echo [i] Git no instalado (es opcional)
)

REM PASO 3: Verificar archivos críticos
echo.
echo.
echo PASO 2: Verificación de Integridad del Proyecto
echo ─────────────────────────────────────────────────────────────
echo.

set "archivos_ok=1"

REM Lista de archivos críticos
if exist "index.php" (echo [✓] index.php) else (echo [✗] index.php FALTA & set "archivos_ok=0")
if exist "bienvenida.php" (echo [✓] bienvenida.php) else (echo [✗] bienvenida.php FALTA & set "archivos_ok=0")
if exist "sistema_comparativo.php" (echo [✓] sistema_comparativo.php) else (echo [✗] sistema_comparativo.php FALTA & set "archivos_ok=0")
if exist "clases\Jacobi.php" (echo [✓] clases\Jacobi.php) else (echo [✗] clases\Jacobi.php FALTA & set "archivos_ok=0")
if exist "clases\GaussSeidel.php" (echo [✓] clases\GaussSeidel.php) else (echo [✗] clases\GaussSeidel.php FALTA & set "archivos_ok=0")
if exist "clases\Validador.php" (echo [✓] clases\Validador.php) else (echo [✗] clases\Validador.php FALTA & set "archivos_ok=0")
if exist "clases\Comparador.php" (echo [✓] clases\Comparador.php) else (echo [✗] clases\Comparador.php FALTA & set "archivos_ok=0")
if exist "clases\AnalizadorAvanzado.php" (echo [✓] clases\AnalizadorAvanzado.php) else (echo [✗] clases\AnalizadorAvanzado.php FALTA & set "archivos_ok=0")
if exist "clases\CasosPrueba.php" (echo [✓] clases\CasosPrueba.php) else (echo [✗] clases\CasosPrueba.php FALTA & set "archivos_ok=0")
if exist "css\estilos.css" (echo [✓] css\estilos.css) else (echo [✗] css\estilos.css FALTA & set "archivos_ok=0")
if exist "js\script.js" (echo [✓] js\script.js) else (echo [✗] js\script.js FALTA & set "archivos_ok=0")

if "!archivos_ok!"=="0" (
    echo.
    echo [ERROR] Faltan archivos críticos del proyecto.
    pause
    exit /b 1
)

echo.
echo PASO 3: Iniciar Servidor PHP Integrado
echo ─────────────────────────────────────────────────────────────
echo.

set "puerto=8000"
cd /d "%~dp0"

echo Iniciando servidor PHP...
echo   Puerto: %puerto%
echo   Raíz: %cd%
echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║ ACCEDER A LA APLICACIÓN:                                  ║
echo ║ ➤ http://localhost:%puerto%/                                     ║
echo ║                                                            ║
echo ║ Para detener: Presiona Ctrl+C                             ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

php -S "localhost:%puerto%"
if %ERRORLEVEL% neq 0 (
    echo.
    echo [ERROR] No se pudo iniciar el servidor PHP
    pause
    exit /b 1
)
