Установка
==========

* Выполнить SQL запросы из файла `additional/dump.sql` предварительно заменить слово `PREFIX` на свой реальный префикс таблиц
* Папку `twping` переместить в `engine/modules`
* Иконку `additional/dle_twping.png` переместить в `engine/skins/images`
* Файл `additional/inc/dle-twping.php` переместить в `engine/inc`
* Откройте файл `engine/inc/editnews.php`

### ищем:
	include_once ENGINE_DIR . '/classes/parse.class.php';

### ПОСЛЕ вставляем:
	require ENGINE_DIR . "/modules/twping/load.php";

### ищем:
	if( $id == $row['id'] ) $found = TRUE;
	if( ! $found ) {
		msg( "error", $lang['cat_error'], $lang['edit_nonews'] );
	}

### ПОСЛЕ вставляем:
	$twping_form = Twping_Form::factory()->target_type("news")->target_id($row['id']);

### ищем:
	elseif( $action == "doeditnews" ) {

		$id = intval( $_GET['id'] );

### ПОСЛЕ вставляем:
	$twping_form = Twping_Form::factory()->target_type("news")->target_id($id);

### в этом же файле ищем:
	{$fix_br}

### ПОСЛЕ вставляем:
	<br/>{$twping_form->get_form()}

### ищем примерно такое:
	if ($item_db[6]) $db->query( "UPDATE "

### ПЕРЕД вставляем:
	$twping_form->text($title)->target_id($item_db[0]);
	Twping_Twping::instance()->send($twping_form->selected(), $twping_form->target());

* Откройте файл `engine/inc/addnews.php`

### ищем:
	if( $action == "addnews" ) {

### ПЕРЕД вставляем:
	// twping - begin
	require ENGINE_DIR . "/modules/twping/load.php";
	$twping_form = Twping_Form::factory()->target_type("news");
	// twping - end

### ищем:
	{$fix_br}

### ПОСЛЕ вставляем:
	<br/>{$twping_form->get_form()}

### ищем код примерно такого содержания:
	$db->query( "INSERT INTO " . PREFIX . "_post.................');
	$row = $db->insert_id();

### ПОСЛЕ вставляем:
	$twping_form->text($title)->target_id($row);
	Twping_Twping::instance()->send($twping_form->selected(), $twping_form->target());

* На сайте https://dev.twitter.com/ создать приложение с правами `read and write`, полученные ключи указать в файле конфигурации `twping/config/twping.php`