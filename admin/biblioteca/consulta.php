<?php

	if(isset($_POST['submit2'])){
	$fecha = $_POST['fecha'];
	$fecha_act = $_POST['fecha_act'];
	include("lpdf.php");
	//echo $fecha;
}
else
{
	?>
	<?
	session_start();
	include("../../config.php");
	
if($_SESSION['cambiar_clave']) {
	header('Location:'.'http://'.$dominio.'/intranet/clave.php');
}

registraPagina($_SERVER['REQUEST_URI'],$db_host,$db_user,$db_pass,$db);


	if((!(stristr($_SESSION['cargo'],'1') == TRUE)) and (!(stristr($_SESSION['cargo'],'c') == TRUE)) )
	{
		header("location:http://$dominio/intranet/salir.php");
		exit;
	}

include("../../menu.php");
include("menu.php");
	
//$datatables_activado = true;
	if(isset($_POST['submit1'])){
	$fecha = $_POST['fecha'];		
		?>
<div align="center">
<div class="page-header">
  <h2>Biblioteca del Centro <small> Consulta de Morosos</small></h1>
<p class="lead text-muted">Fecha elegida: <? echo $fecha;?></small></p>
</div>
<div class='container-fluid'>
  <div class="row">
  <div class="col-sm-8 col-sm-offset-2">
<form name="form1" action="edicion.php" method="post">
<table class='table table-striped' style='width: 90%;'>
<thead>
	<tr>
		<th><input type="checkbox" onClick="selectall(form1)" /></th>
		<th>Grupo </th>
		<th>Alumno </th>
		<th>T�tulo </th>
		<th width="90">Fecha dev. </th>
		<th> </th>
	</tr>
	</thead><tbody>
	<?
	$fecha_act = date('Y-m-d');
	$lista=mysql_query("select curso, apellidos, nombre, ejemplar, devolucion, amonestacion, id, sms from morosos where hoy='$fecha' and devolucion<'$fecha_act'  order by curso, apellidos asc");

	$n=0;
	while ($list=mysql_fetch_array($lista)){
	?>
	<tr>
	<? 
	if ($list[5]=='NO') { 
		$n+=1   
	?>
		<td style="text-align: center"><input type="checkbox" name="id[]"
			value="<? echo $list[6] ;?>" /></td>
		<td style="text-align: center"><? echo $list[0];   ?></td>
		<td><? echo $list[1].', '.$list[2];   ?></td>
		<td><? echo $list[3];   ?></td>
		<td style="text-align: center"><? echo $list[4];   ?></td>
		<td style="text-align: left" nowrap>
		<?
		if ($list[7] == "SI") {
			echo '<i class="fa fa-comment" style="margin-left:6px;" rel="tooltip" title="Se ha enviado SMS de advertencia"></i>';
		}
		?>
		</td>
			<? }
			else {?>
		<td style="text-align: center"></td>
		<td style="text-align: center"><? echo $list[0];   ?></td>
		<td><? echo $list[1].', '.$list[2];   ?></td>
		<td><? echo $list[3];   ?></td>
		<td style="text-align: center"><? echo $list[4];   ?></td>
		<td style="text-align: center"><i class="fa fa-check"></i></td>

		<? } ?>
	</tr>
	<?	}

	?>
</tbody></table>

	<? if($n==0){?>
	<br /><br />
<div align="center">
<div class="alert alert-info alert-block fade in"
	style="max-width: 500px;">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<h5>ATENCI�N:</h5>
Todos los alumnos de esta lista han sido registrados. Ahora s�lo podr�s
consultarla.</div>
</div>
	<? }
	else {?>
<hr>
<button class="btn btn-danger" type="submit" name="borrar" value="Borrar"><i class="fa fa-trash-o " data-bb='confirm-delete'></i> Borrar</button>
&nbsp;&nbsp; &nbsp;&nbsp;
<button class="btn btn-info" type="submit" name="sms" value="sms"><i class="fa fa-play-circle "></i> Enviar SMS</button>
&nbsp;&nbsp; &nbsp;&nbsp;
<button class="btn btn-warning" type="submit" name="registro" value="registro"><i class="fa fa-play-circle "></i> Registrar Amonestaciones</button>

	<? } ?></form>

<form action="consulta.php"
	method="POST" name="listas" class="form-inline">
<input type="hidden" name="fecha" value="<? echo $fecha; ?>" />
<input type="hidden" name="fecha_act" value="<? echo $fecha_act; ?>" />
<button class="btn btn-success" type="submit" name="submit2"
	value="Lista del Curso"><i class="fa fa-file-o  "></i> Listado
en pdf</button>
</form>
	<? }  ?> 
	<? }  ?> 
	<? include("../../pie.php");?>
	
	<script>
	function selectall(form)  
	{  
	 var formulario = eval(form)  
	 for (var i=0, len=formulario.elements.length; i<len ; i++)  
	  {  
	    if ( formulario.elements[i].type == "checkbox" )  
	      formulario.elements[i].checked = formulario.elements[0].checked  
	  }  
	}  
	</script>  
</body>

</html>
