<?php
if(!isset($loader)) { 
	require('core/Loader.php');
	$loader = new Loader();
}


$url   = $loader->getObject('Url');
$page     = $loader->getObject('Page');
$_cat     = $loader->getObject('Category', 'class', false);
$_post    = $loader->getObject('Post', 'class', false);
$_mvtv   = $loader->getObject('MVTV', 'class', false);
$_feiras   = $loader->getObject('Feiras', 'class', false);
$_revista = $loader->getObject('Revista', 'class', false);

$image =  $page->get('image');

$slider = $loader->getObject('Slider', 'class', false);
$sliderData = $slider->getData('home');

$functions = $page->get('functions');

$cat = $_cat->get($id);

$page->header($cat['name'], array('home.css'));

$config = $loader->get('config');
$cor=$config->get('site-color');

$limit = 7;
$data = array(
	'start'  => isset($p) ? $p : 0,
	'limit'  => $limit,
	'order'  => 'DESC',
	'filter' => array('cat' => $cat['id'])
);
$result = $_post->getList($data);

unset($_GET['id']);

$itens = $result['itens'];
?>

<div class="col-left">	
	<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="news">NOTÍCIAS</a></p></div>
	<div class="colfull">
	
		<?php
		$i=1;
		foreach($itens as $post) { 
		 
			$link = $url->get('post', "id={$post['id']}");
			if($i==1){
			?>	
			<article class='first'>
				<?php if(!empty($post['image_id'])) { 
					$largura = "<script type =text/javascript> var largura =  $(document).width(); document.write(largura); </script>";
 					//$largura = "850";
					if($largura>='940'){
						echo "";
						$tan="W=228|H=200";
						}

					else{
						echo '..';
						$tan="W=300|H=202";
						}
					} 
					
					?>
					<div class="image" data-post-id="<?php echo $post['id']; ?>">
						<a href="<?php echo $link; ?>"><?php echo $image->getImage($post['image_id'], $post['image_name'], 'W=300|H=240|CREATE|METHOD=mycrop'); ?></a>
					</div>
				<h2 title="<?php echo $post['title']; ?>"><a class="page-title" href="<?php echo $link; ?>" style="color:<?php echo $cor;?>; font-size: 17px;" ><?php echo $functions->cutText(strip_tags($post['title']), 55);?></a></h2>
				<p><?php echo $functions->limitarTexto(html_entity_decode(strip_tags($post['content']), ENT_COMPAT, 'UTF-8'),315, FALSE);?></p>
				
				
				<p><a class="leia-mais" href="<?php echo $link; ?>" style="color:<?php echo $cor;?>;position: absolute;">Leia Mais</a></p>
				
			</article>
			<?php 
			$i++;
			}
			
			else if($i==4){
				echo '<div class="clear"></div>';
				?>
				<article class='post'>
					<?php if(!empty($post['image_id'])) { ?>
					<div class="image" data-post-id="<?php echo $post['id']; ?>">
						<a href="<?php echo $link; ?>"><?php echo $image->getImage($post['image_id'], $post['image_name'], 'W=225|H=160|CREATE|METHOD=mycrop'); ?></a>
					</div>
					<?php } ?>
					<h2 title="<?php echo $post['title']; ?>"><a class="page-title" href="<?php echo $link; ?>"><?php echo $functions->cutText(strip_tags(html_entity_decode($post['title'], ENT_COMPAT, 'UTF-8')), 55);?></a></h2>
					<p><?php echo $functions->limitarTexto(strip_tags(html_entity_decode($post['content'], ENT_NOQUOTES, 'UTF-8')), 70, FALSE);?></p>
					
					<p><a class="leia-mais" href="<?php echo $link; ?>" style="color:<?php echo $cor;?>;position: absolute;">Leia Mais</a></p>
					
				</article>
				<?php 
				$i++;
			}
			else{
			?>
			<article class='post'>
				<?php if(!empty($post['image_id'])) { ?>
				<div class="image" data-post-id="<?php echo $post['id']; ?>">
					<a href="<?php echo $link; ?>"><?php echo $image->getImage($post['image_id'], $post['image_name'], 'W=225|H=160|CREATE|METHOD=mycrop'); ?></a>
				</div>
				<?php } ?>
				<h2 title="<?php echo $post['title']; ?>"><a class="page-title" href="<?php echo $link; ?>"><?php echo $functions->cutText(strip_tags(html_entity_decode($post['title'], ENT_COMPAT, 'UTF-8')), 55);?></a></h2>
				<p><?php echo $functions->limitarTexto(strip_tags(html_entity_decode($post['content'], ENT_COMPAT, 'UTF-8')), 70, FALSE);?></p>
				
				<p><a class="leia-mais" href="<?php echo $link; ?>" style="color:<?php echo $cor;?>;position: absolute;">Leia Mais</a></p>
				
			</article>
			<?php 
			$i++;
			}
		}
		
		?>
		<div class="clear"></div>
	</div>
	<?php 
