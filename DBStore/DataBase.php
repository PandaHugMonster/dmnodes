<?php
/**
 * Contains class DataBase
 * @package DBStore
 */

/**
 * Interface for implementing by DBStores classes.
 * 
 * @package DBStore
 * @author Ivan Ponomarev <ivan@newnauka.org>
 * @version 0.0.2
 */
interface DataBase {
	
	/**
	 * Returns set of rows
	 * @return array
	 */
	public function getRows();
	
	/**
	 * Returns set of columns of the header
	 * @return array
	 */
	public function getHeader();
	
	/**
	 * Obtain data-table from the store
	 * @param mixed $store name of the table in the exact store
	 * @param boolean $force reobtain data from store
	 */
	public function retrieveData($store, $force = false);
	
	/**
	 * Return the array view of the object
	 * @return array
	 */
	public function toArray();
}
