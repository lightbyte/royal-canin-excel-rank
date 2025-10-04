import './bootstrap';

// Funcionalidad de filtro y scroll
document.addEventListener('DOMContentLoaded', function() {
    const filtroInput = document.getElementById('filtro-codigo');
    const filas = document.querySelectorAll('.ranking-row');

    if (filtroInput == undefined) {
        return;
    }
    
    // Función de filtro
    function filtrarTabla() {
        const filtro = filtroInput.value.toLowerCase().trim();
        
        filas.forEach(fila => {
            const codigo = fila.dataset.codigo;
            if (codigo.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }
    
    // Función para ir a mi posición
    function irAMiPosicion() {
        const miClinica = document.getElementById('mi-clinica');
        if (miClinica) {
            miClinica.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            // Resaltar temporalmente
            miClinica.classList.add('bg-blue-100');
            setTimeout(() => {
                miClinica.classList.remove('bg-blue-100');
            }, 2000);
        }
    }
    
    // Event listeners
    filtroInput.addEventListener('keyup', filtrarTabla);
    
    
    // Auto-scroll al cargar la página
    setTimeout(irAMiPosicion, 500);
    
});