// 		$_banner = $loader->getObject('Banner', 'class', false);
// 		$bannerList = $_banner->getByType(Banner::BANNER_TYPE_LATERAL, 2);
		
// 		foreach($bannerList as $banner) {
// 			echo "<div syle='width:48%; margin-left:4%'><div class='bannersm'>";
// 				$_banner->render($banner);
// 				$_banner->updateViews($banner);
// 			echo "</div></div>";
			
// 		} 
// 	?>
	<div class="col2">
		<?php 
				$data = array(
						'revista'  => 1,
						'status'  => '1',
				);
				$rev = $_revista->get($data);
				
				$img = array(
						'capac'  => 'ddd',
						'id'  => $rev['id']
				);
				
				$linkrev = "revista.php?id=".$rev['id'];
		?>
		<div class="main-titlep">
			<p  style="border-top:2px solid <?php echo $cor;?>;"> <a href="revista.php?revista=1">REVISTA MV</a></p>
			<a href="anuncie" class="button" style="background:<?php echo $cor;?>;">Anuncie</a>
			<a href="<?php echo $rev['assine']; ?>" class="button" style="background:<?php echo $cor;?>;">Assine</a>
		</div>
		<div class="full">
			<?php 
				$fImage = $_revista->getFeaturedImage($img);
				if($fImage) echo "<a href='". $linkrev."'>".$image->getImage($fImage['id'], $fImage['name'], 'W=150|H=200|LEFT|CREATE|METHOD=mycrop')."</a>";			
			?>
			<p><a class="page-title" href="<?php echo $linkrev; ?>">Ed. <?php echo $rev['edicao']; ?> - <?php echo $_post->formatDate($rev['cadastro'], 'F\/Y'); ?></a></p>
			<p><?php echo $functions->cutText(strip_tags(html_entity_decode($rev['descricao'], ENT_COMPAT, 'UTF-8')), 380);?></p>
			<p><a class="leia-mais" href="<?php echo $linkrev; ?>"  style="color:<?php echo $cor;?>;">Leia Mais</a></p>
		</div>
		<div class="full">
			<div class="slider responsive magazine" id="slider-mv">
				<div class="slider-content">
					<ul>
						<?php 
						$data = array(
								'status'  => '1',
								'revista'  => 1,
								'id'  => $rev['id']
						);
						
						$result = $_revista->getList($data);
						
						$i = 1;
						foreach($result as $slider) {
							echo '<li>';
							
							$img = array(
									'capap'  => 'sdadas',
									'id'  => $slider['id']
							);
							$fImage = $_revista->getFeaturedImage($img);
							$link = "revista.php?id=".$slider['id'];
							//$link = $url->get('revista', "id={$slider['id']}");
							
							//if($fImage) echo $image->getImage($fImage['id'], $fImage['name'], 'W=57|CREATE|LEFT|METHOD=mycrop'); 
							if($fImage){
								echo "<div class='nini-revista'>" ;
									echo "<a class='page-title' href=".$link."><span>ED.". $slider['edicao']."</span></a>";
									echo $image->getImage($fImage['id'], $fImage['name'], 'W=57|CREATE|LEFT|METHOD=mycrop');
								echo "</div>";
							} 	
							
							echo '</li>';
							$i++;
						} 
						?>
					</ul>
				</div>
				<div class="slider-nav">
					<span class="arrow prev" data-action="prev" style="background-color:<?php echo $cor;?>">Anterior</span>
					<span class="arrow next" data-action="next" style="background-color:<?php echo $cor;?>">PrÃ³xima</span>
				</div>
			</div>
			<script type="text/javascript">
				$(function() {
					$('#slider-mv').responsiveSlider();
				});
			</script>
		</div>
	</div>
	
	<div class="slider">
		<?php if($sliderData): ?>
			<div class="slider-home">
				<div class="slider" id="slider-home">
					<div class="slider-content">
						<ul>
							
							<?php foreach($sliderData['items'] as $item): 
								?>
							<li>
								<?php if($item['link']): ?><a href="<?php echo $item['link']; ?>" target="_blank"><?php endif ?><?php 
									echo $item['image']; ?><?php if($item['link']): ?></a><?php endif ?>
							</li>
							<?php $i++; endforeach; ?>
						</ul>
					</div>
					<div class="slider-nav">
						
						<ul class="items">
							<?php for($i = 0; $i < $sliderData['count']; $i++): ?>
							<li><?php echo $i + 1; ?></li>
							<?php endfor; ?>
						</ul>
					</div>
				</div>
			</div>
			<script type="text/javascript">
			$(function() {
				var largura = $('div#slider-mv').width();
				$('#slider-home').slider({width:486,height:379});
			});
			</script>
		<?php endif; ?>
	</div>
	<div class="clear"></div>
	<div class="col2">
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;">REVISTAS COMPLEMENTARES</p></div>
			<div class="full">
				<div class="complementar">
				<?php 
					$data = array(
							'revista'  => 2,
							'status'  => '1',
					);
					$rev = $_revista->get($data);
					
					//$link = $url->get('revista', "id={$rev['id']}");
					$link = "revista.php?id=".$rev['id'];
					$teste="dsadsas";
					$img = array(
							'capac'  => $teste,
							'id'  => $rev['id']
					);
						
					$fImage = $_revista->getFeaturedImage($img);
					
				?>
				<a class="page-title" href="<?php echo $link;?>"><img src="upload/files/image/<?php echo $fImage['id'].'/'.$fImage['name']?>" id="img-5373" style="width:100%; max-width:123px;" alt="<?php echo $fImage['name']?>"></a>
				<div class="clear"></div>
				<p>MV Decor <?php echo $rev['edicao']; ?></p>
				<p><?php echo $functions->cutText(strip_tags(html_entity_decode($rev['descricao'], ENT_COMPAT, 'UTF-8')), 40);?></p>
				<p><a class="leia-mais" href="<?php echo $link; ?>" style="color:<?php echo $cor;?>;">Leia Mais</a></p>
			</div>
			
			<div class="complementar">
				<?php 
					$data = array(
							'revista'  => 3,
							'status'  => '1',
					);
					$rev = $_revista->get($data);
					
					//$link = $url->get('revista', "id={$rev['id']}");
					$link = "revista.php?id=".$rev['id'];
					$teste="dsadsas";
					$img = array(
							'capac'  => $teste,
							'id'  => $rev['id']
					);
						
					$fImage = $_revista->getFeaturedImage($img);
					
				
				?>
				<a class="page-title" href="<?php echo $link;?>"><img src="upload/files/image/<?php echo $fImage['id'].'/'.$fImage['name']?>" id="img-5373" style="width:100%; max-width:123px;" alt="<?php echo $fImage['name']?>"></a>
				<div class="clear"></div>
				<p>MV Norte e Nordeste <?php echo $rev['edicao']; ?></p>
				<p><?php echo $functions->cutText(strip_tags(html_entity_decode($rev['descricao'], ENT_COMPAT, 'UTF-8')), 35);?></p>
				<p><a class="leia-mais" href="<?php echo $link; ?>" style="color:<?php echo $cor;?>;">Leia Mais</a></p>
			</div>
			
			<div class="complementar">
				<?php 
					$data = array(
							'revista'  => 5,
							'status'  => '1',
					);
					$rev = $_revista->get($data);
					
					//$link = $url->get('revista', "id={$rev['id']}");
					$link = "revista.php?id=".$rev['id'];
					$teste="i";
					$img = array(
							'capac'  => $teste,
							'id'  => $rev['id']
					);
						
					$fImage = $_revista->getFeaturedImage($img);
					
				?>
				<a class="page-title" href="<?php echo $link;?>"><img src="upload/files/image/<?php echo $fImage['id'].'/'.$fImage['name']?>" id="img-5373" style="width:100%; max-width:123px;" alt="<?php echo $fImage['name']?>"></a>
				<div class="clear"></div>
				<p>REVISTA 100% PDV <?php echo $rev['edicao']; ?></p>
				<p><?php echo $functions->cutText(strip_tags(html_entity_decode($rev['descricao'], ENT_COMPAT, 'UTF-8')), 38);?></p>
				<p><a class="leia-mais" href="<?php echo $link; ?>"  style="color:<?php echo $cor;?>;">Leia Mais</a></p>
			</div>
		</div>
	</div>
	
	<div class="col3">
		<?php 
		$filtro= array( 
				'status' => 1,
				'order'  => 'DESC'
		);
		$video = $_mvtv->get($filtro);
		
		$link = $url->get('mvtv', "id={$video['id']}");
		$fImage = $_mvtv->getFeaturedImage($video['id']);
		?>
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="mvtv">MVTV</a></p></div>
			<div class="full">
				<div class="image" data-post-id="<?php echo $post['id']; ?>">
					<a href="<?php echo $link; ?>"><?php echo $image->getImage($fImage['id'], $fImage['name'], $img.'W=200|H=118|CREATE|METHOD=mycrop'); ?></a>
				</div>
				<h2><?php echo $video['titulo']; ?></h2>
				
				<p><?php echo $functions->cutText(strip_tags(html_entity_decode($video['descricao'], ENT_COMPAT, 'UTF-8')),60);?></p>
				
				<p><a class="leia-mais" href="<?php echo $link; ?>" style="color:<?php echo $cor;?>;">Assista Agora</a></p>
			</div>
	</div>
	<?php $block = $loader->getObject('StaticBlock', 'class', false); ?>
	<div class="col3">
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="http://blogdoari.moveisdevalor.com.br/">Blog do Ari</a></p></div>
		<?php 
		function curl($url){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);
				return $data;
			}
			//Busca dados do blog
			$jsonString = curl("https://blogdoari.com.br/apimv.php");
		
			if (0 === strpos(bin2hex($jsonString), 'efbbbf')) {
			    $jsonString = substr($jsonString, 3);
			}
			
			$data = json_decode($jsonString);
			
			
			if($data[0]->idA!='0'){
				$img = str_replace("http", "https", $data[0]->img);
				?>
				<div class="full">
				<div class="image" data-post-id="<?php echo $post['id']; ?>">
					<a href="http://blogdoari.com.br/artigos?id=<?php echo $data[0]->idA; ?>">
					<?echo $img;?></a>
				</div>
				<h2><?php echo $data[0]->fc_title; ?></h2>
				
				<p><?php echo $functions->cutText(strip_tags(html_entity_decode($data[0]->fc_content, ENT_COMPAT, 'UTF-8')),60);?></p>
				
				<p><a class="leia-mais" href="http://blogdoari.com.br/artigos?id=<?php echo $data[0]->idA; ?>" style="color:<?php echo $cor;?>;">Leia Mais</a></p>
				</div>
				
				<?php 
			}
		?>
	</div>
	<div class="clear"></div>
	<div class="col4">
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="dmaisc">AGÊNCIA D + C</a></p></div>
		<div class="full">
			<center><?php echo $block->get('home-dmaisc'); ?> </center>
			<p><a class="leia-mais" href="dmaisc" style="color:<?php echo $cor;?>;position: absolute;">Leia Mais</a></p>
		</div>
	</div>
	
	<div class="col4">
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="intelligence-consultoria">INTELLIGENCE CONSULTORIA</a></p></div>
		<div class="full">
			<center><?php echo $block->get('home-consultoria'); ?> </center>
			<p><a class="leia-mais" href="intelligence-consultoria" style="color:<?php echo $cor;?>;position: absolute;">Leia Mais</a></p>
		</div>
	</div>
	
	<div class="colr4">
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="http://impulso.net.br/">INSTITUTO IMPULSO</a></p></div>
		<div class="full">
			<center><?php echo $block->get('home-impulso'); ?> </center>
			<p><a class="leia-mais" href="http://impulso.net.br/" style="color:<?php echo $cor;?>;position: absolute;">Leia Mais</a></p>
		</div>
	</div>
