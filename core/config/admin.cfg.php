<?php
/*
 * action => configuration array
 */
	
$Admin_Cfg = array(
	'error_index' => array(
		'folder' => 'error',
		'title' => 'Ошибка',
		'headers' => array(),
	),
	'groups_index' => array(
		'folder' => 'forum',
		'title' => 'Разделы',
		'headers' => array(
			'Название', 
		),
	),
	'notes_index' => array(
		'folder' => 'extra',
		'title' => 'Объявления',
		'headers' => array(
			'Текст', 
			'Дата создания',
		),
	),
	'notes_edit' => array(
		'folder' => 'extra',
		'title' => 'Редактирование объявления',
		'headers' => array(),
	),
	'themes_index' => array(
		'folder' => 'forum',
		'title' => 'Последние темы',
		'headers' => array(
			'Название', 
			'КC',
			'Автор',
			'IP',
			'Дата создания',
			'Дата обновления',
		),
	),
	'themes_edit' => array(
		'folder' => 'forum',
		'title' => 'Редактирование темы',
		'headers' => array(),
	),
	'messages_index' => array(
		'folder' => 'forum',
		'title' => 'Последние сообщения',
		'headers' => array(
			'Текст сообщения', 
			'Автор',
			'IP-адрес',
			'Дата создания',
		),
	),
	'messages_edit' => array(
		'folder' => 'forum',
		'title' => 'Редактирование сообщения',
		'headers' => array(),
	),
	'messages_filter' => array(
		'folder' => 'forum',
		'title' => 'Поиск по IP',
		'headers' => array(),
	),
	'stoplight_index' => array(
		'folder' => 'forum',
		'title' => 'Опасные сообщения',
		'headers' => array(
			'Текст сообщения', 
			'Автор',
			'IP-адрес',
			'Дата создания',
			'Уровень опасности'
		),
	),
	'stoplight_complaint' => array(
		'folder' => 'forum',
		'title' => 'Список жалоб',
		'headers' => array(
			'Ник',
			'Правило'
		),
	),
	'userlist_index' => array(
		'folder' => 'users',
		'title' => 'Список пользователей',
		'headers' => array(
			'Ник', 
			'E-mail',
			'Ф.И.О.',
			'IP-адрес'
		),
	),
	'userlist_edit' => array(
		'folder' => 'users',
		'title' => 'Редактирование пользователя',
		'headers' => array(),
	),
	'package_index' => array(
		'folder' => 'commercial',
		'title' => 'Пакеты услуг',
		'headers' => array(),
	),
	'package_edit' => array(
		'folder' => 'commercial',
		'title' => 'Пакеты услуг редактирование',
		'headers' => array(),
	),
	'package_add' => array(
		'folder' => 'commercial',
		'title' => 'Пакеты услуг добавление',
		'headers' => array(),
	),
	'packservices_index' => array(
		'folder' => 'commercial',
		'title' => 'Сервисы',
		'headers' => array(),
	),
	'addmoney_index' => array(
		'folder' => 'addmoney',
		'title' => 'Пополнение баланса',
		'headers' => array(),
	),
	'networks_add' => array(
		'folder' => 'bans',
		'title' => 'Запретить подсети',
		'headers' => array(),
	),
	'networks_edit' => array(
		'folder' => 'bans',
		'title' => 'Редактировать подсети',
		'headers' => array(),
	),
	'networks_index' => array(
		'folder' => 'bans',
		'title' => 'Запрещенные подсети',
		'headers' => array(),
	),
	'ip_index' => array(
		'folder' => 'bans',
		'title' => 'Запрещенные IP',
		'headers' => array(),
	),
	'ip_add' => array(
		'folder' => 'bans',
		'title' => 'Забанить IP-адрес',
		'headers' => array(
			'IP-адрес', 
			'Начало бана',
			'Истекает',
			'Комментарий',
			'Тип бана',
			'Забанил',
			'Подтверждено?',
		),
	),
	'ip_edit' => array(
		'folder' => 'bans',
		'title' => 'Редактирование бана',
		'headers' => array(),
	),
	'nicknames_index' => array(
		'folder' => 'bans',
		'title' => 'Запрещенные пользователи',
		'headers' => array(
			'Пользователь', 
			'Начало бана',
			'Истекает',
			'Комментарий',
			'Подтверждено?',
		),
	),
	'nicknames_list' => array(
		'folder' => 'bans',
		'title' => 'Поиск пользователей',
		'headers' => array(),
	),
	'nicknames_edit' => array(
		'folder' => 'bans',
		'title' => 'Поиск пользователей',
		'headers' => array(),
	),
	'words_index' => array(
		'folder' => 'bans',
		'title' => 'Запрещенные слова',
		'headers' => array(
			'Слово', 
			'Дата добавления',
			'По нику',
			'По заголовку',
			'По содержанию'
		),
	),
	'words_add' => array(
		'folder' => 'bans',
		'title' => 'Добавление фильтра',
		'headers' => array(),
	),
	'words_edit' => array(
		'folder' => 'bans',
		'title' => 'Редактирование фильтра',
		'headers' => array(),
	),
	'pager_send' => array(
		'folder' => 'users',
		'title' => 'Отправка сообщения',
		'headers' => array(),
	),
/*
	'commerce_index' => array(
		'folder' => 'commerce',
		'title' => 'Коммерческие темы',
		'headers' => array(
			'Название', 
			'КC',
			'Автор',
			'IP',
			'Дата создания',
			'Дата обновления',
		),
	),
 *
 */
    'commerce_index' => array(
        'folder' => 'commerce',
        'title' => 'Картинки',
        'headers' => array(
            'Изображение', 
            'Тема/Сообщение',
            'Автор',
            'IP',
            'Дата создания',
        ),
    ),
);
?>