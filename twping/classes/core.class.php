<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

class Twping_Core {
	/**
	 * Auto load classes
	 */
	public static function auto_load($class)
	{
		$pieces = explode("_", $class);
		array_shift($pieces);

		$path = TWPINGPATH . "classes/" . strtolower(implode("/", $pieces)) . ".class.php";
		if (file_exists($path))
		{
			require_once $path;
		}
	}
}