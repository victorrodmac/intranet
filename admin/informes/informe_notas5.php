<?php
require('../../bootstrap.php');


include("../../menu.php");
include("menu.php");
?>
<br />
<div align="center" style="max-width:920px;margin:auto;">
<div class="page-header">
  <h2>Informe de Evaluaciones <small> Estad�sticas de Calificaciones</small></h2>
</div>

<?php
if (isset($_POST['f_curso'])) {
	$f_curso=$_POST['f_curso'];
}

if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')) {

	if (!empty($f_curso) && ($f_curso != $config['curso_actual'])) {
		$exp_c_escolar = explode("/", $f_curso);
		$anio_escolar = $exp_c_escolar[0];
		
		$db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
	}
	if (empty($f_curso)){
		$f_curso = $config['curso_actual'];
	}
}
else {
		$f_curso = $config['curso_actual'];
}
?>

<?php if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')): ?>
<form method="POST" class="well well-large" style="width:450px; margin:auto">
<p class="lead">Informe Hist�rico</p>	
  	<div class="form-group">
  			    <label for="f_curso">Curso escolar</label>
  			    
  			    <select class="form-control" id="f_curso" name="f_curso" onChange="submit()">
  			    	<?php $exp_c_escolar = explode("/", $config['curso_actual']); ?>
  			    	<?php for($i=0; $i<5; $i++): ?>
  			    	<?php $anio_escolar = $exp_c_escolar[0] - $i; ?>
  			    	<?php $anio_escolar_sig = substr(($exp_c_escolar[0] - $i + 1), 2, 2); ?>
  			    	<?php if($i == 0 || (isset($config['db_host_c'.$anio_escolar]) && $config['db_host_c'.$anio_escolar] != "")): ?>
  			    	<option value="<?php echo $anio_escolar.'/'.$anio_escolar_sig; ?>"<?php if ($_POST['f_curso']==$anio_escolar.'/'.$anio_escolar_sig) { echo "selected"; }?>><?php echo $anio_escolar.'/'.$anio_escolar_sig; ?></option>
  			    	<?php endif; ?>
  			    	<?php endfor; ?>
  			    </select>
  	</div>
</form>
<hr />  	
<?php endif; ?>
<div class="tabbable" style="margin-bottom: 18px;">
<ul class="nav nav-tabs">
<li class="active"><a href="#tab0" data-toggle="tab">Eval. Inicial</a></li>	
<li><a href="#tab1" data-toggle="tab">1� Evaluaci�n</a></li>
<li><a href="#tab2" data-toggle="tab">2� Evaluaci�n</a></li>
<li><a href="#tab3" data-toggle="tab">Evaluaci�n Ordinaria</a></li>
<li><a href="#tab4" data-toggle="tab">Evaluaci�n Extraordinaria</a></li>
</ul>

<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">

<?php 
// Comprobamos datos de evaluaciones
$n1 = mysqli_query($db_con, "select * from notas where notas0 not like '' or notas1 not like ''");
if(mysqli_num_rows($n1)>0){}
else{
	echo '<div align="center"><div class="alert alert-warning alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>No hay datos de Calificaciones en la tabla NOTAS. Debes importar las Calificaciones desde S�neca (Administraci�n de la Intranet --> Importar Calificaciones) para que este m�dulo funcione.
          </div></div>';
	exit();
}
?>


<?php
mysqli_query($db_con, "drop table temp3 IF EXISTS");

$titulos = array("0"=>"Eval. Inicial","1"=>"1� Evaluaci�n","2"=>"2� Evaluaci�n","3"=>"Evaluaci�n Ordinaria","4"=>"Evaluaci�n Extraordinaria");
foreach ($titulos as $key=>$val){

// Tabla temporal.
 $crea_tabla2 = "CREATE TABLE  `temp3` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`claveal` VARCHAR( 12 ) NOT NULL ,
`asignatura` INT( 8 ) NOT NULL ,
`nota` TINYINT( 2 ) NOT NULL ,
`unidad` VARCHAR( 80 ) NOT NULL ,
INDEX (  `claveal` )
) ENGINE = MyISAM";
 mysqli_query($db_con, $crea_tabla2); 
 mysqli_query($db_con, "ALTER TABLE  `temp3` ADD INDEX (  `asignatura` )");
	$key == '0' ? $activ=" active" : $activ='';
