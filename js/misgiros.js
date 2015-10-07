window.onload= function()
	{

	// Ajustamos el tamaño de los selects a su contenido
	 document.getElementById('id_rubro').size = document.getElementById('id_rubro').options.length;
	 document.getElementById('id_giro').size = document.getElementById('id_giro').options.length;
	
	// Manejar Giro
	if ( document.getElementById( "rubro" )) 
		{
		obj= document.getElementById('id_rubro');
		obj.onchange=function()
			{
			var sql="select id, nombre from giro where id_rubro=" + this.value +" order by nombre" ;
			var miajax = new ajax("opcionesSelectJson",sql,"id_giro");
			document.getElementById('id_giro').size = document.getElementById('id_giro').options.length;
			}
		}

	// Filtrar rubros
	document.getElementById("filtro").onkeyup = function(){
		
		if (this.value.length>0)
			{

			var sql = "select id, nombre from rubro where nombre like '%" + this.value + "%' order by nombre";
			
			var miajax = new ajax("opcionesSelectJson", sql , "id_rubro");
			
			document.getElementById('id_rubro').size = document.getElementById('id_rubro').options.length;
			}
	
		}
	// Guardar giros frecuentes
	document.getElementById('id_giro').onchange=function(){
	// guardar el giro en tabla misGiros(id, codigo, nombre, id_rubro) y refrescar el combo id_misGiros
		var miajax = new ajax('copiarGiro', this.value, 'id_misGiros');
		}	

	// BorrarGiro
		document.getElementById('id_misGiros').onchange=function(){
		var miajax = new ajax('borrarGiro', this.value, 'id_misGiros');
		}

	}// fin onload


/* AJAX */

/* 
Llena las opciones de un combo cualquiera, al crear este objeto se 
debe llamar al metodo opcionesSelectJson de data_ajax.php,
pasar la sentencia sql en datos e indicar el id del combo en receptor
*/
var ajax = function(metodo, datos, receptor){
request = false;

try {
request = new XMLHttpRequest();
} catch (trymicrosoft) {
	try {
	request = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (othermicrosoft) {
		try {
		request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (failed) {
		request = false;
		}
	}
}

if (!request){	 alert("Error inicializando XMLHttpRequest!"); return false; }

var url = "inc/data_ajax.php?metodo=" + metodo +"&dato=" + datos;

request.open("GET", url, true);

request.onreadystatechange = function(elEvento)
	{
	var evento = elEvento || window.event;

	if (request.readyState == 4)
		{
	    if (request.status == 200)
			{
				
				eval ("var miSelect = document.getElementById('id_" + receptor + "');");
				
				var jsDatos = request.responseText;

				if (jsDatos.length>0)
					{
					
					op = JSON.parse(jsDatos);
					
					var miSelect = document.getElementById(receptor);
					
					// borrar contenido del select
					while(miSelect.options.length >= 1)
						{
						miSelect.options[0] = null;
						}
						
					// recorrer el op y crear las opciones
					for (x=0;x<op.opciones.length; x++)
						{	
						 miSelect.options[x] = new Option(op.opciones[x].text, op.opciones[x].value);	
						}
					miSelect.size=x;
					}
				
			}
		}
	
	}
request.send(null);
}

function borrar(url)
{
	if (confirm('Esta seguro que quiere borrar este registro?'))
	{
		window.location=url;
	}
}
