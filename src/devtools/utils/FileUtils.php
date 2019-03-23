<?php
namespace Ubiquity\devtools\utils;

class FileUtils {
	public static function deleteAllFilesFromFolder($folder){
		$files = glob($folder.'/*');
		foreach($files as $file){
			if(is_file($file))
				unlink($file);
		}
	}

	public static function openFile($filename){
		if(file_exists($filename)){
			return file_get_contents($filename);
		}
		return false;
	}

	public static function writeFile($filename,$data){
		return file_put_contents($filename,$data);
	}

	public static function xcopy($source, $dest, $permissions = 0755){
	    $path = pathinfo($dest);
	    if (!file_exists($path['dirname'])) {
	        mkdir($path['dirname'], 0777, true);
	    } 
		if (is_link($source)) {
			return symlink(readlink($source), $dest);
		}
		if (is_file($source)) {
			return copy($source, $dest);
		}
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			self::xcopy("$source/$entry", "$dest/$entry", $permissions);
		}
		$dir->close();
		return true;
	}

	public static function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	public static function safeMkdir($dir){
		if(!is_dir($dir))
			return mkdir($dir,0777,true);
	}

	public static function cleanPathname($path){
		if($path!==null & $path!==""){
			if(DS==="/")
				$path=\str_replace("\\", DS, $path);
				else
					$path=\str_replace("/", DS, $path);
					$path=\str_replace(DS.DS, DS, $path);
					if(!substr($path, -1)=== DS){
						$path=$path.DS;
					}
		}
		return $path;
	}
}