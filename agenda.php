<?php
if(!isset($loader)) { 
	require('core/Loader.php');
	$loader = new Loader();
}

$page  = $loader->getObject('Page');
$database  = $page->get('database');
$functions = $page->get('functions');

$config = $loader->getObject('Config', 'library');
$config->databaseLoad(array('tips-category-id'));
$agenda= $loader->getObject('Agenda', 'class', false);

$page->header('index', 'form-cadastro.css', '');


if($date==null){
	$date = date("Y-m-d");
}

?>


<section class="agenda">
    <div class="container">
        <h3 class="text-center">Agende sua sala:</h3>
        <div class="form-cadastro">
            <form class="" id="" action="">
                <div class="form-group">
                    <label>Data da aula:</label>
                    <input type="date" class="form-control" id="data" name="data" placeholder="Data da aula" required>
                </div>
                <div class="form-group">
                    <label>Descrição:</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Breve descrição da aula" required>
                </div>
                <div class="form-group" id="lab-time">
                    <div class="col-md-6">
                        <label>Sala:</label>
                        <select class="form-control" id="room_name" name="room_name">
                            <option value="lab1">Laboratório 1</option>
                            <option value="lab2">Laboratório 2</option>
                            <option value="lab3">Laboratório 3</option>
                            <option value="lab4">Laboratório 4</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Horário:</label>
                        <select class="form-control" id="room_name" name="room_name">
                            <option value="1">Aulas 1 e 2</option>
                            <option value="2">Aulas 3 e 4</option>
                        </select>
                    </div>        
                </div>
                <div class="form-group">
                    
                </div>
                
                <div class="form-group">
                    <label>Turno:</label>
                    <select class="form-control" id="shift" name="shift">
                        <option value="manha">Manhã</option>
                        <option value="tarde">Tarde</option>
                        <option value="noite">Noite</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Observações:</label>
                <textarea class="form-control" type="textarea" id="message" placeholder="Observações" maxlength="140" rows="7"></textarea>
                    <!-- <span class="help-block"><p id="characterLeft" class="help-block ">You have reached the limit</p></span>                     -->
                </div>
                
                <button type="submit" id="submit" name="submit" class="btn btn-primary pull-right">Agendar</button>
            </form>
        </div>
    </div>
</section>

<?php 
$page->footer(); 
?>

