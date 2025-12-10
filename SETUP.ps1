#===============================================
# SETUP.PS1 - SCRIPT INSTALACIÓN AUTOMATIZADA
# Sistema Comparativo Jacobi vs Gauss-Seidel
# 
# Uso: ./SETUP.ps1 o PowerShell -ExecutionPolicy Bypass -File SETUP.ps1
# Windows PowerShell Script para configuración automática
#===============================================

Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  INSTALACIÓN AUTOMATIZADA - SISTEMA COMPARATIVO           ║" -ForegroundColor Cyan
Write-Host "║  Jacobi vs Gauss-Seidel - Métodos Numéricos I             ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Verificar si se ejecuta como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")

Write-Host "PASO 1: Verificación del Entorno"
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Yellow
Write-Host ""

# Verificar PHP
Write-Host "Verificando PHP..." -NoNewline
try {
    $phpVersion = php -v 2>$null | Select-Object -First 1
    if ($phpVersion -match "PHP") {
        Write-Host " ✓ ENCONTRADO" -ForegroundColor Green
        Write-Host "  $phpVersion"
    } else {
        Write-Host " ✗ NO ENCONTRADO" -ForegroundColor Red
        Write-Host "  Instala PHP desde: https://www.php.net/downloads" -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host " ✗ NO ENCONTRADO" -ForegroundColor Red
    Write-Host "  Instala PHP desde: https://www.php.net/downloads" -ForegroundColor Yellow
    exit 1
}

# Verificar Git (opcional)
Write-Host "Verificando Git..." -NoNewline
try {
    $gitVersion = git --version 2>$null
    if ($gitVersion) {
        Write-Host " ✓ ENCONTRADO" -ForegroundColor Green
        Write-Host "  $gitVersion"
    } else {
        Write-Host " ✗ NO ENCONTRADO (OPCIONAL)" -ForegroundColor Yellow
    }
} catch {
    Write-Host " ✗ NO ENCONTRADO (OPCIONAL)" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "PASO 2: Verificación de Integridad del Proyecto"
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Yellow
Write-Host ""

# Archivos críticos
$archivoCriticos = @(
    "index.php",
    "bienvenida.php",
    "sistema_comparativo.php",
    "clases\Jacobi.php",
    "clases\GaussSeidel.php",
    "clases\Validador.php",
    "clases\Comparador.php",
    "clases\AnalizadorAvanzado.php",
    "clases\CasosPrueba.php",
    "css\estilos.css",
    "js\script.js"
)

$todosPresentes = $true
foreach ($archivo in $archivoCriticos) {
    $ruta = Join-Path (Get-Location) $archivo
    if (Test-Path $ruta) {
        Write-Host "  ✓ $archivo" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $archivo (FALTA)" -ForegroundColor Red
        $todosPresentes = $false
    }
}

if (-not $todosPresentes) {
    Write-Host ""
    Write-Host "ERROR: Faltan archivos críticos del proyecto." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "PASO 3: Iniciar Servidor PHP Integrado"
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Yellow
Write-Host ""

$puerto = 8000
$raizDocumentos = Get-Location

Write-Host "Iniciando servidor PHP..." -ForegroundColor Cyan
Write-Host "  Puerto: $puerto"
Write-Host "  Raíz: $raizDocumentos"
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║ ACCEDER A LA APLICACIÓN:                                  ║" -ForegroundColor Green
Write-Host "║ ➤ http://localhost:$puerto                                      ║" -ForegroundColor Green
Write-Host "║                                                            ║" -ForegroundColor Green
Write-Host "║ Para detener: Presiona Ctrl+C                             ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""

# Iniciar servidor
php -S "localhost:$puerto"
