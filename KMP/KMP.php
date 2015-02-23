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
	 * К сожалению этот массив хранится в памяти
	 * В следующих версиях будет исправлено 
	 * загрузка всех данных в память
	 * 
	 * @var array Массив содержащий данные
	 */
	protected $DBData = null;
	
	/**
	 * Количество кластеров
	 * @var integer
	 */
	public $clusters = 3;
	/**
	 * Верхняя граница повторов
	 * @var integer
	 */
	public $limit = 50;
	
	/**
	 * @var array Центройды
	 */
	protected $_centroids;
	/**
	 * @var array Упорядоченые данные
	 */
	protected $_orderedData;
	
	/**
	 * @var array Содержит индексы key => val и ключ и значение - одно и то же значение
	 */
	protected $_ignoreindxs = array();
	
	/**
	 * Колонки которые нужно игнорировать
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
	 * Конструктор. Базовое заполнение
	 * 
	 * @param array $DBData Simple array with header and rows
	 */
	public function __construct($DBData = null, $c = 3) {
		$this->clusters = $c;
		if (isset($DBData))
			$this->fillUpWithData($DBData);
	}
	
	/**
	 * Возвращает строки
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
	 * Заголовок по определённому индексу
	 * или если не задан параметр - возврат всего 
	 * заголовка исключая исключённые элементы
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
	 * Проверка - есть ли строки
	 * @return boolean
	 */
	public function isEmpty() {
		return $this->getRowsQuantity() == 0; 
	}
	/**
	 * Заполнить объект данными
	 * @param array $DBData Table-data array
	 */
	public function fillUpWithData($DBData) {
		$this->DBData = $DBData;
		$this->initCluster();
	}
	/**
	 * Количество строк
	 * @return int
	 */
	public function getRowsQuantity() {
		return count($this->getRows());
	}
	/**
	 * Количество элементов в заголовке
	 * @return int
	 */
	public function getColumnsQuantity() {
		return count($this->getHeader());
	}
	/**
	 * Получить имя колонки по индексу
	 * @param int $index Index of a column
	 * @return string
	 */
	public function getColumnName($index) {
		return $this->getHeader($index); 
	}
	/**
	 * Получить индекс колонки
	 * 
	 * @param string $name название колонки
	 * @return int
	 */
	public function getColumnIndex($name) {
		foreach ($this->getHeader() as $index => $keyname)
			if ($name == $keyname)
				return $index;
		
		return null;
	}
	
	/**
	 * Случайный Кластер
	 * @return number
	 */
	public function randDia() {
		return rand(0, $this->clusters - 1);
	}
	
	/**
	 * Инициализация кластеров
	 * Распределение данных по кластерам в случайном порядке,
	 * но все заданные класстеры должны иметь не менее 1 элемента
	 */
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
	
	/**
	 * Обновить главные точки (Обновление центройдов)
	 */
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
	
	/**
	 * Расчёт дистанции
	 * 
	 * Оба аргумента массивы из одинакового числа эллементов
	 * по сути это массивы которые подразумевают строки в таблице
	 * Расчёт по формуле: res = res + (d1[t] - d2[t])^2, 
	 * после чего взять квадратный корень из res
	 * 
	 * @param array $d1
	 * @param array $d2
	 * @return number
	 */
	public function computeDistance($d1, $d2) {
		$h = 0;
		for ($t = 0; $t < count($d1); $t++) {
			$h += pow($d1[$t] - $d2[$t], 2);
		}
		
		return sqrt($h);
	}
	
	/**
	 * Алгоритм начинается отсюда.
	 */
	public function buildMap() {
		/* 
		 * Задаётся лимит на повторы, чтобы не	
		 * было бесконечного зацикливания 
		 */
		$l = $this->limit;
		/*
		 * Переменная-флаг об изменение состояния (были ли изменения в результирующих данных)
		 */
		$change = true;
		/*
		 * Обновить (задать центройды)
		 * В данной версии берутся случайные центроиды
		 * Центройд = Кластер
		 */
		$this->updateCentroids();
		/*
		 * Обычный счётчик
		 */
		$i = 0;
		
		while ($change || ($i++ <= $l)) {
			$change = false;

			/*
			 * Обработка строк
			 */
			foreach ($this->getRows() as $key => $d1) {
				$dists = array();
				
				/*
				 * Пересчёт дистанции к главным точкам
				 * d1 - это данные строки, d2 - это главные точки
				 */
				foreach ($this->_centroids as $cluster => $d2)
					$dists[$cluster] = $this->computeDistance($d1, $d2);
				
				/*
				 * Берём максимальное возможное INT число
				 * и задаём его как минимальная (дистанция)
				 */
				$min = PHP_INT_MAX;
				/*
				 * Кластер к которому ближе всего точка
				 */
				$lr = 0;
				/*
				 * Поиск минимального растояния до ближайшего 
				 * кластера (+ получение ближайшего кластера в $lr)
				 */
				foreach ($dists as $cluster => $dist) {
					if ($dist < $min) {
						$min = $dist;
						$lr = $cluster;
					}
				}
				
				/*
				 * Если переменная ещё не находится в новом
				 * более близком к ней кластере - изменить 
				 * расположение точки на новый кластер.
				 * То есть - переместить точку в новый более 
				 * близкий кластер если она ещё не там.
				 */
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
			/*
			 * Дополнительный пересчёт центройд
			 * так как могли быть изменена структура
			 * результируещего массива
			 */
			$this->updateCentroids();
			
		}
		/*
		 * Возвращает массив результирующих данных
		 * В случае когда не было больше изменений
		 * или был превышен лимит на повторы $l
		 */
		return $this->_orderedData;
	}

}
