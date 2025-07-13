<form action="" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Содержание
		</td>
		<td>
			<textarea name="message[content]" rows="5" cols="50">{$_dataGrid.content}</textarea>
		</td>
	</tr>
	<tr>
		<td align="right">
			Скрытое?
		</td>
		<td>
			<input type="checkbox" name="message[hidden]" {if $_dataGrid.hidden eq 1}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumeditmessage" />
			<input type="submit" value="Редактировать" />
		</td>
	</tr>
</table>
</form>