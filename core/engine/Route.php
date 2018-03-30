<?php
final class Route {
	
	private $loader;
	
	private $routes;
	
	private $cacheTime = 3600;
	
	private $cacheName = 'routes';
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('Cache', 'library');
		$this->loadDefaultRoutes();
	}
	
	function getRoute($link) {
		$route = null;
		
		if(isset($this->routes[$link])) $route = $this->routes[$link];
		else $route = $this->loadRoute($link);
		
		if(!is_null($route)) {
			$route = explode("?", $route);
			
			if(isset($route[1])) {
				$queries = explode("&", $route[1]);
				
				foreach($queries as $query) {
					$kv = explode("=", $query);
					if(isset($kv[1])) $_GET[$kv[0]] = $kv[1];
					else $_GET[$kv[0]];
				}
			}
			
			$route = $route[0];
			
			unset($_GET['route']);
		}
		else $route = '404.php';
		
		return $route;
	}
	
	function loadDefaultRoutes() {
		$cache  = $this->loader->get('cache');
		$config = $this->loader->get('config');
		
		$config->load('route');
		$this->routes = $config->get('routes');
		
		$routesCached = $cache->get($this->cacheName);
		if($routesCached) 
			$this->routes = array_merge($routesCached, $this->routes);
	}
	
	function loadRoute($link) {
		$cache    = $this->loader->get('cache');
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "select " .
				"	alias, " .
				"	query " .
				"from " .
				"	url_alias " .
				"where " .
				"	alias = '{$link}'";
		
		$dbq = $database->query($sql);
		if($database->getNumRows($dbq) > 0) {
			$row = $database->getArray($dbq);
			$this->routes[$row['alias']] = $row['query'];
			
			// Saving routes on cache
			$cache->save($this->cacheName, $this->routes, $this->cacheTime);
			
			return $row['query'];
		}
		
		return null;
	} 

}
?>