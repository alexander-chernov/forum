<script id="objCalendarScript" type="text/javascript" src="/js/calendar.js"></script>

<form action="" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Заголовок
		</td>
		<td>
			<input type="text" name="theme[caption]" value="{$_dataGrid.caption}" />
		</td>
	</tr>
	<tr>
		<td align="right">
			Скрытая?
		</td>
		<td>
			<input type="checkbox" name="theme[hidden]" {if $_dataGrid.hidden eq 1}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td align="right">
			Закрытая?
		</td>
		<td>
			<input type="checkbox" name="theme[is_locked]" {if $_dataGrid.is_locked eq 1}checked="checked"{/if}/>
		</td>
	</tr>
	<tr>
		<td align="right">
			В топе раздела
		</td>
		<td>
			<input type="checkbox" name="theme[is_top]" {if $_dataGrid.is_top eq 1}checked="checked"{/if}/>
		</td>
	</tr>
	<tr>
		<td align="right">
			В топе горячих?
		</td>
		<td>
			<input type="checkbox" name="theme[hottop]" {if $_dataGrid.hottop eq 1}checked="checked"{/if}/>
		</td>
	</tr>
	<tr>
		<td align="right">
			Дата окончания топа
		</td>
		<td>
			<input id="objDate1" type="text" name="theme[top_end]" value="{if $_dataGrid.top_end neq 0}{$_dataGrid.top_end}{/if}" />
			{literal}
			<script language="JavaScript">
					var crCal1 = Calendar('objDate1', fmtYYYYMMDD24, '-', ':', 2000, 2055, false, objRussianStrings);
			</script>
			{/literal}
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumedittheme" />
			<input type="submit" value="Редактировать" />
		</td>
	</tr>
</table>
</form>