{include file="admin/error/error_messages.tpl"}

<script type="text/javascript">
	{literal}
		function mkSendForm(id, vEvent){
			if (vEvent != undefined){
				document.getElementById('formEvent' + id).value = vEvent;
			}
			document.getElementById('formSend' + id).submit();
		} 
	{/literal}
	{if $_params.banIDs} 
		var indexes = new Array(0, {$_params.banIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

<form action="/.admin/bans/networks/add/" method="GET" id="formSendAdd">
</form>
<input type="button" value="Добавить" onclick="mkSendForm('Add');" />
&nbsp;
<input type="button" value="Удалить" onclick="mkSendForm('Main', 'forumbannetdelete');" />
<form id="formSendMain" action="" method="POST">
<input type="hidden" name="event" value="" id="formEventMain" />
{if count($_dataGrid) > 0 }
	<table class="theme">
		<tr>
			<th><input type="checkbox" onclick="checkAll('bans', this.checked, indexes)" /></th>
			<th>№</th>
			<th>Начальный IP</th>
			<th>Конечный IP</th>
			<th>Начало бана</th>
			<th>Истекает</th>
			<th>Описание сети</th>
			<th>Тип бана</th>
			<th><img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" /></th>
		</tr>
		{foreach key=key item=curItem from=$_dataGrid}
		<tr class="tr{if $key is odd}1{else}2{/if}">
			<td align="center"><input type="checkbox" value="{$curItem.id}" id="bans_{$curItem.id}" name="bans[{$curItem.id}]" /></td>
			<td><strong>{$curItem.counter}</strong></td>
			<td>{$curItem.init_ip}</td>
			<td>{$curItem.end_ip}</td>
			<td>{$curItem.banned_time|date_format:"%d/%m/%Y %H:%M"}</td>
			<td>{$curItem.banned_period|date_format:"%d/%m/%Y %H:%M"}</td>
			<td>{$curItem.network_description}</td>
			<td>{$curItem.ban_type}</td>
			<td align="center" style="vertical-align:middle">
				<a href="/.admin/bans/networks/edit/{$curItem.id}/" target="_blank">
					<img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" />
				</a>
			</td>
		</tr>
		{/foreach}
	</table>
{else}
	<div>Список пуст</div>
{/if}
</form>