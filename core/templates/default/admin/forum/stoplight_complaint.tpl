{include file="admin/error/error_messages.tpl"}

{if count($_dataGrid) > 0 }
	<table class="theme">
	<tr>
	{foreach item=curItem from=$_tableHeaders}
		<th>{$curItem}</th>
	{/foreach}
	</tr>
	{foreach item=curItem from=$_dataGrid}
		<tr class="tr1">
			<td><a href="/.admin/users/pager/send/to/{$curItem.authorID}">{$_params.authors[$curItem.userID]}</a></td>
			<td>{$_params.rules[$curItem.ruleID]}</td>
		</tr>
	{/foreach}
	</table>
{/if}