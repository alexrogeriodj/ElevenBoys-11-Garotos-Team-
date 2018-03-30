<?php
require('../../core/Loader.php');
$loader = new Loader();

$page  = $loader->getObject('Page');
$image = $loader->getObject('Image', 'library');
$_post = $loader->getObject('Post', 'class', false);

$images = $_post->getAllImages($id);

$page->formIni('post-twitter');
?>
	
	<?php $page->field('A', 'Mensagem', 'twitter_message', '', 'CLASS=colfull'); ?>
	
	<div class="image-gallery clear">
		<label>Selecione uma imagem:</label>
		<ul>
			<li class="alpha no-image active">
				<span><i class="icon-picture"></i>Nenhuma</span>
			</li>
			<?php $i = 1; ?>
			<?php foreach($images as $item) { ?>
			<li<?php if($i++ % 6 == 0) { ?> class="alpha"<?php } ?> data-image-id="<?php 
				echo $item['id']; ?>" data-image-name="<?php echo $item['name']; ?>">
				<?php echo $image->getImage($item['id'], $item['name'], 'W=80|H=80|CREATE|METHOD=mycrop'); ?>
			</li>
			<?php } ?>
		</ul>
	</div>

<?php $page->formEnd(); ?>