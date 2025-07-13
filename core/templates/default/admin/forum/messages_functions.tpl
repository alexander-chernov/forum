<div align="left">
	<input type="button" value="Пометить как прочитанные" onclick="deleteConfirm('messages', 'forumreadmessage', 'пометить как прочитанные');" />
	<input type="button" value="Скрыть" onclick="deleteConfirm('messages', 'forumhidemessage', 'скрыть'); return false;" />
	<input type="button" value="Удалить" onclick="deleteConfirm('messages', 'forumtrashmessage', 'удалить'); return false;" />
</div>

{if not isset($_params.themeID) && not isset($_params.addr)}
<div align="right" style="padding-top: 5px">
	Фильтр:
	<a href="/.admin/forum/messages/index/">Все</a>
	|
	<a href="/.admin/forum/messages/index/commerce/all">Все на одной странице</a>
	|
	<a href="/.admin/forum/messages/index/commerce/yes">Коммерческие</a>
	|
	<a href="/.admin/forum/messages/index/commerce/no">Некоммерческие</a>
</div>
{/if}