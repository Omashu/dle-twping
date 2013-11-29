<?php defined('DATALIFEENGINE') or die('No direct script access.');

return array(
	// кодировка сайта
	"charset" => "CP1251", // CP1251 or UTF-8
	// таблица с логами
	"table" => "dletwping",
	// активные сервисы
	"allow_services" => array(
		"twitter",
	),
	// активные типы
	"allow_targets" => array(
		"news",
	),
	// настройка аккаунтов
	"accounts" => array(
		// создайте приложение в твиттере с правами на постинг, получанные ключи укажите тут
		"twitter" => array(
			"login" => array(
				'consumer_key' => 'key',
				'consumer_secret' => 'key',
				'token' => 'key',
				'token_secret' => 'key',
			),
		)
	)
);