</div>

<div class="col-right">

	<?php 
			$_banner = $loader->getObject('Banner', 'class', false);
			$bannerList = $_banner->getByType(Banner::BANNER_TYPE_LATERAL_FULL, 1);
			
			foreach($bannerList as $banner) {
				echo "<div class='banners'>";
					$_banner->render($banner);
					$_banner->updateViews($banner);
				echo "</div>";
				
			} 

			$bannerList = $_banner->getByType(Banner::BANNER_TYPE_LATERAL, 4);
			
			foreach($bannerList as $banner) {
				echo "<div class='banners'>";
					$_banner->render($banner);
					$_banner->updateViews($banner);
				echo "</div>";
				
			} 
			$bannerList = $_banner->getByType(Banner::BANNER_TYPE_OCULT, 1);
				
			foreach($bannerList as $banner) {
				echo "<div class='banners' style='display:none'>";
				$_banner->render($banner);
				$_banner->updateViews($banner);
				echo "</div>";
			
			}
	?>
	
		
	<div class="colfull">
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="cursos-ead">Cursos Ead</a></p></div>
		<center><?php echo $block->get('home-cursos'); ?> </center>
	</div>
	
<!-- 	<div class="colfull"> -->
	<!--	<div class="main-titlep"><p style="border-top:2px solid <?php //echo $cor;?>;"><a href="pesquisas">Pesquisas</a></p></div>-->
