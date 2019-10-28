<?php
namespace  Ubiquity\devtools\cmd;

class ConsoleFormatter {
	const BLACK='0;30',DARK_GREY='1;30',BLUE='0;34',LIGHT_BLUE='1;34',GREEN='0;32',LIGHT_GREEN='1;32',CYAN='0;36',LIGHT_CYAN='1;36',RED='0;31',LIGHT_RED='1;31',PURPLE='0;35',LIGHT_PURPLE='1;35',BROWN='0;33',YELLOW='1;33',LIGHT_GRAY='0;37',WHITE='1;37';
	const BG_BLACK='40',BG_RED='41',BG_GREEN='42',BG_YELLOW='43',BG_BLUE='44',BG_MAGENTA='45',BG_CYAN='46',BG_LIGHT_GRAY='47';
	const BOLD='1',END_BOLD='22',CLEAR='0';

	/**
	 * Returns a colored string
	 * @param string $string
	 * @param string $color
	 * @param string $bgColor
	 * @return string
	 */
	public static function colorize($string, $color = null, $bgColor = null) {
		if(!self::isSupported()){
			return $string;
		}
		$coloredString = "";
		if (isset($color)) {
			$coloredString .= self::escape($color);
		}
		if (isset($bgColor)) {
			$coloredString .= self::escape($bgColor);
		}
		$coloredString .=  $string .self::escape(self::CLEAR);
		return $coloredString;
	}

	private static function prefixLines($str,$prefix){
		$lines=explode("\n", $str);
		array_walk($lines, function(&$line) use($prefix){if(trim($line)!=null) $line=$prefix.$line;});
		return implode("\n", $lines);
	}

	private static function escape($value){
		return "\033[{$value}m";
	}

	public static function showInfo($content,$dColor=self::CYAN){
		return self::colorize(self::formatContent($content),$dColor);
	}

	/**
	 * @return boolean
	 */
	public static function isSupported()
	{
		if (DIRECTORY_SEPARATOR === '\\') {
			if (\function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT)) {
				return true;
			} elseif ('10.0.10586' === PHP_WINDOWS_VERSION_MAJOR.'.'.PHP_WINDOWS_VERSION_MINOR.'.'.PHP_WINDOWS_VERSION_BUILD
				|| false !== \getenv('ANSICON')
				|| 'ON' === \getenv('ConEmuANSI')
				|| 'xterm' === \getenv('TERM')) {
				return true;
			}
			return false;
		} else {
			return function_exists('posix_isatty') && @posix_isatty(STDOUT);
		}
	}

	public static function formatContent($content,$prefix="    · "){
		$content = str_replace ( "<br>", "\n", $content );
		$content=self::formatHtml($content);
		$content= strip_tags ( $content );
		return "\n".self::prefixLines($content,$prefix)."\n";
	}

	public static function showMessage($content, $type='info',$title=null) {
		$header=" ■ ".$type;
		if(isset($title)){
			$header.=' : '.$title;
		}
		$result=self::formatContent($content);
		switch ($type){
			case 'error':
				$header=self::colorize($header,self::LIGHT_RED);
				break;
			case 'success':
				$header=self::colorize($header,self::GREEN);
				break;
			case 'info':
				$header=self::colorize($header,self::CYAN);
				break;
			case 'warning':
				$header=self::colorize($header,self::LIGHT_GRAY);
				break;
		}
		$result=rtrim($result,"\n");
		return ConsoleTable::borderType([[$header.$result]], $type);
	}

	public static function formatHtml($str){
		$reg='@<(b)>(.+?)</\1>@i';
		if(!self::isSupported()){
			return preg_replace($reg, '$2', $str);
		}
		return preg_replace($reg, self::escape(self::BOLD).'$2'.self::escape(self::END_BOLD), $str);
	}
}

