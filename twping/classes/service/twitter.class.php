<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

class Twping_Service_Twitter extends Twping_Service {
	/**
	 * Send data to twiiter
	 * @param string $account ключ аккаунта в массиве
	 * @param array $target инфа о материале
	 * @return boolean
	 */
	public static function send($account,array $target)
	{
		// валидируем цель
		self::validate($target);
		$config = Twping_Twping::instance()->config();

		if (!isset($config["accounts"]["twitter"][$account]))
		{
			return false;
		}

		if (!class_exists("tmhOAuth"))
		{
			require TWPINGPATH . "vendor/tmhOAuth/tmhOAuth.php";
		}

		$keys = $config["accounts"]["twitter"][$account];

		$tmhOAuth = new tmhOAuth(array(
			'consumer_key' => $keys['consumer_key'],
			'consumer_secret' => $keys['consumer_secret'],
			'token' => $keys['token'],
			'secret' => $keys['token_secret'],
		));

		$tmhOAuth->user_request(array(
			'method' => 'POST',
			'url' => $tmhOAuth->url('1.1/statuses/update'),
			'params' => array(
				'status' => $target["text"]
			)
		));

		Twping_Twping::instance()->insert("twitter",$account,$target["text"],$target["target_type"],$target["target_id"]);
		return (bool)$tmhOAuth;
	}
}