<script type="text/javascript">
	{if $_params.banIDs} 
		var indexes = new Array(0, {$_params.banIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

{include file="admin/error/error_messages.tpl"}

	<form id="mainform" action="" method="POST">
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('bans')">Инвертировать выделение</a>&nbsp;
			{include file="admin/pager.tpl" up=1}
		</div>
		{include file="admin/bans/ip_functions.tpl" up=1}
{if count($_dataGrid) > 0 }
		<table class="theme">
			<tr>
				<th><input type="checkbox" onclick="checkAll('bans', this.checked, indexes)" /></th>
				<th>№</th>
				<th>IP-адрес</th>
				<th>Ник</th>
				<th>Реал</th>
				<th>Тип бана</th>
				<th>Начало бана</th>
				<th>Истекает</th>
				<th>Забанил</th>
				{if $_params.confirm eq 1}<th>Подтверждено?</th>{/if}
				<th><img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" /></th>
			</tr>
			{foreach key=key item=curItem from=$_dataGrid}
			<tr class="tr{if $key is odd}1{else}2{/if}">
				<td align="center"><input type="checkbox" id="bans_{$curItem.id}" name="bans[{$curItem.id}]" /></td>
				<td>{$curItem.counter}</td>
				<td>{$curItem.init_ip}</td>
				<td>{$curItem.author}</td>
				<td>{$curItem.realname}</td>
				<td>{$_ban_types[$curItem.ban_type]}</td>
				<td>{$curItem.banned_time|date_format:"%d/%m/%Y %H:%M"}</td>
				<td>
					{if $curItem.banned_period eq $curItem.banned_time}
						Никогда
					{else}
						{$curItem.banned_period|date_format:"%d/%m/%Y %H:%M"}
					{/if}
				</td>
				<td>
					<a hreft="/.admin/users/pager/send/to/{$curItem.authorID}" target="_blank">{$_params.authors[$curItem.adminID]}</a>
				</td>
				{if $_params.confirm eq 1}
					<td>
						{if $curItem.is_confirmed eq 1}
							Да
						{else}
							До {$curItem.banned_month|date_format:"%d/%m/%Y %H:%M"}
						{/if}
					</td>
				{/if}
				<td align="center" style="vertical-align:middle">
					<a href="/.admin/bans/ip/edit/{$curItem.id}/" target="_blank">
						<img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" />
					</a>
				</td>
			</tr>
			{/foreach}
		</table>
		<input type="hidden" value="" id="event" name="event"/>
		<input type="hidden" value="1" id="selector" name="selector"/>
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('bans')">Инвертировать выделение</a>&nbsp;
			{include file="admin/pager.tpl"}
		</div>
{else}
	<div>Не найдено ни одного IP</div>
		<br/><br/>
{/if}
		{include file="admin/bans/ip_functions.tpl" up=2}
	</form>