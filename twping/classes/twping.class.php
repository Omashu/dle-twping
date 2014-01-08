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
	 * @var object database
	 */
	protected $db;

	/**
	 * Construct
	 */
	public function __construct()
	{
		global $db;
		$this->db = $db;
		$this->config = require TWPINGPATH . "config/twping.php";
		$this->table = PREFIX . "_" . $this->config["table"];

		if (isset($this->config["allow_targets"]) AND is_array($this->config["allow_targets"]))
		{
			foreach ($this->config["allow_targets"] as $type => &$type_data)
			{
				if (!isset($type_data["table"]))
				{
					unset($this->config["allow_targets"][$type]);
					continue;
				}

				$type_data["table"] = PREFIX . "_" . $type_data["table"];

				if (!isset($type_data["columns"]) OR !is_string($type_data["columns"]))
				{
					$type_data["columns"] = "*";
				}

				if (!isset($type_data["primary"]) OR !is_string($type_data["primary"]))
				{
					$type_data["primary"] = "id";
				}
			}
		}
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
	public function config()
	{
		return $this->config;
	}

	/**
	 * Send
	 * @param array $services по каким сервисам раскидать ('twitter' => array('account'))
	 * @param array $target инфа о материале, получаем от Twping_Form target();
	 * @return array
	 */
	public function send(array $services = array(), array $target = array())
	{
		$results = array();

		// раскидываем сообщения
		foreach ($services as $service => $accounts)
		{
			$results[$service] = array_map(function($account) use ($service, $target) {
				$class = "Twping_Service_" . $service;
				if (class_exists($class))
				{
					$obj = new $class($service, $account, $target);
					$obj->send();
					unset($obj);
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
		$date_push = $date_push ? $date_push : date("Y-m-d H:i:s");
		// mysql real escape string
		$service = $this->db->safesql($service);
		$account = $this->db->safesql($account);
		$text = $this->db->safesql($text);
		$target_type = $this->db->safesql($target_type);
		$target_id = (int)$target_id;
		$date_push = $this->db->safesql($date_push);

		return (bool)$this->db->query("INSERT INTO {$this->table} (
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
	 * Проверка пинговался ли материал ранее
	 * @param string $target_type
	 * @param string $target_id
	 * @return array
	 */
	public function check_by_target($target_type,$target_id)
	{
		$target_id = (int)$target_id;
		$target_type = $this->db->safesql($target_type);

		$query = $this->db->query("SELECT service, account FROM {$this->table} WHERE target_type = '{$target_type}' AND target_id = {$target_id}");
		$results = array();

		while ($row = $this->db->get_array())
		{
			$results[$row["service"]][$row["account"]] = true;
		}

		return $results;
	}

	/**
	 * Получаем материал по типу и идентификатору
	 * @param string $target_type
	 * @param int $target_id
	 * @return array
	 */
	public function get_by_target($target_type, $target_id)
	{
		$target_id = (int)$target_id;
		$table = isset($this->config["allow_targets"][$target_type]["table"])
			? $this->config["allow_targets"][$target_type]["table"]
			: NULL;

		if (!$target_id OR !$table)
		{
			return array();
		}

		$columns = $this->config["allow_targets"][$target_type]["columns"];
		$primary = $this->config["allow_targets"][$target_type]["primary"];

		$row = $this->db->query("SELECT {$columns} FROM {$table} WHERE {$primary} = '{$target_id}'");
		return $this->db->get_row();
	}

	/**
	 * Get logs
	 * @param int $page
	 * @param int $per_page
	 * @return array
	 */
	public function select($page = 1, $per_page = 25, &$count)
	{
		$page = max(1, (int)$page);
		$per_page = (int)$per_page;
		$offset = ($page-1)*$per_page;
		$query = $this->db->query("SELECT id AS twping_id, service AS twping_service, account AS twping_account, text AS twping_text, target_type AS twping_target_type, target_id AS twping_target_id, date_push AS twping_date_push FROM {$this->table} ORDER BY id DESC LIMIT {$offset},{$per_page}");

		$results = array();
		$target_ids = array();
		while ($row = $this->db->get_array())
		{
			$row["target"] = NULL;
			$target_ids[$row["twping_target_type"]][] = $row["twping_target_id"];
			$results[$row["twping_id"]] = $row;
		}

		$targets = array();
		foreach ($target_ids as $type => $ids)
		{
			$targets[$type] = array();
			if (!count($ids) OR !isset($this->config["allow_targets"][$type]["table"])) continue;
			$ids = array_unique($ids);

			// тянем связи
			$table = $this->config["allow_targets"][$type]["table"];
			$primary = $this->config["allow_targets"][$type]["primary"];
			$query = $this->db->query("SELECT * FROM {$table} WHERE {$primary} IN(".implode(",",$ids).")");
			while ($row = $this->db->get_array())
			{
				$targets[$type][$row["id"]] = $row;
			}
		}

		// цепляем связи к результату
		foreach ($results as &$row)
		{
			$row["target"] = isset($targets[$row["twping_target_type"]][$row["twping_target_id"]])?$targets[$row["twping_target_type"]][$row["twping_target_id"]]:NULL;
		}

		$count = $this->db->query("SELECT COUNT(id) AS count FROM {$this->table}");
		$count = $this->db->get_row();
		$count = $count["count"];

		return $results;
	}
}