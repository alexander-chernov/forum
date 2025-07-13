<form action="" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Слово
		</td>
		<td>
			<input type="text" name="word[filter_string]" value="{$_dataGrid.filter_string}" />
		</td>
	</tr>
	<tr>
		<td align="right">
			По нику
		</td>
		<td>
			<input type="checkbox" name="word[flag_author]" {if $_dataGrid.flag_author eq 1}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td align="right">
			По заголовку
		</td>
		<td>
			<input type="checkbox" name="word[flag_caption]" {if $_dataGrid.flag_caption eq 1}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td align="right">
			По содержанию
		</td>
		<td>
			<input type="checkbox" name="word[flag_content]" {if $_dataGrid.flag_content eq 1}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumeditbadword" />
			<input type="submit" value="Редактировать" />
		</td>
	</tr>
</table>
</form>