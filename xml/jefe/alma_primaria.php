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
if(!(stristr($_SESSION['cargo'],'1') == TRUE))
{
header("location:http://$dominio/intranet/salir.php");
exit;	
}
?>
<?php
include("../../menu.php");
?>
<br />
<div align="center">
<div class="page-header" align="center">
  <h2>Administraci�n <small> Alumnos de Primaria</small></h2>
</div>
<br />
<div class="well well-large" style="width:600px;margin:auto;text-align:left">
<?
if($archivo1){
// Creamos Base de datos y enlazamos con ella.
 $base0 = "DROP TABLE `alma_primaria`";
  mysql_query($base0);

 // Creaci�n de la tabla alma
 $alumnos = "CREATE TABLE  `alma_primaria` (
`Alumno/a` varchar( 255 ) default NULL ,
 `ESTADOMATRICULA` varchar( 255 ) default NULL ,
 `CLAVEAL` varchar( 12 ) default NULL ,
 `DNI` varchar( 10 ) default NULL ,
 `DOMICILIO` varchar( 255 ) default NULL ,
 `CODPOSTAL` varchar( 255 ) default NULL ,
 `LOCALIDAD` varchar( 255 ) default NULL ,
 `FECHA` varchar( 255 ) default NULL ,
 `PROVINCIARESIDENCIA` varchar( 255 ) default NULL ,
 `TELEFONO` varchar( 255 ) default NULL ,
 `TELEFONOURGENCIA` varchar( 255 ) default NULL ,
 `CORREO` varchar( 64 ) default NULL ,
 `CURSO` varchar( 255 ) default NULL ,
 `NUMEROEXPEDIENTE` varchar( 255 ) default NULL ,
 `UNIDAD` varchar( 255 ) default NULL ,
 `apellido1` varchar( 255 ) default NULL ,
 `apellido2` varchar( 255 ) default NULL ,
 `NOMBRE` varchar( 30 ) default NULL ,
 `DNITUTOR` varchar( 255 ) default NULL ,
 `PRIMERAPELLIDOTUTOR` varchar( 255 ) default NULL ,
 `SEGUNDOAPELLIDOTUTOR` varchar( 255 ) default NULL ,
 `NOMBRETUTOR` varchar( 255 ) default NULL ,
 `SEXOPRIMERTUTOR` varchar( 255 ) default NULL ,
 `DNITUTOR2` varchar( 255 ) default NULL ,
 `PRIMERAPELLIDOTUTOR2` varchar( 255 ) default NULL ,
 `SEGUNDOAPELLIDOTUTOR2` varchar( 255 ) default NULL ,
 `NOMBRETUTOR2` varchar( 255 ) default NULL ,
 `SEXOTUTOR2` varchar( 255 ) default NULL ,
 `LOCALIDADNACIMIENTO` varchar( 255 ) default NULL ,
  `FECHAMATRICULA` varchar( 255 ) default NULL ,
 `MATRICULAS` varchar( 255 ) default NULL ,
 `OBSERVACIONES` varchar( 255 ) default NULL,
 `PROVINCIANACIMIENTO` varchar( 255 ) default NULL ,
 `PAISNACIMIENTO` varchar( 255 ) default NULL ,
 `EDAD` varchar( 2 ) default NULL ,
 `NACIONALIDAD` varchar( 32 ) default NULL,
 `SEXO` varchar( 1 ) default NULL ,
 `COLEGIO` varchar( 32 ) default NULL 
 )";

 
 //echo $alumnos;
