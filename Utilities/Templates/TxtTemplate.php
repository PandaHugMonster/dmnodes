<?php 
/**
 * Contains class TxtTemplate
 * @package Utilities.Templates
 */

/**
 * Template for parsing text.
 * @package Utilities
 * @author Ivan Ponomarev <ivan@newnauka.org>
 * @version 0.0.3
 */
class TxtTemplate implements StoreTextTemplate {
	
	/**
	 * Parse the text to the special array
	 * @param string $str
	 * @param string $headerName
	 * @param string $rowsName
	 * @return array
	 */
	public static function parseText($str, $headerName = AbstractDB::HEADER, $rowsName = AbstractDB::ROWS) {
		$tab = array($headerName => array(), $rowsName => array());
		
		$i = 1; $s = 0;
		foreach (preg_split('/\n+/', $str) as $line) {
			$line = preg_replace('/\n+/', "", $line);
			$_tline = preg_replace('/\s*/', "", $line);
			if ($_tline[0] != "#" && count($_tline) > 0) {
			
			
				if ($i++ == 1) {
					$type = $headerName;
					foreach (preg_split('/\s+/', $line) as $item)
						$tab[$type][] = $item;
				} else {
					$type = $rowsName;
					foreach (preg_split('/\s+/', $line) as $item)
						$tab[$type][$s][] = $item;
					$s++;
				}
			}
		}
		
		return $tab;
	}
	
}
