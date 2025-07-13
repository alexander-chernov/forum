Управление форумом
<ul>
<li><a href="/.admin/forum/groups/">Разделы</a></li>
<li><a href="/.admin/forum/messages/">Последние сообщения</a></li>
<li><a href="/.admin/forum/stoplight/">Опасные сообщения</a></li>
<li><a href="/.admin/forum/commerce/">Изображения<sup style="color:#ff0000;">NEW</sup></a></li>
</ul>

Управление банами
<ul>
<li><a href="/.admin/forum/messages/filter/">Поиск по IP</a></li>
<li><a href="/.admin/bans/words/">Запрещенные слова</a></li>
<li><a href="/.admin/bans/ip/">Запрещенные IP</a></li>
<li><a href="/.admin/bans/networks/">Запрещенные подсети</a></li>
<li><a href="/.admin/bans/nicknames/">Запрещенные пользователи</a></li>
<li><a href="/.admin/bans/nicknames/list/">Бан пользователей</a></li>
</ul>

{if $_commercial eq 1}
	Коммерчиский функционал
	<ul>
		<li><a href="/.admin/commercial/package/">Пакеты</a></li>
	</ul>
	<ul>
		<li><a href="/.admin/commercial/addmoney/">Пополнить счет</a></li>
	</ul>
{/if}

Дополнительно
<ul>
{if $_params.admin_notes eq 1}
<li><a href="/.admin/extra/notes/">Объявления</a></li>
{/if}
<li><a href="/forum/" target="_blank">Перейти на форум</a></li>
<li><a href="/?event=cmsuserlogout">Выход</a></li>
</ul>