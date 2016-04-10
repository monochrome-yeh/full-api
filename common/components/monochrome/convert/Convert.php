<?php

namespace common\components\monochrome\convert;

class Convert extends \yii\base\Component {
	
	public function init(){
		parent::init();
	}

	public function __fgetcsv(&$handle, $length = null, $d = ",", $e = '"') {
		ini_set('auto_detect_line_endings', '1');
		$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = "";
		$eof=false;
		while ($eof != true) {
			$_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
			$itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
			if ($itemcnt % 2 == 0){
				$eof = true;
			}
		}
			$_line = iconv("big5","UTF-8//IGNORE",addslashes($_line));
			
			$_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));

			$_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
			preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
			$_csv_data = $_csv_matches[1];
		for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
			$_csv_data[$_csv_i] = preg_replace("/^" . $e . "(.*)" . $e . "$/s", "$1", $_csv_data[$_csv_i]);
			$_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);

		}
			return empty ($_line) ? false : $_csv_data;
	}

}