mysql_query($alumnos) or die ('<div align="center"><div class="alert alert-danger alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>
No se ha podido crear la tabla <strong>Alma_primaria</strong>. Ponte en contacto con quien pueda resolver el problema.
</div></div><br />
<div align="center">
  <input type="button" value="Volver atr�s" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
</div>');

  $SQL6 = "ALTER TABLE  `alma_primaria` ADD INDEX (  `CLAVEAL` )";
  $result6 = mysql_query($SQL6) or die ('<div align="center"><div class="alert alert-danger alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>
No se ha podido crear el �ndice de la tabla. Busca ayuda.
</div></div><br />
<div align="center">
  <input type="button" value="Volver atr�s" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
</div>');


// Descomprimimos el zip de las calificaciones en el directorio exporta/
include('../../lib/pclzip.lib.php');   
$archive = new PclZip($_FILES['archivo1']['tmp_name']);  
      if ($archive->extract(PCLZIP_OPT_PATH, '../primaria') == 0) 
	  {
        die("Error : ".$archive->errorInfo(true));
      } 

// Recorremos directorio donde se encuentran los ficheros y aplicamos la plantilla.
if ($handle = opendir('../primaria')) {
   while (false !== ($file = readdir($handle))) {   	
      if ($file != "." && $file != ".."&& $file != ".txt") { 
      $colegio = substr($file,0,-4); 
// Importamos los datos del fichero CSV (todos_alumnos.csv) en la tab�a alma.

$fp = fopen ('../primaria/'.$file , "r" ) or die('<div align="center"><div class="alert alert-danger alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>
No se han podido abrir los archivos de datos. �Est�n los archivos de los Colegios en el directorio ../primaria?
</div></div><br />
<div align="center">
  <input type="button" value="Volver atr�s" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
</div>'); 
$row = 1;
while (($data = fgetcsv($fp, 1000, "|")) !== FALSE)
{
   	$datos = "INSERT INTO alma_primaria VALUES (";
  for($i=0;$i<37;$i++){ 	
   $datos.= "\"". trim($data[$i]) . "\", ";
  }

$datos=substr($datos,0,strlen($datos)-2);
$datos.=", \"$colegio\"";
$datos.=")";
// echo $datos."<br>";
	mysql_query($datos);
}
fclose($fp);

      }
      
   }
   closedir($handle);
}  
 
// Procesamos datos
$crear = "ALTER TABLE  alma_primaria
ADD  `APELLIDOS` VARCHAR( 40 ) NULL AFTER  `UNIDAD` ,
ADD  `NIVEL` VARCHAR( 5) NULL AFTER  `NOMBRE` ,
ADD  `GRUPO` VARCHAR( 1 ) NULL AFTER  `NIVEL`,
ADD  `PADRE` VARCHAR( 78 ) NULL AFTER  `GRUPO`
";
mysql_query($crear);

// Separamos Nivel y Grupo, que viene juntos en el campo Unidad, que finalmente nos cargamos
  $SQL0 = "SELECT UNIDAD, CLAVEAL  FROM  alma_primaria";
  $result0 = mysql_query($SQL0);

 while  ($row0 = mysql_fetch_array($result0))
 {
$trozounidad0 = explode("-",$row0[0]);
$actualiza= "UPDATE alma_primaria SET NIVEL = '$trozounidad0[0]', GRUPO = '$trozounidad0[1]' where CLAVEAL = '$row0[1]'";
	mysql_query($actualiza);
 }

 // Apellidos unidos formando un solo campo.
   $SQL2 = "SELECT apellido1, apellido2, CLAVEAL, NOMBRE FROM  alma_primaria";
  $result2 = mysql_query($SQL2);
 while  ($row2 = mysql_fetch_array($result2))
 {
 	$apellidos = trim($row2[0]). " " . trim($row2[1]);
	$apellidos1 = trim($apellidos);
	$nombre = $row2[3];
	$nombre1 = trim($nombre);
	$actualiza1= "UPDATE alma_primaria SET APELLIDOS = \"". $apellidos1 . "\", NOMBRE = \"". $nombre1 . "\" where CLAVEAL = \"". $row2[2] . "\"";
	mysql_query($actualiza1);
 }
 
 // Apellidos y nombre del padre.
   $SQL3 = "SELECT PRIMERAPELLIDOTUTOR, SEGUNDOAPELLIDOTUTOR, NOMBRETUTOR, CLAVEAL FROM  alma_primaria";
  $result3 = mysql_query($SQL3);
 while  ($row3 = mysql_fetch_array($result3))
 {
 	$apellidosP = trim($row3[2]). " " . trim($row3[0]). " " . trim($row3[1]);
	$apellidos1P = trim($apellidosP);
	$actualiza1P= "UPDATE alma_primaria SET PADRE = \"". $apellidos1P . "\" where CLAVEAL = \"". $row3[3] . "\"";
	mysql_query($actualiza1P);
 }
 
  // Eliminaci�n de campos innecesarios por repetidos
  $SQL3 = "ALTER TABLE alma_primaria
  DROP `apellido1`,
  DROP `Alumno/a`,
  DROP `apellido2`,
  DROP `estadomatricula`";
  $result3 = mysql_query($SQL3);

  // Eliminaci�n de alumnos dados de baja
    $SQL4 = "DELETE FROM alma_primaria WHERE `NIVEL` = '' AND `GRUPO` = ''";
    $SQL5 = "DELETE FROM alma_primaria WHERE `claveal` = 'N� Id. Escol'";
    $result4 = mysql_query($SQL4);
    $result5 = mysql_query($SQL5);
echo '<div align="center"><div class="alert alert-success alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
La tabla de Alumnos de Primaria para la Matriculaci�n ha sido creada.<br />Ya puedes proceder a matricular a los ni�os de los Colegios.
</div></div><br />';
}
else
{
	echo '<div align="center"><div class="alert alert-danger alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>
Parece que te est�s olvidando de enviar el archivo con los datos de los alumnos. Aseg�rate de enviar el archivo comprimido con los datos de los Colegios.
</div></div><br />';
}
?>
<br />
<div align="center">
  <input type="button" value="Volver atr�s" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
</div></div>
</div>
</body>
</html>