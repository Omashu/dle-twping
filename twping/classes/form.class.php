<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

class Twping_Form {

	/**
	 * @var string target_type
	 */
	protected $target_type = NULL;

	/**
	 * @var int target_id
	 */
	protected $target_id = NULL;

	/**
	 * @var string text
	 */
	protected $text = NULL;

	/**
	 * @var array services
	 */
	protected $services = array();

	/**
	 * Create form
	 */
	public static function factory(array $services = array())
	{
		return new Twping_Form($services);
	}

	/**
	 * Construct
	 */
	public function __construct($services)
	{
		$this->configure($services);
	}

	/**
	 * Configure
	 * @param array $services
	 * @return void
	 */
	public function configure(array $services = array())
	{
		if (empty($services))
		{
			// all
			$this->services = array();
			$config = Twping_Twping::config();
			foreach ($config["allow_services"] as $service)
			{
				$this->services[$service] = isset($config["accounts"][$service])
					? array_keys($config["accounts"][$service])
					: array();
			}
		}
	}

	/**
	 * Возвращает список сервисов в конфигурации
	 * @return array
	 */
	public function services()
	{
		return $this->services;
	}

	/**
	 * Возвращает информацию о цели (для пинга)
	 * @return array
	 */
	public function target()
	{
		return array(
			"target_type" => $this->target_type,
			"target_id" => $this->target_id,
			"text" => $this->text,
		);
	}

	/**
	 * Возвращает выбранные сервисы и аккаунты
	 * @return array
	 */
	public function selected()
	{
		if (empty($_POST) OR !isset($_POST["twping_selected"]) OR !is_array($_POST["twping_selected"]))
		{
			return array();
		}

		$results = array();
		foreach ($_POST["twping_selected"] as $service => $accounts)
		{
			$accounts = array_values($accounts);
			$results[$service] = $accounts;
		}

		return $results;
	}

	/**
	 * Getter or setter
	 * @param mixed $value
	 * @return mixed
	 */
	public function target_type($value = NULL)
	{
		if (is_null($value))
		{
			return $this->target_type;
		}

		$this->target_type = (string)$value;
		return $this;
	}

	/**
	 * Getter or setter
	 * @param mixed $value
	 * @return mixed
	 */
	public function target_id($value = NULL)
	{
		if (is_null($value))
		{
			return $this->target_id;
		}

		$this->target_id = (int)$value;
		return $this;
	}

	/**
	 * Getter or setter
	 * @param mixed $value
	 * @return mixed
	 */
	public function text($value = NULL)
	{
		if (is_null($value))
		{
			return $this->text;
		}

		$this->text = (string)$value;
		return $this;
	}

	/**
	 * Генерируем форму
	 * @return string
	 */
	public function get_form()
	{
		if ($this->target_id AND $this->target_type)
		{
			$selected = Twping_Twping::instance()->check_by_target($this->target_type, $this->target_id);
		}

		$form = "<div style='margin:5px 0;padding:5px;background:#e7e7e7;border:1px solid #ddd;overflow:hidden;'>";
		foreach ($this->services as $service => $accounts)
		{
			$selected_by_service = isset($selected[$service])?$selected[$service]:array();
			$form .= "<div style='overflow:hidden;background:#fff;padding:5px;float:left;'>
					<label style='border-bottom:1px solid #ddd;'><span style='font-size:20px;'>{$service}:</span></label>
					<div style='padding:5px 0;'>";

			$form_accounts = array();
			foreach ($accounts as $account)
			{
				$form_accounts[] = "<label style='color:gray;margin-right:5px;padding-right:5px;'><input type='checkbox' name='twping_selected[{$service}][]' value='{$account}' data-service='twitter' ".(!isset($selected_by_service[$account])?"checked":"")."/> {$account}</label>";
			}

			$form .= implode("", $form_accounts);
			$form .= "</div></div>";
		}

		return $form . "</div>";
	}
}