<div style="width:45%; float: left">
	<table class="theme">
	<tr>
	{foreach item=curItem from=$_tableHeaders}
		<th colspan="2">Разделы</th>
	{/foreach}
	</tr>
	{if count($_dataGrid) > 0 }
		{foreach key=key item=curItem from=$_dataGrid}
		<tr class="tr{if $key is odd}1{else}2{/if}">
			<td><a href="/.admin/forum/themes/index/{$curItem.groupID}/">{$curItem.caption}</a></td>
			<td>
				<a href="/forum/{$curItem.groupID}/" target="_blank">
					<img src="/images/admin/book_next.png" title="Перейти на форум" alt="Перейти на форум" />
				</a>
			</td>
		</tr>
		{/foreach}
	{else}
		<tr class="tr1">
			<td colspan="2">Вы не имеет прав ни на один раздел :(</td>
		</tr>
	{/if}
	</table>
</div>

<div style="width:50%; float: right">
	<table class="theme">
	<tr>
		<th>Объявления</th>
	</tr>
	{if count($_params.notes) > 0 }
		{foreach key=key item=curItem from=$_params.notes}
		<tr class="tr{if $key is odd}1{else}2{/if}">
			<td>
				<strong>{$curItem.created|date_format:"%d/%m/%Y %H:%M"}</strong>
				<br />
				{$curItem.content|nl2br}
			</td>
		</tr>
		{/foreach}
	{else}
		<tr class="tr1">
			<td>Объявлений пока нет.</td>
		</tr>
	{/if}	
	</table>
</div>