<!-- 		<div class="full"> -->
	<!--		<center><?php //echo $block->get('home-pesquisas'); ?> </center>-->
	<!--		<p><a class="leia-mais" href="pesquisas" style="color:<?php //echo $cor;?>;">Leia Mais</a></p>-->
<!-- 		</div> -->
<!-- 	</div> -->
	
	<div class="colfull">
		
		<div class="main-titlep"><p style="border-top:2px solid <?php echo $cor;?>;"><a href="feiras-e-eventos">Proximos Eventos</a></p></div>
		<?php 
		$filtro= array(
				'status' => '1',
				'limit' => '5',
				'date' => date('Y-m-d'),
				
		);
		$result = $_feiras->getlist($filtro);
		foreach($result as $itens) {
			echo "<div class='feiras'>";	
				echo "<p>".$itens['titulo']." - ".$_feiras->formatDate($itens['date_ini'], 'd\ ')." a ".$_feiras->formatDate($itens['date_fim'], 'd\/m')."</p>";
				echo "<p><a class='leia-mais' href=http://".$itens['link']." style='color:".$cor."; margin-left: 10px; margin-top:5px;'>SAIBA MAIS</a></p>";		
			echo "</div>";
		}
		?>
		<center><p><a class="leia-mais" href="feiras-e-eventos" style="color:<?php echo $cor;?>;text-align: center; float: none; margin:10px 0">CLIQUE AQUI PARA VER MAIS EVENTOS</a></p></center>
		<div class="clear"></div>
     </div>
     
     <div class="colfull">
		
		<?php echo $block->get('prev-temp'); ?> 

     </div>
