<div align="left">
	<input type="button" value="Снять баны" onclick="deleteConfirm('bans', 'forumunbanuser', 'удалить')" />
	{if $_params.confirm eq 1}
		<input type="button" value="Подтвердить баны" onclick="deleteConfirm('bans', 'forumbanconfirmnick', 'изменить')" />
	{/if}
</div>	