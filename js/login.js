jQuery(document).ready(function () { 

	// evita la tecla punto y el enter
	$('#rut').keypress(function(event){
		
	    if (event.keyCode == 46 || event.keyCode == 13) 

	        event.preventDefault();

	  });
	

	// quita los puntos si los hubiera
	$('#rut').focusout(function(event){
		var rut= this.value;
		rut= rut.replace(/\./g, '');
		this.value=rut;
	});

// Fin de la funcion default del document
});