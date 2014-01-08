<?php defined('DATALIFEENGINE') or die('No direct script access.');

$twping = array();

/*кодировка сайта CP1251 or UTF-8*/
$twping["charset"] = "UTF-8";

/*таблица с логами*/
$twping["table"] = "dletwping";

/*активные сервисы*/
$twping["allow_services"][] = "twitter";

/*активные типы данных*/
$twping["allow_targets"]["post"]["table"] = "post";
$twping["allow_targets"]["post"]["columns"] = "*";
$twping["allow_targets"]["post"]["primary"] = "id";

/*настройка аккаунтов сервисов*/
$twping["accounts"]["twitter"]["acc1_youkey"]["consumer_key"] 		= "value";
$twping["accounts"]["twitter"]["acc1_youkey"]["consumer_secret"] 	= "value";
$twping["accounts"]["twitter"]["acc1_youkey"]["token"] 						= "value";
$twping["accounts"]["twitter"]["acc1_youkey"]["token_secret"] 		= "value";

// пример для подключения второго аккаунта
// $twping["accounts"]["twitter"]["acc2_youkey"]["consumer_key"] 			= "value";
// $twping["accounts"]["twitter"]["acc2_youkey"]["consumer_secret"] 	= "value";
// $twping["accounts"]["twitter"]["acc2_youkey"]["token"] 						= "value";
// $twping["accounts"]["twitter"]["acc2_youkey"]["token_secret"] 			= "value";

/**
 * настройка текстовых сообщений
 * сообщение для аккаунта acc1_youkey в твиттер
 * можно указать маску типа string, либо лямбда функцию где выполнить нужные действия
 * доступные ключи: :title - название, :date - дата публикации, :autor - автор и прочие колонки из таблицы
 */
$twping["messages"]["twitter"]["acc1_youkey"] = function($target) {
	// генерируем прямую ссылку
	global $config;

	if ($config["allow_alt_url"] === "yes")
	{
		if ($config["seo_type"] == 1 OR $config["seo_type"] == 2)
		{
			if ($target["target_data"]["category"] AND $config["seo_type"] == 2)
			{
				$URL = $config["http_home_url"] . get_url($target["target_data"]["category"]) . "/" . $target["target_data"]["id"] . "-" . $target["target_data"]["alt_name"] . ".html";
			} else
			{
				$URL = $config["http_home_url"] . $target["target_data"]["id"] . "-" . $target["target_data"]["alt_name"] . ".html";
			}
		} else
		{
			$URL = $config["http_home_url"] . date('Y/m/d/', $target["target_data"]['date']) . $target["target_data"]["alt_name"] . ".html";
		}
	} else
	{
		$URL = $config["http_home_url"] . "index.php?newsid=" . $target["target_data"]['id'];
	}

	return $target["target_data"]["title"] . " - " . $URL;
};
// $twitter["messages"]["twitter"]["acc2_youkey"] = ":title";

/**
 * дефолтные сообщения
 */
$twping["default_messages"]["twitter"] = ":title";

return $twping;