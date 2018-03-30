<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'admin');

$database  = $page->get('database');
$functions = $page->get('functions');
$image     = $page->get('image');
$session   = $page->get('session');

$page->header('Imagens', 'image.css', 'image.js', array('pageTitle' => 'Gerenciador de Imagens'));

if($session->hasAccess('USR')) {

	$page->bar('Use o campo abaixo para efetuar upload de imagens no site');
	$page->field('FI', '', 'image', 0, 'MAX=5MB');
	
	$html = '<div id="images">';
	
	$sqlpag = "select count(image_id) from image";
	$pager = $page->pager($database->get($sqlpag), 'image', 'NRP=5');
	
	
	$sql = "select * from image order by date_added desc";
				
	$dbq = $database->query($sql, $pager);
	while($row = $database->getObject($dbq)) {
		
		$html .= '<div class="item">';
		
		$link = $image->getImage($row->image_id, $row->name, 'NOTAG');
		
		$html .= '<div class="image">';
		$html .= "<a href=\"$link\">";
		$html .= $image->getImage($row->image_id, $row->name, 'W=120|H=80|CREATE|METHOD=mycrop');
		$html .= '</a>';
		$html .= '</div>';
		
		$html .= '<div class="info">';
		$html .= '<ul>';
		
		$remove = $session->hasAccess('ADM') ? "[<span id=\"img-$row->image_id\" class=\"remove\">remover</span>]" : '';
		
		$html .= "<li><span>Arquivo:</span> $row->name $remove</li>";
		$html .= "<li><span>Código:</span> [IMG ID=$row->image_id NAME=$row->name]</li>";
		$html .= "<li><span>Link:</span> $link</li>";
		$html .= "<li><span>Legenda:</span> $row->legend</li>";
		$html .= "<li><span>Upload em:</span> " . $functions->formatDate($row->date_added, 'TIME') . "</li>";
		$html .= '</ul>';
		$html .= '</div>';
		
		$html .= '<div class="clear"></div>';
		
		$html .= '</div>';
	
	}
	
	$html .= '</div>';
	
	echo $html;
	
	echo $page->getPager();
	
}
else echo $session->getMessage();

$page->footer();
?>