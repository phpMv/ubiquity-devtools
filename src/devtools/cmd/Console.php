<?php
namespace  Ubiquity\devtools\cmd;

class Console {
	public static function readConsoleInput(){
		return readline();
	}

	public static function question($prompt,array $propositions=null){
		echo ConsoleFormatter::colorize($prompt,ConsoleFormatter::BLACK,ConsoleFormatter::BG_YELLOW);
		if(is_array($propositions)){
			if(sizeof($propositions)>2){
				$props="";
				foreach ($propositions as $index=>$prop){
					$props.="[".($index+1)."] ".$prop."\n";
				}
				echo ConsoleFormatter::formatContent($props);
				do{
					$answer=self::readConsoleInput();
				}while((int)$answer!=$answer || !isset($propositions[(int)$answer-1]));
				$answer=$propositions[(int)$answer-1];
			}else {
				echo " (".implode("/", $propositions).")\n";
				do{
					$answer=self::readConsoleInput();
				}while(array_search($answer, $propositions)===false);
			}
		}else{
			$answer=self::readConsoleInput();
		}

		return $answer;
	}

	public static function isYes($answer){
		return array_search($answer, ["yes","y"])!==false;
	}

	public static function isNo($answer){
		return array_search($answer, ["no","n"])!==false;
	}

	public static function isCancel($answer){
		return array_search($answer, ["cancel","z"])!==false;
	}
}
