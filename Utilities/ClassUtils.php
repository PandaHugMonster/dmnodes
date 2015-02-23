<?php
/**
 * Contains class ClassUtils
 * @package Utilities
 */

/**
 * Class utils
 * @package Utilities
 * @author Ivan Ponomarev <ivan@newnauka.org>
 * @version 0.0.2
 */
class ClassUtils {

	/**
	 * Create new instance of classname-string
	 * @param string $class Classname for the new object
	 * @return object
	 */
	public static function ClassInstance($class) {
		$reflection = new ReflectionClass($class);
		$object = $reflection->newInstanceArgs();
		return $object;
	}
}
