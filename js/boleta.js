var indiceProducto=0;
window.onload=function()
{	
	updateClock();
	setInterval('updateClock()', 1000 );

	var indiceActual=0;

	var ar_selects= document.getElementsByTagName("select");
	// Asignar manejador de evento on click a los selects para que seleccionen toda la fila
	for (a=0;a<ar_selects.length;a++)
	{
	// excluirselect 
	if(ar_selects[a].id!='listaProductos' )
			{
			ar_selects[a].onchange=function()
					{
					indiceActual=this.selectedIndex;
					for (a=0;a<ar_selects.length;a++)
						{
						ar_selects[a].selectedIndex=indiceActual;
						}
					this.blur();
					}
			}
	}

	// Manejadores de evento para llenar cliente y factura
	var b = document.getElementById("del");

	b.addEventListener('click', elimina, false);

}

// Llena el input text de giros y fija el id del hidden
function fijarGiro(id, nombre){
	document.getElementById('id_giro').value = id;
	document.getElementById('giro').value = nombre;

}

// Busca el producto a partir del codigo leido y lo pone en el detalle de la factura
function llenarFactura()
	{
		var cod = document.getElementById('codBarra').value;; // Codigo  ej: 1111
		var data = cod ;
		var miajax = new ajax("llenarFactura",data,"detalle");
		document.getElementById('codBarra').value='';
		return false;
	}


//elimina un producto de la factura
function elimina()
{
var ind= document.forms[0].uni.selectedIndex;	
document.forms[0].uni.options[ind] = null;
document.forms[0].detalle.options[ind] = null;
document.forms[0].sTotal.options[ind] = null;
document.forms[0].Total.options[ind] = null;
calcular();
}

// Suma los totales y calcula iva, neto y total
function calcular()
	{
	var total=0;
	var iva=0;
	var neto=0;
	var largo = document.forms[0].Total.options.length;

	for (x=0;x<largo;x++)
		{
		neto += parseFloat(document.forms[0].Total.options[x].value).round(2);
		}
	iva = parseFloat(neto * 0.16).round(2);
	total = (neto + iva).round(2);
	document.getElementById('txNeto').value = neto;
	document.getElementById('txIva').value = iva;
	document.getElementById('txTotal').value = total;
	}


//Deja seleccionada la opcion del select name=combo que tiene value=valor 
function fijarOpcion( valor, combo )
	{
	eval( "var n= document.forms[0]." + combo + ".options.length");

	var idC='';
	for(x=0;x<n;x++)
		{
		eval ( "idC = document.forms[0]." + combo + ".options[" + x + "].value;" );
		// alert( idC +"=="+ valor );
		if ( idC == valor )
			{

			eval ( "document.forms[0]." + combo + ".options[" + x + "].selected = true;" );
			}
		}
	}

// Busca un id de producto en el combo de detalle, si lo encuentra fija el indice y retorna true, si no retorna false
function buscarProducto(idp)
{
var largo = document.forms[0].detalle.options.length;
for (x=0;x<largo;x++)
	{

	if (idp==document.forms[0].detalle.options[x].value)
		{
		indiceProducto= x; // global
		return true;
		}	
	}
return false;
}

function updateClock ( )
{
  var currentTime = new Date ( );

  var currentHours = currentTime.getHours ( );
  var currentMinutes = currentTime.getMinutes ( );
  var currentSeconds = currentTime.getSeconds ( );

  // Pad the minutes and seconds with leading zeros, if required
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

  // Choose either "AM" or "PM" as appropriate
  var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

  // Convert the hours component to 12-hour format if needed
  currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

  // Convert an hours component of "0" to "12"
  currentHours = ( currentHours == 0 ) ? 12 : currentHours;

  // Compose the string for display
  var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

  // Update the time display
  document.getElementById("clock").innerHTML = currentTimeString;
}

function generarBoleta(){
var n= document.forms[0].uni.options.length;
for (a=0;a<n;a++){	
					document.forms[0].uni.options[a].selected = true;
					document.forms[0].detalle.options[a].selected = true;
					document.forms[0].sTotal.options[a].selected = true;
					document.forms[0].Total.options[a].selected = true;
					}
	document.getElementById('frmFactura').submit();
}


// Objeto ajax para llenar la factura o los datos de cliente
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

request.onreadystatechange = function(elEvento){

var evento = elEvento || window.event;
if (request.readyState == 4)
	{

    if (request.status == 200)
		{
		
			if (receptor=='detalle')
			{
			/* 
			Llenamos el detalle de la factura 1111
			request.responseText recibe una estructura json con los datos
			*/
			var jsDatos = request.responseText;
			
			 //alert(jsDatos);

			if (jsDatos.length>0)
				{
				
				prod = JSON.parse(jsDatos);

				var largo =document.forms[0].uni.options.length;
				//var cantidad= parseFloat(document.getElementById('cant').value).round(2);
				if(buscarProducto(prod.id))
					{
					// sumamos y calculamos total					
					var nr = parseFloat(document.forms[0].uni.options[indiceProducto].value).round(2);
					nr+=1;
					var precio = parseFloat(document.forms[0].sTotal.options[indiceProducto].value).round(2);
					var total = (nr * precio).round(2); 
					document.forms[0].uni.options[indiceProducto] = new Option (nr, nr);
					document.forms[0].Total.options[indiceProducto] = new Option(total, total);
					
					}else{

					// agregamos el nuevo producto

					document.forms[0].uni.options[largo] = new Option(1,1);
					document.forms[0].uni.size= largo + 1;

					document.forms[0].detalle.options[largo] = new Option(prod.nombre,prod.id);
					document.forms[0].detalle.size=largo + 1;
					
					document.forms[0].sTotal.options[largo] = new Option(prod.precio,prod.precio);
					document.forms[0].sTotal.size=largo + 1;
					
					document.forms[0].Total.options[largo] = new Option(prod.precio,prod.precio);
					document.forms[0].Total.size=largo + 1;

					}
				calcular();
				} 
			}

		
		}else if (request.status == 404){
			
			alert("Url no existe:" + url);

			}else{
			
			alert("Error: El codigo de estado es" + request.status );
		}
	}
}
request.send(null);
}

Number.prototype.round = function(p) {
  p = p || 10;
  return parseFloat( this.toFixed(p) );
};