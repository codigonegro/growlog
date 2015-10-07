var indiceProducto=0;
window.onload=function()
{	

	updateClock();
	setInterval('updateClock()', 1000 );


	$('#id_region').on('change', function() {

		var sql='select id, nombre from comuna where id_region=' + this.value;

		var miajax = new ajax("opcionesSelectJson",sql,"id_comuna");
	  
	});



	$("#btnGuardar").on('click', function(){
		var n= document.forms[0].uni.options.length;
		for (a=0;a<n;a++){	
							document.forms[0].uni.options[a].selected = true;
							document.forms[0].detalle.options[a].selected = true;
							document.forms[0].sTotal.options[a].selected = true;
							document.forms[0].Total.options[a].selected = true;
							}
	 	document.getElementById('frmFactura').submit();
		});

	// Asignar manejador de evento on click a los selects para que seleccionen toda la fila del producto

	var indiceActual=0;
	var ar_selects= document.getElementsByTagName("select");
	for (a=0;a<ar_selects.length;a++)
	{
	// Excluir los select de cliente, municipio, estado y localidad
	if(ar_selects[a].id!='id_region' && ar_selects[a].id!='id_comuna' && ar_selects[a].id!='listaProductos' &&ar_selects[a].id!='pago')
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

	// Manejadores de evento para llenar cliente y factura probar copiando y pegando : 0001 y 0002
	
	var r = document.getElementById("rut");
	var b = document.getElementById("del");


	r.addEventListener('blur', llenarCliente, false);
	b.addEventListener('click', elimina, false);

}

// Llena el input text de giros y fija el id del hidden
function fijarGiro(id, nombre){
	document.getElementById('id_giro').value = id;
	document.getElementById('giro').value = nombre;

}


// Busca el producto a partir del codigo leido y lo pone en el detalle de la factura
function llenarFactura(cod)
	{
		//alert("test");

//		var cod = document.getElementById('codBarra').value;; // Codigo  ej: 1111
		var idC= document.getElementById('id_cliente').value;
		var data = cod ;
		
		var miajax = new ajax("llenarFactura",data,"detalle");
		this.value= '';
	
	}

// Busca el cliente a partir del rfc y llena el formulario con sus datos
function llenarCliente()
	{
		if(validaRut(this)){
		r = this.value; // rfc
		if(r.length>0)
		{
		// Nota: todos los subclientes van en la misma tabla
		var miajax = new ajax("llenarCliente",r,"cliente");
		// this.value= '';
		return false;
		}else{
			alert("Primero digite el rut del cliente or favor");
		return false;
		}
	alert("Rut incorrecto");
	}
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
		
		if (receptor=='id_comuna')
		{

		var jsDatos = request.responseText;
		 // alert(jsDatos);
		
			if (jsDatos.length>0)
				{
				
				op = JSON.parse(jsDatos);
				
				// crear el objeto select a partir del id que viene en "receptor"
				var miSelect= document.getElementById(receptor);

				// borrar contenido del select
				while(miSelect.options.length > 1)
					{
					miSelect.options[0] = null;
					}
					 
				miSelect.options[0] = new Option('Elija:', 0);					
		
				// recorrer el op y crear las opciones
				for (x=1;x<op.opciones.length; x++)
					{	
					 miSelect.options[x] = new Option(op.opciones[x].text, op.opciones[x].value);	
					}
				}

		
		}


		if (receptor=='detalle')
			{
			/* 
			Llenamos el detalle de la factura 1111
			request.responseText recibe una estructura json con los datos
			*/
			var jsDatos = request.responseText;
			
			console.log(jsDatos);

			if (jsDatos == 0 || jsDatos=='0' ){ alert("sin stock"); return false; }
		

			if (jsDatos.length>0)
				{
				
				prod = JSON.parse(jsDatos);

				var largo =document.forms[0].uni.options.length;
				// var cantidad= parseFloat(document.getElementById('cant').value).round(2);
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

		if (receptor=='cliente'){
			
			/* 
			Llenamos los campos del cliente
			request.responseText recibe una estructura json con los datos
			*/

			var jsDatos = request.responseText;
			
			  console.log(jsDatos);

			if (jsDatos.length>0){
				cli = JSON.parse(jsDatos);
				document.getElementById("email").value = cli.email;
				document.getElementById("nombre").value = cli.nombres; 
				document.getElementById("fono").value= cli.fono;
				document.getElementById("rzSocial").value= cli.rzSocial;
				document.getElementById("direccion").value= cli.direccion;
				document.getElementById("id_cliente").value = cli.id;
				fijarOpcion(cli.idRegion, 'id_region');
				document.forms[0].id_comuna.options[0] = new Option(cli.comuna, cli.idComuna);
				}
			}

		if (receptor=='dropGiro'){

			document.getElementById('dropGiro').innerHTML=request.responseText;
		}

		if (receptor=='comuna')
		{

		 document.getElementById(receptor).innerHTML = request.responseText;
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


function validaRut(elem)
{

var rutIngresado= elem.value.replaceAll('.','');
elem.value=rutIngresado;
var arRut = elem.value.split("-");
var elRut = arRut[0];
var digito= arRut[1];
var tabla =elem.getAttribute("data-tbl");
var dvr = '0';
var	suma = 0;
var mul  = 2;

for (i= elRut.length-1; i>= 0; i--)
			{
			suma = suma + elRut.charAt(i) * mul;
			if (mul == 7) {	mul = 1; }
			mul++;
			}

		res = suma % 11;
		dvr = 11 - res;

		if (res==1)
			{
			dvr = 'k';
			}else if (res==0){
			dvr = '0';
			}
	if(digito != dvr) {
		document.getElementById("rut").value="";
		return(false);
		}else{
		return (true);
		}

}
// añadimos un metodo al objeto string que permite reemplazar todas las apariciones de una sub cadena
String.prototype.replaceAll = function(search, replace)
{
    if (replace === undefined) {
        return this.toString();
    }

    return this.replace(new RegExp('[' + search + ']', 'g'), replace);
};