<script type="text/javascript">
	{if $_params.banIDs} 
		var indexes = new Array(0, {$_params.banIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

{include file="admin/error/error_messages.tpl"}

{if count($_dataGrid) > 0 }
	<form id="mainform" action="" method="POST">
		<div style="float:right">{include file="admin/pager.tpl" up=1}</div>
		{include file="admin/bans/nicknames_functions.tpl"}
		<table class="theme">
			<tr>
				<th><input type="checkbox" onclick="checkAll('bans', this.checked, indexes)" /></th>
				<th>№</th>
				{foreach item=curItem from=$_tableHeaders}
					<th>{$curItem}</th>
				{/foreach}
				<th><img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" /></th>
			</tr>
			{foreach key=key item=curItem from=$_dataGrid}
			<tr class="tr{if $key is odd}1{else}2{/if}">
				<td align="center"><input type="checkbox" id="bans_{$curItem.userID}" name="bans[{$curItem.userID}]"  value="{$curItem.userID}" /></td>
				<td>{$curItem.counter}</td>
				<td>{$curItem.user_name}</td>
				<td>{$curItem.when_banned|date_format:"%d/%m/%Y %H:%M"}</td>
				<td>{$curItem.ban_end|date_format:"%d/%m/%Y %H:%M"}</td>
				<td>{$curItem.admin_comment|nl2br}</td>
				{if $_params.confirm eq 1}
					<td>{if $curItem.is_confirmed eq 1}Да{else}Нет{/if}</td>
				{/if}
				<td align="center" style="vertical-align:middle">
				<a href="/.admin/bans/nicknames/edit/{$curItem.userID}/" target="_blank">
					<img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" />
				</a>
			</td>
			</tr>
			{/foreach}
		</table>
		<input type="hidden" value="" id="event" name="event"/>
		<div style="float:right">{include file="admin/pager.tpl"}</div>
		{include file="admin/bans/nicknames_functions.tpl"}
	</form>
{else}
	<div>Список пуст</div>
{/if}