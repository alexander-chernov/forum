{include file="admin/header.tpl"}
<table class="grid">
<tr>
	<td class="col1">
		<div class="menu">{include file="admin/menu.tpl"}</div>
	</td>
	<td class="col2">
		{include file="admin/$_folder/$_action.tpl"}
	</td>
</tr>
</table>
{include file="admin/footer.tpl"}