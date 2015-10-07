/* requiere jqery */
/* variables globales para capturar el id al click de cada radio */

$id_producto=0; /* el signo $ es para que se vean dentro de la clase jquery */
$id_modelo=0;

jQuery(function ($) {

/*   	Busqueda del Producto 		*/
$('#patronProducto').keyup('click keypress resize', function (event) { 

var patron = this.value;

var campo = $('#campo option:selected').val();


/* familia, marca y modelo son claves foraneas, en el combo de seleccion usamos  comodin.nombre  que es lo que ira en la variable campo */
var sql  ="select p.id as id, p.codigo_barra as codigo, f.nombre as familia, mc.nombre as marca, m.nombre as modelo, p.nombre as nombre";
sql +=" from producto as p, modelo as m, marca as mc, familia as f ";
sql +=" where p.id_familia=f.id and mc.id=p.id_marca and m.id=p.id_modelo ";
sql +="and " + campo + " like '%" + patron + "%'";


var parametros = {	
					"metodo": "listarRadio", 
					"sql" : sql,
                	"tabla" : "producto",
                	"titulos": true	
                };

$.ajax({
                data:  parametros,
                url:   'inc/data_ajax_jquery.php',
                dataTypes: 'json',
                type:  'post',
                beforeSend: function () {
                        $("#lstProductos").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#lstProductos").html(response);
                }
        });

});

/* Busqueda de modelos */
$('#patronModelo').keyup('click keypress resize', function (event) { 

var patron = this.value;


/* familia, marca y modelo son claves foraneas, en el combo de seleccion usamos  comodin.nombre  que es lo que ira en la variable campo */
var sql  ="select * from modelo where nombre like '%" + patron + "%'";


var parametros = {	
			"metodo": "listarRadio",
			"sql" : sql,
                	"tabla" : "modelo",
                	"titulos": false,
                };

$.ajax({
                data:  parametros,
                url:   'inc/data_ajax_jquery.php',
                dataTypes: 'json',
                type:  'post',
                beforeSend: function () {
                        $("#lstModelos").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#lstModelos").html(response);
                }
        });

});

// vinculación de producto - moelo
$('#btnVincular').click('click', function (event) { 

if ($id_producto==0 && $id_modelo==0)
	{
	alert("Primero seleccione un producto y un modelo");
	return false;
	}

var parametros = {	
					"metodo": "relacionar",
					"idProducto": $id_producto,
					"idModelo": $id_modelo
                };

$.ajax({
                data:  parametros,
                url:   'inc/data_ajax_jquery.php',
                dataTypes: 'json',
                type:  'post',
                success:  function (response) {
                        $("#lstVinculados").html(response);
                }
        });

});

});

function mostrar(id)
{
	
	var parametros = {	
					"metodo": "mostrarRelacionados",
					"idProducto": id,
                                        "link"  : false 
                };

$.ajax({
                data:  parametros,
                url:   'inc/data_ajax_jquery.php',
                dataTypes: 'json',
                type:  'post',
                success:  function (response) {
                        $("#lstVinculados").html(response);
                }
        });

}

// Borra la relacion producto modelo 
function borrarModelo(id, idP)
{

if (confirm("seguro que desea eliminar esta relacion?"))
	{
		
		var parametros = {	
						"metodo": "borrarRelacion",
						"idRel": id,
						"idP": idP
	                };

		$.ajax({
	                data:  parametros,
	                url:   'inc/data_ajax_jquery.php',
	                dataTypes: 'json',
	                type:  'post',
	                success:  function (response) {
	                        $("#lstVinculados").html(response);
	                }
	        });
	}

}