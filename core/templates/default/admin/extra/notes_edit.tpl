<form action="" id="editform" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Текст объявления
		</td>
		<td>
			<textarea name="notes[content]" rows="10" cols="50">{$_dataGrid.content}</textarea>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumeditnotes" />
			<input type="submit" value="Редактировать" />
		</td>
	</tr>
</table>
</form>