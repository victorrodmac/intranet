<?
session_start();
include("../../config.php");
if($_SESSION['autentificado']!='1')
{
session_destroy();
header("location:http://$dominio/intranet/salir.php");	
exit;
}
registraPagina($_SERVER['REQUEST_URI'],$db_host,$db_user,$db_pass,$db);
?>
<?
 include("../../menu.php");
 include("menu.php");
?>
<div align="center">
<div class="page-header" align="center">
  <h2>Informes de Tutor�a <small> Activar Informe</small></h2>
</div>
<br />
    
 <br /> 
 <?php
$tutor=$_POST['tutor'];
if(empty($alumno) or empty($tutor))
{
	echo '<br /><div align="center"><div class="alert alert-warning alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>
Debes rellenar todos los datos, y parece que te has olvidado del Alumno o del Tutor.<br>Vuelve atr�s e int�ntalo de nuevo.<br /><br />
<input name="volver" type="button" onClick="history.go(-1)" value="Volver" class="btn btn-danger">
</div></div><hr>';
exit;
}
#Vamos a rellenar los datos del alumno objeto del informe en la base de datos infotut
$alumno=$_POST['alumno'];
$tutor=$_POST['tutor'];
foreach ($_POST['fecha'] as $valor){
$date[]=$valor;
}
$fecha=$date[2]."-".$date[1]."-".$date[0];
$trozos = explode (" --> ", $alumno);
$claveal = $trozos[1];
$nombre_comp = $trozos[0];
$trozos1 = explode (", ", $nombre_comp);
$apellidos = $trozos1[0];
$nombre = $trozos1[1];
$falumno=mysql_query("SELECT CLAVEAL, APELLIDOS, NOMBRE, NIVEL, GRUPO, COMBASI FROM alma WHERE claveal ='$claveal'");
$dalumno = mysql_fetch_array($falumno);
$asignaturas=chunk_split($dalumno[5],3,"-");
$asig=explode("-",$asignaturas);
$hoy = date('Y\-m\-d');

$duplicado = mysql_query("select claveal from infotut_alumno where claveal = '$claveal' and f_entrev = '$fecha'");
if(mysql_num_rows($duplicado)>0)
{
	echo '<br /><div align="center"><div class="alert alert-warning alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>';
			echo "Ya hay un <b>Informe Tutorial</b> activado para el alumno/a <b> $nombre $apellidos </b>para el d�a
<b>$date[0] del $date[1] de $date[2]</b>, y no queremos duplicarlo, verdad?";
echo '<br /><br /><input type="button" onClick="history.back(1)" value="Volver" class="btn btn-danger">
		</div></div>';
exit;
}
 $insertar=mysql_query("INSERT infotut_alumno (CLAVEAL,APELLIDOS,NOMBRE,NIVEL,GRUPO,F_ENTREV,TUTOR,FECHA_REGISTRO)
 VALUES ('$dalumno[0]',\"$dalumno[1]\",'$dalumno[2]','$dalumno[3]','$dalumno[4]',
 '$fecha','$tutor', '$hoy')") or die ("Error en la activaci�n del informe: " . mysql_error());
 
 echo '<br /><div align="center"><div class="alert alert-success alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>';
			echo "El <b>Informe Tutorial</b> del alumno/a <b> $nombre $apellidos </b>para el d�a<b>
$date[0] del $date[1] de $date[2]</b> se ha activado.";
echo '<br /><br /><input type="button" onClick="history.back(1)" value="Volver" class="btn btn-success">
		</div></div>';
exit;
?>
</div>
	<? include("../../pie.php");?>								
</body>
</html>