</div>


<?php 
$page->footer(); 

?>

<script>

$(function(){
	var $conteudo    = $('#conteudo').width(); // largura total
	var $banner	   = $('#banner'); // objeto banner
	var $tempo	   = 100000; // milisegundos
	var $intervalo;

	// evento click
	$(".fechar").click(function(event){
		event.preventDefault();
		fechar(); // chamada a funÃ§Ã£o
	});

		// funcao que fecharÃ¡ o banner
		function fechar(){
			$banner.animate({opacity: '-=1.0'}, 700);
			$("#banner").hide("slow");
		}

		// funcao para contagem
		function contador(){
			$intervalo = window.setInterval(function() {
				var tempoContagem 	= $("#contador").html();
				var atualizaContagem 	= eval(tempoContagem) - eval(1);
				$("#contador").html(atualizaContagem);

				// chegando em zero o contador Ã© parado
				if(atualizaContagem == 0){
					pararContagem();
				}
			}, 1000);
		}

		// funcao para limpar o contador
		function pararContagem(){
			window.clearInterval($intervalo);
		}

		// deslocamento do banner
		$banner.show();
		$banner.animate({opacity: '+=1.0'}, 900);
		// chamada da funcao que farÃ¡ a contagem
		contador();
		// setando o tempo de execuÃ§Ã£o do banner
		setTimeout(fechar, $tempo*1000);

		//Centraliza Div #banner
		var banner = document.querySelector('.conteudo'),
		w = window,
		d = document,
		e = d.documentElement,
		g = d.getElementsByTagName('body')[0],
		x = w.innerWidth || e.clientWidth || g.clientWidth,
		y = w.innerHeight|| e.clientHeight|| g.clientHeight;

		banner.style.top = (y / 2) - (banner.clientHeight / 2) + 'px';
		banner.style.left = (x / 2) - (banner.clientWidth / 2) + 'px';
});



