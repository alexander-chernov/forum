<form action="" id="editform" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Текст объявления
		</td>
		<td>
			<textarea name="notes[content]" rows="10" cols="50"></textarea>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumaddnotes" />
			<input type="submit" value="Добавить" />
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
	{if $_params.notesIDs} 
		var indexes = new Array(0, {$_params.notesIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

{include file="admin/error/error_messages.tpl"}

{if count($_dataGrid) > 0 }
	<form id="mainform" action="" method="POST">
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('notes')">Инвертировать выделение</a>&nbsp;
		</div>
		{include file="admin/extra/notes_functions.tpl"}
		
		<table class="theme">
		<tr>
			<th><input type="checkbox" onclick="checkAll('notes', this.checked, indexes)" /></th>
		{foreach item=curItem from=$_tableHeaders}
			<th>{$curItem}</th>
		{/foreach}
			<th><img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" /></th>
		</tr>
		{foreach key=key item=curItem from=$_dataGrid}
		<tr class="tr{if $curItem.is_top eq 1}3{else}{if $key is odd}1{else}2{/if}{/if}">
			<td align="center"><input type="checkbox" id="notes_{$curItem.id}" name="notes[{$curItem.id}]" /></td>
			<td>{$curItem.content|nl2br}</td>
			<td>{$curItem.created|date_format:"%d/%m/%Y %H:%M"}</td>
			<td align="center" style="vertical-align:middle">
				<a href="/.admin/extra/notes/edit/{$curItem.id}/" target="_blank">
					<img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" />
				</a>
			</td>
		</tr>
		{/foreach}
		</table>
		
		<input type="hidden" value="forumdeletenotes" id="event" name="event"/>
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('notes')">Инвертировать выделение</a>&nbsp;
		</div>
		{include file="admin/extra/notes_functions.tpl"}
	</form>
{else}
	<div>Список объявлений пуст</div>
{/if}