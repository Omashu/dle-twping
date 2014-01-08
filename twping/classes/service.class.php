<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

abstract class Twping_Service {

	protected $target;
	protected $service;
	protected $account;
	protected $account_settings;
	protected $message;
	protected $mask;

	public function __construct($service,$account,$target)
	{
		$this->service = $service;
		$this->account = $account;
		$this->target = $target;

		$config = Twping_Twping::instance()->config();
		if (!isset($config["accounts"][$this->service][$this->account]))
		{
			throw new Twping_Exception("Invalid service or account");
		}

		$this->account_settings = $config["accounts"][$this->service][$this->account];
		$this->mask = isset($config["messages"][$this->service][$this->account])
			? $config["messages"][$this->service][$this->account]
			: (isset($config["default_messages"][$this->service]) ? $config["default_messages"][$this->service] : NULL);

		if (is_null($this->mask))
		{
			throw new Twping_Exception("Invalid mask");
		}
		
		if (is_object($this->mask))
		{
			$this->message = call_user_func_array($this->mask, array($this->target, $this->service, $this->account));
		} else
		{
			$replace = array();
			foreach ($this->target["target_data"] as $key => $value)
			{
				$replace[":{$key}"] = $value;
			}
			$this->message = str_replace(array_keys($replace), array_values($replace), $this->mask);
		}

		if ($config["charset"] !== "UTF-8")
		{
			$this->message = iconv($config["charset"], "UTF-8", $this->message);
		}
	}

	public function send() {}
	public function __destruct() {
		Twping_Twping::instance()->insert($this->service,$this->account,$this->message,$this->target["target_type"],$this->target["target_id"]);
	}
}