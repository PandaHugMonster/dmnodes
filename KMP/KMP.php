<?php 
/**
 * Contains class KMP, K-means version of Ponomarev Ivan
 * 
 * @package KMP
 */
/**
 * Class containing TreeNodes and Basic information about data-table.
 * 
 * <code>
 * 
 * $store = new StoreText("TxtTemplate");
 * $store->retrieveData("samples/weightheight.txt");
 * $tree = new KMP($store->toArray());
 * $tree->buildMap();
 * 
 * </code>
 * 
 * @package KMP
 * @author Ivan Ponomarev <ivan@newnauka.org>
 * @version 0.0.2
 */
class KMP {
	
	const IGNORE_INDEXES = 0;
	const IGNORE_KEYS = 1;
	
	/**
	 * @var array Contains table splitted to rows and headerы
	 */
	protected $DBData = null;
	
	protected $clusters = 3;
	protected $limit = 50;
	
	protected $_centroids;
	protected $_orderedData;
	
	/**
	 * @var array Contains indexes of ignoring columns key => val both have the same value
	 */
	protected $_ignoreindxs = array();
	
	/**
	 * Set columns that will be ignored in analysis
	 * 
	 * @param array $params Two kind of arrays inside: self::IGNORE_KEYS => array(), self::IGNORE_INDEXES => array() 
	 */
	public function ignoreColumns($params) {
		foreach ($this->DBData[AbstractDB::HEADER] as $i => $item) {
			if ((isset($params[self::IGNORE_KEYS]) && in_array($item, $params[self::IGNORE_KEYS])) || (isset($params[self::IGNORE_INDEXES]) && in_array($i, $params[self::IGNORE_INDEXES])))
				$this->_ignoreindxs[$i] = $i;
		}
	}
	
	/**
	 * The constructor of the class
	 * 
	 * @param array $DBData Simple array with header and rows
	 */
	public function __construct($DBData = null, $c = 3) {
		$this->clusters = $c;
		if (isset($DBData))
			$this->fillUpWithData($DBData);
	}
	
	/**
	 * Method returns set of rows for current node
	 * Ignoring columns are excluded
	 * @return array
	 */
	public function getRows() {
		$result = array();
		
		foreach ($this->DBData[AbstractDB::ROWS] as $r => $row)
			foreach ($row as $i => $item)
				if (!in_array($i, $this->_ignoreindxs))
					$result[$r][$i] = $item;
		return $result;
	}
	
	/**
	 * Method returns header of the current node
	 * Ignoring columns are excluded
	 * @param int $index Index of the column
	 * @return array
	 */
	public function getHeader($index = null) {
		$result = array();
		
		foreach ($this->DBData[AbstractDB::HEADER] as $i => $item) {
			if (!in_array($i, $this->_ignoreindxs)) {
				if (isset($index)) {
					if ($index == $i)
						return $item;
				} else {
					$result[$i] = $item;
				}
			}
		}
		
		return $result;
	}
	/**
	 * Check that rows empty or not
	 * @return boolean
	 */
	public function isEmpty() {
		return $this->getRowsQuantity() == 0; 
	}
	/**
	 * Set a table with data
	 * @param array $DBData Table-data array
	 */
	public function fillUpWithData($DBData) {
		$this->DBData = $DBData;
		$this->initCluster();
	}
	/**
	 * Get the rows' quantity
	 * @return int
	 */
	public function getRowsQuantity() {
		return count($this->getRows());
	}
	/**
	 * Get the quantity of header items
	 * @return int
	 */
	public function getColumnsQuantity() {
		return count($this->getHeader());
	}
	/**
	 * Get name of the column by index
	 * @param int $index Index of a column
	 * @return string
	 */
	public function getColumnName($index) {
		return $this->getHeader($index); 
	}
	/**
	 * Get index of the column by name
	 * @param string $name The name of column
	 * @return int
	 */
	public function getColumnIndex($name) {
		foreach ($this->getHeader() as $index => $keyname)
			if ($name == $keyname)
				return $index;
		
		return null;
	}
	
	public function randDia() {
		return rand(0, $this->clusters - 1);
	}
	
	public function initCluster() {
		for ($i = 0; $i < $this->clusters; $i++)
			$this->_orderedData[$i] = array();
		
		// Упорядочивание данных в промежуточный результирующий массив
		$i = 0;
		foreach ($this->getRows() as $key => $val) {
			if ($i > ($this->clusters - 1)) {
				$r = $this->randDia();
				$this->_orderedData[$r][$key] = $val;
			} else 
				$this->_orderedData[$i][$key] = $val;
			// XXX Пустое увеличение I, включить в тело else
			$i++;
		}
	}
	
	public function updateCentroids() {
		foreach ($this->_orderedData as $cluster => $rows) {
			$s = array();
			
			foreach ($rows as $key => $val) {
				foreach ($val as $i => $g) {
					if (!isset($s[$i]))
						$s[$i] = 0;
					$s[$i] += $g;
				}
			}
			
			foreach ($s as $i => $y) {
				$this->_centroids[$cluster][$i] = $y / (count($rows)?count($rows):1);
			}
		}
	}
	
	public function computeDistance($d1, $d2) {
		$h = 0;
		for ($t = 0; $t < count($d1); $t++) {
			$h += pow($d1[$t] - $d2[$t], 2);
		}
		
		return sqrt($h);
	}
	
	public function buildMap() {
		$l = $this->limit;
		$change = true;
		$this->updateCentroids();
		$i = 0;
		
		while ($change || ($i++ <= $l)) {
			$change = false;
			
			
			
			foreach ($this->getRows() as $key => $d1) {
				$dists = array();
				foreach ($this->_centroids as $cluster => $d2)
					$dists[$cluster] = $this->computeDistance($d1, $d2);
				
				$min = PHP_INT_MAX;
				$lr = 0;
				foreach ($dists as $cluster => $dist) {
					if ($dist < $min) {
						$min = $dist;
						$lr = $cluster;
					}
				}
				
				if (!isset($this->_orderedData[$lr][$key])) {
					$change = true;
					$i = 0;

					foreach ($this->_orderedData as $cluster => $rows)
						if ($cluster != $lr && isset($rows[$key])) {
							unset($this->_orderedData[$cluster][$key]);
						}
						
					$this->_orderedData[$lr][$key] = $d1;
				}
				
			}
			
			$this->updateCentroids();
			
		}
		
		return $this->_orderedData;
	}

}
