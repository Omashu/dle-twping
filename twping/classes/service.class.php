<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

abstract class Twping_Service {
	public static function send($account,array $target) {}

	/**
	 * Validate target data
	 * @return void || Exception
	 */
	public static function validate(array &$target = array())
	{
		// валидируем $target
		$target["target_id"] = isset($target["target_id"])
			? (int) $target["target_id"]
			: 0;

		$config = Twping_Twping::instance()->config();
		if (!isset($target["target_type"]) or !in_array($target["target_type"], $config["allow_targets"]))
		{
			throw new Twping_Exception("Invalid target type");
		}

		if (!$target["target_id"])
		{
			throw new Twping_Exception("Invalid target id");
		}

		if (!isset($target["text"]) or !is_string($target["text"]))
		{
			throw new Twping_Exception("Invalid text");
		}

		if ($config["charset"] === "CP1251")
		{
			$target["text"] = iconv($config["charset"], "UTF-8", $target["text"]);
		}
	}
}