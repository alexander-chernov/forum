<form action="" id="editform" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			Слово
		</td>
		<td>
			<input type="text" name="word[filter_string]" value="" />
		</td>
	</tr>
	<tr>
		<td align="right">
			По нику
		</td>
		<td>
			<input type="checkbox" name="word[flag_author]"  />
		</td>
	</tr>
	<tr>
		<td align="right">
			По заголовку
		</td>
		<td>
			<input type="checkbox" name="word[flag_caption]"  />
		</td>
	</tr>
	<tr>
		<td align="right">
			По содержанию
		</td>
		<td>
			<input type="checkbox" name="word[flag_content]" />
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumaddbadword" />
			<input type="submit" value="Добавить" />
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
	{if $_params.wordIDs} 
		var indexes = new Array(0, {$_params.wordIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

{include file="admin/error/error_messages.tpl"}

{if count($_dataGrid) > 0 }
	<form id="mainform" name="mainform" action="" method="POST">
		<div style="float:right">{include file="admin/pager.tpl" up=1}</div>
		{include file="admin/bans/words_functions.tpl"}
		<table class="theme">
		<tr>
			<th><input type="checkbox" onclick="checkAll('words', this.checked, indexes)" /></th>
			<th>№</th>
		{foreach item=curItem from=$_tableHeaders}
			<th>{$curItem}</th>
		{/foreach}
		</tr>
		{foreach key=key item=curItem from=$_dataGrid}
		<tr class="tr{if $key is odd}1{else}2{/if}">
			<td align="center"><input type="checkbox" id="words_{$curItem.wordID}" name="words[{$curItem.wordID}]" /></td>
			<td>{$curItem.counter}</td>
			<td><a href="/.admin/bans/words/edit/{$curItem.wordID}">{$curItem.filter_string}</a></td>
			<td>{$curItem.added|date_format:"%d/%m/%Y %H:%M"}</td>
			{if $curItem.flag_author}
				<td class="badword" style="background-color: rgb(179, 18, 53)">Да</td>
			{else}
				<td style="background-color: rgb(255, 0, 0)">&nbsp;</td>
			{/if}
			{if $curItem.flag_caption}
				<td class="badword" style="background-color: rgb(179, 18, 53)">Да</td>
			{else}
				<td style="background-color: rgb(255, 0, 0)">&nbsp;</td>
			{/if}
			{if $curItem.flag_content}
				<td class="badword" style="background-color: rgb(179, 18, 53)">Да</td>
			{else}
				<td style="background-color: rgb(255, 0, 0)">&nbsp;</td>
			{/if}
		</tr>
		{/foreach}
		</table>
		<input type="hidden" name="event" id="event" value="forumopenbadword" />
		<div style="float:right">{include file="admin/pager.tpl"}</div>
		{include file="admin/bans/words_functions.tpl"}
	</form>
{else}
	<div>Список пуст</div>
{/if}