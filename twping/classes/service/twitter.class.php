<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

class Twping_Service_Twitter extends Twping_Service {

	public function __construct($service,$account,$target)
	{
		parent::__construct($service,$account,$target);

		if (!class_exists("tmhOAuth"))
		{
			require TWPINGPATH . "vendor/tmhOAuth/tmhOAuth.php";
		}
	}

	/**
	 * Send data to twiiter
	 * @return boolean
	 */
	public function send()
	{
		$keys = $this->account_settings;
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
				'status' => $this->message
			)
		));
		
		return (bool)$tmhOAuth;
	}
}