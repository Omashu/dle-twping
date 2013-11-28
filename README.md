Установка
==========

* Выполнить SQL запросы из файла `additional/dump.sql` предварительно заменить слово `PREFIX` на свой реальный префикс таблиц
* Папку `twping` переместить в `engine/modules`
* Иконку `additional/dle_twping.png` переместить в `engine/skins/images`
* Откройте файл `engine/inc/editnews.php`

### ищем:
	if( $id == $row['id'] ) $found = TRUE;
	if( ! $found ) {
		msg( "error", $lang['cat_error'], $lang['edit_nonews'] );
	}

### ПОСЛЕ вставляем:
	// twping - begin
	require ENGINE_DIR . "/modules/twping/load.php";
	$twping_form = Twping_Form::factory()->target_type("news")->target_id($row['id']);
	// twping - end

### в этом же файле ищем:
	echo <<<HTML
	<div class="dle_aTab" style="display:none;">

### ПЕРЕД вставляем:
	echo $twping_form->get_form();

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
	echo <<<HTML
	<div class="dle_aTab" style="display:none;">

### ПЕРЕД вставляем:
	echo $twping_form->get_form();

### ищем код примерно такого содержания:
	$db->query( "INSERT INTO " . PREFIX . "_post.................');
	$row = $db->insert_id();

### ПОСЛЕ вставляем:
	$twping_form->text($title)->target_id($row);
	Twping_Twping::instance()->send($twping_form->selected(), $twping_form->target());