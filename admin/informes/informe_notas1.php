<?php
require('../../bootstrap.php');


include("../../menu.php");
include("menu.php");
?>
<br />
<div align="center" style="max-width:920px;margin:auto;">
<div class="page-header">
  <h2>Informe de Evaluaciones <small> Estadísticas de Calificaciones</small></h2>
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
<p class="lead">Informe Histórico</p>	
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
<li><a href="#tab1" data-toggle="tab">1ª Evaluación</a></li>
<li><a href="#tab2" data-toggle="tab">2ª Evaluación</a></li>
<li><a href="#tab3" data-toggle="tab">Evaluación Ordinaria</a></li>
<li><a href="#tab4" data-toggle="tab">Evaluación Extraordinaria</a></li>
</ul>

<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
<?php 
// Comprobamos datos de evaluaciones
$n1 = mysqli_query($db_con, "select * from notas where notas0 not like '' or notas1 not like ''");
if(mysqli_num_rows($n1)>0){}
else{
	echo '<div align="center"><div class="alert alert-warning alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCIÓN:</h5>No hay datos de Calificaciones en la tabla NOTAS. Debes importar las Calificaciones desde Séneca (Administración de la Intranet --> Importar Calificaciones) para que este módulo funcione.
          </div></div>';
	exit();
}
?>


<?php
mysqli_query($db_con, "drop table suspensos IF EXISTS");

$titulos = array("0"=>"Eval. Inicial","1"=>"1ª Evaluación","2"=>"2ª Evaluación","3"=>"Evaluación Ordinaria","4"=>"Evaluación Extraordinaria");
foreach ($titulos as $key=>$val){
	
// Creamos la tabla en cada evaluación
 $crea_tabla = "CREATE TABLE IF NOT EXISTS `suspensos` (
  `claveal` varchar(12) NOT NULL,
  `suspensos` tinyint(4) NOT NULL,
  `pil` tinyint(4) NOT NULL,
  `grupo` varchar( 64 ) NOT NULL,
  `nivel` varchar( 64 ) NOT NULL,
  KEY `claveal` (`claveal`)
)";
 mysqli_query($db_con, $crea_tabla);
	
	$key == '0' ? $activ=" active" : $activ='';
