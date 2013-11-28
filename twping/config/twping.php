<?php defined('DATALIFEENGINE') or die('No direct script access.');

return array(
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
			"QwteqQwteq" => array(
				'consumer_key' => 'eXz3sO73o45rCPgtSxYXoA',
				'consumer_secret' => 'rMHEUnZlozF9On63I0QKp7jwcwjRKkg9H5aBq0vzVYs',
				'token' => '2220018194-7scBwDrxh35sj5rRXi7VcnPnoqCkIYLCsxKqZsk',
				'token_secret' => 'Wn0fW83kDQ1yDL7hEXfvRTOBzByH7SBSmUy3aUwLVQhUA',
			),
		)
	)
);