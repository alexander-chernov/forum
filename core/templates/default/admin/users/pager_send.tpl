{include file="admin/error/error_messages.tpl"}

{if count($_errors) eq 0}
<form action="" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Имя пользователя
		</td>
		<td>
			<input readonly="readonly" type="text" name="user_name" value="{$_params.user_name}" />
			<input readonly="readonly" type="hidden" name="pager[userto]" value="{$_params.userto}" />
		</td>
	</tr>
	</tr>
	<tr>
		<td align="right">
			Сообщение
		</td>
		<td>
			<textarea name="pager[content]" rows="5" cols="80">{if $_params.content}{$_params.content}{/if}</textarea>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumpagersend" />
			<input type="submit" value="Добавить" />
		</td>
	</tr>
</table>
</form>
{/if}