<?php
final class Url {
	
	private $loader;
	
	private $rewrite = true;
	
	private $urls = array();
	
	private $cacheTime = 3600;
	
	private $cacheName = 'urls';
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('Cache', 'library');
		
		$config = $this->loader->get('config');
		$config->databaseLoad(array('friendly-url'));
		
		$this->rewrite = $config->get('friendly-url') == 'ON';
		
		if($this->rewrite) $this->loadDefaultLinks();
	}

	public function rewriteIsOn() {
		return $this->rewrite;
	}
	
	public function get($name, $query=null, $urlBase=true) {
		if($urlBase) $urlBase = $this->loader->getUrlSite() . '/';
		
		if(empty($name)) return $urlBase;
		
		$link = "{$name}.php" . ($query ? "?{$query}" : '');
		
		if(!$this->rewrite) return $urlBase.$link;
		
		$url = null;
		
		if(isset($this->urls[$link])) 
			$url = $this->urls[$link];
		else {
			$url = $this->getUrl($link);
			
			if(is_null($url)) {
				$url = $link;
				$this->urls[$url] = $url;
			}
				
			// Saving routes on cache 
			$this->loader->get('cache')->save($this->cacheName, $this->urls, $this->cacheTime);
		}
		
		return $urlBase.$url;
	}
	
	public function loadDefaultLinks() {
		$cache  = $this->loader->get('cache');
		$config = $this->loader->get('config');
		
		$config->load('route');
		$routes = $config->get('routes');
		
		foreach($routes as $k => $v) {
			$this->urls[$v] = $k;
		}
		
		$urlsCached = $cache->get($this->cacheName);
		if($urlsCached) 
			$this->urls = array_merge($urlsCached, $this->urls);
	}
	
	public function field($label, $query, $base='') {
		$html = '';
		$html .= "<div class=\"field field-url\" id=\"in-url_alias\">";
		
		$label = "<label for=\"fd-link\">{$label}</label>";
		$label = '';
		
		if($this->rewrite) {
			$alias = preg_replace('$' . $base . '$', '', $this->getUrl($query), 1);
			$html .= '<div class="url">';
				$html .= $label . $this->loader->getUrlSite() . "/{$base}<span id=\"url_alias\">{$alias}</span>";
			$html .= '</div>';
			$html .= '<div class="actions">' .
					'	<i class="icon-pencil edit" title="Editar"></i>' .
					'	<i class="icon-ok-sign save" style="display:none;" title="Salvar"></i>' .
					'</div>';
			$html .= '<input type="hidden" name="url_alias" id="fd-url_alias" value="' . $alias . '" />';
			$html .= '<input type="hidden" name="url_query" id="fd-url_query" value="' . $query . '" />';
			$html .= '<script type="text/javascript">fnUrlAlias();</script>';
		}
		else {
			$html .= '<div class="url">';
				$html .= $label . $this->loader->getUrlSite() . "/<span id=\"url-alias\">{$query}</span></strong>";
			$html .= '</div>';
		}
		
		$html .= '</div>';
		
		echo $html;
	}
	
	public function clearUrl($text) {
		$text = strtolower($text);

		$a = utf8_decode('áàãâéêíóôõúüçñ ');
		$b = utf8_decode('aaaaeeiooouucn-');
		$c = '/[^a-zA-Z0-9_.-]/';
		
		return preg_replace($c, '', utf8_encode(strtr(utf8_decode($text), $a, $b)));
	}
	
	public function makeAlias($alias, $query=null) {
		$aliasTest = $alias;
		
		$i = 1;
		while($this->hasAlias($aliasTest, $query)) {
			$aliasTest = $alias . '-' . $i;
			$i++;
		}
		
		return $aliasTest;
	}
	
	public function getUrl($link) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "select " .
				"	alias, " .
				"	query " .
				"from " .
				"	url_alias " .
				"where " .
				"	query = '{$link}'";
		
		$dbq = $database->query($sql);
		if($database->getNumRows($dbq) > 0) {
			$row = $database->getArray($dbq);
			$this->urls[$row['query']] = $row['alias'];
			return $row['alias'];
		}
		
		return null;
	}
	
	public function saveUrl($query, $alias=null, $base='') {
		$database = $this->loader->getObject('DataBase', 'database');
		
		if(empty($alias)) $alias = $query;
		
		$sql = "select 1 from url_alias where query = '{$query}'";
		if($database->get($sql) == '1') {
			$sql = "update " .
					"	url_alias " .
					"set " .
					"	alias = '{$base}{$alias}' " .
					"where " .
					"	query = '{$query}'";
			
			if($database->query($sql)) {
				$this->loader->get('cache')->delete($this->cacheName);
				return true;
			}
		}
		else {
			$id = $database->get('select coalesce(max(url_alias_id), 0) + 1 from url_alias');
			
			$sql = "insert into url_alias (" .
					"	url_alias_id, " .
					"	query, " .
					"	alias" .
					") " .
					"values (" .
					"	{$id}, " .
					"	'{$query}', " .
					"	'{$base}{$alias}'" .
					")";
			
			if($database->query($sql)) return true;
		}
		
		return false;		
	}
	
	public function removeUrl($query) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "delete from url_alias where query = '{$query}'";
		return $database->query($sql);
	}
	
	public function hasAlias($alias, $query=null) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "select 1 from url_alias where lower(alias) = lower('{$alias}')";
		if(!empty($query)) $sql .= " and query != '{$query}'";
		return $database->get($sql) == '1'; 
	}

}
?>