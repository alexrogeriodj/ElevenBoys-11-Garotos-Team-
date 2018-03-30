<?php
final class Player {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
	}

	public function getSrc($server, $link) {
		$src = '';
		
		switch ($server) {
			case 'Y': $src = 'http://www.youtube.com/embed/' . $this->getCodeYoutube($link); break;
			case 'V': $src = 'http://player.vimeo.com/video/' . $this->getCodeVimeo($link);  break;
		}
		
		return $src;
	}

	public function player($server, $link, $play='N', $w='626', $h='352') {
		
		$player = '';
		
		switch ($server) {
			case 'Y': $player = $this->youtube($link, $play, $w, $h); break;
			case 'V': $player = $this->vimeo($link, $play, $w, $h);   break;
			default:  $player = $this->flvf4v($link, $play, $w, $h);  break; 
		}
		
		return $player;
		
	}
	
	public function youtube($link, $play='N', $w='626', $h='352') {
		return '<iframe src="http://www.youtube.com/embed/' . $this->getCodeYoutube($link) . 
				'?rel=0' . ($play == 'S' ?  '&amp;autoplay=1' : '') . '" width="' . $w . '" height="' . $h . '" ' .
				'frameborder="0" allowfullscreen></iframe>';
	}
	
	public function vimeo($link, $play='N', $w='626', $h='352') {
		return  '<iframe src="http://player.vimeo.com/video/' . $this->getCodeVimeo($link) . 
				($play == 'S' ?  '?autoplay=1' : '') . '" ' .
				'width="' . $w . '" height="' . $h . '" frameborder="0" ' .
				'webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}
	
	public function flvf4v($link, $play='N', $w='626', $h='352') {
		$style = 'display:block;width:' . $w . 'px;height:' . $h . 'px;';
		$html = '<a id="sdi-player" href="' . $link . '" style="' . $style . '"></a>';
		$html .= '<script type="text/javascript"> ';
		$html .= 'flowplayer("sdi-player", "' . $this->loader->getRootPath() . '/flowplayer/flowplayer-3.2.7.swf"); ';
		$html .= '</script>';
		return $html;
	}
	
	public function getCodeYoutube($url) {
		return preg_match('/(v=)([^&]+)/', $url, $matches) ? $matches[2] : $url;
	}
	
	public function getCodeVimeo($url) {
		return preg_match('/^http:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/', $url, $matches) ? $matches[3] : $url;
	}
	
}
?>