#!/bin/bash

#===============================================
# SETUP.SH - SCRIPT INSTALACIÓN LINUX/MAC
# Sistema Comparativo Jacobi vs Gauss-Seidel
#
# Uso: chmod +x SETUP.sh && ./SETUP.sh
# Bash script para configuración automática
#===============================================

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Mostrar encabezado
clear
echo -e "${CYAN}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║  INSTALACIÓN AUTOMATIZADA - SISTEMA COMPARATIVO           ║"
echo "║  Jacobi vs Gauss-Seidel - Métodos Numéricos I             ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo -e "${NC}"
echo ""

# PASO 1: Verificar PHP
echo -e "${YELLOW}PASO 1: Verificación del Entorno${NC}"
echo "─────────────────────────────────────────────────────────────"
echo ""

echo -n "Verificando PHP... "
if command -v php &> /dev/null; then
    echo -e "${GREEN}✓ ENCONTRADO${NC}"
    php -v | head -1 | sed 's/^/  /'
else
    echo -e "${RED}✗ NO ENCONTRADO${NC}"
    echo "  Instala PHP:"
    echo "  • macOS: brew install php"
    echo "  • Ubuntu/Debian: sudo apt-get install php"
    echo "  • Otros: https://www.php.net/downloads"
    exit 1
fi

# Verificar Git (opcional)
echo -n "Verificando Git (opcional)... "
if command -v git &> /dev/null; then
    echo -e "${GREEN}✓ ENCONTRADO${NC}"
    git --version | sed 's/^/  /'
else
    echo -e "${YELLOW}✗ NO ENCONTRADO (OPCIONAL)${NC}"
fi

# PASO 2: Verificar archivos críticos
echo ""
echo -e "${YELLOW}PASO 2: Verificación de Integridad del Proyecto${NC}"
echo "─────────────────────────────────────────────────────────────"
echo ""

ARCHIVOS=(
    "index.php"
    "bienvenida.php"
    "sistema_comparativo.php"
    "clases/Jacobi.php"
    "clases/GaussSeidel.php"
    "clases/Validador.php"
    "clases/Comparador.php"
    "clases/AnalizadorAvanzado.php"
    "clases/CasosPrueba.php"
    "css/estilos.css"
    "js/script.js"
)

TODOS_PRESENTES=true
for archivo in "${ARCHIVOS[@]}"; do
    if [ -f "$archivo" ]; then
        echo -e "${GREEN}  ✓ $archivo${NC}"
    else
        echo -e "${RED}  ✗ $archivo (FALTA)${NC}"
        TODOS_PRESENTES=false
    fi
done

if [ "$TODOS_PRESENTES" = false ]; then
    echo ""
    echo -e "${RED}ERROR: Faltan archivos críticos del proyecto.${NC}"
    exit 1
fi

# PASO 3: Iniciar servidor
echo ""
echo -e "${YELLOW}PASO 3: Iniciar Servidor PHP Integrado${NC}"
echo "─────────────────────────────────────────────────────────────"
echo ""

PUERTO=8000
RAIZ=$(pwd)

echo "Iniciando servidor PHP..."
echo "  Puerto: $PUERTO"
echo "  Raíz: $RAIZ"
echo ""
echo -e "${GREEN}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║ ACCEDER A LA APLICACIÓN:                                  ║"
echo "║ ➤ http://localhost:$PUERTO/                                     ║"
echo "║                                                            ║"
echo "║ Para detener: Presiona Ctrl+C                             ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo -e "${NC}"
echo ""

# Iniciar servidor
cd "$(dirname "$0")"
php -S "localhost:$PUERTO"

if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: No se pudo iniciar el servidor PHP${NC}"
    exit 1
fi
