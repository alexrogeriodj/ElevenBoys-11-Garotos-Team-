<?php 
class Cache {

	private $loader;
	private $cachePath;

	public function __construct($loader) {
		$this->loader = $loader;
		$this->loader->load('FileFunctions', 'library');
		$this->cachePath = $this->loader->getRootPath() . '/cache/';
	}

	public function get($name) {
		//$name = md5($name);
		if (!file_exists($this->cachePath.$name)) return false;
		
		$data = FileFunctions::readFile($this->cachePath.$name);
		$data = unserialize($data);
		
		if (time() >  $data['time'] + $data['ttl']) {
			unlink($this->cachePath.$name);
			return false;
		}
		
		return $data['data'];
	}

	public function save($name, $data, $ttl = 60) {
		//$name = md5($name);	
		$contents = array(
				'time'	=> time(),
				'ttl'	=> $ttl,			
				'data'	=> $data
			);
		
		if (FileFunctions::writeFile($this->cachePath.$name, serialize($contents))) {
			@chmod($this->cachePath.$name, 0777);
			return true;			
		}

		return false;
	}

	public function delete($name, $multiple=true) {
		//$name = md5($name);
		if($multiple) {
			$files = glob($this->cachePath.$name);
		
			if($files) {
	    		foreach($files as $file) {
	      			if(file_exists($file)) 
	      				unlink($file);
	    		}
			}
		}
		else {
			if(file_exists($this->cachePath.$name)) 
				unlink($this->cachePath.$name);
		}
	}
	
	public function clean() {
		return FileFunctions::deleteFiles($this->cachePath);
	}

	public function cacheInfo($type = NULL) {
		return FileFunctions::getDirFileInfo($this->cachePath);
	}

	public function getMetadata($name) {
		//$name = md5($name);
		if(!file_exists($this->cachePath.$name)) return false;

		$data = FileFunctions::readFile($this->cachePath.$name);
		$data = unserialize($data);

		if (is_array($data)) {
			$mtime = filemtime($this->cachePath.$name);

			if (!isset($data['ttl'])) return false;

			return array(
				'expire'	=> $mtime + $data['ttl'],
				'mtime'		=> $mtime
			);
		}

		return false;
	}

}