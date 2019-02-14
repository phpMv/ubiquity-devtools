<?php
class ConsoleTable {
	const TOP_LEFT='┌', TOP_RIGHT='┐';

	const H_LINE='─',V_LINE='│';

	const M_COL_TOP='┬',M_COL_ROW='┼',M_COL_BOTTOM='┴';

	const M_ROW_LEFT='├',M_ROW_RIGHT='┤';

	const BOTTOM_LEFT='└',BOTTOM_RIGHT='┘';

	const BINARY_VALUES=[
			'0011'=>self::TOP_LEFT,
			'1011'=>self::M_COL_TOP,
			'1001'=>self::TOP_RIGHT,
			'0111'=>self::M_ROW_LEFT,
			'1111'=>self::M_COL_ROW,
			'1101'=>self::M_ROW_RIGHT,
			'0110'=>self::BOTTOM_LEFT,
			'1110'=>self::M_COL_BOTTOM,
			'1100'=>self::BOTTOM_RIGHT,
			'1010'=>self::H_LINE,
			'0101'=>self::V_LINE,
			'0000'=>' '
	];


	private $h_lines=[];
	private $v_lines=[];

	/**
	 * @var array
	 */
	private $datas;

	/**
	 * @var array
	 */
	private $colWidths;

	private $colCount;

	private $rowCount;

	private $padding=1;

	private $indent=0;

	/**
	 * Get the printable cell content
	 * @param integer $index The column index
	 * @param array   $row   The table row
	 * @return string
	 */
	private function getCellOutput($index, $row = null){
		$cell       = $row ? $row[$index] : '-';
		$width      = $this->colWidths[$index];
		$padding    = str_repeat($row ? ' ' : '-', $this->padding);
		$output = '';
		if ($index === 0) {
			$output .= str_repeat(' ', $this->indent);
		}
		$output .=($this->v_lines[$index]==1)? self::V_LINE:' ';

		$output .= $padding; # left padding
		$cell    = trim(preg_replace('/\s+/', ' ', $cell)); # remove line breaks
		$content = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $cell);
		$delta   = strlen($cell) - strlen($content)+$this->padding;
		$output .= str_pad($cell, $width-$delta , $row ? ' ' : '-'); # cell content
		//$output .= $padding; # right padding
		if ($row && $index == count($row)-1) {
			$output .= ($this->v_lines[count($row)]==1)?self::V_LINE:' ';
		}
		return $output;
	}

	private function initializeBorders(){
		$this->h_lines=array_fill(0,sizeof($this->datas)+1 , 1);
		$this->v_lines=array_fill(0, $this->colCount+1, 1);
	}

	public function setDatas($datas){
		$this->datas=$datas;
		$this->calculateColWidths();
		$this->initializeBorders();
		//$this->removeGrid();
		$this->factorize();
	}

	public function factorize(){
		$flag=[];
		foreach ($this->datas as $y=>&$row) {
			if (is_array($row)) {
				$index=0;
				foreach ($row as &$col) {
					if((isset($flag[$index]) && $col!==$flag[$index]) || !isset($flag[$index])){
						$flag[$index]=$col;
					}else{
						$col=' ';
						$this->h_lines[$y]=0;
					}
					$index++;
				}
			}
		}
	}

	public function getTable(){
		$res='';
		$y=0;
		foreach ($this->datas as $row) {
			$res.=$this->border($y).PHP_EOL;
			$index=0;
			foreach ($row as $col) {
				$res.=$this->getCellOutput($index,$row);
				$index++;
			}
			$res.=PHP_EOL;
			$y++;
		}
		$res.=$this->border(sizeof($this->datas)).PHP_EOL;
		return $res;
	}

	public function removeVGrid($start=2){
		for($i=$start;$i<$this->colCount;$i++){
			$this->v_lines[$i]=0;
		}
	}

	public function removeHGrid($start=2){
		$size=sizeof($this->datas);
		for($i=$start;$i<$size;$i++){
			$this->h_lines[$i]=0;
		}
	}

	public function removeGrid($hStart=2,$vStart=2){
		$this->removeHGrid($hStart);
		$this->removeVGrid($vStart);
	}

	private function border($row){
		$line=($this->h_lines[$row])?self::H_LINE:' ';
		$res='';
		for ($i=0;$i<$this->colCount;$i++){
			$res.=$this->getBorderValue($row, $i);
			$res.=str_repeat($line, $this->colWidths[$i]);
		}
		$res.=$this->getBorderValue($row, $this->colCount);
		return $res;
	}

	private function getBorderValue($row,$col){
		$res=[null,null,null,null];
		if($row==0){
			$res[1]='0';
		}
		if($col==0){
			$res[0]='0';
		}
		if($row==$this->rowCount){
			$res[3]='0';
		}
		if($col==$this->colCount){
			$res[2]='0';
		}
		if($this->h_lines[$row]==1){
			if(!isset($res[0])){
				$res[0]='1';
			}
			if(!isset($res[2])){
				$res[2]='1';
			}
		}
		if($this->v_lines[$col]==1){
			if(!isset($res[1])){
				$res[1]='1';
			}
			if(!isset($res[3])){
				$res[3]='1';
			}
		}
		foreach ($res as &$r){
			if($r==null){
				$r='0';
			}
		}
		$res=implode('', $res);
		return self::BINARY_VALUES[$res];
	}

	/**
	 * Calculate the maximum width of each column
	 * @return array
	 */
	private function calculateColWidths(){
		$colCount=0;
		foreach ($this->datas as $row) {
			if (is_array($row)) {
				if(sizeof($row)>$colCount){
					$colCount=sizeof($row);
				}
				$index=0;
				foreach ($row as $col) {
					$content = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $col);
					if (!isset($this->colWidths[$index])) {
						$this->colWidths[$index] = strlen($content)+2*$this->padding;
					} else {
						if (strlen($content)+2*$this->padding > $this->colWidths[$index]) {
							$this->colWidths[$index] = strlen($content)+2*$this->padding;
						}
					}
					$index++;
				}
			}
		}
		$this->colCount=$colCount;
		$this->rowCount=sizeof($this->datas);
		return $this->colWidths;
	}
}

