CREATE TABLE IF NOT EXISTS `PREFIX_dletwping` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`service` varchar(16) NOT NULL,
	`account` varchar(254) NOT NULL,
	`text` text NOT NULL,
	`target_type` varchar(32) NOT NULL,
	`target_id` int(11) NOT NULL,
	`date_push` datetime NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO PREFIX_admin_sections (
	id,
	name,
	title,
	descr,
	icon,
	allow_groups)
	VALUES (
		NULL,
		'dle-twping',
		'Auto ping',
		'Логирование всех отправлений',
		'dle_twping.png',
		'');