?>
<div class="tab-pane fade in<?php echo $activ;?>" id="<?php echo "tab".$key;?>">
<h3>Resultados de los Alumnos por Nivel</h3><br />
<p class="help-block text-warning" align="left">En 4º de ESO y 2º de Bachillerato, los alumnos titulan con <strong>0</strong> asignaturas suspensas. En el resto de los grupos de ESO y Bachillerato los alumnos promocionan con <strong>2 o menos</strong> asignaturas suspensas. </p>
<table class="table table-striped table-bordered"  align="center" style="width:auto" valign="top">
<thead>
<th></th>
<th class='text-info'>Alumnos</th>
<th class='text-warning'>Repiten</th>
<th>0 Susp</th>
<th>1-2 Susp</th>
<th>3-5 Susp</th>
<th>6-8 Susp</th>
<th>9+ Susp.</th>
<th class='text-success'>Promo./Tit.</th>
</thead>
<tbody>
<?php
// Evaluaciones ESO
$nivele = mysqli_query($db_con, "select * from cursos");
while ($orden_nivel = mysqli_fetch_array($nivele)){
$niv = mysqli_query($db_con, "select distinct curso, idcurso from alma, cursos where curso=nomcurso and curso = '$orden_nivel[1]'");
while ($ni = mysqli_fetch_array($niv)) {
	$idn = $ni[1];
	if ($idn=="101140") { $nivel="1E"; }
	elseif ($idn=="101141") { $nivel="2E"; }
	elseif ($idn=="101142") { $nivel="3E"; }
	elseif ($idn=="6029" or $idn=="2063") { $nivel="1B"; }
	else{ $nivel = $ni[1]; }
	$n_grupo+=1;
	$curso = $ni[0];
	
	$rep = ""; 
	$promo = "";
$notas1 = "select notas". $key .", claveal1, matriculas, unidad from alma, notas where alma.CLAVEAL1 = notas.claveal and alma.curso = '$curso'";
//echo $notas1."<br>";

$result1 = mysqli_query($db_con, $notas1);
$todos = mysqli_num_rows($result1);
if ($todos < '1') {
	echo '<div align="center"><div class="alert alert-warning alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCIÓN:</h5>No hay datos de Calificaciones del Curso <strong class=text-danger>'.$curso.'</strong>. 
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
		//mysqli_query($db_con, "insert into temp values('','$claveal','$bloque[0]','$cali[0]')");
	}
	
mysqli_query($db_con, "insert into suspensos  (
`claveal` ,
`suspensos` ,
`pil` ,
`grupo`,
`nivel`
)
VALUES (
'$claveal', '$susp',  '$pil', '$grupo', '$curso'
)");
	}

// Calculamos
$cer = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and suspensos = '0'");
$cero = '';
$cero=mysqli_num_rows($cer);

$uno_do = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and suspensos > '0' and suspensos < '3'");
$uno_dos='';
$uno_dos=mysqli_num_rows($uno_do);

$tres_cinc = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and suspensos > '2' and suspensos < '6'");
$tres_cinco='';
$tres_cinco=mysqli_num_rows($tres_cinc);

$seis_och = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and suspensos > '5' and suspensos < '9'");
$seis_ocho='';
$seis_ocho=mysqli_num_rows($seis_och);

$nuev = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and suspensos > '8'");
$nueve='';
$nueve=mysqli_num_rows($nuev);

//$tota = mysqli_query($db_con, "select distinct notas.claveal from notas, alma where alma.claveal1 = notas.claveal and nivel = '$curso'");
$tota = mysqli_query($db_con, "select distinct claveal from suspensos where nivel = '$curso'");
$total='';
$total=mysqli_num_rows($tota);

// Promocion
	$extra1 = " and suspensos = '0'";
	$prom1 = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and grupo not like '1%' and grupo not like '2%' and grupo not like '3%'  $extra1");
	$promo1=mysqli_num_rows($prom1);
	if ($promo1==0) { $promo1=""; }

	$extra2 = " and suspensos < '3'";
	$prom2 = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and (grupo like '1%' or grupo like '2%' or grupo like '3%')  $extra2");
	$promo2=mysqli_num_rows($prom2);
	if ($promo2==0) { $promo2=""; }

$n_pil = mysqli_query($db_con, "select distinct claveal, grupo from suspensos where nivel = '$curso' and pil = '1'");
$num_pil='';
$num_pil=mysqli_num_rows($n_pil);

$porcient = (($promo1+$promo2)*100)/$total;
$porciento='';
if ($porcient>49) {
	$porciento = "<span class='text-success'>".substr($porcient,0,5)."%</span>";
}
else{
	$porciento = "<span class='text-danger'>".substr($porcient,0,5)."%</span>";	
}

?>

<tr>
<th><?php echo $curso;?></th>
<th class='text-info'><?php echo $total;?></th>
<td class='text-warning'><?php echo $num_pil;?></td>
<td><?php echo $cero;?></td>
<td><?php echo $uno_dos;?></td>
<td><?php echo $tres_cinco;?></td>
<td><?php echo $seis_ocho;?></td>
<td><?php echo $nueve;?></td>
<th><?php echo $porciento." <span class='pull-right'>(".$promo2."".$promo1.")</span>";?></th>
</tr>
<?php
}
}
?>
</tbody>
</table>

<!--  Estadísticas por asignatura -->
<br />
</div>
<?php
mysqli_query($db_con, "drop table suspensos");
}
mysqli_close();
?>
</div>
</div>
</div>
</div>

<?php include("../../pie.php");?> 
</body>
</html>