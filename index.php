<?php
require('core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader);

$database  = $page->get('database');
$functions = $page->get('functions');

$config = $loader->getObject('Config', 'library');
$config->databaseLoad(array('tips-category-id'));
$agenda= $loader->getObject('Agenda', 'class', false);

$page->header('', 'agenda.css', 'post.js');


if($date==null){
	$date = date("Y-m-d");
}

 
$page->formIni('agenda', 'FILTER|NOAJAX|ACTION=|METHOD=get');
?>

<section class="form">
	<div class="container">
	
		<div class="row">
			<?php
				$page->field('S', 'Sala', 'room', $room, 'LEFT|CLASS=col-md-2 col-sm-9 col-xs-12',
						'select id, title from room order by title');
				$page->field('T', 'Data', 'data', $data, 'CLASS=col-md-3 col-sm-9 col-xs-12');
				$page->field('S', 'Periodo', 'turno', $turno, 'VAL=:1:2:3|TXT=Selecione:Manhã:Tarde:Noite|CLASS=col-md-3 col-sm-9 col-xs-12');
				$page->field('S', 'Aulas', 'aulas', $aulas, 'VAL=:1:2|TXT=Selecione:Aula 1 - Aula 2:Aula 3 - Aula 4|CLASS=col-md-3 col-sm-9 col-xs-12');
				
				$dateLink = $functions->formatDate($obj->date_ini, 'Y-m-d');
				
				$page->button(array("VAL=Filtrar|SBM"));
			
			$page->formEnd();
			?>
		</div>
	</div>

</section>

<section class="agenda">
	<div class="container">
		<div class="header">	
			<div class="row">
				<div class="col-md-2 col-sm-9 col-xs-12" >
				Data
				</div>
				<div class="col-md-2 col-sm-9 col-xs-12" >
				Periodo
				</div>
				<div class="col-md-2 col-sm-9 col-xs-12" >
				Aulas
				</div>
				<div class="col-md-6 col-sm-9 col-xs-12" >
				Agenda
				</div>
				
			</div>
		</div>
			<?php 
			
			
			if($data==''){
				$dateStart 		= date(d/m/Y);
				$dateEnd = date("d/m/Y", time() + (10 * 86400));
			}
			else{
				$dateStart 		= $data;
				$dateEnd = $data;
			}
			
			$dateStart 		= explode('-', array_reverse(explode('/', substr($dateStart, 0, 10)))).substr($dateStart, 10);
			$dateStart 		= new DateTime($dateStart);
			
			
			//End date
			
			$dateEnd 		= explode('-', array_reverse(explode('/', substr($dateEnd, 0, 10)))).substr($dateEnd, 10);
			$dateEnd 		= new DateTime($dateEnd);
			
			//Prints days according to the interval
			$dateRange = array();
			
			while($dateStart <= $dateEnd){
				$date =$dateStart->format('d-m-Y');
				$dateStart = $dateStart->modify('+1day');
				$dateDb = $dateStart->format('Y-m-d');
				echo "<div class='dia'>";
					echo "<div class='row' >";
						echo "<div class='col-md-2 col-sm-9 col-xs-12' >$date</div>";
						echo "<div class='col-md-10 col-sm-9 col-xs-12'>";
							echo "<div class='row' >";
								if($turno=='' or $turno=='1'){
									echo "<div class='col-md-2 col-sm-9 col-xs-12 turno'>manha</div>";
									echo "<div class='col-md-10 col-sm-9 col-xs-12 ' >";
										echo "<div class='row'>";
											if($aulas=='' or $aulas=='1'){
												echo "<div class='col-md-3 col-sm-9 col-xs-12 aula'>aula 1 - aula 2</div>";
												echo "<div class='col-md-9 col-sm-9 col-xs-12 agenda'>";
													$row= $agenda->buscaDados($room, $dateDb);
													if($row[id]!=null){
														echo $row[title].' - '.$row[name];
														if($page->session->getUserId()!=null){
															if($row[account_id]==$page->session->getUserId()){
																echo "<td><a href='agenda.php?id=$schedules->id' class='btn btn-primary'>Editar</a></td>";
															}
															else{
																echo "<td><a href='#' class='btn btn-secondary'>Editar</a></td>";
															}
														}
													}
													else{
														echo "Disponivel";
														if($page->session->getUserId()!=null){
															echo "<td><a href='agenda.php?op=I' class='btn btn-primary'>Agendar</a></td>";
														}
														
													}
												echo "</div>";
											}
											if($aulas=='' or $aulas=='2'){
												echo "<div class='col-md-3 col-sm-9 col-xs-12 aula'>aula 3 - aula 4</div>";
												echo "<div class='col-md-9 col-sm-9 col-xs-12 agenda'>";
													$row= $agenda->buscaDados($room, $dateDb);
													if($row[id]!=null){
														echo $row[title].' - '.$row[name];
														if($page->session->getUserId()!=null){
															if($row[account_id]==$page->session->getUserId()){
																echo "<td><a href='agenda.php?id=$schedules->id' class='btn btn-primary'>Editar</a></td>";
															}
															else{
																echo "<td><a href='#' class='btn btn-secondary'>Editar</a></td>";
															}
														}
													}
													else{
														echo "Disponivel";
														if($page->session->getUserId()!=null){
															echo "<td><a href='agenda.php?op=I' class='btn btn-primary'>Agendar</a></td>";
														}
														
													}
												echo "</div>";
											}
										echo "</div>";
									echo "</div>";
								}
								if($turno=='' or $turno=='2'){
									echo "<div class='col-md-2 col-sm-9 col-xs-12 turno'>tarde</div>";
									echo "<div class='col-md-10 col-sm-9 col-xs-12' >";
										echo "<div class='row' >";
											if($aulas=='' or $aulas=='1'){
												echo "<div class='col-md-3 col-sm-9 col-xs-12 aula'>aula 1 - aula 2</div>";
												echo "<div class='col-md-9 col-sm-9 col-xs-12 agenda'>";
													$row= $agenda->buscaDados($room, $dateDb);
													if($row[id]!=null){
														echo $row[title].' - '.$row[name];
														if($page->session->getUserId()!=null){
															if($row[account_id]==$page->session->getUserId()){
																echo "<td><a href='agenda.php?id=$schedules->id' class='btn btn-primary'>Editar</a></td>";
															}
															else{
																echo "<td><a href='#' class='btn btn-secondary'>Editar</a></td>";
															}
														}
													}
													else{
														echo "Disponivel";
														if($page->session->getUserId()!=null){
															echo "<td><a href='agenda.php?op=I' class='btn btn-primary'>Agendar</a></td>";
														}
														
													}
												echo "</div>";
											}
											if($aulas=='' or $aulas=='2'){
												echo "<div class='col-md-3 col-sm-9 col-xs-12 aula'>aula 3 - aula 4</div>";
												echo "<div class='col-md-9 col-sm-9 col-xs-12 agenda' >";
												$row= $agenda->buscaDados($room, $dateDb);
												if($row[id]!=null){
													echo $row[title].' - '.$row[name];
													if($page->session->getUserId()!=null){
														if($row[account_id]==$page->session->getUserId()){
															echo "<td><a href='agenda.php?id=$schedules->id' class='btn btn-primary'>Editar</a></td>";
														}
														else{
															echo "<td><a href='#' class='btn btn-secondary'>Editar</a></td>";
														}
													}
												}
												else{
													echo "Disponivel";
													if($page->session->getUserId()!=null){
														echo "<td><a href='agenda.php?op=I' class='btn btn-primary'>Agendar</a></td>";
													}
													
												}
												echo "</div>";
											}
										echo "</div>";
									echo "</div>";
								}
								if($turno=='' or $turno=='3'){
									echo "<div class='col-md-2 col-sm-9 col-xs-12 turno'>noite</div>";
									echo "<div class='col-md-10 col-sm-9 col-xs-12' >";
									echo "<div class='row' >";
									if($aulas=='' or $aulas=='1'){
										echo "<div class='col-md-3 col-sm-9 col-xs-12 aula'>aula 1 - aula 2</div>";
										echo "<div class='col-md-9 col-sm-9 col-xs-12 agenda' >";
										$row= $agenda->buscaDados($room, $dateDb);
										if($row[id]!=null){
											echo $row[title].' - '.$row[name];
											if($page->session->getUserId()!=null){
												if($row[account_id]==$page->session->getUserId()){
													echo "<td><a href='agenda.php?id=$schedules->id' class='btn btn-primary'>Editar</a></td>";
												}
												else{
													echo "<td><a href='#' class='btn btn-secondary'>Editar</a></td>";
												}
											}
										}
										else{
											echo "Disponivel";
											if($page->session->getUserId()!=null){
												echo "<td><a href='agenda.php?op=I' class='btn btn-primary'>Agendar</a></td>";
											}
											
										}
										echo "</div>";
									}
									if($aulas=='' or $aulas=='2'){
										echo "<div class='col-md-3 col-sm-9 col-xs-12 aula'>aula 3 - aula 4</div>";
										echo "<div class='col-md-9 col-sm-9 col-xs-12 agenda'>";
										$row= $agenda->buscaDados($room, $dateDb);
										if($row[id]!=null){
											echo $row[title].' - '.$row[name];
											if($page->session->getUserId()!=null){
												if($row[account_id]==$page->session->getUserId()){
													echo "<td><a href='agenda.php?id=$schedules->id' class='btn btn-primary'>Editar</a></td>";
												}
												else{
													echo "<td><a href='#' class='btn btn-secondary'>Editar</a></td>";
												}
											}
										}
										else{
											echo "Disponivel";
											if($page->session->getUserId()!=null){
												echo "<td><a href='agenda.php?op=I' class='btn btn-primary'>Agendar</a></td>";
											}
											
										}
										echo "</div>";
									}
									echo "</div>";
									echo "</div>";
								}
							echo "</div>";
						echo "</div>";
						
					echo "</div>";
				echo "</div>";
			}
			?>	
	</div>
</section>




<?php 



?>

