<?php
namespace Ubiquity\devtools\cmd;

use Ubiquity\utils\base\UDateTime;

class ConsoleTable {

	const TOP_LEFT = '╭', TOP_RIGHT = '╮';

	const H_LINE = '─', V_LINE = '│';

	const M_COL_TOP = '┬', M_COL_ROW = '┼', M_COL_BOTTOM = '┴';

	const M_ROW_LEFT = '├', M_ROW_RIGHT = '┤';

	const BOTTOM_LEFT = '╰', BOTTOM_RIGHT = '╯';

	const BINARY_VALUES = [
		'0011' => self::TOP_LEFT,
		'1011' => self::M_COL_TOP,
		'1001' => self::TOP_RIGHT,
		'0111' => self::M_ROW_LEFT,
		'1111' => self::M_COL_ROW,
		'1101' => self::M_ROW_RIGHT,
		'0110' => self::BOTTOM_LEFT,
		'1110' => self::M_COL_BOTTOM,
		'1100' => self::BOTTOM_RIGHT,
		'1010' => self::H_LINE,
		'0101' => self::V_LINE
	];

	private $h_lines = [];

	private $v_lines = [];

	/**
	 *
	 * @var array
	 */
	private $datas;

	/**
	 *
	 * @var array
	 */
	private $colWidths;

	/**
	 *
	 * @var array
	 */
	private $rowHeight;

	private $colCount;

	private $rowCount;

	private $padding = 5;

	private $indent = 0;

	private $borderColor = ConsoleFormatter::LIGHT_GRAY;

	private $preserveSpaceBefore = false;

	/**
	 * Get the printable cell content
	 *
	 * @param integer $index
	 *        	The column index
	 * @param array $row
	 *        	The table row
	 * @return string
	 */
	private function getCellOutput($index, $row = null) {
		$cell = $row ? $row[$index] : '-';
		$width = $this->colWidths[$index];
		$padding = str_repeat($row ? ' ' : '-', $this->padding);
		$output = '';
		if ($index === 0) {
			$output .= str_repeat(' ', $this->indent);
		}
		$output .= ($this->v_lines[$index] == 1) ? $this->getVLine() : ' ';

		$output .= $padding; // left padding
		if (is_string($cell)) {
			$cell = rtrim(preg_replace('/\s+/', ' ', $cell)); // remove line breaks
			if (! $this->preserveSpaceBefore) {
				$cell = ltrim($cell);
			}
		} else {
			$cell = '{}';
		}
		$content = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $cell);

		$delta = - mb_strlen($cell, 'UTF-8') + mb_strlen($content, 'UTF-8') + $this->padding;
		$output .= $this->mb_str_pad($cell, $width - $delta, $row ? ' ' : '-'); // cell content

