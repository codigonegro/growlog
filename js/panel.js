/*
Evitar que se envie el formulario alpresionar enter

si existe el id rubro,  refrescar giro
si existe el id region, refrescar comuna
si existe el id rut, validar al perder foco
si existe el id email, validar al perder foco

*/

window.onload=function(){
// Controla el enter
document.onkeypress=manejarTecla;

// Validar Rut
if ( document.getElementById( "rut" )) 
{
	document.getElementById( "rut" ).onblur =  validaRut;
	document.getElementById( "rut" ).onfocus=function()
		{
		document.getElementById( "E_rut" ).innerHTML="";
		}
}

// Valida rut cuenta bancaria
if ( document.getElementById( "rut_cuenta" )) 
	{
	document.getElementById( "rut_cuenta" ).onblur =  validaRut;

	document.getElementById( "rut_cuenta" ).onfocus=function()
		{
		document.getElementById( "E_rut_cuenta" ).innerHTML="";
		}
}

// Manejar Comuna
if ( document.getElementById( "region" )) {
	obj= document.getElementById('id_region');
	obj.onchange=function(){
	var miajax = new ajax("llenarComunas",this.value,"comuna");
	}
}

// Manejar Giro
if ( document.getElementById( "rubro" )) {
	obj= document.getElementById('id_rubro');
	obj.onchange=function(){
	var miajax = new ajax("llenarGiros",this.value,"giro");
	}
}

// Manejar Subfamilias
if ( document.getElementById( "familia" )) {
	obj= document.getElementById('id_familia');
	obj.onchange=function(){
		 var miajax = new ajax("llenarSubfamilia",this.value,"subFamilia");
		}
	}
}

/*  Funciones */

function manejarTecla(elEvento)
{

var evento = elEvento || window.event; // obtención del evento, el or (||) es para que sea valido para IE y Mozilla
var caracter = evento.charCode || evento.keyCode; // idem para el caracter digitado

//  obtener el indice del siguiente elemento del formulario
nextInd = getIndicePorNombre(evento.target.name);
nextInd +=1;

// aqui asumimos que el nombre del botón submit es submit, si tiene otro nombre cambialo aquí
if (evento.target.name !='submit') // si no es submit
	{

		if (caracter==13)  // si la tecla es es enter
			{
			// pasamos el foco al siguiente elemento y retornamos false para que no se envie el formulario
			try{
			document.forms[0].elements[nextInd].focus();
			}catch(err){/* nada */}
			return false;
			}

	// aceptar solo teclas numericas  "-" =45 enter="13"
	if(evento.target.className =='int')
			{

			if( caracter==8 || caracter==45 || caracter==13 ){return (true);}

			if (caracter < 48 || caracter > 57)
				{ return false; }
			}

	}// fin if si no es submit
}

/* Retorna el indice del control de formulario cuyo nombre es pasado como argumento */
function getIndicePorNombre(nombre)
{
var test=0;
try{
	for (x=0;x<document.forms[0].elements.length ;x++ )
		{
		if(nombre == document.forms[0].elements[x].name)
			{ test=1; }
		}
	if(test==1)
		{
		for (a=0;a<document.forms[0].elements.length ;a++ )
			{
			if(nombre == document.forms[0].elements[a].name)
				{   	return(a);  	}
			}
		}
	if(test==0){
			for (b=0;b<document.forms[1].elements.length ;b++ )
				{
				if(nombre == document.forms[1].elements[b].name)
						{   	return(b);  	}
				}
		}
}catch(err){/* nada */}
}

function validaRut()
{

//alert(this.value.replace('.',''));
var rut= this.value;
rut= rut.replace(/\./g, '');
this.value=rut;
rutIngresado=rut;

var arRut = this.value.split("-");
var elRut = arRut[0];
var digito= arRut[1];
//var tabla =this.getAttribute("data-tbl");
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
		//alert("rut incorrecto " + dvr);
		this.value="";
		document.getElementById("E_"+this.name).innerHTML="Rut incorrecto";
		// document.forms[0].elements[getIndicePorNombre('rut')].focus();
		}else{
		var miajax = new ajax("buscarRut", rutIngresado, 'alert');
		}

}

/* AJAX */
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

		if(receptor=='alerta' || receptor=='alert')
			 {

			 if (request.responseText=='Este rut ya existe')
				 	{
				 	 document.getElementById('rut').value='';
				 	 document.forms[0].elements[getIndicePorNombre('rut')].focus();
				 	 alert("Error:" + request.responseText);
				 	}
		 receptor="";
		 }else if(receptor=='null'){
		 /* nada si es null */
		 }else{// si es otra cosa se trata de un divId
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

function borrar(url)
{
	if (confirm('Esta seguro que quiere borrar este registro?'))
	{
		window.location=url;
	}
}
