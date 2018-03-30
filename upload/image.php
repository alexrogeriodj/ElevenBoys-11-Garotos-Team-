<?php
ini_set('memory_limit', '64M');
require('../core/Loader.php');
$loader = new Loader();

$loader->requireObject('DataBase', 'database');
$loader->requireObject('Functions', 'library');

$database = $loader->get('database');
$functions = $loader->get('functions');

$version  = 1;
$urlSite  = $loader->getUrlSite();
$dirTheme = $loader->get('config')->get('DIR_THEME');
$theme    = isset($theme) ? $theme : 'default';

$loader->set('theme', $theme);

$urlTheme = "$urlSite/$dirTheme/$theme";

foreach($_GET as $k => $v) ${$k} = $v;

if(!isset($max)) $max = 'DFT';
if(!isset($ver)) $ver = $version;

$msg = array();

if(isset($_FILES['imagem']) and is_file($_FILES['imagem']['tmp_name'])){
	
	$loader->requireObject('Session', 'library');
	$session = $loader->get('session');
	
	if($session->isLogged()) {
		
		$config = $loader->get('config');
		$config->load('upload');
		$loader->set('config', $config);
		
		$name     = $_FILES['imagem']['name'];
		$tmp_name = $_FILES['imagem']['tmp_name'];
		$error    = $_FILES['imagem']['error'];
		$size     = $_FILES['imagem']['size'];
		
		$name = $functions->clearString($name);
		
		$vld = $config->get('UP_VALID_IMG');
		$ext = strtolower(end(explode('.', $name)));
		
		$ums = $config->get('UP_MAX_SIZE');
		$ums = $ums[$max];
		
		if($size > ($ums * 1024))
		
			$msg = array(0, 'O arquivo enviado é muito grande, envie arquivos de até ' . $ums . 'Kb.', '');
		
		else if(array_search($ext, $vld) === false) 
		
			$msg = array(0, 'Formato de arquivo inválido, enviar em: ' . join(', ', $vld) . '.', '');
		
		else if($error == 0) {
		
			if(is_uploaded_file($tmp_name)) {
			
				$uid = $session->getUserID();
				$tit = substr($name, 0, -4);
				
				$id = $database->get('select max(image_id) from image');
				if(empty($id)) $id = 1; else $id++;
				
				$sql = "insert into image (" .
						"	image_id, " .
						"	name, " .
						"	legend, " .
						"	status, " .
						"	account_id, " .
						"	date_added" .
						") values (" .
						"	{$id}, " .
						"	'{$name}', " .
						"	'{$tit}', " .
						"	1, " .
						"	{$uid}, " .
						"	now()" .
						")";
				$dbq = $database->query($sql);
				
				if($dbq) {
					
					$updir = $config->get('UP_DIR_IMG');
					$dir = $loader->getRootPath() . "/$updir/$id";
					
					mkdir($dir, 0755, true);
					
					if(move_uploaded_file($tmp_name, "$dir/$name")) {
						
						chmod("$dir/$name", 0755);
						
						if(isset($rsz)) {
							
							$width = null;
							$height = null;
							
							$prms = explode(';', $rsz);
							foreach($prms as $p) {
								$p = explode(':', $p, 2);
								if(count($p) > 1) ${$p[0]} = $p[1];
								else ${$p[0]} = true;
							}
							
							$resize = false;
							
							if(isset($maxWidth) or isset($maxHeight)) {
								$image = $loader->getObject('Image', 'library');
								$size = $image->getImageSize("$dir/$name");
								
								if(isset($maxWidth) and $maxWidth < $size[0]) {
									$resize = true;
									$width = $maxWidth;
								}
								if(isset($maxHeight) and $maxHeight < $size[1]) {
									$resize = true;
									$height = $maxHeight;
								}
							}
							else $resize = true;
							
							if($resize) {
								$loader->requireObject('Canvas', 'library', true, false);
								$canvas = $loader->get('canvas');
								
								$canvas->load("$dir/$name");
								$canvas->resize($width, $height, $method);
								$canvas->save("$dir/$name");
							}

						}
						
						$exe = array();
						
						if(file_exists("_{$src}.php")) {
							
							$loader->requireObject('Image', 'library');
							
							include("_{$src}.php");
							$exe = execute($id, $sid, $loader);
							
						}
						
						if(count($exe) > 0) $msg = $exe;
						else $msg = array($id, "Imagem '$name' enviada com sucesso.", "$urlSite/$updir/$id/$name");
						
					}
					else {
						$sql = "delete from image where image_id = {$id}";
						$database->query($sql);
						$msg = array(0, 'Não foi possível salvar a imagem.', '');
					}
						
				}
				else $msg = array(0, 'Erro de inserção no Banco de Dados.', '');
	
			}
			else $msg = array(0, 'Erro ao processar imagem.', '');
	
		}
		else $msg = array(0, 'Erro no upload da imagem.', '');
	
	}
	else $msg = array(-1, $session->getMessage(), '');

}
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo "$urlTheme/css/image.up.css?$ver" ?>" media="screen" />
	<script type="text/javascript" src="<?php echo "$urlSite/js/jquery.js?$ver" ?>"></script>
	<script type="text/javascript" src="<?php echo "$urlSite/js/image.up.js?$ver" ?>"></script>
	<script type="text/javascript" src="<?php echo "$urlTheme/js/$src.up.js?$ver" ?>"></script>
</head>
<body class="<?php echo $src; ?>">

<form action="" method="post" id="form-upload" enctype="multipart/form-data">
	<input type="hidden" name="src" value="<?php echo $src ?>"/>
	<input type="hidden" name="sid" value="<?php echo $sid ?>"/>
	<div class="field">
		<span class="background">Upload</span>
		<span class="filename">Selecione um arquivo</span>
		<input type="file" class="input" id="file" name="imagem" />
		<input type="submit" class="button" value="Enviar" />
	</div>
</form>

<?php 
if(count($msg) > 0) {
	$msg[1] = str_replace("'", "\'", $msg[1]);
	$html = '<script type="text/javascript">';
	$format = "fnResult={sid:'%s',title:'%s',id:%s,msg:'%s',url:'%s',urlSite:'%s'}; ";
	$html .= sprintf($format, $sid, $tit, $msg[0], $msg[1], $msg[2], $urlSite);
	$html .= '</script>';
	echo $html;
}
?>

</body>
</html>