		if ($row && $index == count($row) - 1) {
			$output .= ($this->v_lines[count($row)] == 1) ? $this->getVLine() : ' ';
		}
		return $output;
	}

	private function getVLine() {
		return ConsoleFormatter::colorize(self::V_LINE, $this->borderColor);
	}

	private function initializeBorders() {
		$this->h_lines = array_fill(0, sizeof($this->datas) + 1, 1);
		$this->v_lines = array_fill(0, $this->colCount + 1, 1);
	}

	/**
	 * mb_str_pad version for multibyte encoding
	 *
	 * @see http://php.net/manual/fr/function.str-pad.php#116244
	 * @param string $str
	 * @param int $pad_len
	 * @param string $pad_str
	 * @param string $dir
	 * @param string $encoding
	 * @return string
	 */
	private function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL) {
		$content = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $str);
		$str_len = mb_strlen($content);
		$pad_str_len = mb_strlen($pad_str);
		if (! $str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
			$str_len = 1; // @debug
		}
		if (! $pad_len || ! $pad_str_len || $pad_len <= $str_len) {
			return $str;
		}

		$result = null;
		$repeat = ceil($str_len - $pad_str_len + $pad_len);
		if ($dir == STR_PAD_RIGHT) {
			$result = $str . str_repeat($pad_str, $repeat);
			$result = mb_substr($result, 0, $pad_len);
		} else if ($dir == STR_PAD_LEFT) {
			$result = str_repeat($pad_str, $repeat) . $str;
			$result = mb_substr($result, - $pad_len);
		} else if ($dir == STR_PAD_BOTH) {
			$length = ($pad_len - $str_len) / 2;
			$repeat = ceil($length / $pad_str_len);
			$result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length)) . $str . mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
		}

		return $result;
	}

	public function setDatas($datas) {
		$this->datas = $datas;
		$this->calculateColWidths();
		$this->initializeBorders();
		// $this->removeGrid();
		$this->factorize();
	}

	public function factorize() {
		$flag = [];
		foreach ($this->datas as $y => &$row) {
			if (is_array($row)) {
				$index = 0;
				foreach ($row as &$col) {
					if ((isset($flag[$index]) && $col !== $flag[$index]) || ! isset($flag[$index])) {
						$flag[$index] = $col;
					} else {
						$col = '≡';
						$this->h_lines[$y] = 0;
					}
					$index ++;
				}
			}
		}
	}

	private function getLineRow($row, $yl) {
		$result = [];
		foreach ($row as $col) {
			if ($col instanceof \DateTime) {
				$lines = [
					UDateTime::elapsed($col)
				];
			} else {
				$lines = \explode("\n", $col);
			}
			$result[] = (isset($lines[$yl])) ? $lines[$yl] : ' ';
		}
		return $result;
	}

	public function getTable() {
		$res = '';
		$y = 0;
		foreach ($this->datas as $row) {
			$res .= $this->border($y) . PHP_EOL;
			$rowHeight = $this->rowHeight[$y];
			for ($yl = 0; $yl < $rowHeight; $yl ++) {
				$lineRow = $this->getLineRow($row, $yl);
				$index = 0;
				foreach ($lineRow as $col) {
					$res .= $this->getCellOutput($index, $lineRow);
					$index ++;
				}
				$res .= PHP_EOL;
			}
			$y ++;
		}
		$res .= $this->border(sizeof($this->datas)) . PHP_EOL;
		return $res;
	}

	public function removeVGrid($start = 2) {
		for ($i = $start; $i < $this->colCount; $i ++) {
			$this->v_lines[$i] = 0;
		}
	}

	public function removeHGrid($start = 2) {
		$size = sizeof($this->datas);
		for ($i = $start; $i < $size; $i ++) {
			$this->h_lines[$i] = 0;
		}
	}

	public function removeGrid($hStart = 2, $vStart = 2) {
		$this->removeHGrid($hStart);
		$this->removeVGrid($vStart);
	}

	private function border($row) {
		$line = ($this->h_lines[$row]) ? self::H_LINE : ' ';
		$res = str_repeat(' ', $this->indent);
		for ($i = 0; $i < $this->colCount; $i ++) {
			$res .= $this->getBorderValue($row, $i);
			$res .= str_repeat($line, $this->colWidths[$i]);
		}
		$res .= $this->getBorderValue($row, $this->colCount);
		return ConsoleFormatter::colorize($res, $this->borderColor);
	}

	private function getBorderValue($row, $col) {
		$res = [
			null,
			null,
			null,
			null
		];
		if ($row == 0) {
			$res[1] = '0';
		}
		if ($col == 0) {
			$res[0] = '0';
		}
		if ($row == $this->rowCount) {
			$res[3] = '0';
		}
		if ($col == $this->colCount) {
			$res[2] = '0';
		}
		if ($this->h_lines[$row] == 1) {
			if (! isset($res[0])) {
				$res[0] = '1';
			}
			if (! isset($res[2])) {
				$res[2] = '1';
			}
		}
		if ($this->v_lines[$col] == 1) {
			if (! isset($res[1])) {
				$res[1] = '1';
			}
			if (! isset($res[3])) {
				$res[3] = '1';
			}
		}
		foreach ($res as &$r) {
			if ($r == null) {
				$r = '0';
			}
		}
		$res = implode('', $res);
		return self::BINARY_VALUES[$res];
	}

	/**
	 * Calculate the maximum width of each column
	 *
	 * @return array
	 */
	private function calculateColWidths() {
		$colCount = 0;
		$y = 0;
		foreach ($this->datas as $row) {
			if (is_array($row)) {
				if (sizeof($row) > $colCount) {
					$colCount = sizeof($row);
				}
				$index = 0;
				$this->rowHeight[$y] = 1;
				foreach ($row as $col) {
					if ($col instanceof \DateTime) {
						$col = UDateTime::elapsed($col);
					}
					if (is_string($col)) {
						$lines = explode("\n", $col);
						$size = sizeof($lines);
						if ($size > $this->rowHeight[$y]) {
							$this->rowHeight[$y] = $size;
						}
						foreach ($lines as $line) {
							$content = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $line);
							$len = mb_strlen($content, 'UTF-8');
							if (! isset($this->colWidths[$index])) {
								$this->colWidths[$index] = $len + 2 * $this->padding;
							} else {
								if ($len + 2 * $this->padding > $this->colWidths[$index]) {
									$this->colWidths[$index] = $len + 2 * $this->padding;
								}
							}
						}
					}
					$index ++;
				}
			}
			$y ++;
		}
		$this->colCount = $colCount;
		$this->rowCount = sizeof($this->datas);
		return $this->colWidths;
	}

	/**
	 *
	 * @param number $padding
	 */
	public function setPadding($padding) {
		$this->padding = $padding;
	}

	/**
	 *
	 * @param number $indent
	 */
	public function setIndent($indent) {
		$this->indent = $indent;
	}

	/**
	 *
	 * @param string $borderColor
	 */
	public function setBorderColor($borderColor) {
		$this->borderColor = $borderColor;
	}

	public static function borderColor($text, $color = ConsoleFormatter::LIGHT_GRAY) {
		$border = new ConsoleTable();
		$border->setIndent(5);
		$border->setBorderColor($color);
		$border->setDatas($text);
		$border->setPreserveSpaceBefore(true);
		return $border->getTable();
	}

	public static function borderType($text, $type) {
		switch ($type) {
			case 'error':
				$color = ConsoleFormatter::LIGHT_RED;
				break;
			case 'success':
				$color = ConsoleFormatter::LIGHT_GREEN;
				break;
			case 'info':
				$color = ConsoleFormatter::LIGHT_CYAN;
				break;
			case 'warning':
				$color = ConsoleFormatter::LIGHT_GRAY;
				break;
			default:
				$color = ConsoleFormatter::WHITE;
				break;
		}
		return self::borderColor($text, $color);
	}

	/**
	 *
	 * @param boolean $preserveSpaceBefore
	 */
	public function setPreserveSpaceBefore($preserveSpaceBefore) {
		$this->preserveSpaceBefore = $preserveSpaceBefore;
	}
}

