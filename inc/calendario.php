<?php
class calendario
{
var $anio;

var $meses = array("enero"=>31, "febrero"=>28, "marzo"=>31, "abril"=>30, "mayo"=>31, "junio"=>30, "julio"=>31, "agosto"=>31, "septiembre"=>30, "octubre"=>31, "noviembre"=>30, "diciembre"=>31);

function calendario()
	{
	// ajuste años bisiestos
	$this->anio = strftime("%Y", time());
	if ($this->esbisiesto() ){ $this->cambiar_bisiesto; }
	}

function esbisiesto()
	{
	if ( ($this->anio % 4 == 0) && (($this->anio % 100 != 0) || ($this->anio % 400 == 0)) )
		{return true;  }else {return false;}
	}

function cambiar_bisiesto()
	{
	$this->meses["febrero"]=29;
	}

function estemes()
	{
// ciclo for que genera la tabla del mes en curso
	}


}
?>