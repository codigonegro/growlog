window.onload=function()
{
/* Controlar el Teclado  */
document.onkeypress=validaChar;
}

function validaChar(elEvento)
{

var evento = elEvento || window.event; // obtención del evento, el or (||) es para que sea valido para IE y Mozilla

var caracter = evento.charCode || evento.keyCode; // idem para el caracter digitado
// bloqueamos el caracter - para que no usen el rut completo ni la fecha con guiones
if (caracter==45){return false;}
if (caracter==13){alert("Elija un criterio de búsqueda"); return false; }
}

