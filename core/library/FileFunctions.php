<?php
final class FileFunctions {

	static function readFile($file) {
		if(!file_exists($file)) return false;

		if(function_exists('file_get_contents')) 
			return file_get_contents($file);
		
		if (!$fp = @fopen($file, FOPEN_READ)) return false;
		
		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0) 
			$data =& fread($fp, filesize($file));

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}
	
	static function writeFile($path, $data, $mode = 'wb') {
		if (!$fp = @fopen($path, $mode)) return false;

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;
	}
	
	static function deleteFiles($path, $del_dir = false, $level = 0) {
		// Trim the trailing slash
		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if (!$current_dir = @opendir($path)) return false;

		while (false !== ($filename = @readdir($current_dir))) {
			if ($filename != "." and $filename != "..") {
				if (is_dir($path.DIRECTORY_SEPARATOR.$filename)) {
					// Ignore empty folders
					if (substr($filename, 0, 1) != '.') {
						self::deleteFiles($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
					}
				}
				else 
					unlink($path.DIRECTORY_SEPARATOR.$filename);
			}
		}
		@closedir($current_dir);

		if ($del_dir == TRUE AND $level > 0) {
			return @rmdir($path);
		}

		return TRUE;
	}
	
	static function getFilenames($source_dir, $include_path = false, $_recursion = false) {
		static $_filedata = array();

		if ($fp = @opendir($source_dir)) {
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === false) {
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			while (false !== ($file = readdir($fp))) {
				if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0) {
					self::getFilenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0) {
					$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
				}
			}
			return $_filedata;
		}
		else return false;
	}

	static function getDirFileInfo($source_dir, $top_level_only = TRUE, $_recursion = false) {
		static $_filedata = array();
		$relative_path = $source_dir;

		if ($fp = @opendir($source_dir)) {
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === false) {
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			// foreach (scandir($source_dir, 1) as $file) // In addition to being PHP5+, scandir() is simply not as fast
			while (false !== ($file = readdir($fp))) {
				if (@is_dir($source_dir.$file) AND strncmp($file, '.', 1) !== 0 AND $top_level_only === false) {
					self::getDirFileInfo($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0) {
					$_filedata[$file] = self::getFileInfo($source_dir.$file);
					$_filedata[$file]['relative_path'] = $relative_path;
				}
			}

			return $_filedata;
		}
		else return false;
	}

	static function getFileInfo($file, $returned_values = array('name', 'server_path', 'size', 'date')) {

		if (!file_exists($file)) return false;

		if (is_string($returned_values))
			$returned_values = explode(',', $returned_values);
		
		foreach ($returned_values as $key) {
			switch ($key) {
				case 'name':
					$fileinfo['name'] = substr(strrchr($file, DIRECTORY_SEPARATOR), 1);
					break;
				case 'server_path':
					$fileinfo['server_path'] = $file;
					break;
				case 'size':
					$fileinfo['size'] = filesize($file);
					break;
				case 'date':
					$fileinfo['date'] = filemtime($file);
					break;
				case 'readable':
					$fileinfo['readable'] = is_readable($file);
					break;
				case 'writable':
					// There are known problems using is_weritable on IIS.  It may not be reliable - consider fileperms()
					$fileinfo['writable'] = is_writable($file);
					break;
				case 'executable':
					$fileinfo['executable'] = is_executable($file);
					break;
				case 'fileperms':
					$fileinfo['fileperms'] = fileperms($file);
					break;
			}
		}

		return $fileinfo;
	}
	
}