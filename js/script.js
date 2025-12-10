/**
 * SCRIPT.JS - LÓGICA DEL CLIENTE
 * ============================
 * Manejo de eventos y comportamiento dinámico de la aplicación web
 * 
 * FUNCIONALIDADES:
 * - Validación de entrada (lado cliente)
 * - Manejo de eventos de formulario
 * - Comunicación AJAX con backend PHP
 * - Visualización de gráficas con Chart.js
 * - Renderizado de matrices con MathJax
 * 
 * DEPENDENCIAS EXTERNAS:
 * - Chart.js 3.9 (gráficas de convergencia)
 * - MathJax 3.2 (renderizado de matrices LaTeX)
 */

// Esperar a que DOM esté completamente cargado antes de ejecutar
document.addEventListener('DOMContentLoaded', function(){
  // Obtener referencia al formulario principal
  const form = document.getElementById('sistemaForm');
  
  // Si el formulario no existe en esta página, retornar
  if (!form) return;
  
  // Agregar listener de envío de formulario
  form.addEventListener('submit', function(e){
    // El navegador manejará el envío POST hacia el backend PHP
    // Backend procesará: validación, ejecución de algoritmos, comparación
  });
});

