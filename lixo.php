<section class="agenda">
	<div class="container x_panel">
		<div class="row text-center">
			<table class="table table-hover table-dark">
			  <thead>
			    <tr>
			      <th scope="col">Data</th>
			      <th scope="col">Periodo</th>
			      <th scope="col">Agenda</th>
			      <th scope="col">Responsavel</th>
			      <th scope="col">Ação</th>
			    </tr>
			  </thead>
			  <tbody>
			
			<?php 
				if($data==''){
					$dateStart 		= date(d/m/Y);
					echo $data;
				}
				else{
					$dateStart 		= $data;
				}
				
				$dateStart 		= implode('-', array_reverse(explode('/', substr($dateStart, 0, 10)))).substr($dateStart, 10);
				$dateStart 		= new DateTime($dateStart);
				
				
				//End date
				$dateEnd = date("d/m/Y", time() + (10 * 86400));
				$dateEnd 		= implode('-', array_reverse(explode('/', substr($dateEnd, 0, 10)))).substr($dateEnd, 10);
				$dateEnd 		= new DateTime($dateEnd);
				
				//Prints days according to the interval
				$dateRange = array();
				
				while($dateStart <= $dateEnd){
					$date =$dateStart->format('d-m-Y');
					$dateStart = $dateStart->modify('+1day');
				
					$dateDb = $dateStart->format('Y-m-d');
					
					
					
					
				function{
					$and = "";
					if(isset($room) && !empty($room))
						$and .= " and s.room_id='{$room}'";
						
					if(isset($status) && !empty($status))
						$and .= " and a.status = '{$status}'";
						// fim do sql dos filtros
						$database  = $page->get('database');
						
					$sql = "select " .
							"	s.id, " .
							"	s.title, " .
							"	s.shift_class, " .
							"	a.account_id, " .
							"	a.name " .
							"from " .
							"	schedule s " .
							"	inner join account a " .
							"	on s.account_id=a.account_id " .
							"where " .
							"	date_scheduling='$dateDb' " .
							"	{$and} " ;
					
					$schedules = $database->getObject($database->query($sql));
					
					$dados .="<td>$schedules->title</td>";
					$dados .="<td><?php echo $schedules->name;?></td>";
					if($schedules->id!=null){
						if($schedules->account_id==$page->session->getUserId()){
							$dados .="<td><a href='agenda.php?id=$schedules->id' class='btn btn-primary'>Editar</a></td>";
						}
						else{
							$dados .="<td><a href='#' class='btn btn-secondary'>Editar</a></td>";
						}
					}
					else {
						$dados .="<td><a href='#' class='btn btn-secondary'>Agendar</a></td>";
					}
					
				}
				?>
					<tr>
						<th scope="row" rowspan="3"><?php echo $date;?></br> 
							Terça-feira
						</th>
			     		<td>Manhã</td>
				      	<?php 
				      	if($schedules->shift_class=='1'){
				      		echo $dados;
						}
						else{$table;}
						?>
				      </tr>
				      <tr>
					    <td>Tarde</td>
					    
					    <?php 
					    if($schedules->shift_class!='2'){
					    	echo $dados;
					    }
					    else{$table;}
					   ?>
					  </tr>
					  <tr>
					    <td>Noite</td>
					    <?php 
					    if($schedules->shift_class!='3'){
					    	echo $dados;
					    }
					    else{$table;}
					    ?>
					  </tr>
					<?php 
					
					}
				
				
		
				?>
			
			  </tbody>
			</table>
		</div>
	</div>
</section>



echo "<div class='col-md-4 col-sm-9 col-xs-12 ' >";
if($aulas=='' or $aulas=='1'){
	echo "<div class='col-md-12 col-sm-9 col-xs-12' >aula 1 - aula 2</div>";
}
if($aulas=='' or $aulas=='2'){
	echo "<div class='col-md-12 col-sm-9 col-xs-12' >aula 3 - aula 4</div>";
}
echo "</div>";
echo "<div class='col-md-2 col-sm-9 col-xs-12' >Data</div>";
echo "<div class='col-md-2 col-sm-9 col-xs-12' >Data</div>";
