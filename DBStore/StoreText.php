<?php 
/**
 * Contains class StoreText
 * @package DBStore
 */

/**
 * Class for extracting data-tables from text files.
 * 
 * @package DBStore
 * @author Ivan Ponomarev <ivan@newnauka.org>
 * @version 0.0.2
 */
class StoreText extends AbstractDB implements DataBase {
	
	/**
	 * @var string Path to the file
	 */
	private $fileName = null;
	/**
	 * @var string The File's content 
	 */
	private $fileContent = null;
	/**
	 * @var string Classname of the parser
	 */
	private $template = null;
	
	/**
	 * The constructor of StoreText
	 * @param StoreTextTemplate $template The name of the template
	 */
	public function __construct($template) {
		$this->template = $template;
	}
	
	/**
	 * Obtain data-table from the store
	 * @param mixed $store name of the table in the exact store
	 * @param boolean $force reobtain data from store
	 */
	public function retrieveData($store, $force = false) {
		
		if (!file_exists($store))
			throw new Exception("No file \"{$store}\"");
		
		if (!isset($this->_DataTable) || $force) {
			$this->fileName = $store;
			
			$file = fopen($this->fileName, "r");
			if (isset($file))
				$this->fileContent = "";
			
			while (!feof($file)) {
				$this->fileContent .= fgets($file);
			}
			fclose($file);
			
			$temp = $this->template;
			
			$this->_DataTable = $temp::parseText($this->fileContent);
		}
	}
	
}
