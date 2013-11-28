<?php defined('DATALIFEENGINE') or die('No direct script access.');

define("TWPINGPATH", ENGINE_DIR . "/modules/twping/");
require TWPINGPATH . "classes/core.class.php";
spl_autoload_register(array('Twping_Core', 'auto_load'));