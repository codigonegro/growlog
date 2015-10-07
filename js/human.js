var strKey='';
window.onload=function()
{
	document.getElementById("soyhumano").style.display='none';
	
	document.getElementById("btnHumano").onclick = function()
		{
		
		document.getElementById("soyhumano").style.display='block';

		document.getElementById("key").style.display='block';

		var ctx = document.getElementById('key').getContext('2d');
		
	  	ctx.fillStyle="#237533";
	  	
	  	ctx.font = "22px serif";
	  	
	  	ajax(ctx);
  	
		}

	document.getElementById('btnSubmit').onclick=function(){
		if (strKey == document.getElementById("desafio").value){
			document.forms[0].submit();
		}else{
			alert("el texto no coincide, intente de nuevo");

		}
	}
}

var ajax = function(obj){
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

var url = "inc/random.php";

request.open("GET", url, true);

request.onreadystatechange = function(elEvento)
	{
	var evento = elEvento || window.event;

	if (request.readyState == 4)
		{
	    if (request.status == 200)
			{

				obj.fillText(request.responseText, 10, 19);
				strKey=request.responseText;
							
			}
		}
	
	}
request.send(null);
}