?>
<div class="tab-pane fade in<?php echo $activ;?>" id="<?php echo "tab".$key;?>">
<?php
// Evaluaciones ESO
$nivele = mysqli_query($db_con, "select distinct curso from alma order by curso");
while ($orden_nivel = mysqli_fetch_array($nivele)){
$niv = mysqli_query($db_con, "select distinct curso from alma where curso = '$orden_nivel[0]'");
while ($ni = mysqli_fetch_array($niv)) {
	$n_grupo+=1;
	$curso = $ni[0];
	$rep = ""; 
	$promo = "";
		$todos="";
$notas1 = "select notas". $key .", claveal1, matriculas, unidad, curso from alma, notas where alma.CLAVEAL1 = notas.claveal and alma.curso = '$curso'";
//echo $notas1."<br>";

$result1 = mysqli_query($db_con, $notas1);
$todos = mysqli_num_rows($result1);
if ($todos < '1') {
echo '<div align="center"><div class="alert alert-warning alert-block fade in" style="max-width:920px">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>No hay datos de Calificaciones del Curso <strong class=text-danger>'.$curso.'</strong>. 
          </div></div>';
}
while($row1 = mysqli_fetch_array($result1)){
$asignatura1 = substr($row1[0], 0, strlen($row1[0])-1);
$claveal = $row1[1];
$grupo = $row1[3];
$nivel_curso = $row1[4];
if ($row1[2]>"1") {
	$pil = "1";
}
else{
	$pil = '0';
}
$trozos1 = explode(";", $asignatura1);
$num = count($trozos1);
$susp="";
 for ($i=0;$i<$num; $i++)
  {
$bloque = explode(":", $trozos1[$i]);
$nombreasig = "select nombre from calificaciones where codigo = '" . $bloque[1] . "'";
$asig = mysqli_query($db_con, $nombreasig);
$cali = mysqli_fetch_row($asig);
if($cali[0] < '5' and !($cali[0] == ''))	{
	$susp+=1; 
	}
		if (strlen($bloque[0])>0 and strlen($cali[0])>0) {
			mysqli_query($db_con, "insert into temp3 values('','$claveal','$bloque[0]','$cali[0]','$grupo')");
			}
		}
	}
}
}
?>
<h3>Resultados de los Alumnos por Materias y Grupo</h3><br />
<?php
$nivele = mysqli_query($db_con, "select distinct curso from alma order by curso");
while ($orden_nivel = mysqli_fetch_array($nivele)){
?>
	<legend><?php echo $orden_nivel[1]; ?></legend><hr />
<?php
// UNIDADES DEL CURSO
$niv = mysqli_query($db_con, "select distinct unidad from alma where curso = '$orden_nivel[0]' order by unidad");
while ($ni = mysqli_fetch_array($niv)) {
	$unidad = $ni[0];
	?>
	<p class="lead"><?php echo $unidad; ?></p>
<table class="table table-striped table-bordered"  align="center" style="width:700px;" valign="top">
<thead>
<th class='text-info'>Asignatura</th>
<th class='text-info'>Matriculados</th>
<th class='text-info'>Aprob. (%)</th>
<th class='text-info'>Al. Aprob.</th>
</thead>
<tbody>	
	<?php
$sql = "select distinct asignaturas.nombre, asignaturas.codigo from asignaturas, profesores where profesores.materia = asignaturas.nombre and asignaturas.curso = '$orden_nivel[0]' and profesores.grupo = '$unidad' and abrev not like '%\_%' and asignaturas.nombre not like 'Refuerz%'";
//echo $sql;	
$as = mysqli_query($db_con, $sql);
while ($asi = mysqli_fetch_array($as)) {
	$n_c = mysqli_query($db_con, "select distinct nivel from alma where curso = '$orden_nivel[0]'");
	$niv_cur = mysqli_fetch_array($n_c);
	$nomasi = $asi[0];
	$codasi = $asi[1];
	$nivel_curso=$orden_nivel[1];
		if (stristr($nivel_curso,"3") and stristr($codasi,"25201") and $key==3) {
		$codasi="25177";
		$nomasi="Ciencias de la Naturaleza";
	}
	if (stristr($nivel_curso,"3") and (stristr($codasi,"25200")) and $key==3) {
		$codasi="";
		$nomasi="";
	}
	if (!($codasi=="")) {
		
	$cod_nota = mysqli_query($db_con, "select id from temp3 where asignatura = '$codasi' and nota < '5' and unidad = '$unidad'");
	$cod_apro = mysqli_query($db_con, "select id from temp3 where asignatura = '$codasi' and nota > '4' and unidad = '$unidad'");
	
	//echo "select id from temp3 where asignatura = '$codasi'<br>";
	$num_susp='';
	$num_susp = mysqli_num_rows($cod_nota);
	$num_apro='';
	$num_apro = mysqli_num_rows($cod_apro);
	$combas = mysqli_query($db_con, "select claveal from alma where combasi like '%$codasi%' and unidad = '$unidad'");
	$num_matr='';
	$num_matr = mysqli_num_rows($combas);
	
	$porcient_asig = ($num_susp*100)/$num_matr;
	$porciento_asig='';
if ($porcient_asig>49) {
	$porciento_asig = "<span class='text-success'>".substr($porcient_asig,0,4)."%</span>";
}
else{
	$porciento_asig = "<span class='text-danger'>".substr($porcient_asig,0,4)."%</span>";	
}
	
	$porcient_asig2 = ($num_apro*100)/$num_matr;
	$porciento_asig2='';
if ($porcient_asig2>49) {
	$porciento_asig2 = "<span class='text-success'>".substr($porcient_asig2,0,4)."%</span>";
}
else{
	$porciento_asig2 = "<span class='text-danger'>".substr($porcient_asig2,0,4)."%</span>";	
}

if ($num_matr>0) {
			echo "<tr><th>$nomasi</th><td>$num_matr</td><td>";
	echo $porciento_asig2."</td><td>".$num_apro."</span></td></tr>";
	}
	}
}
?>
</tbody>
</table>
<br />
<hr />
<?php
}
}
?>
</div>
<?php
mysqli_query($db_con, "drop table temp3");
}
?>
</div>
</div>
</div>
</div>

	<?php include("../../pie.php"); ?>
	
</body>
</html>
