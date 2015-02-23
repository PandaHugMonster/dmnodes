<?php 
/**
 * Contains class AbstractDB
 * @package Utilities
 */

/**
 * Interface for template that parse text.
 * @package Utilities
 * @author Ivan Ponomarev <ivan@newnauka.org>
 * @version 0.0.2
 */
interface StoreTextTemplate {
	
	/**
	 * Function that parse the string
	 * @param string $str Content to parse
	 * @param string $headerName Array Key of header row
	 * @param string $rowsName Array Key of all rows except Header
	 */
	public static function parseText($str, $headerName = "header", $rowsName = "rows");
	
}
