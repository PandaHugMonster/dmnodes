<?php 
/**
 * Contains class AbstractDB
 * @package DBStore
 */

/**
 * This class needed to be extended by Files and DB 
 * classes for extracting data from any kind of stores.
 * 
 * @package DBStore
 * @author Ivan Ponomarev  <ivan@newnauka.org>
 * @version 0.0.2
 * @abstract
 */
abstract class AbstractDB {
	
	const HEADER = "Header";
	const ROWS = "Rows";

	/**
	 * @var array Data-table variable contains array with 2 subarrays [AbstractDB::HEADER, AbstractDB::ROWS]
	 */
	protected $_DataTable = null;
	
	/**
	 * Returns set of rows
	 * @return array
	 */
	public function getRows() {
		return $this->_DataTable[AbstractDB::ROWS];
	}
	
	/**
	 * Returns set of columns of the header
	 * @return array
	 */
	public function getHeader() {
		return $this->_DataTable[AbstractDB::HEADER];
	}
	
	/**
	 * Obtain data-table from the store
	 * @param mixed $store name of the table in the exact store
	 * @param boolean $force reobtain data from store
	 */
	abstract public function retrieveData($store, $force = false);
	
	/**
	 * Return the array view of the object
	 * @return array
	 */
	public function toArray() {
		return $this->_DataTable;
	}
	
}
