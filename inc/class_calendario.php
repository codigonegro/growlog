<?php
class calendario
{
var $dia, $mes, $ano;
var $tipo_semana; // 1 para abreviada 0 para nombre completo
var $tipo_mes;    // 1 para abreviada 0 para nombre completo

var $MESCOMPLETO = array();
var $MESABREVIADO = array();
var $SEMANACOMPLETA = array();
var $SEMANAABREVIADA = array();
var $ARRMES = array();
var $ARRDIASSEMANA = array();

var $idCliente;
var $idRecurso;

function __construct()
	{
	// valores por defecto
	$this->tipo_mes=0;
	$this->tipo_semana=0;

	$this->MESCOMPLETO[1] = 'Enero';
	$this->MESCOMPLETO[2] = 'Febrero';
	$this->MESCOMPLETO[3] = 'Marzo';
	$this->MESCOMPLETO[4] = 'Abril';
	$this->MESCOMPLETO[5] = 'Mayo';
	$this->MESCOMPLETO[6] = 'Junio';
	$this->MESCOMPLETO[7] = 'Julio';
	$this->MESCOMPLETO[8] = 'Agosto';
	$this->MESCOMPLETO[9] = 'Septiembre';
	$this->MESCOMPLETO[10] = 'Octubre';
	$this->MESCOMPLETO[11] = 'Noviembre';
	$this->MESCOMPLETO[12] = 'Diciembre';

	$this->MESABREVIADO[1] = 'Ene';
	$this->MESABREVIADO[2] = 'Feb';
	$this->MESABREVIADO[3] = 'Mar';
	$this->MESABREVIADO[4] = 'Abr';
	$this->MESABREVIADO[5] = 'May';
	$this->MESABREVIADO[6] = 'Jun';
	$this->MESABREVIADO[7] = 'Jul';
	$this->MESABREVIADO[8] = 'Ago';
	$this->MESABREVIADO[9] = 'Sep';
	$this->MESABREVIADO[10] = 'Oct';
	$this->MESABREVIADO[11] = 'Nov';
	$this->MESABREVIADO[12] = 'Dic';


	$this->SEMANACOMPLETA[0] = 'Lunes';
	$this->SEMANACOMPLETA[1] = 'Martes';
	$this->SEMANACOMPLETA[2] = 'Miércoles';
	$this->SEMANACOMPLETA[3] = 'Jueves';
	$this->SEMANACOMPLETA[4] = 'Viernes';
	$this->SEMANACOMPLETA[5] = 'Sábado';
	$this->SEMANACOMPLETA[6] = 'Domingo';


	$this->SEMANAABREVIADA[0] = 'Lun';
	$this->SEMANAABREVIADA[1] = 'Mar';
	$this->SEMANAABREVIADA[2] = 'Mie';
	$this->SEMANAABREVIADA[3] = 'Jue';
	$this->SEMANAABREVIADA[4] = 'Vie';
	$this->SEMANAABREVIADA[5] = 'Sab';
	$this->SEMANAABREVIADA[6] = 'Dom';

	// Verificamos si es con nombres completos o abreviados
	if($this->tipo_semana == 0)
		{
		$this->ARRDIASSEMANA = $this->SEMANACOMPLETA;
		}elseif($this->tipo_semana == 1){
		$this->ARRDIASSEMANA = $this->SEMANAABREVIADA;
		}

	if($this->tipo_mes == 0)
		{
		$this->ARRMES = $this->MESCOMPLETO;
		}elseif($this->tipo_mes == 1){
		$this->ARRMES = $this->MESABREVIADO;
		}

	}

function despliegue()
	{
	
		// Verificamos si se ha enviado un dia, mes o año. Caso contrario usar fecha actual
		if((!$this->dia) || strlen($this->dia)==0){ $this->dia = date(d);  }
		if((!$this->mes) || strlen($this->mes)==0){ $this->mes = date(n); }
		if((!$this->ano) || strlen($this->mes)==0){ $this->ano = date(Y); }

		// Calculamos los parámetros del mes actual
		$TotalDiasMes = date(t,mktime(0,0,0,$this->mes,$this->dia,$this->ano));
		$diaSemanaEmpiezaMes = date(w,mktime(0,0,0,$this->mes,1,$this->ano));
		$diaSemanaTerminaMes = date(w,mktime(0,0,0,$this->mes,$TotalDiasMes,$this->ano));
		$EmpiezaMesCalOffset = $diaSemanaEmpiezaMes;
		$TerminaMesCalOffset = 6 - $diaSemanaTerminaMes;
		$TotalDeCeldas = $TotalDiasMes + $diaSemanaEmpiezaMes + $TerminaMesCalOffset;

		// Ajustes para los parametros de los enlaces de navegación
		if($this->mes == 1){
		$mesAnterior = 12;
		$mesSiguiente = $this->mes + 1;
		$anoAnterior = $this->ano - 1;
		$anoSiguiente = $this->ano;
		}elseif($this->mes == 12){
		$mesAnterior = $this->mes - 1;
		$mesSiguiente = 1;
		$anoAnterior = $this->ano;
		$anoSiguiente = $this->ano + 1;
		}else{
		$mesAnterior = $this->mes - 1;
		$mesSiguiente = $this->mes + 1;
		$anoAnterior = $this->ano;
		$anoSiguiente = $this->ano;
		$anoAnteriorAno = $this->ano - 1;
		$anoSiguienteAno = $this->ano + 1;
		}

		// Generación de los encabezados (Mes actual y enlaces de navegación)
		$salida .= "<table>";

		$salida .= " <tr >";

		$salida .= " <td colspan=\"7\" align=\"center\">";

		$salida .= " <table>";

		$salida .= " <tr>";

		$salida .= " <td><a href=\"". $PHP_SELF ."?mes=" . $this->mes . "&ano=" . $anoAnteriorAno. "&idR=$this->idRecurso\"><img src=\"ico/backward.png\" border=\"0\"></a></td>";

		$salida .= " <td><a href=\"". $PHP_SELF ."?mes=" . $mesAnterior . "&ano=" . $anoAnterior . "&idR=$this->idRecurso\"><img src=\"ico/back.png\" border=\"0\"></a></td>";

		$salida .= " <td><b>". $this->ARRMES[$this->mes]. " - $this->ano</b></td>";

		$salida .= " <td><a href=\"".$PHP_SELF."?mes=$mesSiguiente&ano=$anoSiguiente" . "&idR=$this->idRecurso\"><img src=\"ico/play.png\" border=0></a></td>";

		$salida .= " <td><a href=\"".$PHP_SELF."?mes=$this->mes&ano=$anoSiguienteAno&idR=$this->idRecurso\"><img src=\"ico/forward.png\" border=\"0\"></a></td>";

		$salida .= " </tr>";

		$salida .= " </table>";

		$salida .= " </td>";

		$salida .= "</tr>";


		$salida .= "<tr>";

		// Fila con los días de la semana

		foreach($this->ARRDIASSEMANA AS $key)
		{
		$salida .= "<td>$key</td>";
		}

		$salida .= "</tr>";

		// Dias del mes
		for($a=1;$a<= $TotalDeCeldas;$a++){ // $a solo cuenta las celdas

		if(!$b) {$b = 0;} // contador de dias semana

		if($b == 7) {$b = 0;}

		if($b == 0) {$salida .= '<tr>'; }

		if(!$c) {$c = 1;}// contador dias del mes

		if($a >= $EmpiezaMesCalOffset AND $c <= $TotalDiasMes)

			{
			$fechaActual =$c ."-" . $this->mes. "-" .$this->ano;	
			$salida .= "<td class=\"dia\" id=\"d$fechaActual". "d" .$this->idRecurso ."d". $this->idCliente ."\">		$c	</td>";
			$c++;
			}else{
			$salida .= "<td> &nbsp;</td>";
			}

		if($b == 6) $salida .= '</tr>';
		$b++;
		}
		$salida .= "</table>";

		return($salida);
	}

}
?>