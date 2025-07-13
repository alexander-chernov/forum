<b>{$_pageTitle}</b> "{$_dataGrid.user_name}"

<form action="" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Имя
		</td>
		<td>
			<input type="text" name="theme[user_name]" value="{$_dataGrid.user_name}" />
		</td>
	</tr>
	<tr>
		<td align="right">
			Ф.И.О.
		</td>
		<td>
			<input type="text" name="theme[user_fio]" value="{$_dataGrid.user_fio}" />
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumedituser" />
			<input type="submit" value="Редактировать" />
		</td>
	</tr>
</table>
</form>