<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

class Twping_Twping {

	/**
	 * @var object Twping_Twping
	 */
	protected static $instance;

	/**
	 * @var array configuration
	 */
	protected $config;

	/**
	 * @var string table name
	 */
	protected $table;

	/**
	 * Construct
	 */
	public function __construct()
	{
		$this->config = require TWPINGPATH . "config/twping.php";
		$this->table = PREFIX . "_" . $this->config["table"];
	}

	/**
	 * Get a singleton
	 * @return object Twping_Twping
	 */
	public static function instance()
	{
		if (!isset(Twping_Twping::$instance))
		{
			Twping_Twping::$instance = new Twping_Twping();
		}

		return Twping_Twping::$instance;
	}

	/**
	 * Get config
	 * @return array
	 */
	public static function config()
	{
		return Twping_Twping::instance()->config;
	}

	/**
	 * Send
	 * @param array $services по каким сервисам раскидать ('twitter' => array('account')), пропустить, чтобы раскидать по всем
	 * @param array $target инфа о материале, который пингуем array('target_type' => 'news', 'target_id' = int, 'text' => string)
	 * @return array OR exception Twping_Exception
	 */
	public function send(array $services = array(), array $target = array())
	{
		if (empty($services))
		{
			foreach ($this->config["allow_services"] as $service)
			{
				$services[$service] = isset($this->config["accounts"][$service])
					? array_keys($this->config["accounts"][$service])
					: array();
			}
		}

		if (empty($services))
		{
			throw new Twping_Exception("Empty services, please set the services and accounts");
		}

		$results = array();

		// раскидываем сообщения
		foreach ($services as $service => $accounts)
		{
			$results[$service] = array_map(function($account) use ($service, $target) {
				$class = "Twping_Service_" . $service;
				if (class_exists($class))
				{
					return call_user_func_array(array($class, "send"), array($account, $target));
				}
			}, $accounts);
		}

		return $results;
	}

	/**
	 * Insert log data
	 * @param string $service
	 * @param string $account
	 * @param string $text
	 * @param string $target_type
	 * @param string $target_id
	 * @param string $date_push
	 * @return integer|boolean
	 */
	public function insert($service,$account,$text,$target_type,$target_id,$date_push = NULL)
	{
		global $db;
		$date_push = $date_push ? $date_push : date("Y-m-d H:i:s");
		// mysql real escape string
		$service = $db->safesql($service);
		$account = $db->safesql($account);
		$text = $db->safesql($text);
		$target_type = $db->safesql($target_type);
		$target_id = (int)$target_id;
		$date_push = $db->safesql($date_push);

		return (bool)$db->query("INSERT INTO {$this->table} (
			service,
			account,
			text,
			target_type,
			target_id,
			date_push) VALUES (
			'{$service}',
			'{$account}',
			'{$text}',
			'{$target_type}',
			'{$target_id}',
			'{$date_push}'
		)");
	}

	/**
	 * Get by target
	 * @param string $target_type
	 * @param string $target_id
	 * @return array
	 */
	public function check_by_target($target_type,$target_id)
	{
		global $db;

		$target_id = (int)$target_id;
		$target_type = $db->safesql($target_type);

		$query = $db->query("SELECT service, account FROM {$this->table} WHERE target_type = '{$target_type}' AND target_id = {$target_id}");
		$results = array();

		while ($row = $db->get_array())
		{
			$results[$row["service"]][$row["account"]] = true;
		}

		return $results;
	}

	/**
	 * Get logs
	 * @param int $page
	 * @param int $per_page
	 * @return array
	 */
	public function select($page = 1, $per_page = 25, &$count)
	{
		global $db;
		$page = max(1, (int)$page);
		$per_page = (int)$per_page;
		$offset = ($page-1)*$per_page;
		$query = $db->query("SELECT id AS twping_id, service AS twping_service, account AS twping_account, text AS twping_text, target_type AS twping_target_type, target_id AS twping_target_id, date_push AS twping_date_push FROM {$this->table} ORDER BY id DESC LIMIT {$offset},{$per_page}");

		$results = array();
		while ($row = $db->get_array())
		{
			$results[$row["twping_id"]] = $row;
		}

		$target_ids = array();
		foreach ($results as $row)
		{
			$row["target"] = NULL;
			$target_ids[$row["twping_target_type"]][] = $row["twping_target_id"];
		}

		$targets = array();
		foreach ($target_ids as $type => $ids)
		{
			$targets[$type] = array();
			if (!count($ids)) continue;
			$ids = array_unique($ids);

			// тянем связи
			$query = $db->query("SELECT id,title FROM ".PREFIX."_post WHERE id IN(".implode(",",$ids).")");
			while ($row = $db->get_array())
			{
				$targets[$type][$row["id"]] = $row;
			}
		}

		// цепляем связи к результату
		foreach ($results as &$row)
		{
			$row["target"] = isset($targets[$row["twping_target_type"]][$row["twping_target_id"]])?$targets[$row["twping_target_type"]][$row["twping_target_id"]]:NULL;
		}

		$count = $db->query("SELECT COUNT(id) AS count FROM {$this->table}");
		$count = $db->get_row();
		$count = $count["count"];

		return $results;
	}
}