<?php 
function MathRoundMonto($monto) {
    
    // Si el monto tiene decimales menores a 0.50, redondear hacia abajo con 2 decimales
    if ($monto - floor($monto) < 0.50) {
        // Redondeo hacia abajo (2 decimales)
        return floor($monto * 100) / 100; 
    } 

    // Si el monto tiene decimales mayores o iguales a 0.50, redondear hacia arriba al entero más cercano
    else {
        // Redondeo hacia arriba (entero)
        return ceil($monto); 
    }
}