<?php
/**
 * Contains class CLIParams
 * @package Utilities
 */

/**
 * Class for getting and process the arguments from command line.
 * @package Utilities
 * @author Ivan Ponomarev <ivan@newnauka.org>
 * @version 0.0.1
 */
class CLIParams {
	
	const NO_VALUE = 0;
	const HAS_VALUE = 1;
	
	private $_line = null;
	private $_processed = array();
	
	private $_keys = array();
	
	/**
	 * Create an object with processed Arguments.
	 * If arguments have been passed, execute method: executeProcess.
	 * 
	 * @see executeProcess()
	 * @param array $keys Array of applying keys in form ("-k" => CLIParams::NO_VALUE) or ("--with-value" => CLIParams::HAS_VALUE) 
	 * @param string $args String of command line arguments
	 */
	public function __construct($keys, $args = null) {
		$this->_keys = $keys;
		
		$this->executeProcess($args);
	}
	
	/**
	 * Method update data about command line arguments in the object.
	 * @param string $args String of command line arguments
	 */
	public function executeProcess($args = null) {
		if (isset($args) || isset($argv)) {
			$this->_line = isset($args)?$args:$argv;
			$flag = false;
			//
			foreach (explode(" ", $this->_line) as $item) {
				foreach ($this->_keys as $key => $type) {
					if ($flag) {
						$prev = $item;
						
						$flag = false;
					}

					$flag = $type == self::HAS_VALUE;
					$prev = &$this->_processed[$key]; 
				}
				
			}
		}
	}
	
	public function show() {
		print_r($this->_processed);
	}
	
}