</script>


<style type="text/css">
/*Banner */

#banner{
position: fixed;
margin-top:0%;
/* margin-left:50%; */
top:00px;
width:100%; /* largura */
height:100%;
display:none;
color:#FFFFFF;
/* background: rgba(255, 255, 255, 0.7); */
background: rgba(0, 0, 0, 0.7);
opacity: 0.0;
}
#conteudo{
position:fixed;
width:630px; /* largura */
height:436px;
z-index:9999999999999;
}
#banner a{ color:#FFFFFF;text-decoration:none }
#banner p { padding: 5px 10px 0; }
p.link{clear:both; }
#fechar{
font-family:Verdana, Geneva, sans-serif;
font-size:10px;
font-weight:bold;
position:absolute;
float:right;
width:20px;
height:20px;
color:#3f050a;
text-align:center;
top:5px;
right:0px;
}

</style>
<!-- </div> -->
<!-- <div id="banner" class="banner"> -->
<!--  	<div id="conteudo" class="conteudo"> -->
<!-- 		<div id="fechar"> -->
		<!--  <a href="#" title="Fechar" class="fechar" style="color:#FFF">X</a>-->
<!--     	</div>  -->
	<!--  <a href="http://moveisdevalor.com.br/portal/final-de-ano"><img src="popup2/pop-up-fimdeano.jpg" alt="Moveis de valor" width="630" style="margin-top: -5px;"></a>-->
<!--    </div>   -->
<!-- </div>  -->
<!-- <div class="container"> -->